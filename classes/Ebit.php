<?php

require_once("includes/simple_html_dom.php");

class Ebit {

    public $nome;
    public $url;
    public $medalha;
    public $avaliacao;
    public $atualizacao;

    const SITE_POR_PAGINA = 10;
    const EBIT_URL_BASE = "http://www.ebit.com.br";
    const EBIT_URL_INICIAL = "http://www.ebit.com.br/avaliacao-lojas";
    const EBIT_URL_BUSCA = "http://www.ebit.com.br/reputation/searchResults/ALL/ALL/ALL/BEST_EVALUATED";

    function atualizarUrlLoja($id) {
        $db = DB::singleton();
        $db->abrirBD();

        $url = null;
        $query = "SELECT url FROM ebit WHERE idEmpresa = ?";

        $stmt = $db->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($url);
        $stmt->store_result();


        if ($stmt->num_rows === 0) {
            throw new Exception("Nenhuma URL Ebit para empresa ID " . $id);
        }

        $stmt->fetch(); //bind result de $url
        $stmt->close();
        $html = file_get_html($url);

        if (!is_object($html)) {
            $link = " ";
        } else {
            $link = rtrim(str_replace("http://", "", trim($html->find('li.store-datail-esquerda a', 0)->href)), "/");
        }



        $queryUpdate = "UPDATE empresa SET site = ?, atualizacao = NOW() WHERE id = ?";

        $stmtUpdate = $db->prepare($queryUpdate);
        $stmtUpdate->bind_param("si", $link, $id);
        $isOk = $stmtUpdate->execute();
        if ($isOk === false) {
            throw new Exception("Falha na atualizacao do registro");
        }

        $stmtUpdate->close();
        return true;
    }

    function obterTodasLojasEbit($inicio = 1) {

        $db = DB::singleton();

        $db->abrirBD();
        //Obtendo o total de lojas disponíveis
        $avalicaoInicial = file_get_html(self::EBIT_URL_INICIAL);

//RegExp
        preg_match("/^[0-9]+/", $avalicaoInicial->find("p[id=totalOfItems]", 0)->plaintext, $match);
        $totalLojas = $match[0];

        $totalPaginas = floor($totalLojas / self::SITE_POR_PAGINA);

        echo "Previa de operacao:";
        echo "\r\n\tLojas a serem verificadas -> " . $totalLojas;
        echo "\r\n\tTotal de Requisicoes -> " . $totalPaginas;

        //Quebra o processamento em N requisicoes
        for ($i = $inicio; $i <= $totalPaginas; $i++) {

            echo "\r\nRequisicao $i de $totalPaginas. Carregando " . self::SITE_POR_PAGINA . " lojas por página\r\n";
            $html = file_get_html(self::EBIT_URL_BUSCA . "/$i/" . self::SITE_POR_PAGINA);

            if ($html === false) {
                break;
            }

            $arrSites = array();

            foreach ($html->find('div.return-result ul') as $resultado) {

                if ($resultado == null) {
                    die("Processo finalizado na pagina $i");
                }

                $site = new Ebit();

                //Nome Loja
                $site->nome = utf8_decode($resultado->find('li strong a', 0)->plaintext);

                //URL ebit
                $site->urlEbit = utf8_decode(self::EBIT_URL_BASE . $resultado->find('li a', 0)->href);

                //Medalha
                $site->medalhaEbit = utf8_decode($resultado->find('li.medal div img', 0)->alt);

                //Avaliacao Ebit
                $site->avaliacaoEbit = $resultado->find('li strong', 1)->plaintext;

                $arrSites[] = $site;
            }

            foreach ($arrSites as $loja) {
                $id = 0;
                flush();
                ob_flush();
                $stmt = $db->prepare("SELECT id FROM empresa WHERE nome = ?");
                $stmt->bind_param("s", $loja->nome);
                $stmt->execute();
                $stmt->bind_result($id);
                $stmt->fetch();
                $idEmpresa = $id;
                $stmt->close();
                //Insere
                if ($idEmpresa == null) {
                    echo "Inserindo dados da loja " . $loja->nome . "\r\n";
                    //Insert Empresa
                    $stmtEmpresa = $db->prepare("INSERT INTO empresa (nome, atualizacao)
                                 VALUES (?, NOW())");
                    $stmtEmpresa->bind_param("s", $loja->nome);
                    $stmtEmpresa->execute();

                    $idEmpresa = $stmtEmpresa->insert_id;

                    $stmtEmpresa->close();
                }

                echo "ID:  $idEmpresa";
                $ebitCheck = $db->query("SELECT 1 from ebit where idEmpresa = $idEmpresa") or die(__LINE__ . " - " . $db->error);
                if ($ebitCheck->num_rows === 0) {
                    echo "\tInserindo dados da loja " . $loja->nome . " no Ebit\r\n";
                    //Insert EBIT
                    $stmtEbit = $db->prepare("INSERT INTO ebit (idEmpresa,  medalha, url, avaliacao, atualizacao)
                                 VALUES (?, ?, ?, ?, NOW())") or die($db->error);
                    $stmtEbit->bind_param("isss", $idEmpresa, $loja->medalhaEbit, $loja->urlEbit, $loja->avaliacaoEbit);
                    $stmtEbit->execute();
                    $stmtEbit->close();
                } else {
                    echo "\tAtualizando dados da loja " . $loja->nome . "\r\n";
                    $stmtUpdate = $db->prepare("UPDATE ebit SET
                                          medalha   = ?,
                                          url       = ?,
                                          avaliacao = ?,
                                          atualizacao = NOW()
                                      WHERE idEmpresa = ?");
                    $stmtUpdate->bind_param("sssi", $loja->medalhaEbit, $loja->urlEbit, $loja->avaliacaoEbit, $idEmpresa);
                    $stmtUpdate->execute();
                    $stmtUpdate->close();
                }
            }
            sleep(10); //Evita falhas no acesso ao Ebit, diminuindo a carga.
        }
        $db->close();
    }

}