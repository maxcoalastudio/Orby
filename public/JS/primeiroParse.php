<?php
function contextJS($texto){
    $iniScr = "<script>";
    $fimScr = "</script>";
    $scr = $texto ;
    $retorno = $iniScr . $scr . $fimScr ;
    return $retorno;
}

?>