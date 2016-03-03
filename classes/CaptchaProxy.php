<?php

require_once('includes/simple_html_dom.php');

class CaptchaProxy {

    public $cookieFile = null;
    private $urlInicial = 'http://www.receita.fazenda.gov.br/pessoajuridica/cnpj/cnpjreva/Cnpjreva_Solicitacao2.asp?cnpj=';
    private $urlValida = 'http://www.receita.fazenda.gov.br/pessoajuridica/cnpj/cnpjreva/valida.asp';
    private $urlImagemCaptcha = 'http://www.receita.fazenda.gov.br/scripts/captcha/Telerik.Web.UI.WebResource.axd?type=rca&guid=';

    function CaptchaProxy() {
        $this->cookieFile = $_SERVER['DOCUMENT_ROOT'] . '/receita.txt'; ;
    }

    function gerarImagemCaptcha($idCaptcha) {

        if (preg_match('#^[a-z0-9-]{36}$#', $idCaptcha)) {

            $url = $this->urlImagemCaptcha . $idCaptcha;
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $captchaJpg = curl_exec($ch);
            curl_close($ch);

            if (!empty($captchaJpg)) {
                return $captchaJpg;
            }
        }
    }

    public function obterToken($id) {

        if (preg_match('#^[a-z0-9-]{36}$#', $id)) {
            $url = $this->urlImagemCaptcha . $id;


            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $imgsource = curl_exec($ch);
            curl_close($ch);

            if (!empty($imgsource)) {
                $img = imagecreatefromstring($imgsource);
                return imagejpeg($img);
            }
        }
        return null;
    }

    function obterComprovanteHtml($cnpj, $captcha, $token) {


        if (!file_exists($this->cookieFile)) {
            return false;
        }

//Campos para Postagem
        $camposPost = array
            (
            'origem' => 'comprovante',
            'search_type' => 'cnpj',
            'cnpj' => $cnpj,
            'captcha' => $captcha,
            'captchaAudio' => '',
            'submit1' => 'Consultar',
            'viewstate' => $token
        );

        $post = http_build_query($camposPost, NULL, '&');


        //Forca envio de cookie
        $cookie = array('flag' => 1);

        $ch = curl_init($this->urlValida);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookieFile);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:8.0) Gecko/20100101 Firefox/8.0');
        curl_setopt($ch, CURLOPT_COOKIE, http_build_query($cookie, NULL, '&'));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 8);
        curl_setopt($ch, CURLOPT_REFERER, $this->urlInicial);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $html = curl_exec($ch);

        curl_close($ch);

        return $html;
    }

    function criarCookie() {

        $file = fopen($this->cookieFile, 'w');
        fclose($file);
    }

    function obterCaptcha() {

        $this->criarCookie();

        if (!file_exists($this->cookieFile)) {
            return -1;
        }

        $ch = curl_init($this->urlInicial);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_REFERER, 'http://www.google.com');
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookieFile);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);

        $html = curl_exec($ch);
        if(curl_errno($ch) != 0)
        {
            echo "Falha no acesso a pagina";
            die(curl_error($ch));
        }

        if (!$html) {
            var_dump($html);
            return -2;
        }

        $htmlDom = new Simple_html_dom($html);

        $urlImagem = $tokenValue = '';
        $imgcaptcha = $htmlDom->find('img[id=imgcaptcha]');

        if (count($imgcaptcha)) {

            foreach ($imgcaptcha as $imagem) {
                $urlImagem = $imagem->src;
            }

            if (preg_match('#guid=(.*)$#', $urlImagem, $arr)) {
                $idCaptcha = $arr[1];

                $viewstate = $htmlDom->find('input[id=viewstate]');
                if (count($viewstate)) {
                    foreach ($viewstate as $inputViewstate) {
                        $tokenValue = $inputViewstate->value;
                    }
                }

                if (!empty($idCaptcha) && !empty($tokenValue)) {
                    return array($idCaptcha, $tokenValue);
                }
                return -3;
            }
        }
    }

}