<?php

if (file_exists("datos.txt")) {
    $jsonClientes = file_get_contents("datos.txt"); //obtiene y lee el contenido del archivo datos.txt, que está en json
    $aClientes = json_decode($jsonClientes, true);  //decodificamos el json, recibe el contenido y lo transforma en array. 
} else {
    $aClientes = [];                    //sino existe el archivo setea la variable en 0
}

$id = isset($_GET["id"]) ? $_GET["id"] : "";  //pregunta si existe la variable id
$msj = array("mensaje" => "", "codigo" => "");

if(isset($_GET["do"]) && $_GET["do"] == "eliminar"){   //Eliminar
    // print_r("Voy a eliminar el cliente $id");
     if ($aClientes[$id]["imagen"] != ""){
         unlink("files/".$aClientes[$id]["imagen"]);
     }
     unset($aClientes[$id]);    
     $jsonClientes = json_encode($aClientes);  //recibe el array aClientes y lo pasa a json
     file_put_contents("datos.txt", $jsonClientes);  //una vez tenemos el json queremos guardarlo en el archivo datos.txt
     $msg = array("mensaje" => "Cliente eliminado correctamente", "codigo" =>"danger");
     $id="";
     
}

if ($_POST) {
    $dni = trim($_POST["txtDni"]);                          //postback
    $nombre = trim($_POST["txtNombre"]);
    $telefono = trim($_POST["txtTelefono"]);
    $correo = trim($_POST["txtCorreo"]);
    $nombreImagen = "";

    if($_FILES["archivo"]["error"] === UPLOAD_ERR_OK) {    //subir una imagen
        $nombreRandom = date("Ymdhmsi");  //20200317124235
        $archivo_tmp = $_FILES["archivo"]["tmp_name"];
        $nombreArchivo = $_FILES["archivo"]["name"];
        $extension = pathinfo($nombreArchivo, PATHINFO_EXTENSION);
        $nombreImagen = "$nombreRandom.$extension";
        move_uploaded_file($archivo_tmp,"files/$nombreImagen");
    }

    if (isset($_GET["id"]) && isset($_GET["id"]) >= 0) {

         if($_FILES["archivo"]["error"] === UPLOAD_ERR_OK) {      //SI CARGA UNA NUEVA IMAGEN, 
             if ($aClientes[$id]["imagen"] != ""){                 // Y SI ESE CLIENTE TENIA UNA IMAGEN
             unlink("files/".$aClientes[$id]["imagen"]);       //ELIMINA LA IMAGEN ANTERIOR
             }
         }

        //Actualización
        $aClientes[$id] = array(  //sobreescribe la posicion del id
            "dni" => $dni,
            "nombre" => $nombre,
            "telefono" => $telefono,
            "correo" => $correo,
            "imagen" =>$nombreImagen);   
            $msj = array("mensaje" => "Cliente actualizado", "codigo" => "success");          
           
    } else {
        //Insertar
        $aClientes[] = array(   //crea una posicion nueva en un nuevo id
            "dni" => $dni,
            "nombre" => $nombre,
            "telefono" => $telefono,
            "correo" => $correo,
            "imagen" =>$nombreImagen); 
            $msg = array("mensaje"=>"Cliente ingresado satisfactoriamente", "codigo" => "primary");                       
    }

    $jsonClientes = json_encode($aClientes);  //recibe un array y lo pasa a json
    file_put_contents("datos.txt", $jsonClientes);  //una vez tenemos el json queremos guardarlo en un archivo datos.txt
}

// $id = $_GET["id"];
// print_r($aClientes[$id]);


//a través de GET podemos enviar datos que se verán en la query string (en la url).
//eliminar querystring index.php?id=0&do=eliminar , para id=0
//request lee tanto GET como POST.
?>


<!doctype html>
<html lang="es">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap and Fontawesome CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.0-2/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.0-2/css/fontawesome.min.css">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:100,200,300,400,500,600,700,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <title>ABM Clientes</title>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-12 text-center py-5">
                <h1>Registro de Clientes</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-12">
               
                <form action="" method="POST" enctype="multipart/form-data">
                <?php if(isset($msg["mensaje"]) && $msg["mensaje"] != ""): ?>
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-<?php echo $msg["codigo"]; ?>" role="alert">
                                 <?php echo $msg["mensaje"];?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <div class="row">
                        <div class="col-12 form-group">
                            <label for="txtDni">Dni:</label>
                            <input type="text" class="form-control" id="txtDni" name="txtDni" required value="<?php echo isset($aClientes[$id]) ? $aClientes[$id]["dni"] : ""; ?>">
                        </div>
                        <div class="col-12 form-group">
                            <label for="txtNombre">Nombre:</label>
                            <input type="text" class="form-control" id="txtNombre" name="txtNombre" required value="<?php echo isset($aClientes[$id]) ? $aClientes[$id]["nombre"] : ""; ?>">
                        </div>
                        <div class="col-12 form-group">
                            <label for="txtTelefono">Teléfono:</label>
                            <input type="text" class="form-control" id="txtTelefono" name="txtTelefono" required value="<?php echo isset($aClientes[$id]) ? $aClientes[$id]["telefono"] : ""; ?>">
                        </div>
                        <div class="col-12 form-group">
                            <label for="txtCorreo">Correo:</label>
                            <input type="text" class="form-control" id="txtCorreo" name="txtCorreo" required value="<?php echo isset($aClientes[$id]) ? $aClientes[$id]["correo"] : ""; ?>">
                        </div>
                        <div class="col-12 form-group">
                            <label for="archivo">Archivo adjunto: </label>
                            <input type="file" name="archivo" class="form-control-file" id="archivo">
                        </div>
                        <div class="col-12 form-group">
                            <button type="submit" class="btn btn-primary" name="btnGuardar">Guardar</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-md-6 col-12">
                <table class="table table-hover border">
                    <tr>
                        <th>Imagen</th>
                        <th>DNI</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Acciones</th>
                    </tr>
                    <?php foreach ($aClientes as $id => $cliente) : ?>

                        <tr>
                            <td> <img src="files/<?php echo $cliente["imagen"];?>" class="img-thumbnail"></td>
                            <td><?php echo $cliente["dni"]; ?></td>

                            <td><?php echo $cliente["nombre"]; ?></td>

                            <td><?php echo $cliente["correo"]; ?></td>

                            <td style="width: 110px;">
                                <a href="index.php?id=<?php echo $id; ?>"><i class="fas fa-edit"></i></a>
                                <a href="index.php?id=<?php echo $id; ?>&do=eliminar"><i class="fas fa-trash-alt"></i></a>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                </table>
            </div>
            <div class="row">
                <div class="col-12">
                    <a href="index.php"><i class="fas fa-plus"></i></a>
                </div>
            </div>

        </div>

    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
</body>
</html>