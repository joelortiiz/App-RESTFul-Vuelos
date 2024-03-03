<?php

    class PasajeroModel extends Basedatos{
        
        private $table;
        private $conexion;
        
        public function __construct(){
            $this->table = "pasajero";
            $this->conexion = $this->getConexion();
        }
        
        /**
         * Método que devuelve a todos los 
         * pasajeros de la bd
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
                return "Error al cargar los pasajeros.<br>" . $e->getMessage();
            }
        }
    }