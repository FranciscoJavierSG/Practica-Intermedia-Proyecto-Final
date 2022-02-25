<?php
$conexion = mysqli_connect("localhost", "root", "root", "mqtt");

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
}

function temperatura_diaria($anno, $mes, $dia) {
    $sql = "SELECT UNIX_TIMESTAMP(fecha), mensaje FROM tabla WHERE year(fecha) = $anno AND month(fecha) = $mes AND day(fecha) = $dia AND topic = 'Datco/Argentina/Oficina_2/Temperatura' ORDER BY fecha";
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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plataforma de monitoreo de MQTT</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <link rel="stylesheet" href="../../../css/style.css">
    <link rel="stylesheet" href="../../../css/charts.css">
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-light" style="background-image: linear-gradient(to right, #0055A0,#00B0EE); background: rgba('', '', '', 0.6);">
        <div class="container-fluid">
            <a class="navbar-brand img-fluid" href="../../index.html"><img src="../../../jpg/datco_logo_bacan2.png" alt="Grupo Datco Logo" style="width:120px; margin: 0; padding: 0;"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle active" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Temperatura
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="../chile/temp_chile.php">Chile</a></li>
                            <li><a class="dropdown-item" href="temp_argentina.php">Argentina</a></li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Relé
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="../../rele/chile/rele_chile.html">Chile</a></li>
                            <li><a class="dropdown-item" href="../../rele/argentina/rele_arg.html">Argentina</a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="http://190.110.108.59:18083/#/login">Admin Dashboard</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="https://www.grupodatco.com/">Grupo Datco</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div style="margin:10px; outline: 4px solid #000; box-shadow: 5px 10px 8px 5px #000;">
        <div id="grafico" style="width: 100%; height: 400px;"></div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>


</body>

</html>

<script>
    $(function() {
        $('#grafico').highcharts({
            chart: {
                type: 'line',
                zoomType: 'x'
            },
            colors: ['#337ab7', '#cc3c1a'],
            title: {
                text: 'Temperaturas en la oficina 2 de Argentina'
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