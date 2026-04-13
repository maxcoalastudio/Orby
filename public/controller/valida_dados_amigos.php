<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login');
    exit();
}

require_once('public/model/Conexao.php');

// $conn = new Usuarios("sistema_login", "localhost", "root", "");
$conn = new Usuarios();
$id_usuario = $_SESSION['usuario_id'];

// Enviar solicitação de amizade
if (isset($_POST['enviar_solicitacao'])) {
    $id_amigo = $_POST['id_amigo'];
    
    if ($id_amigo > 0 && $id_amigo != $id_usuario) {
        $resultado = $conn->enviarSolicitacao($id_usuario, $id_amigo);
        
        if (isset($resultado['sucesso'])) {
            $_SESSION['sucesso'] = $resultado['sucesso'];
        } else {
            $_SESSION['erro'] = $resultado['erro'];
        }
    }
    header('Location: gerencia_amigos');
    exit();
}

// Aceitar solicitação - VERSÃO SIMPLES (SEM VALIDAÇÃO >0)
if (isset($_POST['aceitar_solicitacao'])) {
    $id_solicitacao = $_POST['id_solicitacao'];
    
    $resultado = $conn->aceitarSolicitacao($id_solicitacao);
    
    if (isset($resultado['sucesso'])) {
        $_SESSION['sucesso'] = $resultado['sucesso'];
        $conn->atualizarSessaoAmigos($id_usuario);
    } else {
        $_SESSION['erro'] = $resultado['erro'];
    }
    
    header('Location: gerencia_amigos');
    exit();
}

// Recusar solicitação - VERSÃO SIMPLES (SEM VALIDAÇÃO >0)
if (isset($_POST['recusar_solicitacao'])) {
    $id_solicitacao = $_POST['id_solicitacao'];
    
    $resultado = $conn->recusarSolicitacao($id_solicitacao);
    $_SESSION['sucesso'] = $resultado['sucesso'];
    
    header('Location: gerencia_amigos');
    exit();
}

// Cancelar solicitação enviada
if (isset($_POST['cancelar_solicitacao'])) {
    $id_solicitacao = $_POST['id_solicitacao'];
    
    $sql = "UPDATE solicitacoes SET status = 'cancelada', data_resposta = CURRENT_TIMESTAMP WHERE id_solicitacao = ? AND id_remetente = ?";
    $stmt = $conn->getPdo()->prepare($sql);
    $stmt->execute([$id_solicitacao, $id_usuario]);
    
    $_SESSION['sucesso'] = "Solicitação cancelada com sucesso!";
    header('Location: gerencia_amigos');
    exit();
}

// Remover amigo - VERSÃO SIMPLIFICADA
if (isset($_POST['remover_amigo'])) {
    $id_amigo = $_POST['id_amigo'];
    
    if ($conn->removerAmigo($id_usuario, $id_amigo)) {
        $_SESSION['sucesso'] = "Amigo removido com sucesso!";
        $conn->atualizarSessaoAmigos($id_usuario);
    } else {
        $_SESSION['erro'] = "Erro ao remover amigo!";
    }
    
    header('Location: gerencia_amigos');
    exit();
}
?>