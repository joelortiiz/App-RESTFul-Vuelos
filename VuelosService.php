<?php
require_once ('./BD/BaseDatos.php');
require_once ('./models/VuelosModel.php');
$vuel = new VuelosModel();

@header("Content-type: application/json");

// GET
// Si se recibe un parámetro por get, solicitamos un vuelo concreto
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['identificador'])) {
        $res = $vuel->getVueloId($_GET["identificador"]);
        echo json_encode($res);
        exit();
    }
    // Si no recibe un parámetro por get, solicitamos un todos los vuelos.
    else {
        $res = $vuel->getAll();
        echo json_encode($res);
        exit();
    }
}

// En caso de que ninguna de las opciones anteriores se haya ejecutado
header("HTTP/1.1 400 Bad Request");