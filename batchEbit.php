<?php

ini_set("max_execution_time", 0);
require_once("classes/Empresa.php");
require_once("classes/Ebit.php");

header('Content-type: text/html; charset=iso-8859-1');

$empresa = new Empresa();
$ebit = new Ebit();
$arrObter = $empresa->obterEmpresasSemSite();

echo "<pre>";
echo "Iniciando processo...\r\n";

$x = 0;
foreach ($arrObter as $k => $v) {

    echo "\tAtualizando loja (" . $v . ")\r\n";
    $ebit->atualizarUrlLoja($k);
    $x++;

    if ($x % 5 === 0) {
        flush();
        ob_flush();
        sleep(2);
    }
}
echo "EOF";

