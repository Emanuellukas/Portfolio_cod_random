<html>
<head>
    <title>TODO supply a title</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.min.js"></script>
</head>


<body style="margin: 0 auto;">
    
    <div style="width: 50%; padding-left: 25%;padding-bottom: 25px;">
        <!-- Configurando os Gráficos -->
        <canvas class="pie-chart">

        </canvas>
    </div>
    <div style="width: 50%; padding-left: 25%;">
        <canvas class="bar-chart">

        </canvas>
    </div>
    <script>
        ajaxGrafico();

        //Função que transformará os dados em JSON
        function ajaxGrafico() { 
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {//Verificando erros
                    console.log(JSON.parse(this.responseText));   
                    js_processaGrafico(JSON.parse(this.responseText));//Convertendo responseText para String pelo JSON
                }
            };
            parametros = 'method=carregaDadosGraf';
            parametros += '&tpTotalizadores=<? echo $_GET['tpTotalizadores']; ?>';
            parametros += '&dtIni=<? echo $_GET['dtIni'];?>';
            parametros += '&dtFim=<? echo $_GET['dtFim'];?>';
            parametros += '&complexcod=<? echo $_GET['complexcod'];?>';
            xhttp.open("POST", "ajaxgrafico.php", true);//Chamando arquivo através do método POST
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

        function js_processaGrafico(dataG) {//Função que processa os dados usados no gráfico e toda a sua cosntrução
            if(dataG.msgerro != ''){
                alert(dataG.msgerro);
            }
            
            
            var arrayCor = [];
            var total = dataG.label.length;
            var i = 0;
            
            while(i < total){
                arrayCor.push(getRandomColorRgba());
                i++;
            }
            
            var ctxbar = document.getElementsByClassName("bar-chart");
            var ctxpie = document.getElementsByClassName("pie-chart");

            //Instância e configuraçções da aparência dos gráficos
            //Tipo, Dados, Opções
            var barGraph = new Chart(ctxpie, {
                type: 'pie',
                data: {
                    labels: dataG.label,
                    datasets: [{
                            label: "Relatório de Atendimentos",
                            data: dataG.data,
                            borderWidth: 2,
                            borderColor: 'rgba(255,255,253,0.85)',
                            backgroundColor: arrayCor
                        }]
                }
            });
            var barGraph = new Chart(ctxbar, {
                type: 'bar',
                data: {
                    labels: dataG.label,
                    datasets: [{
                            label: "Relatório de Atendimentos",
                            data: dataG.data,
                            borderWidth: 2,
                            borderColor: 'rgba(255,255,253,0.85)',
                            backgroundColor: arrayCor
                        }]
                }
            });

        }
    </script>
</body>
</html>
