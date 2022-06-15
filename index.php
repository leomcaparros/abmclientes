<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (file_exists("archivo.txt")) {
    //Si el archivo existe, cargar los clientes en la variable aClientes
    $strJson = file_get_contents ("archivo.txt");
    $aClientes = json_decode($strJson, true);
} else {
    //Si el archivo no existe, es porque no hay clientes
    $aClientes = array ();
}

if(isset($_GET["id"])){
    $id = $_GET["id"];
} else {
    $id="";
}

if (isset($_GET ["do"]) && $_GET ["do"] == "eliminar") {
    unset ($aClientes[$id]);

    $strJson = json_encode($aClientes);
    file_put_contents("archivo.txt", $strJson);

    header("Location: index.php");
}
if($_POST){

    $nombre = $_POST["txtNombre"];
    $dni = $_POST["txtDni"];
    $telefono = $_POST["txtTelefono"];
    $correo = $_POST["txtCorreo"];
    $nombreImagen = "";

    if ($_FILES["archivo"]["error"] === UPLOAD_ERR_OK) {

        
        if(isset($aClientes[$id]["imagen"]) && $aClientes[$id]["imagen"] != ""){
        // si viene una imagen nueva, eliminar la anterior

            if(file_exists("imagenes/" . $aClientes[$id]["imagen"])){
                unlink("imagenes/" . $aClientes[$id]["imagen"]);
            }
        }
        $nombreAleatorio = date("Ymdhmsi") . rand(1000, 2000); //202205171842371010
        $archivo_tmp = $_FILES["archivo"]["tmp_name"]; //C:\tmp\ghjuy6788765
        $extension = pathinfo($_FILES["archivo"]["name"], PATHINFO_EXTENSION);
        if($extension == "jpg" || $extension == "png" || $extension == "jpeg"){
            $nombreImagen = "$nombreAleatorio.$extension";
            move_uploaded_file($archivo_tmp, "imagenes/$nombreImagen");
        }
} else {
    //Sino imagen es vacio
    if($id >= 0){
        $nombreImagen = $aClientes[$id]["imagen"];
    } else {
        $nombreImagen = "";
    }
}

if ($id >= 0) {
// Estoy editando
$aClientes[$id]= array ("nombre" => $nombre, 
                    "dni" => $dni,
                    "telefono" => $telefono,
                    "correo" => $correo,
                    "imagen" => $nombreImagen
);
} else {
// Estoy insertando un nuevo cliente
$aClientes[]= array ("nombre" => $nombre, 
                    "dni" => $dni,
                    "telefono" => $telefono,
                    "correo" => $correo,
                    "imagen" => $nombreImagen
);
}

// Convertir el array de clientes en Json
$strJson = json_encode($aClientes);

//Almacenar en un archivo.txt el Json con file_put_contents
file_put_contents("archivo.txt", $strJson);

}


?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABM Clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="css/Fontawesome/css/all.min.css">
    <link rel="stylesheet" href="css/Fontawesome/css/fontawesome.min.css">
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-12 text-center py-5">
                <h1>Registro de clientes</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-3 offset-1 me-5">
                <form action="" method="POST" enctype="multipart/form-data">
                    
                    <label for="txtDni">DNI: *</label>
                    <input type="text" name="txtDni" id="txtDni" class="form-control" required value="<?php echo isset($aClientes[$id])? $aClientes[$id]["dni"] : ""; ?>">

                    <label for="txtNombre"> Nombre: *</label>
                    <input type="text" name="txtNombre" class="form-control my-2" required value="<?php echo isset($aClientes[$id])? $aClientes[$id]["nombre"] : ""; ?>">

                    <label for="txtTelefono">Telefono:</label>
                    <input type="tel" name="txtTelefono" class="form-control my-2" value="<?php echo isset($aClientes[$id])? $aClientes[$id]["telefono"] : ""; ?>">

                    <label for="txtCorreo">Correo: *</label>
                    <input type="email" name="txtCorreo" class="form-control my-2" required value="<?php echo isset($aClientes[$id])? $aClientes[$id]["correo"] : ""; ?>">
                    <label for="">Archivo adjunto</label>
                    <input type="file" name="archivo" id="archivo" accept=".jpg, .jpeg, .png">
                    <small class="d-block">Archivos admitidos: .jpg, .jpeg, .png</small>
                    <button type="submit" name="btnGuardar" class="btn bg-primary text-white">GUARDAR</button>
                    <a href="index.php" class="btn btn-danger my-2">NUEVO</a>
                </form>
            </div>
            <div class="col-6 ms-5">
                <table class="table table-hover shadow border">
                    <tr>
                        <th>Imagen:</th>
                        <th>DNI:</th>
                        <th>Nombre:</th>
                        <th>Telefono:</th>
                        <th>Correo:</th>
                        <th>Acciones:</th>
                    </tr>
                        <?php

                        foreach($aClientes as $pos => $cliente): ?> 
                        <tr>
                            <td><img src="imagenes/<?php echo $cliente["imagen"]; ?>" class="img-thumbnail"></td>
                            <td><?php echo $cliente["dni"]; ?></td>
                            <td><?php echo $cliente["nombre"]; ?></td>
                            <td><?php echo $cliente["telefono"]; ?></td>
                            <td><?php echo $cliente["correo"]; ?></td>
                            <td>
                            <a href="?id=<?php echo $pos; ?>"><i class="fa-solid fa-pen-to-square"></a></i>
                            <a href="?id=<?php echo $pos; ?>&do=eliminar"><i class="fa-solid fa-trash-can"></a></i>
                            </td>
                        </tr>
                        <?php endforeach; ?>                 
                </table>
            </div>
        </div>
    </div>
</body>

</html>
