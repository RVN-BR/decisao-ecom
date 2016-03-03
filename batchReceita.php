<?php
session_start();
ini_set("max_execution_time", 0);
require_once("classes/Empresa.php");
require_once('classes/CaptchaProxy.php');
require_once("classes/ReceitaFederal.php");
header('Content-type: text/html; charset=iso-8859-1');


$captchaProxy = new CaptchaProxy();

$captchaInfo = $captchaProxy->obterCaptcha();

if (!is_array($captchaInfo)) {
    echo "<pre>";
    var_dump($captchaProxy);
    var_dump($captchaInfo);
    echo 'Não foi possível obter Captcha e Token';
    exit;
}

$empresas = Empresa::obterEmpresaSemReceitaFederal();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>TCC - Rodolfo Andrade</title>
        <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
        <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>

        <script>
            $(document).ready(function() {

                $("#captcha").focus();

                $("#receita").submit(function()
                {
                    $.ajax({
                        type: "POST",
                        url: 'grava-receita-federal.php',
                        data: {
                            cnpj: $("#cnpj").val(),
                            captcha: $("#captcha").val(),
                            viewstate: $("#viewstate").val(),
                            idEmpresa: $("#idEmpresa").val()
                        }
                    }).done(function(data)
                    {
                        alert(data);
                        location.reload();
                    });
                    return false;
                });


            });
        </script>

        <style>
            body {
                font-family: "Trebuchet MS", "Helvetica", "Arial",  "Verdana", "sans-serif";
                font-size: 70%;
            }
        </style>

    </head>
    <body>
        <form name="receita" id="receita">
            Empresas a serem cadastradas com CNPJ: <?php echo count($empresas); ?>
            <br />
            <input name="cnpj" id="cnpj" type="hidden" value="<?php echo reset($empresas); ?>"/>
            <br />
            <img src="receitaToken.php?id=<?php echo $captchaInfo[0]; ?>" border="0" />
            <br />
            <input name="captcha" id="captcha" type="text" maxlength="6" required />
            <b style="color: red">O que vê na imagem acima?</b>
            <br />
            <input type="hidden" id="viewstate" name="viewstate" value="<?php echo $captchaInfo[1]; ?>" />
            <input type="hidden" id="idEmpresa" name="idEmpresa" value="<?php echo key($empresas); ?>" />
            <input type="submit" id="gravar" value="Acessar RFB"/>
        </form>
        <div id="resp"> </div>


    </body>
</html>
