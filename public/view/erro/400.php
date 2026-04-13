<?php
if(isset($_POST['sair'])){
    session_unset();
    session_destroy();
    header('Location: login');
    exit();
}

?>
<h1>Orby sente muito!</h1>
<h2>[Erro 400] o servidor não porcessou a requisição[dados invalidos/ muito grande/ corrompido].</h2>
<form action="" method="post">
    <input type="submit" name="sair" value="Sair">
<form>
