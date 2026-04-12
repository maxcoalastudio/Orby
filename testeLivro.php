<?php
require_once('livro.class.php');
require_once('editora.class.php');
$editora = new Editora('Campus', 'campus@mail.como');
$meulivro = new Livro('PHP como programar', 2020, 14.7, 7, 1, $editora);
echo $meulivro->getEditora()->getNome();
echo $meulivro->atualizarPreco(2.22) . "</br>";
echo $meulivro->mostrarPreco();
?>
