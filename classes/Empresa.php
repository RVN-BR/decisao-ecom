<?php

require_once("Confiometro.php");
require_once("DB.php");
require_once("Ebit.php");
require_once("Endereco.php");
require_once("RegistroBr.php");
require_once("ReceitaFederal.php");
require_once("ReclameAqui.php");

class Empresa {

    public $nome;
    public $site;
    public $receita = null;
    public $ebit = null;
    public $registroBr = null;
    public $timestamp;

    const EMPRESA_BUSCA_POR_URL = 1;
    const EMPRESA_BUSCA_POR_NOME = 2;

    function __construct() {
        $this->reclameAqui = new ReclameAqui();
        $this->receita = new ReceitaFederal();
        $this->confiometro = new Confiometro();
        $this->registroBr = new RegistroBr();
        $this->ebit = new Ebit();
    }

    static function obterEmpresaSemDetalheReclameAqui()
    {
        $id = $site = null;

        $db = DB::singleton();
        $db->abrirBD();
        $query = "SELECT e.id, TRIM(ra.site) site
                 FROM empresa e
                 JOIN reclameaqui ra ON ra.idEmpresa = e.id
                 WHERE LENGTH(ra.nome) < 2 || dateCadastro is null ";

        $stmt = $db->prepare($query);
        $stmt->execute() ;
        $stmt->bind_result($id, $site);
        $stmt->store_result();

        $arr = null;
        while ($stmt->fetch()) {
            $arr[$id] = $site;
        }
        $stmt->close();

        return $arr;
    }

    static function obterEmpresaSemReclameAqui()
    {
        $id = $nome = null;

        $db = DB::singleton();
        $db->abrirBD();
        $query = "SELECT e.id, TRIM(e.nome) nome
                 FROM empresa e
                 LEFT JOIN reclameaqui ra ON ra.idEmpresa = e.id
                 WHERE ra.id is null AND e.nome is not null";

        $stmt = $db->prepare($query);
        $stmt->execute();
        $stmt->bind_result($id, $nome);
        $stmt->store_result();

        $arr = null;
        while ($stmt->fetch()) {
            $arr[$id] = $nome;
        }
        $stmt->close();

        return $arr;
    }

    static function obterEmpresaSemReceitaFederal() {

        $id = $cnpj = null;

        $db = DB::singleton();
        $db->abrirBD();
        $query = "SELECT e.id, r.documento
                 FROM empresa e
                 JOIN registrobr r ON r.idEmpresa = e.id
                 LEFT JOIN receitafederal rf ON rf.idEmpresa = e.id
                 WHERE rf.id is null AND r.documento is not null and LENGTH(r.documento) > 14";

        $stmt = $db->prepare($query);
        $stmt->execute();
        $stmt->bind_result($id, $cnpj);
        $stmt->store_result();

        $arr = null;
        while ($stmt->fetch()) {
            $cnpj_numberonly = preg_replace("/[^0-9\s]/", "", $cnpj);
            $arr[$id] = substr($cnpj_numberonly, 1);
        }
        $stmt->close();

        return $arr;
    }

    static function obterEmpresaSemRegistroBr() {

        $id = $site = null;
        $db = DB::singleton();
        $db->abrirBD();

        $query = "SELECT e.id, e.site FROM empresa e LEFT JOIN registrobr r ON r.idEmpresa = e.id WHERE r.id is null AND e.site is not null";

        $stmt = $db->prepare($query);
        $stmt->execute();
        $stmt->bind_result($id, $site);
        $stmt->store_result();

        $arr = null;
        while ($stmt->fetch()) {
            $arr[$id] = $site;
        }
        $stmt->close();

        return $arr;
    }

    static function obterEmpresasSemReclameAqui() {
        $id = $nome = null;
        $db = DB::singleton();
        $db->abrirBD();

        $query = "SELECT e.id, e.nome FROM empresa e
                  LEFT JOIN reclameaqui ra
                    ON ra.idEmpresa = e.id
                 WHERE ra.id is null";

        $stmt = $db->prepare($query);
        $stmt->execute();
        $stmt->bind_result($id, $nome);
        $stmt->store_result();

        $arr = null;
        while ($stmt->fetch()) {
            $arr[$id] = $nome;
        }
        $stmt->close();

        return $arr;
    }

    static function obterEmpresasSemSite() {

        $id = $nome = null;
        $db = DB::singleton();
        $db->abrirBD();

        $query = "SELECT id, nome FROM empresa WHERE site is null";

        $stmt = $db->prepare($query);
        $stmt->execute();
        $stmt->bind_result($id, $nome);
        $stmt->store_result();

        $arr = null;
        while ($stmt->fetch()) {
            $arr[$id] = $nome;
        }
        $stmt->close();

        return $arr;
    }

    static function obterId($arg, $tipoBusca = self::EMPRESA_BUSCA_POR_NOME) {
        $db = DB::singleton();
        $db->abrirBD();

        $id = null;
        $query = "SELECT id FROM empresa WHERE ";

        switch ($tipoBusca) {
            case self::EMPRESA_BUSCA_POR_NOME:
                $query .= " nome like ? ";
                break;

            case self::EMPRESA_BUSCA_POR_URL:
                $query .= " site like ? ";
                $arg = "%" . $arg;
                break;

            default:
                throw new Exception("Busca invalida");
        }

        $stmt = $db->prepare($query);
        $stmt->bind_param("s", $arg);
        $stmt->execute();
        $stmt->bind_result($id);
        $stmt->store_result();
        $stmt->fetch();
        $numColumns = $stmt->num_rows;
        if ($numColumns > 1) {
            throw new Exception("Busca retornou mais de um resultado");
        }

        if ($numColumns == 0) {
            throw new Exception("Busca nao retornou resultados");
        }

        return $id;
    }

    /**
     *
     * @param string Nome inteiro ou parcial
     * @param int Limite de colunas a retornar
     * @return array As empresas com campo chave/valor
     */
    static function obterEmpresasArray($nomeParcial, $limit = 10) {
        $id = $nome = null;
        $db = DB::singleton();
        $db->abrirBD();

        $limit = (int) $limit; //Casting de seguranca

        $query = "SELECT e.id, CONCAT(e.nome, ' - ', e.site) nome
                  FROM
                    empresa e
                  LEFT JOIN confiometro     conf
                        ON  conf.idEmpresa  = e.id
                  LEFT JOIN receitafederal  rf
                        ON  rf.idEmpresa    = e.id
                  LEFT JOIN reclameaqui     ra
                        ON  ra.idEmpresa    = e.id
                  WHERE
                        e.nome              LIKE ?
                    OR  rf.nomeEmpresarial  LIKE ?
                    OR  rf.nomeFantasia     LIKE ?
                    OR  ra.nome             LIKE ?
                    OR  e.site              LIKE ?

                  ORDER BY e.nome,2 ASC LIMIT $limit";
        $stmt = $db->prepare($query) or die($db->error);
        $bindNome = $nomeParcial . "%";
        $bindSite = "%$nomeParcial%";
        $stmt->bind_param("sssss", $bindNome, $bindNome, $bindNome, $bindNome, $bindSite) or die($db->error);

        $stmt->execute() or die($db->error);
        $stmt->bind_result($id, $nome) or die($db->error);
        $stmt->store_result() or die($db->error);
        $i = 0;

        if ($stmt->num_rows == 0) {
            $empresas = array();
        } else {
            while ($stmt->fetch()) {
                $empresas[$i] = array((int) $id, utf8_encode($nome));
                $i++;
            }
        }
        $stmt->close();
        return $empresas;
    }

    /**
     *
     * @param string Nome inteiro ou parcial
     * @param int Limite de colunas a retornar
     * @return string As empresas codificadas em json
     */
    static function obterEmpresasJson($nomeParcial, $limit = 10) {
        return json_encode(self::obterEmpresasArray($nomeParcial, $limit));
    }

    static function obterDadosJson($id) {
        $jsonArray = json_encode(self::obterDadosArray($id));
        return $jsonArray;
   }

    static function obterDadosArray($id) {
        $db = DB::singleton();
        $db->abrirBD();
        $db->set_charset("utf8");
        $query = "SELECT
                    e.nome as EmpresaNome,
                    e.site as EmpresaSite,

                    eb.url as EbitUrl,
                    eb.medalha as EbitMedalha,
                    eb.avaliacao as EbitAvaliacao,
                    eb.atualizacao as EbitAtualizacao,

                    rg.entidade as RegistroEntidade,
                    rg.documento as RegistroDocumento,
                    rg.pais as RegistroPais,
                    rg.responsavel as RegistroResponsavel,
                    rg.dataExpiracao as RegistroDataExpiracao,
                    rg.dataAlteracao as RegistroDataAlteracao,
                    rg.dataCriacao as RegistroDataCriacao,
                    rg.status as RegistroStatus,
                    rg.atualizacao as RegistroAtualizacao,

                    rf.cnpj as ReceitaFederalCnpj,
                    rf.dataAbertura as ReceitaFederalDataAbertura,
                    rf.nomeEmpresarial as ReceitaFederalNomeEmpresarial,
                    rf.nomeFantasia as ReceitaFederalNomeFantasia,
                    rf.atividadePrimaria as ReceitaFederalAtividadePrimaria,
                    rf.atividadeSecundaria as ReceitaFederalAtividadeSecundaria,
                    rf.tipoEmpresa as ReceitaFederalTipoEmpresa,
                    rf.logradouro as ReceitaFederalEnderecoLogradouro,
                    rf.numero as ReceitaFederalEnderecoNumero,
                    rf.complemento as ReceitaFederalEnderecoComplemento,
                    rf.cep as ReceitaFederalEnderecoCep,
                    rf.bairro as ReceitaFederalEnderecoBairro,
                    rf.municipio as ReceitaFederalEnderecoMunicipio,
                    rf.uf as ReceitaFederalEnderecoUf,
                    rf.situacaoCadastral as ReceitaFederalSituacaoCadastral,
                    rf.dataSituacaoCadastral as ReceitaFederalDataSituacaoCadastral,
                    rf.atualizacao as ReceitaFederalAtualizacao,

                    ra.nome,
                    ra.dateCadastro,
                    ra.site,
                    ra.fone,
                    CASE ra.reputacao
                        WHEN 'http://www.reclameaqui.com.br/images/ico-bom.gif' THEN 'Boa'
                        WHEN 'http://www.reclameaqui.com.br/images/ico-otimo.gif' THEN 'Otima'
                        WHEN 'http://www.reclameaqui.com.br/images/ico-regular.gif' THEN 'Regular'
                        WHEN 'http://www.reclameaqui.com.br/images/ico-naorecomendado.gif' THEN 'Nao recomendada'
                        WHEN 'http://www.reclameaqui.com.br/images/premio/selo-finalista-otima-reputacao.png' THEN 'Premio Epoca/ReclameAqui 2013 - Otima'
                        WHEN 'http://www.reclameaqui.com.br/images/ico-semindice.gif' THEN 'Sem indice'
                        WHEN 'http://www.reclameaqui.com.br/images/ico-excelente.gif' THEN 'Excelente'
                        WHEN 'http://www.reclameaqui.com.br/images/ico-ruim.gif' THEN 'Ruim'
                        WHEN 'http://www.reclameaqui.com.br/images/iem-analise.png' THEN 'Em analise'
                        ELSE '?'
                    END reputacao,
                    ra.atendida,
                    ra.solucao,
                    ra.voltariaFazerNegocio,
                    ra.notaConsumidor,
                    ra.tempoMedioResposta,
                    ra.avaliacao,
                    ra.avaliacaoNaoAtendida,
                    ra.avaliacaoAtendida,
                    ra.atualizacao


            FROM empresa e
            LEFT JOIN ebit eb
                    ON eb.idEmpresa = e.id
            LEFT JOIN registrobr rg
                    ON rg.idEmpresa = e.id
            LEFT JOIN receitafederal rf
                    ON rf.idEmpresa = e.id
            LEFT JOIN reclameaqui ra
                    ON ra.idEmpresa = e.id
            WHERE e.id = ?";
        $stmt = $db->prepare($query) or die($db->error);
        $stmt->bind_param("i", $id) or die($db->error);

        $stmt->execute() or die($db->error);

        $empresa = new Empresa();
        $stmt->bind_result(
                $empresa->nome,
                $empresa->site,

                $empresa->ebit->url,
                $empresa->ebit->medalha,
                $empresa->ebit->avaliacao,
                $empresa->ebit->atualizacao,

                $empresa->registroBr->entidade,
                $empresa->registroBr->documento,
                $empresa->registroBr->pais,
                $empresa->registroBr->responsavel,
                $empresa->registroBr->dataExpiracao,
                $empresa->registroBr->dataAlteracao,
                $empresa->registroBr->dataCriacao,
                $empresa->registroBr->status,
                $empresa->registroBr->atualizacao,

                $empresa->receita->cnpj,
                $empresa->receita->dataAbertura,
                $empresa->receita->nomeEmpresarial,
                $empresa->receita->nomeFantasia,
                $empresa->receita->atividadePrimaria,
                $empresa->receita->atividadeSecundaria,
                $empresa->receita->tipoEmpresa,
                $empresa->receita->endereco->logradouro,
                $empresa->receita->endereco->numero,
                $empresa->receita->endereco->complemento,
                $empresa->receita->endereco->cep,
                $empresa->receita->endereco->bairro,
                $empresa->receita->endereco->municipio,
                $empresa->receita->endereco->uf,
                $empresa->receita->situacaoCadastral,
                $empresa->receita->dataSituacaoCadastral,
                $empresa->receita->atualizacao,

                $empresa->reclameAqui->nome,
                $empresa->reclameAqui->dateCadastro,
                $empresa->reclameAqui->site,
                $empresa->reclameAqui->fone,
                $empresa->reclameAqui->reputacao,
                $empresa->reclameAqui->atendida,
                $empresa->reclameAqui->solucao,
                $empresa->reclameAqui->voltariaFazerNegocio,
                $empresa->reclameAqui->notaConsumidor,
                $empresa->reclameAqui->tempoMedioResposta,
                $empresa->reclameAqui->avaliacao,
                $empresa->reclameAqui->avaliacaoNaoAtendida,
                $empresa->reclameAqui->avaliacaoAtendida,
                $empresa->reclameAqui->atualizacao
                ) or die($db->error);
        $stmt->store_result() or die($db->error);


        if ($stmt->fetch() === false) {
            $empresa = null;
        } else {
            $empresa->registroBr->dominio = $empresa->site;
            $empresa->ebit->nome = $empresa->nome;
            $empresa->timestamp = date("H:i:s - d/m/Y");
            $empresa->confiometro->nome = NULL;
        }

        return $empresa;
    }

}
