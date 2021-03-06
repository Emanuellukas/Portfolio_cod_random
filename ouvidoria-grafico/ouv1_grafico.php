<html>
<head> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.min.js"></script>
    <script type="text/javascript" src="scripts/jquery-2.1.1.min.js"></script>
    
<?
//@ Autoria de Lucas Emanuel da Silva Nunes - @Emanuellukas (gitHub)
require_once("std/db_stdClass.php");
require_once("libs/db_stdlib.php");
require_once("libs/db_conecta.php");
require_once("libs/db_sessoes.php");
require_once("libs/db_utils.php");
require_once("libs/db_app.utils.php");
require_once("libs/db_usuariosonline.php");
require_once("libs/JSON.php");
require_once("dbforms/db_funcoes.php");
require_once("dbforms/db_classesgenericas.php");

$date = date('d-m-y', db_getsession('DB_datausu'));

?>
<title>Relat�rio de Atendimentos - <?echo $date;?></title>
<style>
    h3{
        text-align: left; 
        font-family: serif;
        font-size: 110%;
    
    }
    .pie{
        padding-top: 40px; 
        width: 50%; 
        padding-left: 15%;
        padding-right: 15%;
        padding-bottom: 25px;
    }
    .bar{
        width: 75%;
        padding-top: 20px;
    }
</style>
</head>
<?
//Switch para exibi��o da Complexidade escolhida
$complexGraf = $_GET['complex'];
switch ($complexGraf){
    case 11:
        $complexGraf = 'Baixa';
        break;
    case 5:
        $complexGraf = 'M�dia';
        break;
    case 12:
        $complexGraf = 'Alta';
        break;
    default:
        $complexGraf = '';
}

//Switch para exibi��o do Totalizador escolhido
$totalizador = $_GET['tpTotalizadores'];
switch($totalizador){
    case 'tipoproc':
        $totalizador = 'Tipo de Processo';
        break;
    case 'formrec':
        $totalizador = 'Foma de reclama��o';
        break;
    case 'situ':
        $totalizador = 'Situa��o';
        break;
    default:
        $totalizador = 'Totalizador n�o resgatado com sucesso';
}

$dtini = $_GET['dtIni'];
$dtfim = $_GET['dtFim'];

if($dtini != '' && $dtfim != ''){
    $exibe = $dtini ." a ". $dtfim;
}else{
    $exibe = "";
}

?>
<body style="margin: 0 auto;">   
    <div class="col2" style="">
        <fieldset id="cabecalhoPDF" style="border: 4px groove; margin-bottom: 100px; display: none;">
            <table>
                <tr>
                    <td>
                        <table style="float: left;">
                            <tr>
                                <td><h4>Governo do Estado de Rond�nia</h4></td>
                                <td class="td_direita"><h4>�rg�o: Governadoria</h4></td>
                            </tr>
                            <tr>
                                <td>Av. Farquar, 2986</td>
                            </tr>
                            <tr>
                                <td>Porto Velho</td>
                            </tr>
                            <tr>
                                <td>6932165104</td>
                            </tr>
                            <tr>
                                <td>04.564.530/0000-13</td>
                            </tr>                                    
                        </table>
                        <table style="float: right; padding-left: 30%;">
                            <tr>
                                <td>Data Escolhida: <? echo $exibe; ?></td>
                            </tr>
                            <tr>
                                <td >Unidade: <?= db_getsession('DB_nomedepto'); ?></td>
                            </tr>
                            <tr>
                                <td>Usu�rio: <?= db_getsession('DB_login'); ?></td>
                            </tr>
                            <tr>
                                <td>Complexidade: <? echo $complexGraf;?></td>
                            </tr>
                            <tr>
                                <td> Totalizador: <? echo $totalizador;?></td>
                            </tr>
                        </table>
                        </td>
                    </tr>
                </table>
            </fieldset>
        <div class="pie">
            <!-- Configurando os Gr�ficos -->
            <canvas class="pie-chart">

            </canvas>
        </div>
        <hr style="width: 75%; color: #c0bcbc;"/>
        <div class="bar">
            <canvas class="bar-chart">

            </canvas>
        </div>
    </div>

<h5 class="sumir">*Esta organiza��o n�o corresponde � impress�o original*</h5>
<input style="margin-left: 45%;" type="button" id="imprimirGraf" name="imprimirGraf" class="sumir" value="Imprimir" onclick="js_imprimirGraf()">
<script>
                
        ajaxGrafico();
        
        //Fun��o que transformar� os dados em JSON
        function ajaxGrafico() { 
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {//Verificando erros
                    console.log(JSON.parse(this.responseText));   
                    js_processaGrafico(JSON.parse(this.responseText));//Convertendo responseText para String pelo JSON
                }
            };
            //Passagem de par�metros
            parametros = 'method=carregaDadosGraf';
            parametros += '&tpTotalizadores=<? echo $_GET['tpTotalizadores']; ?>';
            parametros += '&dtIni=<? echo $_GET['dtIni'];?>';
            parametros += '&dtFim=<? echo $_GET['dtFim'];?>';
            parametros += '&complex=<? echo $_GET['complex'];?>';
            
            //Chamada Ajax utilizando m�todo POST
            xhttp.open("POST", "ajaxgrafico.php", true);//Chamando arquivo atrav�s do m�todo POST
            xhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded;charset=iso-8859-1');
            xhttp.send(parametros);
        }

        //Gera cores aleatorias tipo RGBA
        function getRandomColorRgba() {
            var cor = "rgba("
                    + Math.floor(Math.random() * 255) + ","
                    + Math.floor(Math.random() * 255) + ","
                    + Math.floor(Math.random() * 255) + ","
                    + "0.70)";
            return cor;
        }
        
        function insertPercentLabel(){
            
        }

        function js_processaGrafico(dataG) {//Fun��o que processa os dados usados no gr�fico e toda a sua constru��o
            if(dataG.msgerro != ''){//Controle de erro. Retornar� a mensagem armazenada em ajaxgrafico.php
                alert(dataG.msgerro);
            }

            var arrayCor = [];
            var total = dataG.label.length;
            var i = 0;
            
            while(i < total){//Condi��o para que seja preenchida cores randomicas a partir de cada registro de label
                arrayCor.push(getRandomColorRgba());
                i++;
            }

            var ctxbar = document.getElementsByClassName("bar-chart");
            var ctxpie = document.getElementsByClassName("pie-chart");

            var data = {
                    labels: dataG.label,
                    datasets: [{
                            label: "Gr�fico de Atendimentos",
                            data: dataG.data,
                            borderWidth: 4,
                            borderColor: 'rgba(255,255,253,0.85)',
                            backgroundColor: arrayCor,
                            scaleStartValue: 0
                        }]
                };
            var databar = {
                    labels: dataG.labelbar,
                    datasets: [{
                            label: "Gr�fico de Atendimentos",
                            data: dataG.data,
                            borderWidth: 4,
                            borderColor: 'rgba(255,255,253,0.85)',
                            backgroundColor: arrayCor,
                            scaleStartValue: 0
                        }]
                };
            //Inst�ncia e configura���es da apar�ncia dos gr�ficos
            //Tipo, Dados, Op��es
            new Chart(ctxpie, {
                type: 'pie',
                data: data         
            });
            
            new Chart(ctxbar, {
                type: 'bar',
                data: databar,
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero:true
                            }
                        }]
                    }
                }
            });
        }
        
        function js_imprimirGraf(text){
            $(".sumir").hide();
            $("#cabecalhoPDF").show();
            document.text;
            window.print(); 
        }
        
    </script>
</body>
</html>
