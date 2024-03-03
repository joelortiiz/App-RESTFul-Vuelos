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
            return "error al cargar.<br>" . $e->getMessage();
        }
    }

    // Devuelve un array departamento
    public function getUnPasaje($nupasaje) {
        try {
            $sql = "SELECT * FROM $this->table WHERE idpasaje=?";
            $sentencia = $this->conexion->prepare($sql);
            $sentencia->bindParam(1, $nupasaje);
            $sentencia->execute();
            $row = $sentencia->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                return $row;
            }
            return "sin datos";
        } catch (PDOException $e) {
            return "error al cargar.<br>" . $e->getMessage();
        }
    }

    private function comprobaciones($pasajerocod, $identificador, $numasiento) {
        $resultados = [];

        // Comprobación de existencia de pasajero en el vuelo
        $sql1 = "SELECT * FROM $this->table WHERE pasajerocod = ? AND identificador = ?";
        $stmt1 = $this->conexion->prepare($sql1);
        $stmt1->execute([$pasajerocod, $identificador]);
        $resultados['pasajero_vuelo_existente'] = $stmt1->fetch();

        // Comprobación de asiento ocupado
        $sql2 = "SELECT * FROM $this->table WHERE numasiento = ? AND identificador = ?";
        $stmt2 = $this->conexion->prepare($sql2);
        $stmt2->execute([$numasiento, $identificador]);
        $resultados['asiento_ocupado'] = $stmt2->fetch();

        return $resultados;
    }

    public function borrar($pasajeno) {
        try {
            $sql = "DELETE FROM $this->table WHERE idpasaje = ?";
            $sentencia = $this->conexion->prepare($sql);
            $sentencia->bindParam(1, $pasajeno);
            $sentencia->execute();
            if ($sentencia->rowCount() == 0)
                return false;
            else
                return true;
        } catch (PDOException $e) {
            return "error al borrar.<br>" . $e->getMessage();
        }
    }

    public function guardar($post) {
        try {
            $comprobaciones = $this->comprobaciones($post['pasajerocod'], $post['identificador'], $post['numasiento']);

            if ($comprobaciones['pasajero_vuelo_existente']) {
                return "error al insertar. el pasajero " . $post['pasajerocod'] . " ya está en el vuelo " . $post['identificador'];
            }

            if ($comprobaciones['asiento_ocupado']) {
                return "error al insertar. el número de asiento " . $post['numasiento'] . " ya está ocupado en el vuelo " . $post['identificador'];
            }

            // Inserción del pasaje
            $sql_insert = "INSERT INTO $this->table (pasajerocod, identificador, numasiento, clase, pvp) VALUES (?, ?, ?, ?, ?)";
            $stmt_insert = $this->conexion->prepare($sql_insert);
            $stmt_insert->execute([$post['pasajerocod'], $post['identificador'], $post['numasiento'], $post['clase'], $post['pvp']]);
            return "registro insertado correctamente";
        } catch (PDOException $e) {
            return "error sql al insertar: " . $e->getMessage();
        }
    }

    public function actualiza($put, $idpasaje) {
        try {
            $comprobaciones = $this->comprobaciones($put['pasajerocod'], $put['identificador'], $put['numasiento']);

            if ($comprobaciones['pasajero_vuelo_existente']) {
                return "error al actualizar. el pasajero " . $put['pasajerocod'] . " ya está en el vuelo " . $put['identificador'];
            }

            if ($comprobaciones['asiento_ocupado']) {
                return "error al actualizar. el número de asiento " . $put['numasiento'] . " ya está ocupado en el vuelo " . $put['identificador'];
            }

            // Actualización del pasaje
            $sql_update = "UPDATE $this->table SET pasajerocod = ?, identificador = ?, numasiento = ?, clase = ?, pvp = ? WHERE idpasaje = ?";
            $stmt_update = $this->conexion->prepare($sql_update);
            $stmt_update->execute([$put['pasajerocod'], $put['identificador'], $put['numasiento'], $put['clase'], $put['pvp'], $idpasaje]);

            if ($stmt_update->rowCount() > 0) {
                return "registro actualizado correctamente";
            } else {
                return "error al actualizar. no se encontró el pasaje a actualizar";
            }
        } catch (PDOException $e) {
            return "error sql al actualizar: " . $e->getMessage();
        }
    }
}
