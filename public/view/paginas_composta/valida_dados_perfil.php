<?php
require_once ('public/model/Conexao.php');
// $conn = new Usuarios("sistema_login", "localhost", "root", "");
$conn = new Usuarios();

if($_SESSION['usuario_id']){
    $id = $_SESSION['usuario_id'];
    $info = $conn->buscarInfoUsuario($id);
    }
if($info){
    if(isset($_POST['update'])){
        if (isset($_FILES['imagem_perfil']) && $_FILES['imagem_perfil']['error'] == 0) {
            $imagem_perfil = file_get_contents($_FILES['imagem_perfil']['tmp_name']);
        } else {
            $imagem_perfil = $info['imagem_perfil'];
        }

        if (isset($_FILES['foto_capa']) && $_FILES['foto_capa']['error'] == 0) {
            $foto_capa = file_get_contents($_FILES['foto_capa']['tmp_name']);
        } else {
            $foto_capa = $info['foto_capa'];
        }

        $status_relacionamento = $_POST['status_relacionamento'];
        $sexo = $_POST['sexo'];
        $bio = $_POST['bio'];
        $links = $_POST['links'];
        $localizacao = $_POST['localizacao'];
        $privacidade = $_POST['privacidade'];
        $conn->atualizarInfoUsuario($id, $imagem_perfil, $foto_capa, $status_relacionamento, $sexo, $bio, $links, $localizacao, $privacidade);
        header('Location: perfil');
        }
    }else{
        if(isset($_POST['update'])){
            if (isset($_FILES['imagem_perfil']) && $_FILES['imagem_perfil']['error'] == 0) {
                $imagem_perfil = file_get_contents($_FILES['imagem_perfil']['tmp_name']);
            } else {
                $imagem_perfil = null;
            }

            if (isset($_FILES['foto_capa']) && $_FILES['foto_capa']['error'] == 0) {
                $foto_capa = file_get_contents($_FILES['foto_capa']['tmp_name']);
            } else {
                $foto_capa = null;
            }

            $status_relacionamento = $_POST['status_relacionamento'];
            $sexo = $_POST['sexo'];
            $bio = $_POST['bio'];
            $links = $_POST['links'];
            $localizacao = $_POST['localizacao'];
            $privacidade = $_POST['privacidade'];
            $conn->criarInfoUsuario($id, $imagem_perfil, $foto_capa, $status_relacionamento, $sexo, $bio, $links, $localizacao, $privacidade);
            header('Location: perfil');
        }
    }

?>
