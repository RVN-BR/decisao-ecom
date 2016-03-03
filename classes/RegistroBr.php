<?php

require_once("DB.php");
require_once("Empresa.php");
require_once("Endereco.php");
require_once("includes/simple_html_dom.php");

class RegistroBr {

    private static $URL = 'http://registro.br/cgi-bin/whois/?qr=';
    public $dominio;
    public $documento;
    public $entidade;
    public $pais;
    public $responsavel;
    public $dataExpiracao;
    public $dataCriacao;
    public $dataAlteracao;
    public $status;
    public $atualizacao;

    function atualizarOuInserirInformacao($dominio) {
        $db = DB::singleton(); //Instancia o objeto de conexao SQLite
        $db->abrirBD();

        $this->dominio = $dominio;

        //Obtendo os dados do RegistroBR

        $dados_html = file_get_html(self::$URL . $this->dominio);
        $dados = $dados_html->find("pre", 0)->innertext;

        if (strpos($dados, "% Consulta inválida\r\n") !== false) {
            echo "Consulta inválida para este domínio. \r\n";
            return;
        }

//RegExp - Dominio
        $match = null;
        preg_match("/domínio:( ).*/", $dados, $match);
        $this->dominio = trim(str_replace("domínio:", "", $match[0]));

//RegExp - Entidade
        preg_match("/entidade:( ).*/", $dados, $match);
        $this->entidade = trim(str_replace("entidade:", "", $match[0]));

//RegExp - Documento
        preg_match("/documento:( ).*/", $dados, $match);
        $this->documento = trim(strip_tags(str_replace("documento:", "", $match[0])));

//RegExp - Pais
        preg_match("/país:( ).*/", $dados, $match);
        $this->pais = trim(str_replace("país:", "", $match[0]));

//RegExp - Responsavel
        preg_match("/responsável:( ).*/", $dados, $match);
        $this->responsavel = trim(str_replace("responsável:", "", $match[0]));

//RegExp - Criacao do Dominio
        preg_match("/criado:( ).*/", $dados, $match);
        $this->dataCriacao = substr(trim(str_replace("criado:", "", $match[0])), 0, 10);

//RegExp - Expiracao do Dominio
        preg_match("/expiração:( ).*/", $dados, $match);
        $this->dataExpiracao = substr(trim(str_replace("expiração:", "", $match[0])), 0, 10);

//RegExp - Criacao do Dominio
        preg_match("/alterado:( ).*/", $dados, $match);
        $this->dataAlteracao = substr(trim(str_replace("alterado:", "", $match[0])), 0, 10);

        preg_match("/status:( ).*/", $dados, $match);
        $this->status = trim(str_replace("status:", "", $match[0]));

        $empresa = new Empresa();
        $id = $empresa->obterId("www." . $this->dominio, $empresa::EMPRESA_BUSCA_POR_URL);


        $query = "SELECT 1 FROM registrobr where idEmpresa = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->store_result();

        $numRow = $stmt->num_rows;
        $stmt->close();


//UPDATE
        if ($numRow == 1) {

            $queryUpdate = "UPDATE registrobr SET
                                entidade        = ?,
                                documento       = ?,
                                pais            = ?,
                                responsavel     = ?,
                                dataExpiracao   = STR_TO_DATE(?,'%d/%m/%Y'),
                                dataCriacao     = STR_TO_DATE(?,'%d/%m/%Y'),
                                dataAlteracao   = STR_TO_DATE(?,'%d/%m/%Y'),
                                status          = ?,
                                atualizacao     = NOW()
                            WHERE
                                idEmpresa       = ?
                            ";
            $stmtUpdate = $db->prepare($queryUpdate);
            $stmtUpdate->bind_param("ssssssssi", $this->entidade, $this->documento, $this->pais, $this->responsavel, $this->dataExpiracao, $this->dataCriacao, $this->dataAlteracao, $this->status, $id);
            $stmtUpdate->execute();
        } else { //INSERT
            $queryInsert = "INSERT INTO registrobr
                            (   idEmpresa,
                                entidade,
                                documento,
                                pais,
                                responsavel,
                                dataExpiracao,
                                dataCriacao,
                                dataAlteracao,
                                status,
                                atualizacao
                            )
                            VALUES
                            (
                                ?,
                                ?,
                                ?,
                                ?,
                                ?,
                                STR_TO_DATE(?,'%d/%m/%Y'),
                                STR_TO_DATE(?,'%d/%m/%Y'),
                                STR_TO_DATE(?,'%d/%m/%Y'),
                                ?,
                                NOW()
                            )";
            $stmtInsert = $db->prepare($queryInsert);
            $stmtInsert->bind_param("issssssss", $id, $this->entidade, $this->documento, $this->pais, $this->responsavel, $this->dataExpiracao, $this->dataCriacao, $this->dataAlteracao, $this->status
            );

            $stmtInsert->execute();
        }
    }

}