<?php

require_once('classes/DB.php');
require_once('classes/Endereco.php');
require_once("includes/simple_html_dom.php");

class ReceitaFederal {

    public $cnpj;
    public $dataAbertura;
    public $nomeEmpresarial;
    public $nomeFantasia;
    public $atividadePrimaria;
    public $atividadeSecundaria;
    public $tipoEmpresa;
    public $endereco;
    public $situacaoCadastral;
    public $dataSituacaoCadastral;
    public $atualizacao;

    public function ReceitaFederal() {
        $this->endereco = new Endereco();
    }

    public function atualizarOuInserirInformacao($html, $idEmpresa) {
        $db = DB::singleton(); //Instancia o objeto de conexao SQLite
        $db->abrirBD();

        //Obtendo o HTML-DOM via string
        $dados_html = str_get_html($html);
        $dados = $dados_html->find("table", 2);

        if (empty($dados) || $dados == NULL) {
            echo "Ocorreu uma falha na obtencao dos dados da RFB";
//          echo $dados_html;
            return;
        }



        $this->cnpj = trim($dados->find("table", 1)->find("td", 0)->find("font", 1)->plaintext);
        $this->dataAbertura = trim($dados->find("table", 1)->find("td", 2)->find("font", 1)->plaintext);

        $this->nomeEmpresarial = trim($dados->find("table", 2)->find("td", 0)->find("font", 1)->plaintext);

        $this->nomeFantasia = trim($dados->find("table", 3)->find("td", 0)->find("font", 1)->plaintext);

        $this->atividadePrimaria = trim($dados->find("table", 4)->find("td", 0)->find("font", 1)->plaintext);

        $this->atividadeSecundaria = trim($dados->find("table", 5)->find("td", 0)->find("font", 1)->plaintext);

        $this->tipoEmpresa = trim($dados->find("table", 6)->find("td", 0)->find("font", 1)->plaintext);

        $this->endereco->logradouro = trim($dados->find("table", 7)->find("td", 0)->find("font", 1)->plaintext);
        $this->endereco->numero = trim($dados->find("table", 7)->find("td", 2)->find("font", 1)->plaintext);
        $this->endereco->complemento = trim($dados->find("table", 7)->find("td", 4)->find("font", 1)->plaintext);

        $this->endereco->cep = trim($dados->find("table", 8)->find("td", 0)->find("font", 1)->plaintext);
        $this->endereco->bairro = trim($dados->find("table", 8)->find("td", 2)->find("font", 1)->plaintext);
        $this->endereco->cidade = trim($dados->find("table", 8)->find("td", 4)->find("font", 1)->plaintext);
        $this->endereco->uf = trim($dados->find("table", 8)->find("td", 6)->find("font", 1)->plaintext);

        $this->situacaoCadastral = trim($dados->find("table", 9)->find("td", 0)->find("font", 1)->plaintext);
        $this->dataSituacaoCadastral = trim($dados->find("table", 9)->find("td", 2)->find("font", 1)->plaintext);


        $query = "SELECT 1 FROM receitafederal where idEmpresa = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("i", $idEmpresa);
        $stmt->execute();
        $stmt->store_result();

        $numRow = $stmt->num_rows;
        $stmt->close();


//UPDATE
        if ($numRow == 1) {

            $queryUpdate = "UPDATE receitafederal
                            SET
                            dataAbertura	 =  ?
                           ,nomeEmpresarial	 =  ?
                           ,nomeFantasia	 =  ?
                           ,atividadePrimaria	 =  ?
                           ,atividadeSecundaria	 =  ?
                           ,tipoEmpresa          =  ?
                           ,logradouro           =  ?
                           ,numero               =  ?
                           ,complemento          =  ?
                           ,cep                  =  ?
                           ,bairro               =  ?
                           ,municipio            =  ?
                           ,uf                   =  ?
                           ,situacaoCadastral	 =  ?
                           ,dataSituacaoCadastral =  ?
                           ,atualizacao          = NOW()
                           WHERE
                             idEmpresa       = ? ";

            $stmtUpdate = $db->prepare($queryUpdate);
            $stmtUpdate->bind_param("sssssssssssssssi", $this->dataAbertura, $this->nomeEmpresarial, $this->nomeFantasia, $this->atividadePrimaria, $this->atividadeSecundaria, $this->tipoEmpresa, $this->endereco->logradouro, $this->endereco->numero, $this->endereco->complemento, $this->endereco->cep, $this->endereco->bairro, $this->endereco->municipio, $this->endereco->uf, $this->situacaoCadastral, $this->dataSituacaoCadastral, $idEmpresa);
            $stmtUpdate->execute() or die ('Falha na atualizacao dos dados: ' . $stmtUpdate->error);
        } else { //INSERT
            $queryInsert = "INSERT INTO receitafederal
                            (
                            idEmpresa
                           ,dataAbertura
                           ,nomeEmpresarial
                           ,nomeFantasia
                           ,atividadePrimaria
                           ,atividadeSecundaria
                           ,tipoEmpresa
                           ,logradouro
                           ,numero
                           ,complemento
                           ,cep
                           ,bairro
                           ,municipio
                           ,uf
                           ,situacaoCadastral
                           ,dataSituacaoCadastral
                           ,atualizacao
                            )
                            VALUES
                            (
                                ?,
                                STR_TO_DATE(?, '%d/%m/%Y'),
                                ?,
                                ?,
                                ?,
                                ?,
                                ?,
                                ?,
                                ?,
                                ?,
                                ?,
                                ?,
                                ?,
                                ?,
                                ?,
                                STR_TO_DATE(?, '%d/%m/%Y'),
                                NOW()
                            )";
            $stmtInsert = $db->prepare($queryInsert);
            $stmtInsert->bind_param("isssssssssssssss",
                    $idEmpresa,
                    $this->dataAbertura,
                    $this->nomeEmpresarial,
                    $this->nomeFantasia,
                    $this->atividadePrimaria,
                    $this->atividadeSecundaria,
                    $this->tipoEmpresa,
                    $this->endereco->logradouro,
                    $this->endereco->numero,
                    $this->endereco->complemento,
                    $this->endereco->cep,
                    $this->endereco->bairro,
                    $this->endereco->cidade,
                    $this->endereco->uf,
                    $this->situacaoCadastral,
                    $this->dataSituacaoCadastral
            );

            $stmtInsert->execute() or die('Falha na insercao - ' . $stmtInsert->error);
        }
    }

}