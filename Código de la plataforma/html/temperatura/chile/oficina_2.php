<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plataforma de monitoreo de MQTT</title>
    <link rel="stylesheet" href="../../../css/charts.css">
</head>

<body>
    <?php
    $conexion = mysqli_connect("localhost", "root", "root", "mqtt");

    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    function temperatura_diaria($anno, $mes, $dia) {
        $sql = "SELECT UNIX_TIMESTAMP(fecha), mensaje FROM tabla WHERE year(fecha) = $anno AND month(fecha) = $mes AND day(fecha) = $dia AND topic = 'Datco/Chile/Oficina_2/Temperatura' ORDER BY fecha";
        # Cosas importantes: 
        # 1. UNIX_TIMESTAMP se usa ya que con eso trabaja Highcharts (la página de dónde se sacó el script del gráfico)
        # 2. El ORDER BY fecha es importante ya que sin él, se desordenan los datos.
        global $conexion;

        $resultado = mysqli_query($conexion, $sql);

        while ($row = mysqli_fetch_array($resultado)) {
            echo "[";
            echo ($row[0] * 1000) - 10800000;
            echo ", ";
            echo $row[1];
            echo "],";
        }
    }
    ?>

    <div id="grafico" style="width:100%; height: 400px;"></div>

    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>

    <script>
        $(function() {
            $('#grafico').highcharts({
                chart: {
                    type: 'line',
                    zoomType: 'x'
                },
                colors: ['#337ab7', '#cc3c1a'],
                title: {
                    text: 'Temperatura'
                },

                xAxis: {
                    type: 'datetime',
                    title: {
                        text: 'Fecha'
                    }
                },

                yAxis: {
                    title: {
                        text: 'Temperatura'
                    }
                },

                series: [{
                    name: 'Temp',
                    data: [<?php
                            $fecha = $_POST['fecha'];
                            $anno = substr("$fecha", 0, 4);
                            $mes = substr("$fecha", 5, 2);
                            $dia = substr("$fecha", 8, 2);
                            temperatura_diaria($anno, $mes, $dia);
                            ?>]
                }, ],
            });
        });
    </script>
</body>

</html>