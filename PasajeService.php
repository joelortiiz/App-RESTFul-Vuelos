<?php

require_once ('./BD/BaseDatos.php');
require_once ('./models/VuelosModel.php');

$pasaje = new PasajesModel();

@header("Content-type: application/json");

// GET
// Si se recibe un parámetro por get, solicitamos un vuelo concreto
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['id'])) {
        $res = $pasaje->getUnPasaje($_GET['id']);
        echo json_encode($res);
        exit();
    } else {
        $res = $pasaje->getAll();
        echo json_encode($res);
        exit();
    }
}

// Borrar DELETE
if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    $id = $_GET['id'];
    $res = $pasaje->borrar($id);
    $resul['resultado'] = $res;
    echo json_encode($resul);
    exit();
}

// Crear un nuevo reg POST
// Los campos del array que venga se deberán llamar como los campos de la tabla Departamentos
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // se cargan toda la entrada que venga en php://input
    $post = json_decode(file_get_contents('php://input'), true);
    $res = $pasaje->guardar($post);
    $resul['resultado'] = $res;
    echo json_encode($resul);
    exit();
}

// Actualizar PUT, se reciben los datoc como en el put
// Los campos del array que venga se deberán llamar como los campos de la tabla Departamentos
if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    if (isset($_GET['id'])) {
        $put = json_decode(file_get_contents('php://input'), true);
        $res = $pasaje->actualiza($put, $_GET['id']);
        $resul['mensaje'] = $res;
        echo json_encode($resul);
        exit();
    }
}

// En caso de que ninguna de las opciones anteriores se haya ejecutado
header("HTTP/1.1 400 Bad Request");