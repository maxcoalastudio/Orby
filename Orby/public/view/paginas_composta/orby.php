<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login');
    exit();
}

require_once('public/model/Conexao.php');
include_once('public/JS/PHJSP.php');

// $conn = new Usuarios("sistema_login", "localhost", "root", "");
$conn = new Usuarios();
$id = $_SESSION['usuario_id'];
$info = $conn->buscarInfoUsuario($id);


/*
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


if (isset($_POST['update'])) {
    $imagemPerfil = $_FILES['imagem_perfil'];
    $fotoCapa = $_FILES['foto_capa'];

    if ($imagemPerfil['error'] == 0 && ($imagemPerfil['size'] > 2 * 1024 * 1024 || !in_array($imagemPerfil['type'], array('image/jpeg', 'image/png', 'image/gif')))) {
        $_SESSION['erro_imagem_perfil'] = "A imagem de avatar deve ter menos de 2MB e ser do tipo JPEG, PNG ou GIF!";
        header('Location: perfil');
        
    }

    if ($fotoCapa['error'] == 0 && ($fotoCapa['size'] > 2 * 1024 * 1024 || !in_array($fotoCapa['type'], array('image/jpeg', 'image/png', 'image/gif')))) {
        $_SESSION['erro_foto_capa'] = "A imagem de capa deve ter menos de 2MB e ser do tipo JPEG, PNG ou GIF!";
        header('Location: perfil');
        
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
*/
//pegando o nome e email do usuario na sessão atual
$nome = $_SESSION['usuario_nome'];
$email = $_SESSION['email'];
$amigos_ids = $_SESSION['amigos_ids'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Orby'; ?></title>
    
    <!-- CSS Principal -->
    <link rel="stylesheet" href="public/css/main.css">
    
    <!-- Google Fonts (opcional) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo " Home: " . $nome; ?> </title>
</head>
<body>
    <!-- cabeçalho com logo e menu nav -->
    <header>
        <?php require_once("public/view/modulos/header.php"); ?>
    </header>


    <section id="apresentacao_home">
        <div>
            <h2>Feed para <?php echo "$nome"; ?></h2>
            <!--<h3>Seu email é <?php echo "$email"; ?></h3>-->
        </div>
    </section>
    <section id="criar_postagem">
        <div>
            <form action="" method="post">
                <h2>Criar Postagem</h2>
                Postagem: <input type="area" name="feed" value="<?php echo ""; ?>" placeholder="Minha postagem ..."><br />
                <input type="submit" name="postar" value="Postar">
            </form>
        </div>
    </section>

    <section>
        <!-- aqui vai ser o feeding alimentado pelos amigos e por ele com sequencia por datas buscadas do banco de dados relacionado com o email ou id-->
    </section>

    <!--
    <section id="dados_perfil_usuario">
        <form id="formulario" action="" method="post" enctype="multipart/form-data">
            <h2>Perfil de <?php echo $nome; ?></h2>
            <img id="avatar" src="<?php echo (!empty($info['imagem_perfil'])) ? 'data:image/jpeg;base64,' . base64_encode($info['imagem_perfil']) : '../image/perfil_default.jpg'; ?>" alt="Avatar de <?php echo $nome ;?>"><br/>
            <img id="capa" src="<?php echo (!empty($info['foto_capa'])) ? 'data:image/jpeg;base64,' . base64_encode($info['foto_capa']) : '../image/capa_default.jpg'; ?>" alt="Capa de <?php echo $nome ;?>"><br/>
            
            <label for="imagem_perfil">Trocar avatar:</label><br />
            <input type="file" name="imagem_perfil" id="imagem_perfil">
            
            <?php if (isset($_SESSION['erro_imagem_perfil'])) { echo '<p style="color: red;">' . $_SESSION['erro_imagem_perfil'] . '</p>'; unset($_SESSION['erro_imagem_perfil']); } ?>
            <br />
            
            <label for="foto_capa">Foto de Capa:</label><br />
            <input type="file" name="foto_capa" id="foto_capa">
            
            <?php if (isset($_SESSION['erro_foto_capa'])) { echo '<p style="color: red;">' . $_SESSION['erro_foto_capa'] . '</p>'; unset($_SESSION['erro_foto_capa']); } ?>
            <br />
            
            <label for="status_relacionamento">Status de Relacionamento: </label><br />
            <input type="text" name="status_relacionamento" id="status_relacionamento" value="<?php echo $info['status_relacionamento'] ?? ''; ?>"><br />
            <label for="sexo">Sexo: </label><br />
            <input type="text" name="sexo" id="sexo" value="<?php echo $info['sexo'] ?? ''; ?>"><br />
            <label for="bio">Bio: </label><br />
            <textarea name="bio" id="bio"><?php echo $info['bio'] ?? ''; ?></textarea><br />
            <label for="links">Links:</label><br />
            <input type="text" name="links" id="links" value="<?php echo $info['links'] ?? ''; ?>"><br />
            <label for="localizacao">Localização: </label><br />
            <input type="text" name="localizacao" id="localizacao" value="<?php echo $info['localizacao'] ?? ''; ?>"><br />
            <label for="privacidade">Privacidade: </label><br />
            <select name="privacidade" id="privacidade">
                <option value="publico" <?php if ($info["privacidade"] == 'publico') echo 'selected' ?? ''; ?>>Público</option>
                <option value="privado" <?php if ($info["privacidade"] == 'privado') echo 'selected' ?? ''; ?>>Privado</option>
                <option value="amigos" <?php if ($info["privacidade"] == 'amigos') echo 'selected' ?? ''; ?>>Amigos</option>
            </select><br />
            <button type="submit" name="update">Atualizar</button>
        </form>
    </section>

    -->


    <section id="esquerda">
    <p>Aqui vai as midias</p>    
    <?php require_once("public/view/modulos/midia.php"); ?>
    </section>


    <section id="direita">
    <p>Aqui vai a lista de amigos online e Clã</p>   
    <?php require_once("public/view/modulos/amigos.php"); ?>
    </section>

    <footer>
        <?php require_once("public/view/modulos/footer.php"); ?>
    </footer>



    <!-- teste -->
    <title>Exemplo PHJSP - Correto</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .destaque { background-color: yellow; font-weight: bold; }
        #mensagem { color: blue; margin-top: 20px; }
    </style>
</head>
<body>
    <h1 id="titulo">Título Original</h1>
    <p id="paragrafo">Este é um parágrafo de exemplo</p>
    <div id="container">
        <p>Conteúdo do container</p>
    </div>
    <div id="mensagem"></div>
    
    <button id="botao">Clique para alterar</button>
    
    <?php
    include_once('JS/PHJSP.php');
    $js = new PHJSP();
    //$js->alert('Olá tudo bem ?');

    // Definir função
    $js->function('iniciar', [], '
        let titulo = document.getElementById("titulo");
        if (titulo) {
            titulo.innerHTML = "Título modificado por PHP";
        }
    ');
    
    // Usar addEventListener com função nomeada
    $js->raw('document.addEventListener("DOMContentLoaded", iniciar, false);');
    
    echo $js;

    ?>

</body>
</html>
