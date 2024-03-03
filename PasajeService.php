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
    $res = $pasaje->guardar($post);
    $resul['resultado'] = $res;
    echo json_encode($resul);
    exit();
}

// PUT
if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    if (isset($_GET['id'])) {
        $put = json_decode(file_get_contents('php://input'), true);
        $res = $pasaje->actualiza($put, $_GET['id']);
        $resul['mensaje'] = $res;
        echo json_encode($resul);
        exit();
    }
}

// 400 Bad Request
header("HTTP/1.1 400 Bad Request");
