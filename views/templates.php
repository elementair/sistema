<?php

class templates{
    public $campos;
    public $acciones;
    public $campos_lista_cliente;
    public $link;

    public function __construct($link){
        $this->link = $link;
        if(isset($_GET['seccion'])) {
            $this->campos_lista($_GET['seccion']);
        }
    }
    public function genera_campos($input, $campo, $valor,$arreglo, $clase_elimina,$link){
        $directiva = new Directivas();
        $cols = $input['cols'];
        $required = '';
        $pattern = false;
        $accion = $_GET['accion'];
        $html = '';
        $vistas = $input['vista'];
        if(in_array($accion,$vistas)) {

            if (isset($input['requerido'])) {
                $required = $input['requerido'];
            }
            if (isset($input['pattern'])) {
                $pattern = $input['pattern'];
            }
            if ($input['tipo'] == 'text') {
                $html = $html . $directiva->genera_input_text(
                    $campo, $cols, $valor, $required, $pattern,$arreglo,false,false);
            }
            elseif ($input['tipo'] == 'select') {
                $tabla_foranea = $input['tabla_foranea'];
                $html = $html . $directiva->input_select(
                    $tabla_foranea, $valor, $cols, false,false,false,$this->link);
            }
            elseif ($input['tipo'] == 'multi_select') {
                $tabla_foranea = $input['tabla_foranea'];
                $html = $html . $directiva->input_select_multimple(
                    $tabla_foranea, $valor, $cols, false,false,false,$this->link);
            }
            elseif ($input['tipo'] == 'select_personalizado') {
                $tabla_foranea = $input['tabla_foranea'];
                $etiqueta = $input['etiqueta'];
                $requerido = $input['requerido'];
                $nombre_contenedor = $input['nombre_contenedor'];
                $nombre_campo = $input['nombre_campo'];
                $html = $html . $directiva->input_select_personalizado(
                        $tabla_foranea, $valor, $cols, false,false,
                        false,$link,$etiqueta,$nombre_contenedor,$requerido,$nombre_campo);
            }
            elseif ($input['tipo'] == 'autocomplete') {
                $tabla_foranea = $input['tabla_foranea'];
                if($valor != ''){
                    $id = $valor;
                    $modelo = new $tabla_foranea($link);
                    $resultado = $modelo->obten_por_id($tabla_foranea,$id);
                    $registro = $resultado['registros'][0];
                    $valor = $registro[$tabla_foranea.'_codigo'];
                    $valor = $valor.' '.$registro[$tabla_foranea.'_nombre'];
                }
                $html = $html . $directiva->autocomplete($tabla_foranea, $valor, $cols, false);
            }
            elseif ($input['tipo'] == 'select_columnas') {
                $tabla_foranea = $input['tabla_foranea'];
                $columnas = $input['columnas'];
                $html = $html . $directiva->input_select_columnas($tabla_foranea, $valor, $cols, false, $columnas,$link);
            }
            elseif ($input['tipo'] == 'checkbox') {
                $html = $html . $directiva->checkbox($valor, $cols, $campo);
            }
            elseif ($input['tipo'] == 'editor_texto') {
                $html = $html . $directiva->editor_texto($campo, $campo, $valor,$cols);
            }
            elseif ($input['tipo'] == 'textnom') {
                $html = $html . $directiva->input_nombre($valor, $cols, $campo);
            }
            elseif ($input['tipo'] == 'textarea') {
                $html = $html . $directiva->textarea($valor, $cols, $campo);
            }
            elseif ($input['tipo'] == 'password') {
                $html = $html . $directiva->password($valor, $cols, $campo);
            }
            elseif ($input['tipo'] == 'email') {
                $html = $html . $directiva->email($valor, $cols, $campo);
            }
            elseif ($input['tipo'] == 'fecha') {
                $html = $html . $directiva->fecha($valor, $cols, $campo);
            }
            elseif ($input['tipo'] == 'archivo') {
                $html = $html . $directiva->input_archivo($valor, $cols, $campo);
            }
            elseif ($input['tipo'] == 'lista') {
                $id = false;
                $tablas = $input['tablas'];
                $campos_llenables = $input['campos_llenables'];
                $tabla_relacionada = $input['tabla_relacionada'];
                if(isset($_GET['registro_id'])) {
                    $id = $_GET['registro_id'];
                }
                $campo_busqueda = $input['campo_busqueda'];

                $html = $html . $directiva->lista(
                    $cols, $campo, $tablas, $campos_llenables,'[]',
                    $tabla_relacionada, $id, $campo_busqueda, $clase_elimina);
            }
        }
        return $html;
    }

    public function genera_campos_alta($tabla,$arreglo, $link){
        $html = "";
        $estructura = new consultas_base();
        $campos_alta = $estructura->estructura_bd[$tabla]['campos'];
        foreach ($campos_alta as $campo=>$input){
           $html = $html.$this->genera_campos($input,$campo,'',$arreglo, true, $link);
        }
        return $html;
    }

    public function alta($accion, $breadcrumbs, $tabla,$arreglo, $link){
        $directiva = new Directivas();
        $html = $directiva->encabezado_form_alta($accion);
        $html = $html.$breadcrumbs;
        $html = $html."<div class='col-md-8 col-md-offset-2 alta'>";
        $html = $html.$this->genera_campos_alta($tabla,$arreglo, $link);
        $html = $html.$directiva->btn_enviar(12,'Guardar');
        $html = $html."</div></form>";
        return $html;
    }

    public function modifica($controlador, $seccion, $arreglo){
        $directiva = new Directivas();
        $html = $directiva->encabezado_form_modifica($controlador->registro_id);
        $html = $html.$controlador->breadcrumbs;
        $html = $html."<div class='col-md-8 col-md-offset-2 modifica'>";
        $html = $html.$this->genera_campos_modificables($seccion, $controlador->registro, $arreglo);
        $html = $html.$directiva->btn_enviar(12,'Modificar');
        $html = $html."</div>";
        $html = $html."</form>";
        return $html;
    }

    public function genera_campos_modificables($tabla, $registro,$arreglo){
        $html = "";
        $estructura = new consultas_base();
        $campos_alta = $estructura->estructura_bd[$tabla]['campos'];
        $valor = '';
        foreach ($campos_alta as $campo=>$input){
            if(isset($input['externa'])){
                if($input['externa']) {
                    $valor = $registro[$campo];
                }
            }
            else{
                $valor = $registro[$tabla.'_'.$campo];
            }

            $html = $html.$this->genera_campos($input,$campo,$valor,$arreglo, false, $this->link);
        }
        return $html;
    }
    public function campos_lista($tabla){
        $campos = new Elemento_Lista($this->link);

        $filtro = array('seccion_menu.nombre' => $tabla, 'elemento_lista.status'=>'1');
        $resultado = $campos->filtro_and('elemento_lista', $filtro);

        $registros = $resultado['registros'];
        $this->campos = array();
        foreach ($registros as $registro){
            $this->campos[] = $registro['elemento_lista_nombre'];
        }
    }
    public function acciones($tabla){
        $modifica = array('grupo','seccion_menu','accion','menu','usuario',
            'elemento_lista','grupo_servicios','servicios','promociones', 'servicios_promociones',
            'empleados', 'servicios_empleados','clientes');


        $desactiva_bd = array('grupo','seccion_menu','accion','menu','usuario',
            'elemento_lista','grupo_servicios','servicios','promociones', 'servicios_promociones',
            'empleados', 'servicios_empleados','clientes');

        $activa_bd = array('grupo','seccion_menu','accion','menu','usuario',
            'elemento_lista','grupo_servicios','servicios','promociones', 'servicios_promociones',
            'empleados', 'servicios_empleados','clientes');

        $elimina_bd = array('grupo','seccion_menu','accion','menu','usuario',
            'elemento_lista','grupo_servicios','servicios','promociones', 'servicios_promociones',
            'empleados', 'servicios_empleados','clientes');

        $asigna_accion = array('grupo');


        $this->acciones = array();
        if(in_array($tabla, $modifica)){
            $this->acciones[] = 'modifica';
        }
        if(in_array($tabla, $desactiva_bd)){
            $this->acciones[] = 'desactiva_bd';
        }
        if(in_array($tabla, $activa_bd)){
            $this->acciones[] = 'activa_bd';
        }
        if(in_array($tabla, $elimina_bd)){
            $this->acciones[] = 'elimina_bd';
        }

        if(in_array($tabla, $asigna_accion)){
            $this->acciones[] = 'asigna_accion';
        }


    }

    public function obten_panel($status){
        if($status == 1){
            $panel_class = 'panel panel-info';
        }
        else{
            $panel_class = 'panel panel-danger';
        }
        return $panel_class;
    }

    public function obten_elementos_body($tabla, $registro){
        $directiva = new Directivas();
        $this->campos_lista($tabla);
        $html = "";



        foreach($this->campos as $campo){

            $tipo = false;

            if($campo == 'grupo_servicios_archivo'){
                $tipo = 'imagen';
            }
            elseif ($campo == 'servicios_archivo'){
                $tipo = 'imagen';
            }
            elseif ($campo == 'promociones_archivo'){
                $tipo = 'imagen';
            }
            elseif ($campo == 'empleados_archivo'){
                $tipo = 'imagen';
            }
            elseif ($campo == 'clientes_archivo'){
                $tipo = 'imagen';
            }
            elseif ($campo == 'usuario_archivo'){
                $tipo = 'imagen';
            }

            $etiqueta = strtoupper($campo);
            $etiqueta = str_replace('_', ' ',$etiqueta);

            if($campo == $tabla.'_status'){
                $html = $html.$directiva->span_lista_status($registro[$campo]);
            }
            else {
                $html = $html.$directiva->span_etiqueta($etiqueta, $registro[$campo],$tipo);
            }
        }
        return $html;
    }
    public function obten_acciones($tabla, $id, $status,$link){
        $directiva = new Directivas();
        $this->acciones($tabla);
        $html = "";

        $modelo_accion = new Accion($link);


        $icono = 0;
        foreach ($this->acciones as $accion){
            $permiso = 0;
            $acciones_permitidas = $modelo_accion->obten_accion_permitida_session($tabla, $accion);
            $accion_permitida = $acciones_permitidas['registros'];


            $permiso = $accion_permitida[0]['n_registros'];

            if ($accion == 'modifica' && $permiso == 1) {
                    $html = $html . $directiva->link_modifica($tabla, $id);
            }
            if ($accion == 'elimina_bd' && $permiso == 1) {
                    $html = $html . $directiva->link_elimina($id);
            }
            if (($accion == 'activa_bd' || $accion == 'desactiva_bd') && $icono == 0 && $permiso == 1) {
                    $icono = 1;
                    $html = $html . $directiva->link_cambia_status($tabla, $id, $status);
            }
            
            if ($accion == 'asigna_accion' && $permiso == 1) {
                $html = $html . $directiva->link_asigna_accion($tabla, $id);
            }
            

        }
        return $html;
    }

    public function body_registro($seccion, $registro){
        $html = "<div class='panel-body'>";
        $html = $html.$this->obten_elementos_body($seccion, $registro);
        $html = $html."</div>";
        return $html;
    }
    public function footer_registro($seccion, $registro,$link){
        $html = "<div class='panel-footer'>";
        if($seccion == 'accion_grupo'){
            $status = 1;
        }
        else{
            $status = $registro[$seccion.'_status'];
        }
        $html = $html.$this->obten_acciones($seccion, $registro[$seccion.'_id'], $status,$link);
        $html = $html."</div>";
        return $html;
    }
    public function registro($panel_class, $registro, $seccion,$link){
        $directiva = new Directivas();
        $html = "<div class='col-md-3'>";
        $html = $html."<div class='registro $panel_class'>";
        $html = $html.$directiva->div_encabezado_registro_lista($registro[$seccion.'_id']);
        $html = $html.$this->body_registro($seccion, $registro);
        $html = $html.$this->footer_registro($seccion, $registro,$link);
        $html = $html."</div>";
        $html = $html."</div>";
        return $html;
    }

    public function panel_completo($salto_linea, $registro, $seccion,$link){
        $html = "";
        if($salto_linea == 0){
            $html = $html."<div class='row'>";
        }
        if($seccion == 'accion_grupo'){
            $status = 1;
        }
        else {
            $status = $registro[$seccion . '_status'];
        }
        $panel_class = $this->obten_panel($status);
        $html = $html.$this->registro($panel_class, $registro, $seccion,$link);
        if($salto_linea == 3){
            $html = $html."</div><br>";
        }
        return $html;
    }

    public function lista($registros, $seccion,$link){
        $html = "";
        $contador = 0;
        $salto_linea = 0;
        foreach ($registros as $key => $registro) {
            $salto_linea = $contador%4;
            $contador++;
            $html = $html.$this->panel_completo($salto_linea, $registro, $seccion,$link);
        }
        if($salto_linea < 3){
            $html = $html."</div><br>";
        }
        return $html;
    }

    public function lista_completa($breadcrumbs, $seccion, $registros,$link){
        $directiva = new Directivas();
        $html = $breadcrumbs;
        $html = $html.$directiva->div_busqueda();
        $html = $html."<br>";
        $html = $html."<div class='panel panel-default lista'>";
        $html = $html."<div class='panel-heading'>";
        $html = $html.str_replace('_',' ',ucfirst($seccion));
        $html = $html."</div>";
        $html = $html."<div class='panel-body' id='contenido_lista'>";
        $html = $html.$this->lista($registros,$seccion,$link);
        $html = $html."</div></div>";
        return $html;
    }
}