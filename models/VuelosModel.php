<?php

class VuelosModel extends Basedatos {

    private $table;
    private $conexion;

    public function __construct() {
        $this->table = "vuelos";
        $this->conexion = $this->getConexion();
    }

    // Recibe el array de los post
    public function guardar($post) {
        try {
            $sql = "insert into $this->table values ( ?,?,?,?,?,?)";
            $sentencia = $this->conexion->prepare($sql);
            // extraemos los parámetros de la variable post
            // suponemos que se llaman igual
            $sentencia->bindParam(1, $post['dept_no']);
            $sentencia->bindParam(2, $post['dnombre']);
            $sentencia->bindParam(3, $post['loc']);
            $num = $sentencia->execute();
            return "Registro insertado: " . $post['dept_no'];
        } catch (PDOException $e) {
            return "Error al grabar.<br>" . $e->getMessage();
        }
    }

    public function actualiza($post) {
        try {
            $sql = "update $this->table set dnombre=?, loc=? where dept_no = ?";
            $sentencia = $this->conexion->prepare($sql);
            // extraemos los parámetros de la variable $post
            // suponemos que se llaman igual
            $sentencia->bindParam(3, $post['dept_no']);
            $sentencia->bindParam(1, $post['dnombre']);
            $sentencia->bindParam(2, $post['loc']);
            $num = $sentencia->execute();
            if ($sentencia->rowCount() == 0)
                return "Registro NO actualizado, o no existe o no hay cambios:
" . $post['dept_no'];
            else
                return "Registro actualizado: " . $post['dept_no'];
        } catch (PDOException $e) {
            return "Error al actualizar.<br>" . $e->getMessage();
        }
    }

    // Devuelve un array departamento
    public function getUnDepartamento($nudep) {
        try {
            $sql = "SELECT * FROM $this->table WHERE dept_no=?";
            $sentencia = $this->conexion->prepare($sql);
            $sentencia->bindParam(1, $nudep);
            $sentencia->execute();
            $row = $sentencia->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                return $row;
            }
            return "SIN DATOS";
        } catch (PDOException $e) {
            return "ERROR AL CARGAR.<br>" . $e->getMessage();
        }
    }

    public function getAll() {
        try {
            $sql = "select * from $this->table";
            $statement = $this->conexion->query($sql);
            $registros = $statement->fetchAll(PDO::FETCH_ASSOC);
            $statement = null;
            // Retorna el array de registros
            return $registros;
        } catch (PDOException $e) {
            return "ERROR AL CARGAR.<br>" . $e->getMessage();
        }
    }

    public function borrar($depno) {
        try {
            $sql = "delete from departamentos where dept_no= ? ";
            $sentencia = $this->conexion->prepare($sql);
            $sentencia->bindParam(1, $depno);
            $num = $sentencia->execute();
            if ($sentencia->rowCount() == 0)
                return "Registro NO Borrado, no se localiza: " . $depno;
            else
                return "Registro Borrado: " . $depno;
        } catch (PDOException $e) {
            return "ERROR AL BORRAR.<br>" . $e->getMessage();
        }
    }
}
