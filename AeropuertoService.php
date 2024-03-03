<?php

    require_once ('./BD/Basedatos.php');
    require_once ('./models/AeropuertoModel.php');
    
    $dep = new AeropuertoModel();
    @header("Content-type: application/json");
    
    
    // Consultar GET
    // devuelve o 1 o todos, dependiendo si recibe o no parámetro
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        if (isset($_GET['codaeropuerto'])) {
            $res = $dep->getUnAeropuerto($_GET['codaeropuerto']);
            echo json_encode($res);
            exit();
        } else {
            $res = $dep->getAll();
            echo json_encode($res);
            exit();
        }
    }

    // En caso de que ninguna de las opciones anteriores se haya ejecutado
    header("HTTP/1.1 400 Bad Request");