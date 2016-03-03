<?php

require_once("includes/simple_html_dom.php");
require_once("classes/SugestaoReclameAqui.php");

class ReclameAqui {

    public $nome;
    public $dateCadastro;
    public $site;
    public $fone;
    public $reputacao;
    public $atendida;
    public $solucao;
    public $voltariaFazerNegocio;
    public $notaConsumidor;
    public $tempoMedioResposta;
    public $avaliacao;
    public $avaliacaoNaoAtendida;
    public $avaliacaoAtendida;

    const RA_URL_BASE = "http://www.reclameaqui.com.br/indices";
    const RA_URL_BUSCA = "http://www.reclameaqui.com.br/xml/busca_empresas.php?q=";

    function atualizarDetalhes($idEmpresa, $site) {
        $db = DB::singleton();
        $db->abrirBD();
        $reclameInicial = file_get_html($site);

        $ra = new ReclameAqui();

        $ra->nome = utf8_decode($reclameInicial->find("h1.tituloCinza",0)->plaintext);

        preg_match("/([0-9]{2})\/([0-9]{2})\/([0-9]{4})/", $reclameInicial->find("div#infosHidden",0)->plaintext, $matches);
        $ra->dateCadastro = $matches[0];

        $ra->fone = utf8_decode(trim(str_replace("Fone:", "", $reclameInicial->find("div#infosHidden",0)->find("a.link",1)->plaintext)));

        $ra->reputacao = $reclameInicial->find("table#tabelaDados12", 0)->find("img",0)->src;

        $x = 0;

        $ra->notaConsumidor = trim($reclameInicial->find("table#tabelaDados12", 0)->find("big",0)->plaintext);
        $ra->tempoMedioResposta = utf8_decode(trim($reclameInicial->find("table#tabelaDados12", 0)->find("big",1)->plaintext));
        $ra->avaliacao = trim($reclameInicial->find("table#tabelaDados12", 0)->find("big",2)->plaintext);

        $ra->avaliacaoNaoAtendida = trim($reclameInicial->find("table#tabelaDados12", 0)->find("big",3)->plaintext);
        $ra->avaliacaoAtendida = trim($reclameInicial->find("table#tabelaDados12", 0)->find("big",4)->plaintext);
        $ra->atendida = str_replace("%","",$reclameInicial->find("table#tabelaDados12", 0)->find("big",6)->plaintext);
        $ra->solucao = str_replace("%","",$reclameInicial->find("table#tabelaDados12", 0)->find("big",7)->plaintext);
        $ra->voltariaFazerNegocio = str_replace("%","",$reclameInicial->find("table#tabelaDados12", 0)->find("big",8)->plaintext);


        $stmtUpdate = $db->prepare("UPDATE reclameaqui
                                 SET  nome = ?,
                                    dateCadastro = STR_TO_DATE(?,'%d/%m/%Y'),
                                    fone = ?,
                                    reputacao = ?,
                                    atendida = ?,
                                    solucao = ?,
                                    voltariaFazerNegocio = ?,
                                    notaConsumidor = ?,
                                    tempoMedioResposta = ?,
                                    avaliacao = ?,
                                    avaliacaoNaoAtendida = ?,
                                    avaliacaoAtendida = ?,
                                    atualizacao = NOW()
                                WHERE
                                    idEmpresa = ?") or die($db->error);
        $stmtUpdate->bind_param("sssssdddssiii",
                $ra->nome,
                $ra->dateCadastro,
                $ra->fone,
                $ra->reputacao,
                $ra->atendida,
                $ra->solucao,
                $ra->voltariaFazerNegocio,
                $ra->notaConsumidor,
                $ra->tempoMedioResposta,
                $ra->avaliacao,
                $ra->avaliacaoNaoAtendida,
                $ra->avaliacaoAtendida,
                $idEmpresa
                );
        $stmtUpdate->execute() or die($stmtUpdate->error);
        $stmtUpdate->close();
    }

    function inserirOuAtualizarUrl($idEmpresa, $nome, $idReclameAqui, $url) {
        $db = DB::singleton();
        $db->abrirBD();
        $site = self::RA_URL_BASE . "/$idReclameAqui/$url";

        $query = "SELECT 1 FROM reclameaqui where idEmpresa = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("i", $idEmpresa);
        $stmt->execute();
        $stmt->store_result();

        $numRow = $stmt->num_rows;
        $stmt->close();

        if ($numRow == 0) {
            $stmtInsert = $db->prepare("INSERT INTO reclameaqui (idEmpresa, nome, site, atualizacao)
                                 VALUES (?, ?, ?, NOW() )");
            $stmtInsert->bind_param("iss", $idEmpresa, $nome, $site);
            $stmtInsert->execute();

            $stmtInsert->close();
        } else {
            $stmtUpdate = $db->prepare("UPDATE reclameaqui
                                     SET  nome = ?,
                                          site = ?,
                                          atualizacao = NOW
                                    WHERE
                                        idEmpresa = ?");
            $stmtUpdate->bind_param("ssi", $nome, $site, $idEmpresa);
            $stmtUpdate->execute();
            $stmtUpdate->close();
        }

        return;
    }

    function obterUrl($id, $nome) {
        $nome_encode = urlencode($nome);
        $conteudo = trim(file_get_html(self::RA_URL_BUSCA . "$nome_encode"));

        $result = array();
        //Obtendo sugestoes
        $linhas = explode("\n", $conteudo);

        foreach ($linhas as $sugestao) {

            if (empty($sugestao)) {
                continue;
            }

            //Obtendo campos da linha
            $tmp_sugestao = explode("|", $sugestao);
            $nome = $tmp_sugestao[0];
            $idRa = $tmp_sugestao[1];
            $url = $tmp_sugestao[2];

            $sra = new SugestaoReclameAqui();
            $sra->nome = $nome;
            $sra->id = $idRa;
            $sra->url = $url;


            $result[] = $sra;
        }

        return $result;
    }

}

