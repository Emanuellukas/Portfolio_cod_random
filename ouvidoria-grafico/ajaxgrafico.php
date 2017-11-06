<?php
//Desenvolvido por @Emanuellukas com auxi√≠lio de programadores da empresa
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
<<<<<<< HEAD
$complexcod = $_POST['complex'];
$complexdescr = $_POST['complexdescr'];
=======
$complexcod = $_POST['complexcod'];

function exibirErro(){
    if (empty($und)) {
        $msgErro = 'Nenhum atendimento encontrado para as datas selecionadas.';
    }
}
>>>>>>> 0c09a50f2fc06b45249b07e294c66b0e831cbd2d

//var_dump($_SESSION);exit;
$dtIni = implode('-', array_reverse(explode('/', $dtini)));
$dtFim = implode('-', array_reverse(explode('/', $dtfim)));

if (!empty($dtIni) && !empty($dtFim)) {//Condi√ß√£o para pesquisa usando data
    $wheredt = "and *sigilo* between '$dtIni' and '$dtFim'";
}

<<<<<<< HEAD
if (!empty($complexcod)) {//CondiÁ„o para pesquisa usando complexibilidade
    $wherecomp = "and ov01_prioridade = $complexcod";
}
//Innerjoins padrıes para o funcionamento da pesquisa correta
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

if ($method == 'carregaDadosGraf') {//Verifica o mÈtodo
    switch ($escolha) {//Switch para identificar o radio button marcado no formul·rio
=======
if (!empty($complexcod)) {//Condi√ß√£o para pesquisa usando
    $wherecomp = "and *sigilo* = $complexcod";
}
//Innerjoins padr√µes para o funcionamento da pesquisa correta
$inner = " *PARTE SIGILOSA DO CODIGO*";

if ($method == 'carregaDadosGraf') {//Verifica o m√©todo
    switch ($escolha) {//Switch para identificar o radio button marcado no formul√°rio
>>>>>>> 0c09a50f2fc06b45249b07e294c66b0e831cbd2d
        case 'tipoproc'://Caso seja Tipo de Processo            
            $sql = "SELECT p51_descr, Count(*) FROM *sigilo*
                   $inner 
<<<<<<< HEAD
                    where ov01_instit = 1 and ov01_depart = {$_SESSION['DB_coddepto']} and ov01_situacaoouvidoriaatendimento = 1 and ov15_sequencial is null $wheredt $wherecomp 
=======
                    where ov01_instit = 1 and ov01_depart = 3 and *sigilo* = 1 and ov15_sequencial is null $wheredt $wherecomp 
>>>>>>> 0c09a50f2fc06b45249b07e294c66b0e831cbd2d
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

<<<<<<< HEAD
            if (empty($und)) {//Tratamento de erro caso nada seja retornado na vari·vel $und
                $msgErro = 'Nenhum atendimento encontrado. Verificar as datas e/ou departamento.';
            }
            
            foreach ($und as $i) {//IteraÁ„o dos dados buscados pela query dentro de um array (Label e Data)
                array_push($labels, $i['p51_descr']);
                array_push($data, $i['count']);
            }
            
            //ConfiguraÁ„o para a exibiÁ„o da porcentagem
            foreach($data as $i){//Econtrando o valor total de atendimentos
                $totalAtend += $i;
            }
  
            for($I = 0; $I < count($data); $I++ ){//LaÁo para inclus„o da porcentagem na label do gr·fico
                $percentVal = (100*$data[$I])/$totalAtend;//Calculo da porcentagem que corresponde o valor
                array_push($dataPercent, $percentVal);//IteraÁ„o do valor em um array
                array_push($labelper, $labels[$I].' - '.number_format($percentVal, 2).'%');//formataÁ„o do valor na inserÁ„o do mesmo em um array
            }
            
            //valores atribuÌdos ao Json (responseText) enviado para ouv1_grafico.php
            $retorno = array('label' => $labelper, 'data' => $data, 'msgerro' => $msgErro, 'labelbar' => $labels);
            echo json_encode($retorno);
            break;

        case 'formrec'://Caso seja Forma de reclamÁ„o
            $sql = "SELECT ov01_formareclamacao, Count(*) FROM ouvidoriaatendimento
                    $inner
                    where ov01_instit = 1 and ov01_depart = {$_SESSION['DB_coddepto']} and ov01_situacaoouvidoriaatendimento = 1 and ov15_sequencial is null $wheredt $wherecomp 
=======
            exibirErro()

            foreach ($und as $i) {//Itera√ß√£o dos dados buscados pela query e dentro de um array (Label e Data)
                array_push($labels, $i['p51_descr']);
                array_push($data, $i['count']);
            }

            //valores atribu√≠dos ao Json (responseText) enviado para ouv1_grafico.php
            $retorno = array('label' => $labels, 'data' => $data, 'msgerro' => $msgErro);
            echo json_encode($retorno);
            break;

        //Caso seja Forma de reclam√ß√£o   
        case 'formrec':
            $sql = "SELECT ov01_formareclamacao, Count(*) FROM *sigilo*
                    $inner
                    where ov01_instit = 1 and ov01_depart = 3 and *sigilo* = 1 and ov15_sequencial is null $wheredt $wherecomp 
>>>>>>> 0c09a50f2fc06b45249b07e294c66b0e831cbd2d
                    GROUP BY ov01_formareclamacao
                    HAVING Count(*) > 0";

            $resulta = pg_query($sql);
            $und = pg_fetch_all($resulta);

            $labels = array();
            $data = array();
            $labelper = array();
            $msgErro = "";

<<<<<<< HEAD
            if (empty($und)) {
                $msgErro = 'Nenhum atendimento encontrado. Verificar as datas e/ou departamento.';
            }
            
=======
            exibirErro();

>>>>>>> 0c09a50f2fc06b45249b07e294c66b0e831cbd2d
            foreach ($und as $i) {
                array_push($data, $i['count']);

                //Switch para alterar o que vem em n√∫mero do banco para String, facilita identifica√ß√£o
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
<<<<<<< HEAD
                    default : "Forma de reclamaÁ„o n„o identificada";
=======
                    default : "Forma n√£o identificada";
>>>>>>> 0c09a50f2fc06b45249b07e294c66b0e831cbd2d
                }
            }
            
            //ConfiguraÁ„o para a exibiÁ„o da porcentagem
            foreach($data as $i){//Econtrando o valor total de atendimentos
                $totalAtend += $i;
            }
  
            for($I = 0; $I < count($data); $I++ ){//LaÁo para inclus„o da porcentagem na label do gr·fico
                $percentVal = (100*$data[$I])/$totalAtend;//Calculo da porcentagem que corresponde o valor
                array_push($dataPercent, $percentVal);//IteraÁ„o do valor em um array
                array_push($labelper, $labels[$I].' - '.number_format($percentVal, 2).'%');//formataÁ„o do valor na inserÁ„o do mesmo em um array
            }

<<<<<<< HEAD
            $retorno = array('label' => $labelper, 'data' => $data, 'msgerro' => $msgErro, 'labelbar' => $labels);
=======
            $retorno = array('label' => $labels, 'data' => $data, 'msgerro' => $msgErro);
>>>>>>> 0c09a50f2fc06b45249b07e294c66b0e831cbd2d
            echo json_encode($retorno);
            break;

        case 'situ'://Caso seja pela situa√ß√£o
            $sql = "SELECT situacaoouvidoriaatendimento.ov18_descricao, Count(*) FROM *sigilo*
                    $inner
<<<<<<< HEAD
                    where ov01_instit = 1 and ov01_depart = {$_SESSION['DB_coddepto']} and ov01_situacaoouvidoriaatendimento = 1 and ov15_sequencial is null $wheredt $wherecomp 
=======
                    where ov01_instit = 1 and ov01_depart = 3 and *sigilo* = 1 and ov15_sequencial is null  
>>>>>>> 0c09a50f2fc06b45249b07e294c66b0e831cbd2d
                    GROUP BY ov18_descricao
                    HAVING Count(*) > 0";

            $resulta = pg_query($sql);
            $und = pg_fetch_all($resulta);


            $labels = array();
            $data = array();
            $labelper = array();
            $msgErro = "";

<<<<<<< HEAD
            if (empty($und)) {
                $msgErro = 'Nenhum atendimento encontrado. Verificar as datas e/ou departamento selecionado.';
            }
=======
            exibirErro()
>>>>>>> 0c09a50f2fc06b45249b07e294c66b0e831cbd2d

            foreach ($und as $i) {
                array_push($labels, $i['ov18_descricao']);
                array_push($data, $i['count']);
            }
            
            //ConfiguraÁ„o para a exibiÁ„o da porcentagem
            foreach($data as $i){//Econtrando o valor total de atendimentos
                $totalAtend += $i;
            }
  
            for($I = 0; $I < count($data); $I++ ){//LaÁo para inclus„o da porcentagem na label do gr·fico
                $percentVal = (100*$data[$I])/$totalAtend;//Calculo da porcentagem que corresponde o valor
                array_push($dataPercent, $percentVal);//IteraÁ„o do valor em um array
                array_push($labelper, $labels[$I].' - '.number_format($percentVal, 2).'%');//formataÁ„o do valor na inserÁ„o do mesmo em um array
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
            
            //ConfiguraÁ„o para a exibiÁ„o da porcentagem
            foreach($data as $i){//Econtrando o valor total de atendimentos
                $totalAtend += $i;
            }
  
            for($I = 0; $I < count($data); $I++ ){//LaÁo para inclus„o da porcentagem na label do gr·fico
                $percentVal = (100*$data[$I])/$totalAtend;//Calculo da porcentagem que corresponde o valor
                array_push($dataPercent, $percentVal);//IteraÁ„o do valor em um array
                array_push($labelper, $labels[$I].' - '.number_format($percentVal, 2).'%');//formataÁ„o do valor na inserÁ„o do mesmo em um array
            }

            $retorno = array('label' => $labelper, 'data' => $data, 'msgerro' => $msgErro, 'labelbar' => $labels);
            echo json_encode($retorno);
            break;
        default :
            $msgErro = 'Gr√°fico n√£o processado.';
    }//Fim Switch    
} else {//Erro / Fim carregadados
    $msgErro = 'M√©dotodo carregar dados n√£o est√° funcionando.';
}


