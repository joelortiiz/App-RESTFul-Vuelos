<?php

class PasajesModel extends Basedatos {

    private $table;
    private $conexion;

    public function __construct() {
        $this->table = "pasaje";
        $this->conexion = $this->getConexion();
    }

    public function getAll() {
        try {
            $sql1 = "SELECT p.*, per.nombre FROM $this->table p JOIN pasajero per ON p.pasajerocod = per.pasajerocod ORDER BY p.idpasaje;";
            $statement1 = $this->conexion->query($sql1);
            $registros1 = $statement1->fetchAll(PDO::FETCH_ASSOC);

            $sql2 = "SELECT nombre, pasajerocod FROM pasajero GROUP BY pasajerocod;";
            $statement2 = $this->conexion->query($sql2);
            $registros2 = $statement2->fetchAll(PDO::FETCH_ASSOC);

            $sql3 = "SELECT pa.identificador, v.aeropuertoorigen, v.aeropuertodestino FROM pasaje pa JOIN vuelo v ON pa.identificador = v.identificador GROUP BY identificador;";
            $statement3 = $this->conexion->query($sql3);
            $registros3 = $statement3->fetchAll(PDO::FETCH_ASSOC);

            // Retorna el array de registros en formato JSON
            return array("registros1" => $registros1, "registros2" => $registros2, "registros3" => $registros3);
        } catch (PDOException $e) {
            return "error ->.<br>" . $e->getMessage();
        }
    }

    // Devuelve un array departamento
    public function getUnPasaje($id) {
        try {
            $sql = "SELECT * FROM $this->table WHERE idpasaje= ?";
            $sentencia = $this->conexion->prepare($sql);
            $sentencia->bindParam(1, $id);
            $sentencia->execute();
            $row = $sentencia->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                return $row;
            }
            return "sin datos";
        } catch (PDOException $e) {
            return "error al intentar cargar.<br>" . $e->getMessage();
        }
    }
    public function getPasajesIde($ide) {
        try {
            $sql1 = "SELECT * FROM pasaje WHERE identificador = ?;";
            $sentencia1 = $this->conexion->prepare($sql1);
            $sentencia1->bindParam(1, $ide);
            $sentencia1->execute();
            $registros1 = $sentencia1->fetchAll(PDO::FETCH_ASSOC);

            $sql2 = "SELECT ps.* FROM pasaje p JOIN pasajero ps ON p.pasajerocod = ps.pasajerocod WHERE p.identificador = ?;";
            $sentencia2 = $this->conexion->prepare($sql2);
            $sentencia2->bindParam(1, $ide);
            $sentencia2->execute();
            $registros2 = $sentencia2->fetchAll(PDO::FETCH_ASSOC);

            if ($registros1 && $registros2) {
                return array("registros1" => $registros1, "registros2" => $registros2);
            }
            return false;
        } catch (PDOException $e) {
            return "error ->.<br>" . $e->getMessage();
        }
    }

    public function borrar($id) {
        try {
            $sql = "DELETE FROM $this->table WHERE idpasaje = ?";
            $sentencia = $this->conexion->prepare($sql);
            $sentencia->bindParam(1, $id);
            $sentencia->execute();
            if ($sentencia->rowCount() == 0)
                return false;
            else
                return "Se ha borrado el pasaje $id";
        } catch (PDOException $e) {
            return "error al borrar.<br>" . $e->getMessage();
        }
    }

    public function aniadir($post) {
        try {
            $comprobar = $this->comprobar($post['pasajerocod'], $post['identificador'], $post['numasiento']);

            if ($comprobar['pasajero_vuelo_existe']) {
                return "error al insertar. el pasajero " . $post['pasajerocod'] . " ya está en el vuelo " . $post['identificador'];
            }

            if ($comprobar['asiento_ocu']) {
                return "error al insertar. el número de asiento " . $post['numasiento'] . " ya está ocupado en el vuelo " . $post['identificador'];
            }

            $sql_insert = "INSERT INTO $this->table (pasajerocod, identificador, numasiento, clase, pvp) VALUES (?, ?, ?, ?, ?)";
            $stmt_insert = $this->conexion->prepare($sql_insert);
            $stmt_insert->execute(array($post['pasajerocod'], $post['identificador'], $post['numasiento'], $post['clase'], $post['pvp']));
            return "registro insertado correctamente";
        } catch (PDOException $e) {
            return "error sql al insertar: " . $e->getMessage();
        }
    }

    public function actualiza($put) {

        try {
            $comprobar = $this->comprobar($put['pasajerocod'], $put['identificador'], $put['numasiento']);

            if ($comprobar['pasajero_vuelo_existe']) {
                return "No se puede actualizar porque el pasajero " . $put['pasajerocod'] . " ya está en el vuelo  " . $put['identificador'];
            }

            if ($comprobar['asiento_ocu']) {
                return "No se puede actualizar porque el asiento " . $put['numasiento'] . " del vuelo " . $put['identificador'] . " ya está ocupado";
            }

            $sql_update = "UPDATE $this->table SET pasajerocod = ?, identificador = ?, numasiento = ?, clase = ?, pvp = ? WHERE idpasaje = ?";
            $stmt_update = $this->conexion->prepare($sql_update);
            $stmt_update->execute(array($put['pasajerocod'], $put['identificador'], $put['numasiento'], $put['clase'], $put['pvp'], $put['idpasaje']));

            if ($stmt_update->rowCount() > 0) {
                return "El pasaje se ha actualizado correctamente";
            } else {
                return "Error: No se encontró el pasaje a actualizar";
            }
        } catch (PDOException $e) {
            return "Error al actualizar: " . $e->getMessage();
        }
    }

    private function comprobar($pasajerocod, $identificador, $numasiento) {

        $sql1 = "SELECT * FROM $this->table WHERE pasajerocod = ? AND identificador = ?";        
        // Preparamos y ejecutamos consulta con los datos recibidos

        $stmt1 = $this->conexion->prepare($sql1);
        $stmt1->execute(array($pasajerocod, $identificador));
        $result = [];
        $result['pasajero_vuelo_existe'] = $stmt1->fetch();

        // Comprobación de que si un  asiento está ocupado
        $sql2 = "SELECT * FROM $this->table WHERE numasiento = ? AND identificador = ?";
        // Preparamos y ejecutamos consulta con los datos recibidos
        $stmt2 = $this->conexion->prepare($sql2);
        $stmt2->execute(array($numasiento, $identificador));
        $result['asiento_ocu'] = $stmt2->fetch();

        return $result;
    }
}
