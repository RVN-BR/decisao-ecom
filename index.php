<?php
require_once("classes/DB.php");
require_once("classes/Ebit.php");
require_once("classes/RegistroBr.php");
require_once("classes/Empresa.php");

header('Content-type: text/html; charset=iso-8859-1');

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



                function getInfo(id)
                {
                    $.ajax({
                        url: "busca-dados.php",
                        dataType: "json",
                        data: {
                            empresa: id
                        },
                        success: function(data) {
                            $("#pre").empty();

                            $("#pre").append("\tInformações Gerais:\n");
                            $("#pre").append("\t\tNome:\t" + data.nome + "\n");
                            $("#pre").append("\t\tSite:\t" + data.site + "\n");

                            $("#pre").append("\n");
                            $("#pre").append("\tInformações do EBIT:\n");

                            $("#pre").append("\t\tNome:\t" + data.ebit.nome + "\n");
                            $("#pre").append("\t\tMedalha:\t" + data.ebit.medalha + "\n");
                            $("#pre").append("\t\tAvaliações:\t" + data.ebit.avaliacao + "\n");
                            $("#pre").append("\t\tPágina no EBIT:\t" + data.ebit.url + "\n");
                            $("#pre").append("\t\tInformações obtidas em :\t" + data.ebit.atualizacao + "\n");

                            $("#pre").append("\n");
                            $("#pre").append("\tInformações do Registro.BR (CGI):\n");
                            if (data.registroBr.entidade === null)
                            {
                                $("#pre").append("\t\t --------Indisponiveis--------\n\n");
                            }
                            else
                            {
                                $("#pre").append("\t\tEntidade:\t" + data.registroBr.entidade + "\n");
                                $("#pre").append("\t\tResponsável:\t" + data.registroBr.responsavel + "\n");
                                $("#pre").append("\t\tDomínio:\t" + data.registroBr.dominio + "\n");
                                $("#pre").append("\t\t\tData Criação:\t" + data.registroBr.dataCriacao + "\n");
                                $("#pre").append("\t\t\tData de Alteração:\t" + data.registroBr.dataAlteracao + "\n");
                                $("#pre").append("\t\t\tData de Expiração:\t" + data.registroBr.dataExpiracao + "\n");
                                $("#pre").append("\t\tStatus do Domínio:\t" + data.registroBr.status + "\n");
                                $("#pre").append("\t\tInformações obtidas em :\t" + data.registroBr.atualizacao + "\n");

                                $("#pre").append("\n");
                            }
                            $("#pre").append("\tInformações da Receita Federal do Brasil:\n");
                            if (data.receita.nomeEmpresarial === null)
                            {
                                $("#pre").append("\t\t --------Indisponiveis--------\n\n");
                            }
                            else
                            {


                                $("#pre").append("\t\tNome Empresarial:\t" + data.receita.nomeEmpresarial + "\n");
                                $("#pre").append("\t\tNome Fantasia:\t" + data.receita.nomeFantasia + "\n");
                                $("#pre").append("\t\tCNPJ:\t" + data.receita.cnpj + "\n");
                                $("#pre").append("\t\tSituação Cadastral:\t" + data.receita.situacaoCadastral + "\n");
                                $("#pre").append("\t\tData da Cadastral:\t" + data.receita.dataSituacaoCadastral + "\n");
                                $("#pre").append("\t\tAtividade Primária:\t" + data.receita.atividadePrimaria + "\n");
                                $("#pre").append("\t\tAtividade Secundária:\t" + data.receita.atividadeSecundaria + "\n");
                                $("#pre").append("\t\tTipo de Empresa:\t" + data.receita.tipoEmpresa + "\n");
                                $("#pre").append("\t\tEndereço:\n");
                                $("#pre").append("\t\t\tLogradouro: " + data.receita.endereco.logradouro + "\n");
                                $("#pre").append("\t\t\tNúmero: " + data.receita.endereco.numero + "\n");
                                $("#pre").append("\t\t\tComplemento: " + data.receita.endereco.complemento + "\n");
                                $("#pre").append("\t\t\tBairro: " + data.receita.endereco.bairro + "\n");
                                $("#pre").append("\t\t\tCidade: " + data.receita.endereco.municipio + "\n");
                                $("#pre").append("\t\t\tUF: " + data.receita.endereco.uf + "\n");
                                $("#pre").append("\t\t\tCEP: " + data.receita.endereco.cep + "\n");
                                $("#pre").append("\t\tInformações obtidas em :\t" + data.receita.atualizacao + "\n");
                            }

                            $("#pre").append("\n");
                            $("#pre").append("\tInformações do Reclame Aqui:\n");
                            if (data.reclameAqui.nome === null)
                            {
                                $("#pre").append("\t\t --------Indisponiveis--------\n\n");
                            }
                            else
                            {
                                $("#pre").append("\t\tNome: " + data.reclameAqui.nome + "\n");
                                $("#pre").append("\t\tFone: " + data.reclameAqui.fone + "\n");
                                $("#pre").append("\t\tData de Cadastro: " + data.reclameAqui.dateCadastro + "\n");
                                $("#pre").append("\t\tPágina no Reclame Aqui: " + data.reclameAqui.site + "\n");
                                $("#pre").append("\t\tReputacao: " + data.reclameAqui.reputacao + "\n");
                                $("#pre").append("\t\tAvaliações: " + data.reclameAqui.avaliacao + "\n");
                                $("#pre").append("\t\tReclamações Atendidas: " + data.reclameAqui.avaliacaoAtendida + "\n");
                                $("#pre").append("\t\tReclamações Não Atendidas: " + data.reclameAqui.avaliacaoNaoAtendida + "\n");
                                $("#pre").append("\t\t\tPercentual - Atendidas: " + data.reclameAqui.atendida + "%\n");
                                $("#pre").append("\t\t\tPercentual - Solucionadas: " + data.reclameAqui.solucao + "%\n");
                                $("#pre").append("\t\tVoltariam a fazer negócio: " + data.reclameAqui.voltariaFazerNegocio + "%\n");
                                $("#pre").append("\t\tNota do Consumidor (0-10): " + data.reclameAqui.notaConsumidor + "\n");
                                $("#pre").append("\t\tTempo Médio de Resposta: " + data.reclameAqui.tempoMedioResposta + "\n");
                                $("#pre").append("\t\tInformações obtidas em :\t" + data.reclameAqui.atualizacao + "\n");
                            }

                            $("#pre").append("\n");
                            $("#pre").append("\tInformações do Confiômetro:\n");
                            if (data.confiometro.nome === null)
                            {
                                $("#pre").append("\t\t --------Indisponiveis--------\n\n");
                            }

                            $("#pre").append("\tConsulta realizada em " + data.timestamp + "\n");
                            $("#pre").append("\n");
                            $("#pre").append("\n");
                            $("#pre").append("\tO Sistema de Suporte à Decisão do Consumidor do Comercio Eletrônico é um sistema de uso gratuito \n");
                            $("#pre").append("\te foi desenvolvido pelo aluno Rodolfo Andrade de Oliveira, do curso de Especialização em Tecnolo-\n")
                            $("#pre").append("\tgia e Sistemas de Informação da Universidade Federal do ABC, como componente do Trabalho de Conclu-\n");
                            $("#pre").append("\tsão de Curso, requisito para obtenção do grau de Especialista em Tecnologias e Sistemas de Informa-\n");
                            $("#pre").append("\tção.\n\n");
                            $("#pre").append("\tTodas as informações foram obtidas gratuitamente e são de responsabilidade de seus mantedores.\n");
                            $("#pre").append("\tSistema desenvolvido somente com ferramentas de código livre e aberto.\n");


                            $("#pre").append("</pre>");

                        }
                    });
                }


                $("#nomeEmpresa").click(function() {
                    $("#nomeEmpresa").val("");
                });

                $("#nomeEmpresa").keypress(function(e) {
                    if (e.which === 13) {
                        e.preventDefault();
                    }
                });

                $("#nomeEmpresa").autocomplete({
                    source: function(request, response) {
                        $.ajax({
                            url: "busca-empresa.php",
                            dataType: "json",
                            data: {
                                empresa: request.term
                            },
                            success: function(data) {
                                response($.map(data, function(item) {

                                    return {
                                        label: item[1], //nome
                                        value: item[2],
                                        id: item[0] //id
                                    };
                                }));
                            }
                        });
                    },
                    minLength: 3,
                    select: function(event, ui) {
                        getInfo(ui.item.id);
                    },
                    open: function() {
                        $(this).removeClass("ui-corner-all").addClass("ui-corner-top");
                    },
                    close: function() {
                        $(this).removeClass("ui-corner-top").addClass("ui-corner-all");
                    }
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

        <h2> SAD-CCE - Sistema de Apoio à Decisão para Consumidores do Comércio Eletrônico</h2>



        <div class="ui-widget">
            <label for="nomeEmpresa">Digite o nome da empresa ou site (sem http://): </label>
            <input id="nomeEmpresa" size="40"/>
        </div>

        <div class="ui-widget" id="resp">
            <pre id="pre" style="font-size:150%">

            </pre>
        </div>
    </body>
</html>

