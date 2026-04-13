<?php
session_start();
require_once('public/model/Conexao.php');

// $conn = new Usuarios("sistema_login", "localhost", "root", "");
$conn = new Usuarios();

function criaUsuario($conn){
    // Verificar se o formulário de cadastro foi enviado
        $email = $_POST['email'] ?? '';
        $usuario = $_POST['usuario'] ?? '';
        $senha = password_hash($_POST['senha'], PASSWORD_ARGON2I);
        if (empty($email) || empty($usuario) || empty($senha)){
            header ('Location: 404');
        }else{
            #$conn = new Usuarios("sistema_login", "localhost", "root", "");
            return $conn->criandoUsuario($email, $usuario, $senha);
            }
}

function buscaUsuario($conn){
    // Verificar se o formulário de login foi enviado
        $email = $_POST['email'];
        $senha = $_POST['senha'];
        if ($email ==="" || $senha===""){
            header ('Location: 404');
        }else{
        return $conn->buscarUsuario($email, $senha);
        }
}


if (isset($_POST['cadastrar'])){
    criaUsuario($conn);
} elseif (isset($_POST['login'])) {
    buscaUsuario($conn);
}
?>

