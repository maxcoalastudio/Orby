<?php
$uri = $_SERVER['REQUEST_URI'];

// Remove query string da URI
$uri = strtok($uri, '?');

// Se já tem page no GET, não faz nada
if (isset($_GET['page'])) {
    include 'index.php';
    return true;
}

// Se for a raiz, vai para index
if ($uri === '/' || $uri === '') {
    include 'index.php';
    return true;
}

// Se o arquivo existe fisicamente, serve ele
$filePath = __DIR__ . $uri;
if (file_exists($filePath) && is_file($filePath)) {
    return false;
}

// Se não, redireciona para index com page
$_GET['page'] = ltrim($uri, '/');
include 'index.php';
return true;
