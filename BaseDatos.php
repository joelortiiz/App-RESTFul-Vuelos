<?php



abstract class Basedatos {

    private $servername = "localhost";
    private $database = "vuelos";
    private $username = "root";
    private $password = "";
    private $registros = array();
    private $conexion;
    private $mensajeerror = "";

    public function getConexion() {
        try {
            $this->conexion = new
                    PDO("mysql:host=$this->servername;dbname=$this->database;charset=utf8",
                    $this->username, $this->password);
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE,
                    PDO::ERRMODE_EXCEPTION);
            return $this->conexion;
        } catch (PDOException $e) {
            $this->mensajeerror = $e->getMessage();
        }
    }

    public function closeConexion() {
        $this->conexion = null;
    }

    public function getMensajeError() {
        return $this->mensajeerror;
    }
}
