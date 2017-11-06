<?php

require("libs/db_stdlib.php");
require("libs/db_conecta.php");
include("libs/db_sql.php");
include("classes/db_db_depart_classe.php");
include("libs/db_liborcamento.php");
include("libs/db_utils.php");

$method = $_POST['method'];
$escolha = $_POST['tpTotalizadores'];
$dtini = $_POST['dtIni'];
$dtfim = $_POST['dtFim'];
$complexcod = $_POST['complex'];
$complexdescr = $_POST['complexdescr'];

//var_dump($_SESSION);exit;
$dtIni = implode('-', array_reverse(explode('/', $dtini)));
$dtFim = implode('-', array_reverse(explode('/', $dtfim)));

if (!empty($dtIni) && !empty($dtFim)) {//Condição para pesquisa usando data
    $wheredt = "and ov01_dataatend between '$dtIni' and '$dtFim'";
}

if (!empty($complexcod)) {//Condição para pesquisa usando complexibilidade
    $wherecomp = "and ov01_prioridade = $complexcod";
}
//Innerjoins padrões para o funcionamento da pesquisa correta
$inner = " inner join tipoproc on tipoproc.p51_codigo = ouvidoriaatendimento.ov01_tipoprocesso 
            inner join tipoidentificacao on tipoidentificacao.ov05_sequencial = ouvidoriaatendimento.ov01_tipoidentificacao 
            inner join formareclamacao on formareclamacao.p42_sequencial = ouvidoriaatendimento.ov01_formareclamacao 
            inner join tipoprocgrupo on tipoprocgrupo.p40_sequencial = tipoproc.p51_tipoprocgrupo 
            inner join situacaoouvidoriaatendimento on situacaoouvidoriaatendimento.ov18_sequencial = ouvidoriaatendimento.ov01_situacaoouvidoriaatendimento 
            left join processoouvidoria on processoouvidoria.ov09_ouvidoriaatendimento = ouvidoriaatendimento.ov01_sequencial 
            left join protprocesso on protprocesso.p58_codproc = processoouvidoria.ov09_protprocesso 
            left join processoouvidoriaprorrogacao on processoouvidoriaprorrogacao.ov15_protprocesso = protprocesso.p58_codproc 
            left join ouvidoriaatendimentocidadao on ouvidoriaatendimento.ov01_sequencial = ouvidoriaatendimentocidadao.ov10_ouvidoriaatendimento 
            left join ouvidoriaatendimentocgm on ouvidoriaatendimentocgm.ov11_ouvidoriaatendimento = ouvidoriaatendimento.ov01_sequencial";

if ($method == 'carregaDadosGraf') {//Verifica o método
    switch ($escolha) {//Switch para identificar o radio button marcado no formulário
        case 'tipoproc'://Caso seja Tipo de Processo            
            $sql = "SELECT p51_descr, Count(*) FROM ouvidoriaatendimento
                   $inner 
                    where ov01_instit = 1 and ov01_depart = {$_SESSION['DB_coddepto']} and ov01_situacaoouvidoriaatendimento = 1 and ov15_sequencial is null $wheredt $wherecomp 
                    GROUP BY p51_descr
                    HAVING Count(*) > 0";
            //var_dump($sql);exit;
            $resulta = pg_query($sql); //executa a query
            $und = pg_fetch_all($resulta); //pega todos os dados encontrados e joga dentro de $und

            $labels = array();
            $data = array();
            $labelper = array();
            $dataPercent = array();
            $msgErro = "";

            if (empty($und)) {//Tratamento de erro caso nada seja retornado na variável $und
                $msgErro = 'Nenhum atendimento encontrado. Verificar as datas e/ou departamento.';
            }
            
            foreach ($und as $i) {//Iteração dos dados buscados pela query dentro de um array (Label e Data)
                array_push($labels, $i['p51_descr']);
                array_push($data, $i['count']);
            }
            
            //Configuração para a exibição da porcentagem
            foreach($data as $i){//Econtrando o valor total de atendimentos
                $totalAtend += $i;
            }
  
            for($I = 0; $I < count($data); $I++ ){//Laço para inclusão da porcentagem na label do gráfico
                $percentVal = (100*$data[$I])/$totalAtend;//Calculo da porcentagem que corresponde o valor
                array_push($dataPercent, $percentVal);//Iteração do valor em um array
                array_push($labelper, $labels[$I].' - '.number_format($percentVal, 2).'%');//formatação do valor na inserção do mesmo em um array
            }
            
            //valores atribuídos ao Json (responseText) enviado para ouv1_grafico.php
            $retorno = array('label' => $labelper, 'data' => $data, 'msgerro' => $msgErro, 'labelbar' => $labels);
            echo json_encode($retorno);
            break;

        case 'formrec'://Caso seja Forma de reclamção
            $sql = "SELECT ov01_formareclamacao, Count(*) FROM ouvidoriaatendimento
                    $inner
                    where ov01_instit = 1 and ov01_depart = {$_SESSION['DB_coddepto']} and ov01_situacaoouvidoriaatendimento = 1 and ov15_sequencial is null $wheredt $wherecomp 
                    GROUP BY ov01_formareclamacao
                    HAVING Count(*) > 0";

            $resulta = pg_query($sql);
            $und = pg_fetch_all($resulta);

            $labels = array();
            $data = array();
            $labelper = array();
            $msgErro = "";

            if (empty($und)) {
                $msgErro = 'Nenhum atendimento encontrado. Verificar as datas e/ou departamento.';
            }
            
            foreach ($und as $i) {
                array_push($data, $i['count']);

                //Switch para alterar o que vem em número do banco para String, facilita identificação
                switch ($i['ov01_formareclamacao']) {
                    case '1':
                        array_push($labels, "Pessoalmente");
                        break;
                    case '2':
                        array_push($labels, "Telefone");
                        break;
                    case '3':
                        array_push($labels, "Internet");
                        break;
                    case '4':
                        array_push($labels, "Carta");
                        break;
                    default : "Forma de reclamação não identificada";
                }
            }
            
            //Configuração para a exibição da porcentagem
            foreach($data as $i){//Econtrando o valor total de atendimentos
                $totalAtend += $i;
            }
  
            for($I = 0; $I < count($data); $I++ ){//Laço para inclusão da porcentagem na label do gráfico
                $percentVal = (100*$data[$I])/$totalAtend;//Calculo da porcentagem que corresponde o valor
                array_push($dataPercent, $percentVal);//Iteração do valor em um array
                array_push($labelper, $labels[$I].' - '.number_format($percentVal, 2).'%');//formatação do valor na inserção do mesmo em um array
            }

            $retorno = array('label' => $labelper, 'data' => $data, 'msgerro' => $msgErro, 'labelbar' => $labels);
            echo json_encode($retorno);
            break;

        case 'situ'://Caso seja pela situação
            $sql = "SELECT situacaoouvidoriaatendimento.ov18_descricao, Count(*) FROM ouvidoriaatendimento
                    $inner
                    where ov01_instit = 1 and ov01_depart = {$_SESSION['DB_coddepto']} and ov01_situacaoouvidoriaatendimento = 1 and ov15_sequencial is null $wheredt $wherecomp 
                    GROUP BY ov18_descricao
                    HAVING Count(*) > 0";

            $resulta = pg_query($sql);
            $und = pg_fetch_all($resulta);


            $labels = array();
            $data = array();
            $labelper = array();
            $msgErro = "";

            if (empty($und)) {
                $msgErro = 'Nenhum atendimento encontrado. Verificar as datas e/ou departamento selecionado.';
            }

            foreach ($und as $i) {
                array_push($labels, $i['ov18_descricao']);
                array_push($data, $i['count']);
            }
            
            //Configuração para a exibição da porcentagem
            foreach($data as $i){//Econtrando o valor total de atendimentos
                $totalAtend += $i;
            }
  
            for($I = 0; $I < count($data); $I++ ){//Laço para inclusão da porcentagem na label do gráfico
                $percentVal = (100*$data[$I])/$totalAtend;//Calculo da porcentagem que corresponde o valor
                array_push($dataPercent, $percentVal);//Iteração do valor em um array
                array_push($labelper, $labels[$I].' - '.number_format($percentVal, 2).'%');//formatação do valor na inserção do mesmo em um array
            }
            
            $retorno = array('label' => $labelper, 'data' => $data, 'msgerro' => $msgErro, 'labelbar' => $labels);
            echo json_encode($retorno);
            break;
            
        case 'complex'://Caso seja pelo grau de complexibilidade
            $sql = "SELECT situacaoouvidoriaatendimento.ov18_descricao, Count(*) FROM ouvidoriaatendimento
                    $inner
                    where ov01_instit = 1 and ov01_depart = {$_SESSION['DB_coddepto']} and ov01_situacaoouvidoriaatendimento = 1 and ov15_sequencial is null $wheredt $wherecomp 
                    GROUP BY ov18_descricao
                    HAVING Count(*) > 0";

            $resulta = pg_query($sql);
            $und = pg_fetch_all($resulta);

            $labels = array();
            $data = array();
            $labelper = array();
            $msgErro = "";

            if (empty($und)) {
                $msgErro = 'Nenhum atendimento encontrado. Verificar as datas e/ou departamento selecionado.';
            }

            foreach ($und as $i) {
                array_push($labels, $i['ov18_descricao']);
                array_push($data, $i['count']);
            }
            
            //Configuração para a exibição da porcentagem
            foreach($data as $i){//Econtrando o valor total de atendimentos
                $totalAtend += $i;
            }
  
            for($I = 0; $I < count($data); $I++ ){//Laço para inclusão da porcentagem na label do gráfico
                $percentVal = (100*$data[$I])/$totalAtend;//Calculo da porcentagem que corresponde o valor
                array_push($dataPercent, $percentVal);//Iteração do valor em um array
                array_push($labelper, $labels[$I].' - '.number_format($percentVal, 2).'%');//formatação do valor na inserção do mesmo em um array
            }

            $retorno = array('label' => $labelper, 'data' => $data, 'msgerro' => $msgErro, 'labelbar' => $labels);
            echo json_encode($retorno);
            break;
        default :
            $msgErro = 'Gráfico não processado.';
    }//Fim Switch    
} else {//Erro / Fim carregadados
    $msgErro = 'Médotodo carregar dados não está funcionando.';
}


