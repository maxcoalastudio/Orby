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
$info = $conn->buscarInfoUsuario($id);

if (empty($info)) {
    $info = [
        'imagem_perfil' => null,
        'foto_capa' => null,
        'status_relacionamento' => '',
        'sexo' => '',
        'bio' => '',
        'links' => '',
        'localizacao' => '',
        'privacidade' => 'publico'
    ];
}

$default_info = [
    'imagem_perfil' => null,
    'foto_capa' => null,
    'status_relacionamento' => '',
    'sexo' => '',
    'bio' => '',
    'links' => '',
    'localizacao' => '',
    'privacidade' => 'publico'
];

$info = array_merge($default_info, $info);
$nome = $_SESSION['usuario_nome'];
$email = $_SESSION['email'];

// Carregar amigos
$amigos_atuais = $conn->carregarAmigos($id);
if ($_SESSION['amigos_ids'] != $amigos_atuais) {
    $_SESSION['amigos_ids'] = $amigos_atuais;
}

$amigos_info = [];
if (!empty($_SESSION['amigos_ids'])) {
    $amigos_info = $conn->buscarUsuariosPorIds($_SESSION['amigos_ids']);
}

// Atualizar usuário
if (isset($_POST['atualizar'])) {
    $email = $_POST['email'];
    $usuario = $_POST['usuario'];
    $senha = password_hash($_POST['senha'], PASSWORD_ARGON2I);

    if ($conn->atualizarUsuario($id, $email, $usuario, $senha)) {
        $_SESSION['sucesso'] = "Perfil atualizado com sucesso!";
        $_SESSION['usuario_nome'] = $usuario;
        $_SESSION['email'] = $email;
    } else {
        $_SESSION['erro'] = "Erro ao atualizar perfil!";
    }

    header('Location: perfil');
    exit();
}

// Atualizar informações do perfil
if (isset($_POST['update'])) {
    $imagemPerfil = $_FILES['imagem_perfil'];
    $fotoCapa = $_FILES['foto_capa'];

    if ($imagemPerfil['error'] == 0 && ($imagemPerfil['size'] > 2 * 1024 * 1024 || !in_array($imagemPerfil['type'], array('image/jpeg', 'image/png', 'image/gif')))) {
        $_SESSION['erro_imagem_perfil'] = "A imagem de avatar deve ter menos de 2MB e ser do tipo JPEG, PNG ou GIF!";
        header('Location: perfil');
        exit();
    }

    if ($fotoCapa['error'] == 0 && ($fotoCapa['size'] > 2 * 1024 * 1024 || !in_array($fotoCapa['type'], array('image/jpeg', 'image/png', 'image/gif')))) {
        $_SESSION['erro_foto_capa'] = "A imagem de capa deve ter menos de 2MB e ser do tipo JPEG, PNG ou GIF!";
        header('Location: perfil');
        exit();
    }

    $imagemPerfilData = null;
    if ($imagemPerfil['error'] == 0) {
        $imagemPerfilData = file_get_contents($imagemPerfil['tmp_name']);
    }

    $fotoCapaData = null;
    if ($fotoCapa['error'] == 0) {
        $fotoCapaData = file_get_contents($fotoCapa['tmp_name']);
    }

    if ($conn->atualizarInfoUsuario($id, $imagemPerfilData, $fotoCapaData, $_POST['status_relacionamento'], $_POST['sexo'], $_POST['bio'], $_POST['links'], $_POST['localizacao'], $_POST['privacidade'])) {
        $_SESSION['sucesso'] = "Perfil atualizado com sucesso!";
    } else {
        $_SESSION['erro'] = "Erro ao atualizar perfil!";
    }

    header('Location: perfil');
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de <?php echo $nome; ?></title>
    
    <link rel="stylesheet" href="/public/css/main.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header class="app-header">
        <?php require_once("public/view/modulos/header.php"); ?>
    </header>

    <div class="container page-wrapper">
        <aside class="sidebar-left">
            <?php require_once("public/view/modulos/midia.php"); ?>
        </aside>

        <main class="main-content">
            <div class="profile-cover">
                <img class="profile-cover-img" src="<?php echo (!empty($info['foto_capa'])) ? 'data:image/jpeg;base64,' . base64_encode($info['foto_capa']) : '/public/image/capa_default.jpg'; ?>" alt="Capa">
                <img class="profile-avatar" src="<?php echo (!empty($info['imagem_perfil'])) ? 'data:image/jpeg;base64,' . base64_encode($info['imagem_perfil']) : '/public/image/perfil_default.jpg'; ?>" alt="Avatar">
            </div>
            
            <div class="profile-info">
                <h1 class="profile-name"><?php echo $nome; ?></h1>
                <p class="profile-email"><?php echo $email; ?></p>
                
                <div class="profile-section">
                    <h2 class="section-title">Atualizar Dados</h2>
                    <form action="" method="post">
                        <div class="form-group">
                            <label class="form-label">Email:</label>
                            <input type="email" name="email" class="form-input" value="<?php echo $email; ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Usuário:</label>
                            <input type="text" name="usuario" class="form-input" value="<?php echo $nome; ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nova Senha:</label>
                            <input type="password" name="senha" class="form-input">
                        </div>
                        <button type="submit" name="atualizar" class="btn btn-primary">Atualizar</button>
                    </form>
                </div>
                
                <div class="profile-section">
                    <h2 class="section-title">Informações do Perfil</h2>
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label class="form-label">Trocar avatar:</label>
                            <input type="file" name="imagem_perfil" class="form-input">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Foto de Capa:</label>
                            <input type="file" name="foto_capa" class="form-input">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Status de Relacionamento:</label>
                            <input type="text" name="status_relacionamento" class="form-input" value="<?php echo $info['status_relacionamento']; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Sexo:</label>
                            <input type="text" name="sexo" class="form-input" value="<?php echo $info['sexo']; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Bio:</label>
                            <textarea name="bio" class="form-textarea" rows="4"><?php echo $info['bio']; ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Links:</label>
                            <input type="text" name="links" class="form-input" value="<?php echo $info['links']; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Localização:</label>
                            <input type="text" name="localizacao" class="form-input" value="<?php echo $info['localizacao']; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Privacidade:</label>
                            <select name="privacidade" class="form-select">
                                <option value="publico" <?php echo ($info['privacidade'] == 'publico') ? 'selected' : ''; ?>>Público</option>
                                <option value="privado" <?php echo ($info['privacidade'] == 'privado') ? 'selected' : ''; ?>>Privado</option>
                                <option value="amigos" <?php echo ($info['privacidade'] == 'amigos') ? 'selected' : ''; ?>>Amigos</option>
                            </select>
                        </div>
                        
                        <button type="submit" name="update" class="btn btn-primary">Atualizar Perfil</button>
                    </form>
                </div>
            </div>
        </main>
        
        <aside class="sidebar-right">
            <?php require_once("public/view/modulos/amigos.php"); ?>
        </aside>
    </div>

    <footer class="app-footer">
        <?php require_once("public/view/modulos/footer.php"); ?>
    </footer>
</body>
</html>