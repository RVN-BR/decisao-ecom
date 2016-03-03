<?php

require('classes/CaptchaProxy.php');
$idCaptcha = $_GET['id'];


if (empty($idCaptcha)) {
    die();
}


$captcha = new CaptchaProxy();

$img = $captcha->obterToken($idCaptcha);

if ($img == null) {
    die("Nao foi possivel criar a imagem pelo token");
}

header('Content-type: image/jpg');
imagejpeg($img);
