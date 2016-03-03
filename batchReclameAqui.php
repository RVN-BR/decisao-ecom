<?php

ini_set("max_execution_time", 0);
require_once("classes/Empresa.php");
require_once("classes/ReclameAqui.php");

header('Content-type: text/html; charset=iso-8859-1');

$arrObter = Empresa::obterEmpresaSemReclameAqui();

echo "<pre>";
$reclameAqui = new ReclameAqui();
if (count($arrObter) > 0) {


    echo "Iniciando processo de carga inicial para " . count($arrObter) . " empresas \r\n";

    $x = 0;




    foreach ($arrObter as $k => $v) {
        echo "\tProcessando empresa -> [$k] $v \n";
        $arrSugestao = $reclameAqui->obterUrl($k, $v);

        if (empty($arrSugestao)) {
            echo "\t\tNenhuma sugestao para esta empresa\n";
            continue;
        }

        if (count($arrSugestao) == 1) {
            $unicaSugestao = reset($arrSugestao);
            $site = ReclameAqui::RA_URL_BASE . "/" . $unicaSugestao->id . "/" . $unicaSugestao->url;
            echo "\t\tUnica sugestao $site \n";

            $reclameAqui->inserirOuAtualizarUrl($k, $unicaSugestao->nome, $unicaSugestao->id, $unicaSugestao->url);

            echo "\t\t\tInclusao efetuada.\n";
        } else {
            echo "\t\tMultiplas sugestoes: \n";
            foreach ($arrSugestao as $sugestao) {
                $site = ReclameAqui::RA_URL_BASE . "/" . $sugestao->id . "/" . $sugestao->url;
                echo "\t\t\t $site \n";
            }
        }
        $x++;
        flush();
        ob_flush();
        sleep(mt_rand(5, 10));
    }
    echo "Fim do processamento de carga inicial \n";
}

//Carregando o detalhe das empresas

$arrDetalhe = Empresa::obterEmpresaSemDetalheReclameAqui();

if (count($arrDetalhe) > 0)
{
    echo "Iniciando processo de carga de detalhes para " . count($arrDetalhe) . " empresas \r\n";

    foreach($arrDetalhe as $k => $v)
    {
        echo "\tAtualizando detalhes de $k => $v";
        $reclameAqui->atualizarDetalhes($k, $v);
        echo " - OK\n";
        flush();
        ob_flush();
        sleep(mt_rand(3, 5));
    }
}
else
{
    echo "Nenhum empresa a ter os detalhes inseridos.\n";
}
echo "**Fim da execução.**";