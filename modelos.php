<?php
class Modelos{
    public $tabla;
    public $foraneas_no_insertables;
    public $link;

    public function __construct($link){
        $this->link = $link;

        /*Aqui se declaran los campos que no seran insertados ni modificados en los
        formularios de alta u modifica*/
        $this->foraneas_no_insertables = array();


    }


    public function obten_columnas_completas($tabla){
        $columnas = "";
        $consulta_base = new consultas_base();
        $tablas_select = $consulta_base->estructura_bd[$tabla]['columnas_select'];
        foreach ($tablas_select as $key=>$tabla_select){

            if(is_array($tabla_select)){
                $tabla_base = $tabla_select['tabla_base'];
                $tabla_renombrada = $tabla_select['tabla_renombrada'];
                $resultado_columnas = $this->genera_columnas_consulta($tabla_base, $tabla_renombrada);

            }
            else {
                $resultado_columnas = $this->genera_columnas_consulta($key,false);

            }
            $columnas .= $columnas == ""?"$resultado_columnas":" , $resultado_columnas";
        }

        return $columnas;
    }


    public function genera_consulta_base($tabla){

        $columnas = $this->obten_columnas_completas($tabla);
        /* consulta base ./siste,a/consultas_base.php */
        $consulta_base = new consultas_base();
        $tablas = $consulta_base->obten_tablas_completas($tabla);
        $consulta = "SELECT $columnas FROM $tablas";


        return $consulta;
    }

    public function genera_columnas_consulta($tabla, $tabla_renombrada){
        $columnas_parseadas = $this->obten_columnas($tabla);
        $columnas_sql = "";

        $consulta_base = new consultas_base();
        $subconsultas = $consulta_base->subconsultas($tabla);

        foreach($columnas_parseadas as $columna_parseada){
            if($tabla_renombrada){
                $tabla_nombre = $tabla_renombrada;
            }
            else{
                $tabla_nombre = $tabla;
            }
            $columnas_sql .= $columnas_sql == ""?"$tabla_nombre.$columna_parseada AS $tabla_nombre"."_$columna_parseada":",$tabla_nombre.$columna_parseada AS $tabla_nombre"."_$columna_parseada";
        }
        if($subconsultas){
            $columnas_sql = $columnas_sql.",".$subconsultas;
        }
        return $columnas_sql;
    }

    public function obten_columnas($tabla){
        $consulta = "DESCRIBE $tabla";
        $result = $this->ejecuta_consulta($consulta);
        $columnas = $result['registros'];
        $columnas_parseadas = array();
        foreach($columnas as $columna ){
            foreach($columna as $campo=>$atributo){
                if($campo == 'Field'){
                    $columnas_parseadas[] = $atributo;
                }
            }
        }
        return $columnas_parseadas;
    }

    public function obten_ultimo_id($tabla){
        $result = $this->link->query("SELECT MAX(id) AS id FROM $tabla");
        while( $row = mysqli_fetch_assoc( $result)){
            $new_array[] = $row;
        }

        $ultimo_id = $new_array[0]['id'];
        return $ultimo_id;
    }

    public function ejecuta_consulta($consulta){
       	$result = $this->link->query($consulta);
      	$n_registros = $result->num_rows;

       	if($this->link->error){
     		return array('mensaje'=>$this->link->error, 'error'=>True);
       	}
      	$new_array = array();
      	while( $row = mysqli_fetch_assoc( $result)){
      		$new_array[] = $row; 
      	}
      	return array('registros' => $new_array, 'n_registros' => $n_registros);

  	}

	public function filtro_and($tabla, $filtros){

        $sentencia = "";
        foreach ($filtros as $key => $value) {
            $key = addslashes($key);
            $value = addslashes($value);
        	$sentencia .= $sentencia == ""?"$key = '$value'":" AND $key = '$value'";
        }

        $consulta = $this->genera_consulta_base($tabla);

        $where = " WHERE $sentencia";
        $consulta = $consulta.$where;

        $result = $this->ejecuta_consulta($consulta);
        return $result;
	}

	public function obten_registros_activos($tabla){
        $consulta = $this->genera_consulta_base($tabla);
        $where = " WHERE $tabla.status=1 ";
        $consulta = $consulta.$where;
        $result = $this->ejecuta_consulta($consulta);
        return $result;
	}

    public function obten_registros_session($tabla, $campo, $valor){
        $consulta = $this->genera_consulta_base($tabla);
        $where = " WHERE $tabla.$campo=$valor";
        $consulta = $consulta.$where;
        $result = $this->ejecuta_consulta($consulta);
        return $result;
    }


	public function obten_registros($tabla){
	    $consulta_base = $this->genera_consulta_base($tabla);
        $result = $this->ejecuta_consulta($consulta_base);
        return $result;
	}

	public function filtra_campos_base($valor, $tabla){
	    $valor = addslashes($valor);
        $consultas_base = new consultas_base();
        $where = $consultas_base->genera_filtro_base($tabla, $valor);
        $consulta_base = $this->genera_consulta_base($tabla);
        $consulta = $consulta_base.$where;

        $result = $this->ejecuta_consulta($consulta);
        return $result;
	}

	public function activa_bd($tabla, $id){
		
		$this->link->query("UPDATE $tabla SET status = '1' WHERE id = $id");
		if($this->link->error){
			return array('mensaje'=>'Error al activar', 'error'=>True);
		}
		else{
			$registro_id = $id;
			return array(
				'mensaje'=>'Registro activado con éxito', 'error'=>False, 'registro_id'=>$registro_id);
		}
	}

	public function alta_bd($registro, $tabla){	
        $campos = "";
		$valores = "";
        $campos_no_insertables = array();
		if(array_key_exists($tabla,$this->foraneas_no_insertables)){
            $campos_no_insertables = $this->foraneas_no_insertables[$tabla];
        }

		foreach ($registro as $campo => $value) {
			if($campo == 'status'){
				if($value == '1'){
					$value = 1;
				}
				else{
					$value = 0;
				}
			}
			if($campo == 'visible'){
				if($value == 1){
					$value = 1;
				}
				else{
					$value = 0;
				}
			}


			$campo = addslashes($campo);


            if(!in_array($campo,$campos_no_insertables)) {
                $value = addslashes($value);
                $campos .= $campos == "" ? "$campo" : ",$campo";
                $valores .= $valores == "" ? "'$value'" : ",'$value'";
            }
		}
		$consulta_insercion = "INSERT INTO ". $tabla." (".$campos.") VALUES (".$valores.")";



		$this->link->query($consulta_insercion);
		if($this->link->error){
			return array('mensaje'=>'Error al insertar '.$this->link->error, 'error'=>True);
		}
		else{
			$registro_id = $this->link->insert_id;
			return array(
				'mensaje'=>'Registro insertado con éxito', 'error'=>False, 'registro_id'=>$registro_id);
		}
	}

	public function desactiva_bd($tabla, $id){
		$this->link->query("UPDATE $tabla SET status = '0' WHERE id = $id");
		if($this->link->error){
			return array('mensaje'=>'Error al desactivar', 'error'=>True);
		}
		else{
			$registro_id = $id;
			return array(
				'mensaje'=>'Registro desactivado con éxito', 'error'=>False, 'registro_id'=>$registro_id);
		}
	}

	public function elimina_bd($tabla, $id){ 

		$consulta = "DELETE FROM ".$tabla. " WHERE id = ".$id;

        $this->link->query($consulta);
		if($this->link->error){
			return array('mensaje'=>'Error al eliminar', 'error'=>True);
		}
		else{
			$registro_id = $this->link->insert_id;
			return array(
				'mensaje'=>'Registro eliminado con éxito', 'error'=>False, 'registro_id'=>$registro_id);
		}
	}

	public function modifica_bd($registro, $tabla, $id){
		$campos = "";

        $campos_no_insertables = array();
        if(array_key_exists($tabla,$this->foraneas_no_insertables)){
            $campos_no_insertables = $this->foraneas_no_insertables[$tabla];
        }

        $existe_status = false;
		foreach ($registro as $campo => $value) {
		    if($campo == 'status'){
                $existe_status = true;
            }
            $campo = addslashes($campo);
			$value = addslashes($value);
            if(!in_array($campo,$campos_no_insertables)) {
                $campos .= $campos == "" ? "$campo = '$value'" : ", $campo = '$value'";
            }
		}
		if(!$existe_status){
		    $campos = $campos." , status = '0' ";
        }

		$visible = "";
		if($tabla == 'accion'){
		    if(array_key_exists('visible', $registro)){
		        if($registro['visible']==1){
		            $visible = " , visible = '1' ";
                }
                else{
                    $visible = " , visible = '0' ";
                }
            }
            else{
		        $visible = " , visible = '0' ";
            }
		}

		$consulta = "UPDATE ". $tabla." SET ".$campos." $visible WHERE id = $id";


		$this->link->query($consulta);

		if($this->link->error){
			return array('mensaje'=>$this->link->error.'<br><br>'.$consulta, 'error'=>True);
		}
		else{
			$registro_id = $id;
			return array(
				'mensaje'=>'Registro modificado con éxito', 'error'=>False, 'registro_id'=>$registro_id);
		} 
	}

    public function obten_por_id($tabla, $id){ //finalizado
        $consulta = $this->genera_consulta_base($tabla);
        $where = " WHERE $tabla".".id = $id ";
		$consulta = $consulta.$where;
        $result = $this->ejecuta_consulta($consulta);
        return $result;
	}

}
