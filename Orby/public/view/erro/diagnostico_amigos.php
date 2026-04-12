<?php
session_start();
require_once('public/model/Conexao.php');

$conn = new Usuarios();
$pdo = $conn->getPdo();

echo "<h1>🔍 DIAGNÓSTICO DE AMIGOS</h1>";

// 1. Usuário logado
$id = $_SESSION['usuario_id'] ?? null;
echo "<h2>1. Usuário logado: ID = " . ($id ?? "NÃO LOGADO") . "</h2>";

if ($id) {
    // 2. Verificar tabela amigos
    $amigos = $pdo->query("SELECT * FROM amigos WHERE id_usuario = $id")->fetchAll();
    echo "<h2>2. Amigos na tabela (id_usuario = $id):</h2>";
    if (count($amigos) > 0) {
        echo "<pre>";
        print_r($amigos);
        echo "</pre>";
    } else {
        echo "❌ NENHUM amigo encontrado!<br>";
    }
    
    // 3. Verificar solicitações
    $solicitacoes = $pdo->query("SELECT * FROM solicitacoes WHERE id_destinatario = $id OR id_remetente = $id")->fetchAll();
    echo "<h2>3. Solicitações:</h2>";
    if (count($solicitacoes) > 0) {
        echo "<pre>";
        print_r($solicitacoes);
        echo "</pre>";
    } else {
        echo "❌ NENHUMA solicitação encontrada!<br>";
    }
    
    // 4. Verificar $_SESSION['amigos_ids']
    echo "<h2>4. Sessão amigos_ids:</h2>";
    echo "<pre>";
    print_r($_SESSION['amigos_ids'] ?? "NÃO DEFINIDO");
    echo "</pre>";
    
    // 5. Forçar atualização
    $amigos_atuais = $conn->carregarAmigos($id);
    echo "<h2>5. Amigos atuais (carregarAmigos):</h2>";
    echo "<pre>";
    print_r($amigos_atuais);
    echo "</pre>";
}
?>