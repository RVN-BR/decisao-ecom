<?php

session_start();

require('classes/CaptchaProxy.php');
require('classes/ReceitaFederal.php');

$id = (int) $_POST['idEmpresa'];

if ($id < 1) {
    die('Nenhuma identificacao de empresa informada');
}

$captchaProxy = new CaptchaProxy();

$html = $captchaProxy->obterComprovanteHtml($_POST['cnpj'], $_POST['captcha'], $_POST['viewstate']);

//die($html);
$rf = new ReceitaFederal();

$rf->atualizarOuInserirInformacao($html, $id);

echo "Dados atualizados";
