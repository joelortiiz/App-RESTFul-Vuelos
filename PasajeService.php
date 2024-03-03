<?php

require_once('./BD/BaseDatos.php');
require_once('./models/PasajesModel.php');

$pasaje = new PasajesModel();

@header("Content-type: application/json");

// GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['id'])) {
        $res = $pasaje->getUnPasaje($_GET['id']);
        echo json_encode($res);
        exit();
    } elseif (isset($_GET['identificador'])) {
        $res = $pasaje->getPasajesIde($_GET['identificador']);
        echo json_encode($res);
        exit();
    } else {
        $res = $pasaje->getAll();
        echo json_encode($res);
        exit();
    }
   
}

// DELETE
if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    $id = $_GET['id'];
    $res = $pasaje->borrar($id);
    $resul['resultado'] = $res;
    echo json_encode($resul);
    exit();
}

// POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $post = json_decode(file_get_contents('php://input'), true);
    $res = $pasaje->aniadir($post);
    $resul['resultado'] = $res;
    echo json_encode($resul);
    exit();
}

// PUT
if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
   
        $put = json_decode(file_get_contents('php://input'), true);
        $res = $pasaje->actualiza($put);
        $result['mensaje'] = $res;
        echo json_encode($result);
                echo json_encode($put);

        exit();
    
}

// 400 Bad Request
header("HTTP/1.1 400 Bad Request");
