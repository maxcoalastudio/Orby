<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login');
    exit();
}

require_once('public/model/Conexao.php');

$conn = new Usuarios();
$id = $_SESSION['usuario_id'];

// Buscar solicitações RECEBIDAS
$solicitacoes_recebidas = $conn->getSolicitacoesPendentes($id);

// Buscar solicitações ENVIADAS
$sql_enviadas = "SELECT s.*, u.usuario as nome_destinatario, ui.imagem_perfil 
                 FROM solicitacoes s
                 INNER JOIN usuarios u ON s.id_destinatario = u.id
                 LEFT JOIN user_info ui ON u.id = ui.id_usuario
                 WHERE s.id_remetente = ? AND s.status = 'pendente'
                 ORDER BY s.data_envio DESC";
$stmt = $conn->getPdo()->prepare($sql_enviadas);
$stmt->execute([$id]);
$solicitacoes_enviadas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar informações dos amigos
$amigos_info = [];
if (!empty($_SESSION['amigos_ids'])) {
    $amigos_info = $conn->buscarUsuariosPorIds($_SESSION['amigos_ids']);
}

// Buscar sugestões de amigos
$sql = "SELECT u.id, u.usuario FROM usuarios u 
        WHERE u.id != ? 
        AND u.id NOT IN (SELECT id_amigo FROM amigos WHERE id_usuario = ?)
        AND u.id NOT IN (SELECT id_usuario FROM amigos WHERE id_amigo = ?)
        AND u.id NOT IN (
            SELECT id_destinatario FROM solicitacoes 
            WHERE id_remetente = ? AND status = 'pendente'
        )
        AND u.id NOT IN (
            SELECT id_remetente FROM solicitacoes 
            WHERE id_destinatario = ? AND status = 'pendente'
        )
        ORDER BY RANDOM() LIMIT 10";

$stmt = $conn->getPdo()->prepare($sql);
$stmt->execute([$id, $id, $id, $id, $id]);
$sugestoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$nome = $_SESSION['usuario_nome'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Amigos de <?php echo $nome; ?></title>
    
    <link rel="stylesheet" href="/public/css/main.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <?php require_once("public/view/modulos/header.php"); ?>
    </header>

    <div class="container">
        <h1>Gerenciar Amigos</h1>
        
        <?php if (isset($_SESSION['sucesso'])): ?>
            <div class="mensagem sucesso"><?php echo $_SESSION['sucesso']; unset($_SESSION['sucesso']); ?></div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['erro'])): ?>
            <div class="mensagem erro"><?php echo $_SESSION['erro']; unset($_SESSION['erro']); ?></div>
        <?php endif; ?>

        <!-- SOLICITAÇÕES RECEBIDAS -->
        <h2 class="secao-titulo">Solicitações Recebidas</h2>
        <?php if (!empty($solicitacoes_recebidas)): ?>
            <div class="solicitacoes-grid">
                <?php foreach ($solicitacoes_recebidas as $sol): ?>
                    <div class="card">
                        <span class="status-badge status-pendente">⏳ Pendente</span>
                        <img src="<?php echo (!empty($sol['imagem_perfil'])) ? 'data:image/jpeg;base64,' . base64_encode($sol['imagem_perfil']) : '/public/image/perfil_default.jpg'; ?>" 
                             alt="<?php echo htmlspecialchars($sol['nome_remetente']); ?>">
                        <h3><?php echo htmlspecialchars($sol['nome_remetente']); ?></h3>
                        <p>Quer ser seu amigo</p>
                        <p><small>Enviada em: <?php echo date('d/m/Y H:i', strtotime($sol['data_envio'])); ?></small></p>
                        
                        <form method="POST" action="valida_dados_amigos">
                            <input type="hidden" name="id_solicitacao" value="<?php echo $sol['id_solicitacao']; ?>">
                            <button type="submit" name="aceitar_solicitacao" class="btn btn-success">Aceitar</button>
                            <button type="submit" name="recusar_solicitacao" class="btn btn-danger">Recusar</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-message">Nenhuma solicitação de amizade recebida.</div>
        <?php endif; ?>

        <!-- SOLICITAÇÕES ENVIADAS -->
        <h2 class="secao-titulo">Solicitações Enviadas</h2>
        <?php if (!empty($solicitacoes_enviadas)): ?>
            <div class="solicitacoes-grid">
                <?php foreach ($solicitacoes_enviadas as $sol): ?>
                    <div class="card card-sent">
                        <span class="status-badge status-aguardando">⏳ Aguardando resposta</span>
                        <img src="<?php echo (!empty($sol['imagem_perfil'])) ? 'data:image/jpeg;base64,' . base64_encode($sol['imagem_perfil']) : '/public/image/perfil_default.jpg'; ?>" 
                             alt="<?php echo htmlspecialchars($sol['nome_destinatario']); ?>">
                        <h3><?php echo htmlspecialchars($sol['nome_destinatario']); ?></h3>
                        <p>Você enviou solicitação</p>
                        <p><small>Enviada em: <?php echo date('d/m/Y H:i', strtotime($sol['data_envio'])); ?></small></p>
                        
                        <form method="POST" action="valida_dados_amigos">
                            <input type="hidden" name="id_solicitacao" value="<?php echo $sol['id_solicitacao']; ?>">
                            <button type="submit" name="cancelar_solicitacao" class="btn btn-warning">Cancelar</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-message">Nenhuma solicitação enviada aguardando resposta.</div>
        <?php endif; ?>

        <!-- LISTA DE AMIGOS -->
        <h2 class="secao-titulo">Meus Amigos (<?php echo count($amigos_info); ?>)</h2>
        <div class="friends-grid">
            <?php if (empty($amigos_info)): ?>
                <div class="empty-message">Você ainda não tem amigos. Envie solicitações para alguém!</div>
            <?php else: ?>
                <?php foreach ($amigos_info as $amigo): ?>
                    <div class="card">
                        <img src="<?php echo (!empty($amigo['imagem_perfil'])) ? 'data:image/jpeg;base64,' . base64_encode($amigo['imagem_perfil']) : '/public/image/perfil_default.jpg'; ?>" 
                             alt="<?php echo htmlspecialchars($amigo['usuario']); ?>">
                        <h3><?php echo htmlspecialchars($amigo['usuario']); ?></h3>
                        <p class="bio"><?php echo htmlspecialchars($amigo['bio'] ?? 'Sem bio'); ?></p>
                        
                        <a href="index.php?page=perfil_amigo&id=<?php echo $amigo['id']; ?>" class="btn btn-primary">Ver Perfil</a>
                        
                        <form method="POST" action="valida_dados_amigos" style="display: inline;">
                            <input type="hidden" name="id_amigo" value="<?php echo $amigo['id']; ?>">
                            <button type="submit" name="remover_amigo" class="btn btn-danger">Remover</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- SUGESTÕES DE AMIGOS -->
        <h2 class="secao-titulo">Pessoas que você talvez conheça</h2>
        <div class="friends-grid">
            <?php if (empty($sugestoes)): ?>
                <div class="empty-message">Não há mais sugestões no momento.</div>
            <?php else: ?>
                <?php foreach ($sugestoes as $sugestao): ?>
                    <div class="card">
                        <img src="/public/image/perfil_default.jpg" alt="<?php echo htmlspecialchars($sugestao['usuario']); ?>">
                        <h3><?php echo htmlspecialchars($sugestao['usuario']); ?></h3>
                        <form method="POST" action="valida_dados_amigos">
                            <input type="hidden" name="id_amigo" value="<?php echo $sugestao['id']; ?>">
                            <button type="submit" name="enviar_solicitacao" class="btn btn-primary">Enviar Solicitação</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <footer class="app-footer">
        <?php require_once("public/view/modulos/footer.php"); ?>
    </footer>
</body>
</html>