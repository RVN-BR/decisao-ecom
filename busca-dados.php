<?php

require_once("classes/Empresa.php");

header('Content-type: text/html; charset=iso-8859-1');
$id = (int) $_GET['empresa']; //Casting de seguranca

if ($id < 1) {
    exit;
}

echo Empresa::obterDadosJson($id);
