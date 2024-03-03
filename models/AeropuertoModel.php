<?php

    class AeropuertoModel extends Basedatos{
        
        private $table;
        private $conexion;
        
        public function __construct(){
            $this->table = "aeropuerto";
            $this->conexion = $this->getConexion();
        }
        
        /**
         * Método que devulve todos los aeropuertos
         * 
         * @return type
         */
        public function getAll(){
            try {
                $sql = "SELECT * FROM $this->table;";
                $stmt = $this->conexion->query($sql);
                $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $stmt = null;
                // Devuelve el array 
                return $registros;
            } catch (PDOException $e) {
                return "Error al cargar los Aeropuertos.<br>" . $e->getMessage();
            }
        }
    }