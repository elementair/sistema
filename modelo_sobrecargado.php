<?php
class modelo_sobrecargado extends Modelos {
    public function obten_registros($tabla){
        $consulta_base = $this->genera_consulta_base($tabla);
        $limit = ' LIMIT 50 ';
        $consulta_base = $consulta_base.$limit;
        $result = $this->ejecuta_consulta($consulta_base);
        return $result;
    }

    public function filtra_campos_base($valor, $tabla){
        $valor = addslashes($valor);
        $consultas_base = new consultas_base();
        $where = $consultas_base->genera_filtro_base($tabla, $valor);
        $consulta_base = $this->genera_consulta_base($tabla);
        $consulta = $consulta_base.$where;

        $limit = ' LIMIT 100 ';
        $consulta = $consulta.$limit;
        $result = $this->ejecuta_consulta($consulta);
        return $result;
    }

}