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

$id_amigo = $_GET['id'];
$id_usuario = $_SESSION['usuario_id'];

if ($id_amigo == 0 || $id_amigo == $id_usuario) {
    header('Location: perfil');
    exit();
}

// Busca informações do amigo
$sql = "SELECT u.id, u.usuario, u.email, ui.* 
        FROM usuarios u
        LEFT JOIN user_info ui ON u.id = ui.id_usuario
        WHERE u.id = ?";

$stmt = $conn->getPdo()->prepare($sql);
$stmt->execute([$id_amigo]);
$amigo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$amigo) {
    header('Location: gerencia_amigos');
    exit();
}

if (!isset($amigo['privacidade'])) {
    $amigo['privacidade'] = 'publico';
}

// Verifica status da amizade
$status = $conn->verificarStatusAmizade($id_usuario, $id_amigo);

$nome = $_SESSION['usuario_nome'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de <?php echo $amigo['usuario']; ?></title>
    
    <link rel="stylesheet" href="/public/css/main.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <?php require_once("public/view/modulos/header.php"); ?>
    </header>

    <div class="container">
        <div class="profile-cover">
            <img class="profile-cover-img" src="<?php echo (!empty($amigo['foto_capa'])) ? 'data:image/jpeg;base64,' . base64_encode($amigo['foto_capa']) : '/public/image/capa_default.jpg'; ?>" alt="Capa">
            <img class="profile-avatar" src="<?php echo (!empty($amigo['imagem_perfil'])) ? 'data:image/jpeg;base64,' . base64_encode($amigo['imagem_perfil']) : '/public/image/perfil_default.jpg'; ?>" alt="Avatar">
        </div>
        
        <div class="profile-info">
            <h1 class="profile-name"><?php echo $amigo['usuario']; ?></h1>
            
            <?php if ($status == 'amigos'): ?>
                <div class="badge badge-amigos">✓ Amigos</div>
                <form method="POST" action="valida_dados_amigos" style="display: inline;">
                    <input type="hidden" name="id_amigo" value="<?php echo $amigo['id']; ?>">
                    <button type="submit" name="remover_amigo" class="btn btn-danger">Remover Amigo</button>
                </form>
                
                <p><strong>Email:</strong> <?php echo $amigo['email']; ?></p>
                <div class="profile-bio"><?php echo $amigo['bio'] ?? 'Sem bio'; ?></div>
                <p><strong>Localização:</strong> <?php echo $amigo['localizacao'] ?? 'Não informada'; ?></p>
                <p><strong>Status:</strong> <?php echo $amigo['status_relacionamento'] ?? 'Não informado'; ?></p>
                
            <?php elseif ($status == 'pendente'): ?>
                <?php
                $sql_sol = "SELECT id_solicitacao FROM solicitacoes 
                            WHERE id_remetente = ? AND id_destinatario = ? AND status = 'pendente'";
                $stmt_sol = $conn->getPdo()->prepare($sql_sol);
                $stmt_sol->execute([$id_amigo, $id_usuario]);
                $sol_data = $stmt_sol->fetch(PDO::FETCH_ASSOC);
                $solicitacao_id = $sol_data['id_solicitacao'];
                ?>
                <div class="badge badge-pendente">⏳ Solicitou sua amizade</div>
                <form method="POST" action="valida_dados_amigos" style="display: inline;">
                    <input type="hidden" name="id_solicitacao" value="<?php echo $solicitacao_id; ?>">
                    <button type="submit" name="aceitar_solicitacao" class="btn btn-success">Aceitar</button>
                    <button type="submit" name="recusar_solicitacao" class="btn btn-danger">Recusar</button>
                </form>
                
                <?php if ($amigo['privacidade'] == 'publico'): ?>
                    <div class="profile-bio"><?php echo $amigo['bio'] ?? 'Sem bio'; ?></div>
                    <p><strong>Localização:</strong> <?php echo $amigo['localizacao'] ?? 'Não informada'; ?></p>
                <?php else: ?>
                    <div class="alert alert-warning">🔒 Perfil Privado - Aceite a solicitação para ver mais informações.</div>
                <?php endif; ?>
                
            <?php elseif ($status == 'aguardando_resposta'): ?>
                <div class="badge badge-aguardando">⏳ Aguardando resposta</div>
                
                <?php if ($amigo['privacidade'] == 'publico'): ?>
                    <div class="profile-bio"><?php echo $amigo['bio'] ?? 'Sem bio'; ?></div>
                    <p><strong>Localização:</strong> <?php echo $amigo['localizacao'] ?? 'Não informada'; ?></p>
                <?php else: ?>
                    <div class="alert alert-warning">🔒 Perfil Privado - Aguardando a pessoa aceitar sua solicitação.</div>
                <?php endif; ?>
                
            <?php else: ?>
                <?php if ($amigo['privacidade'] == 'publico'): ?>
                    <div class="profile-bio"><?php echo $amigo['bio'] ?? 'Sem bio'; ?></div>
                    <p><strong>Localização:</strong> <?php echo $amigo['localizacao'] ?? 'Não informada'; ?></p>
                <?php else: ?>
                    <div class="alert alert-warning">🔒 Perfil Privado - Envie uma solicitação para ver as informações.</div>
                <?php endif; ?>
                
                <form method="POST" action="valida_dados_amigos">
                    <input type="hidden" name="id_amigo" value="<?php echo $amigo['id']; ?>">
                    <button type="submit" name="enviar_solicitacao" class="btn btn-primary">Enviar Solicitação</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <footer class="app-footer">
        <?php require_once("public/view/modulos/footer.php"); ?>
    </footer>
</body>
</html>