<?php
class Livro{
    //atributos - variaveis
    private $titulo;
    private $anoPublicado;
    private $preco;
    private $nota;
    private $quantidade;
    private $editora;
    //metodos - funções
    public function __construct($tt, $anoPublicado, $preco, $nota, $quantidade, Editora $ed){
        $this->setTitulo($tt);
        $this->anoPublicado = $anoPublicado;
        $this->preco = $preco;
        $this->nota = $nota;
        $this->quantidade = $quantidade;
        $this->editora = $ed;
    }

    public function listarLivros(){
        echo 'Titulo: ' . $this->getTitulo(). ', Ano: ' . $this->anoPublicado;
    }
    public function atualizarNota($nota){
        $this->nota = $nota;
        //return 1;
    }

    public function atualizarPreco($preco){
        $this->preco = $preco;
        //return 1;
    }
    public function mostrarpreco(){
        return $this->preco;
    }

    public function setTitulo($titulo){
        $this->titulo = 'TT - ' . $titulo;
    }

    public function getTitulo(){
        return $this->titulo . '*******';
    }

    public function disponivel(){
        return $this->quantidade > 0;
    }
    public function setEditora($editora){
        $this->editora = $editora;
    }
    public function getEditora(){
        return $this->editora;
    }

    
}
?>
