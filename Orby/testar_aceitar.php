<?php
require_once('public/model/Conexao.php');
$conn = new Usuarios();
echo "CONEXÃO OK!";
var_dump($conn->getPdo());