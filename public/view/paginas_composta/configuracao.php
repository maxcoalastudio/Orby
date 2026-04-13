<?php
session_start();


require_once('public/model/Conexao.php');
// $conn = new Usuarios("sistema_login", "localhost", "root", "");
$conn = new Usuarios();
$id = $_SESSION['usuario_id'];


if (!isset($_SESSION['usuario_id'])) {
    header('Location: login');
    exit();
}

if (isset($_POST['deletar'])) {
    if (isset($id)) {
        $conn->deletarUsuario($id);
        session_unset();
        session_destroy();
        header('Location: login');
        exit();
    } else {
        $_SESSION['erro'] = "Erro ao deletar perfil!";
        header('Location: perfil');
        exit();
    }
}

$nome = $_SESSION['usuario_nome'];
$email = $_SESSION['email'];
$id = $_SESSION['usuario_id'];
?>

</form>
<h2>Excluir usuário <?= $email; ?></h2>
<form action="" method="post">
    <input type="submit" name="deletar" value="Excluir" onclick="return confirm('Tem certeza que deseja deletar o usuário?')">
</form>
