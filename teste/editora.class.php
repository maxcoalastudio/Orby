<?php
    
class Editora{
    private $nome;
    private $email;

    public function __construct($nm, $em)
    {
        $this->nome = $nm;
        $this->email = $em;
    }
        public function consultar(){
            //TODO
        }
        public function getNome(){
            return $this->nome;
        }
        public function setNome($nm){
            $this->nome = $nm;
        }
        public function getEmail(){
            return $this->email;
        }
        public function setEmail($em){
            $this->email = $em;
        }
    }
?>
