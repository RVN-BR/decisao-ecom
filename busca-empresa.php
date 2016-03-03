<?php

require_once("classes/Empresa.php");
header('Content-type: text/html; charset=iso-8859-1');

$nomeEmpresa = trim($_GET['empresa']);
if (empty($nomeEmpresa) || strlen($nomeEmpresa) < 3) {
    exit;
}


$json = Empresa::obterEmpresasJson($nomeEmpresa);

echo ($json === null ? "" : $json );

