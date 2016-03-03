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

                            $("#pre").append("\tInforma��es Gerais:\n");
                            $("#pre").append("\t\tNome:\t" + data.nome + "\n");
                            $("#pre").append("\t\tSite:\t" + data.site + "\n");

                            $("#pre").append("\n");
                            $("#pre").append("\tInforma��es do EBIT:\n");

                            $("#pre").append("\t\tNome:\t" + data.ebit.nome + "\n");
                            $("#pre").append("\t\tMedalha:\t" + data.ebit.medalha + "\n");
                            $("#pre").append("\t\tAvalia��es:\t" + data.ebit.avaliacao + "\n");
                            $("#pre").append("\t\tP�gina no EBIT:\t" + data.ebit.url + "\n");
                            $("#pre").append("\t\tInforma��es obtidas em :\t" + data.ebit.atualizacao + "\n");

                            $("#pre").append("\n");
                            $("#pre").append("\tInforma��es do Registro.BR (CGI):\n");
                            if (data.registroBr.entidade === null)
                            {
                                $("#pre").append("\t\t --------Indisponiveis--------\n\n");
                            }
                            else
                            {
                                $("#pre").append("\t\tEntidade:\t" + data.registroBr.entidade + "\n");
                                $("#pre").append("\t\tRespons�vel:\t" + data.registroBr.responsavel + "\n");
                                $("#pre").append("\t\tDom�nio:\t" + data.registroBr.dominio + "\n");
                                $("#pre").append("\t\t\tData Cria��o:\t" + data.registroBr.dataCriacao + "\n");
                                $("#pre").append("\t\t\tData de Altera��o:\t" + data.registroBr.dataAlteracao + "\n");
                                $("#pre").append("\t\t\tData de Expira��o:\t" + data.registroBr.dataExpiracao + "\n");
                                $("#pre").append("\t\tStatus do Dom�nio:\t" + data.registroBr.status + "\n");
                                $("#pre").append("\t\tInforma��es obtidas em :\t" + data.registroBr.atualizacao + "\n");

                                $("#pre").append("\n");
                            }
                            $("#pre").append("\tInforma��es da Receita Federal do Brasil:\n");
                            if (data.receita.nomeEmpresarial === null)
                            {
                                $("#pre").append("\t\t --------Indisponiveis--------\n\n");
                            }
                            else
                            {


                                $("#pre").append("\t\tNome Empresarial:\t" + data.receita.nomeEmpresarial + "\n");
                                $("#pre").append("\t\tNome Fantasia:\t" + data.receita.nomeFantasia + "\n");
                                $("#pre").append("\t\tCNPJ:\t" + data.receita.cnpj + "\n");
                                $("#pre").append("\t\tSitua��o Cadastral:\t" + data.receita.situacaoCadastral + "\n");
                                $("#pre").append("\t\tData da Cadastral:\t" + data.receita.dataSituacaoCadastral + "\n");
                                $("#pre").append("\t\tAtividade Prim�ria:\t" + data.receita.atividadePrimaria + "\n");
                                $("#pre").append("\t\tAtividade Secund�ria:\t" + data.receita.atividadeSecundaria + "\n");
                                $("#pre").append("\t\tTipo de Empresa:\t" + data.receita.tipoEmpresa + "\n");
                                $("#pre").append("\t\tEndere�o:\n");
                                $("#pre").append("\t\t\tLogradouro: " + data.receita.endereco.logradouro + "\n");
                                $("#pre").append("\t\t\tN�mero: " + data.receita.endereco.numero + "\n");
                                $("#pre").append("\t\t\tComplemento: " + data.receita.endereco.complemento + "\n");
                                $("#pre").append("\t\t\tBairro: " + data.receita.endereco.bairro + "\n");
                                $("#pre").append("\t\t\tCidade: " + data.receita.endereco.municipio + "\n");
                                $("#pre").append("\t\t\tUF: " + data.receita.endereco.uf + "\n");
                                $("#pre").append("\t\t\tCEP: " + data.receita.endereco.cep + "\n");
                                $("#pre").append("\t\tInforma��es obtidas em :\t" + data.receita.atualizacao + "\n");
                            }

                            $("#pre").append("\n");
                            $("#pre").append("\tInforma��es do Reclame Aqui:\n");
                            if (data.reclameAqui.nome === null)
                            {
                                $("#pre").append("\t\t --------Indisponiveis--------\n\n");
                            }
                            else
                            {
                                $("#pre").append("\t\tNome: " + data.reclameAqui.nome + "\n");
                                $("#pre").append("\t\tFone: " + data.reclameAqui.fone + "\n");
                                $("#pre").append("\t\tData de Cadastro: " + data.reclameAqui.dateCadastro + "\n");
                                $("#pre").append("\t\tP�gina no Reclame Aqui: " + data.reclameAqui.site + "\n");
                                $("#pre").append("\t\tReputacao: " + data.reclameAqui.reputacao + "\n");
                                $("#pre").append("\t\tAvalia��es: " + data.reclameAqui.avaliacao + "\n");
                                $("#pre").append("\t\tReclama��es Atendidas: " + data.reclameAqui.avaliacaoAtendida + "\n");
                                $("#pre").append("\t\tReclama��es N�o Atendidas: " + data.reclameAqui.avaliacaoNaoAtendida + "\n");
                                $("#pre").append("\t\t\tPercentual - Atendidas: " + data.reclameAqui.atendida + "%\n");
                                $("#pre").append("\t\t\tPercentual - Solucionadas: " + data.reclameAqui.solucao + "%\n");
                                $("#pre").append("\t\tVoltariam a fazer neg�cio: " + data.reclameAqui.voltariaFazerNegocio + "%\n");
                                $("#pre").append("\t\tNota do Consumidor (0-10): " + data.reclameAqui.notaConsumidor + "\n");
                                $("#pre").append("\t\tTempo M�dio de Resposta: " + data.reclameAqui.tempoMedioResposta + "\n");
                                $("#pre").append("\t\tInforma��es obtidas em :\t" + data.reclameAqui.atualizacao + "\n");
                            }

                            $("#pre").append("\n");
                            $("#pre").append("\tInforma��es do Confi�metro:\n");
                            if (data.confiometro.nome === null)
                            {
                                $("#pre").append("\t\t --------Indisponiveis--------\n\n");
                            }

                            $("#pre").append("\tConsulta realizada em " + data.timestamp + "\n");
                            $("#pre").append("\n");
                            $("#pre").append("\n");
                            $("#pre").append("\tO Sistema de Suporte � Decis�o do Consumidor do Comercio Eletr�nico � um sistema de uso gratuito \n");
                            $("#pre").append("\te foi desenvolvido pelo aluno Rodolfo Andrade de Oliveira, do curso de Especializa��o em Tecnolo-\n")
                            $("#pre").append("\tgia e Sistemas de Informa��o da Universidade Federal do ABC, como componente do Trabalho de Conclu-\n");
                            $("#pre").append("\ts�o de Curso, requisito para obten��o do grau de Especialista em Tecnologias e Sistemas de Informa-\n");
                            $("#pre").append("\t��o.\n\n");
                            $("#pre").append("\tTodas as informa��es foram obtidas gratuitamente e s�o de responsabilidade de seus mantedores.\n");
                            $("#pre").append("\tSistema desenvolvido somente com ferramentas de c�digo livre e aberto.\n");


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

        <h2> SAD-CCE - Sistema de Apoio � Decis�o para Consumidores do Com�rcio Eletr�nico</h2>



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

