<?php
if(isset($_POST['sair'])){
    session_unset();
    session_destroy();
    header('Location: login');
    exit();
}

?>
<h1>Orby sente muito!</h1>
<h2>[Erro 404] Pagina não encontrada.</h2>
<form action="" method="post">
    <input type="submit" name="sair" value="Sair">
<form>
