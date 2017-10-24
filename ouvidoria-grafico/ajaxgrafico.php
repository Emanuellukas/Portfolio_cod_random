<?php
//Desenvolvido por @Emanuellukas com auxiílio de programadores da empresa
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
$complexcod = $_POST['complexcod'];

function exibirErro(){
    if (empty($und)) {
        $msgErro = 'Nenhum atendimento encontrado para as datas selecionadas.';
    }
}

$dtIni = implode('-', array_reverse(explode('/', $dtini)));
$dtFim = implode('-', array_reverse(explode('/', $dtfim)));

if (!empty($dtIni) && !empty($dtFim)) {//Condição para pesquisa usando data
    $wheredt = "and *sigilo* between '$dtIni' and '$dtFim'";
}

if (!empty($complexcod)) {//Condição para pesquisa usando
    $wherecomp = "and *sigilo* = $complexcod";
}
//Innerjoins padrões para o funcionamento da pesquisa correta
$inner = " *PARTE SIGILOSA DO CODIGO*";

if ($method == 'carregaDadosGraf') {//Verifica o método
    switch ($escolha) {//Switch para identificar o radio button marcado no formulário
        case 'tipoproc'://Caso seja Tipo de Processo            
            $sql = "SELECT p51_descr, Count(*) FROM *sigilo*
                   $inner 
                    where ov01_instit = 1 and ov01_depart = 3 and *sigilo* = 1 and ov15_sequencial is null $wheredt $wherecomp 
                    GROUP BY p51_descr
                    HAVING Count(*) > 0";
            //var_dump($sql);exit;
            $resulta = pg_query($sql); //executa a query
            $und = pg_fetch_all($resulta); //pega todos os dados encontrados e joga dentro de $und

            $labels = array();
            $data = array();
            $msgErro = "";

            exibirErro()

            foreach ($und as $i) {//Iteração dos dados buscados pela query e dentro de um array (Label e Data)
                array_push($labels, $i['p51_descr']);
                array_push($data, $i['count']);
            }

            //valores atribuídos ao Json (responseText) enviado para ouv1_grafico.php
            $retorno = array('label' => $labels, 'data' => $data, 'msgerro' => $msgErro);
            echo json_encode($retorno);
            break;

        //Caso seja Forma de reclamção   
        case 'formrec':
            $sql = "SELECT ov01_formareclamacao, Count(*) FROM *sigilo*
                    $inner
                    where ov01_instit = 1 and ov01_depart = 3 and *sigilo* = 1 and ov15_sequencial is null $wheredt $wherecomp 
                    GROUP BY ov01_formareclamacao
                    HAVING Count(*) > 0";

            $resulta = pg_query($sql);
            $und = pg_fetch_all($resulta);

            $labels = array();
            $data = array();
            $msgErro = "";

            exibirErro();

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
                    default : "Forma não identificada";
                }
            }

            $retorno = array('label' => $labels, 'data' => $data, 'msgerro' => $msgErro);
            echo json_encode($retorno);
            break;

        case 'situ'://Caso seja pela situação
            $sql = "SELECT situacaoouvidoriaatendimento.ov18_descricao, Count(*) FROM *sigilo*
                    $inner
                    where ov01_instit = 1 and ov01_depart = 3 and *sigilo* = 1 and ov15_sequencial is null  
                    GROUP BY ov18_descricao
                    HAVING Count(*) > 0";

            $resulta = pg_query($sql);
            $und = pg_fetch_all($resulta);


            $labels = array();
            $data = array();
            $msgErro = "";

            exibirErro()

            foreach ($und as $i) {
                array_push($labels, $i['ov18_descricao']);
                array_push($data, $i['count']);
            }

            $retorno = array('label' => $labels, 'data' => $data, 'msgerro' => $msgErro);
            echo json_encode($retorno);
            break;
        default :
            $msgErro = 'Gráfico não processado.';
    }//Fim Switch    
} else {//Erro / Fim carregadados
    $msgErro = 'Médotodo carregar dados não está funcionando.';
}


