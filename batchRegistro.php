<?php

ini_set("max_execution_time", 0);
require_once("classes/Empresa.php");
require_once("classes/RegistroBr.php");

header('Content-type: text/html; charset=iso-8859-1');

$arrObter = Empresa::obterEmpresaSemRegistroBr();

echo "<pre>";
echo "Iniciando processo para " . count($arrObter) . " domínios \r\n";

$x = 0;

$registroBr = new RegistroBr();
foreach ($arrObter as $k => $v) {

    echo "\tAtualizando Dominio (" . $v . ")\r\n";
    $registroBr->atualizarOuInserirInformacao($v);
    $x++;
    flush();
    ob_flush();
    sleep(mt_rand(10, 15));
}
echo "EOF";

