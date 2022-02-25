<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta PHP y MySQL</title>
</head>

<body>

    <?php
    $conexion = mysqli_connect("localhost", "root", "root", "mqtt");

    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    $sql = "SELECT DISTINCT topic FROM tabla WHERE 1";

    $resultado = mysqli_query($conexion, $sql);

    ?>

    <form action="respuesta.php" method="POST">
        <select name="topic">
            <?php
            while ($row = mysqli_fetch_array($resultado)) {
                echo "<option>";
                echo $row[0];
                echo "</option>";
            }
            mysqli_close($conexion);
            ?>
        </select><br>
        <input type="date" name="fecha"> <br>
        <input type="submit" name="enviar">
    </form>

</body>

</html>