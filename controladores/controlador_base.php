<?php
class Controlador_Base{
	public $registros;
	public $mensaje;
	public $error;
	public $registro;
	public $registro_id;
	public $breadcrumbs;
	public $lista;
	public $modelo;
	public $directiva;
	public $tabla;
	public $campo_filtro;
	public $valor_filtro;
	public $selected = false;
	public $registro_padre_id;
    public $campo;
    public $link;
    public $campo_resultado=false;

	public function __construct($link){
	    $this->link = $link;
	    $modelo = SECCION;
	    if($modelo == 'session'){
            $this->modelo = new Modelos($this->link);
        }
        else {
            $this->modelo = new $modelo($this->link);
        }
	    $this->directiva = new Directivas();

	    if(isset($_GET['seccion'])){
	        $this->tabla = $_GET['seccion'];

        }
        if(isset($_GET['valor_filtro'])){
            $this->valor_filtro = $_GET['valor_filtro'];
        }
        if(isset($_GET['campo_filtro'])){
            $this->campo_filtro = $_GET['campo_filtro'];
        }
        if(isset($_GET['selected'])){
            $this->selected = $_GET['selected'];
        }
        if(isset($_GET['registro_id'])){
            $this->registro_id = $_GET['registro_id'];
        }
        if(isset($_GET['campo'])){
            $this->campo = $_GET['campo'];
        }
        if(isset($_GET['campo_resultado'])){
            $this->campo_resultado = $_GET['campo_resultado'];
        }
    }

    private function upload_file_(){
        $fecha = new DateTime();

        $directorio_foto = dirname(__DIR__).'/views/'.SECCION.'/archivos/';
        $name_file = $fecha->getTimestamp();
        $extension = pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION);
        $foto = $directorio_foto.$name_file.'.'.$extension;
        move_uploaded_file($_FILES["foto"]["tmp_name"], $foto);

        $ruta_foto = './views/'.SECCION.'/archivos/'.$name_file.'.'.$extension;
        

        $original = false;
        if($extension == 'JPG' || $extension == 'jpg'){
            $original = imagecreatefromJPEG($ruta_foto);
        }
        else{
            if($extension == 'png' || $extension == 'png'){
                $original = imagecreatefrompng($ruta_foto);
            }
        }
        return $ruta_foto;
    }


    protected function upload_file($name_archivo){
        $directorio = dirname(__DIR__).'/views/'.SECCION.'/archivos/';
        $valor_random_img = time();

        $valor_random_img = $valor_random_img.rand(1,100);

        $name_file = $valor_random_img;
        $name_file = str_replace('/', '_', $name_file);
        $extension = pathinfo($_FILES[$name_archivo]["name"], PATHINFO_EXTENSION);
        $video = $directorio.$name_file.'.'.$extension;
        $r = move_uploaded_file($_FILES[$name_archivo]["tmp_name"], $video);
        $ruta_video = './views/'.SECCION.'/archivos/'.$name_file.'.'.$extension;
        
        if($extension == 'png' ){
            $imagen_size = $_FILES[$name_archivo]['size'];
            if($imagen_size >=999999){
                $img = imagecreatefrompng($video);
                $newWidth = imagesx($img)*.70;//get width of original image
                $newHeight = imagesy($img)*.70;

                $width = imagesx($img);
                $height = imagesy($img);

                $newImage = imagecreatetruecolor($newWidth, $newHeight);
                imagealphablending($newImage, false);
                imagesavealpha($newImage,true);
                imagecopyresampled($newImage, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                imagepng($newImage,$video);

            }

        }else{
            if($extension == 'jpg'){
                $imagen_size = $_FILES[$name_archivo]['size'];
                if($imagen_size >=999999){
                    $img = imagecreatefromJPEG($video);
                    $newWidth = imagesx($img)*.70;//get width of original image
                    $newHeight = imagesy($img)*.70;

                    $width = imagesx($img);
                    $height = imagesy($img);

                    $newImage = imagecreatetruecolor($newWidth, $newHeight);
                    imagealphablending($newImage, false);
                    imagesavealpha($newImage,true);
                    imagecopyresampled($newImage, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                    imagecreatefromJPEG($newImage,$video);

                }
            }
        }


        return $ruta_video;
    }

    public function obten_dato_registro(){
        $resultado = $this->modelo->obten_por_id($this->tabla, $this->registro_id);
        $this->registro = $resultado['registros'][0];
        echo $this->registro[$this->tabla.'_'.$this->campo];
    }

    public function option_selected(){

	    if($this->selected){

            $dato_arreglo = explode('_id',$this->campo_filtro);
            $tabla_hija = $dato_arreglo[0];

            $resultado = $this->modelo->obten_por_id($tabla_hija, $this->valor_filtro);
            $registro = $resultado['registros'][0];

            if($this->campo_resultado){
                $this->registro_padre_id = $registro[$this->campo_resultado];
            }
            else{
                $this->registro_padre_id = $registro[$this->tabla.'_id'];
            }

            $resultado = $this->modelo->obten_registros($this->tabla);
            $this->registros = $resultado['registros'];


        }
	    else {
            $resultado = $this->modelo->filtro_and($this->tabla,
                array($this->tabla . "." . $this->campo_filtro => $this->valor_filtro, $this->tabla . '.status' => '1'));
            $this->registros = $resultado['registros'];

        }

    }
    public function activa_bd(){
        $registro_id = $_GET['registro_id'];
        $registro = $this->modelo->activa_bd(SECCION, $registro_id);
        $this->resultado($registro);
    }
    public function alta(){
		$breadcrumbs = array('lista');
		$this->breadcrumbs = $this->directiva->nav_breadcumbs(8, 2, $breadcrumbs);
	}

    public function alta_bd(){
        $tabla = $_GET['seccion'];

        $registro = array();
        foreach ($_POST as $key => $value){
            if($value != ''){
                $registro[$key] = $value;
            }
        }



        if(!empty($_FILES['archivo']['tmp_name'])){
            $archivo = $this->upload_file('archivo');
            $registro['archivo'] = $archivo;
        }

        if(!empty($_FILES['imagen_izquierda']['tmp_name'])){

            $archivo_ii = $this->upload_file('imagen_izquierda');
            $registro['imagen_izquierda'] = $archivo_ii;
        }

        if(!empty($_FILES['imagen_derecha']['tmp_name'])){

            $archivo_id = $this->upload_file('imagen_derecha');
            $registro['imagen_derecha'] = $archivo_id;
        }


        $resultado = $this->modelo->alta_bd($registro, $tabla);
        if($resultado['error']){
            $mensaje = $resultado['mensaje'];
            header("Location: ./index.php?seccion=$tabla&accion=alta&mensaje=$mensaje&tipo_mensaje=error");
            exit;
        }

        header("Location: ./index.php?seccion=$tabla&accion=lista&mensaje=Registro insertado con éxito&tipo_mensaje=exito");
        exit;
    }

    public function datos_select(){
        $tabla = $_GET['seccion'];
        $campo_filtro = $_GET['campo_filtro'];
        $valor_filtro = $_GET['valor_filtro'];

        $resultado = $this->modelo->filtro_and($tabla,
            array($tabla.".".$campo_filtro=>$valor_filtro, $tabla.'.status'=>'1'));
        $this->registros = $resultado['registros'];

    }

    public function desactiva_bd(){
        $tabla = $_GET['seccion'];
        $registro_id = $_GET['registro_id'];
        $registro = $this->modelo->desactiva_bd($tabla, $registro_id);
        $this->resultado($registro);
    }

    public function elimina_bd(){
        $tabla = $_GET['seccion'];
        $registro_id = $_GET['registro_id'];
        $registro = $this->modelo->elimina_bd($tabla, $registro_id);
        $this->resultado($registro);
    }


    public function lista(){
		$breadcrumbs = array('alta');
		$this->breadcrumbs = $this->directiva->nav_breadcumbs(12, 0, $breadcrumbs);
		$resultado = $this->modelo->obten_registros($_GET['seccion']);
		$this->registros = $resultado['registros'];
		if(isset($resultado['mensaje'])) {
            $this->mensaje = $resultado['mensaje'];
        }
        if(isset($this->error)) {
            $this->error = $resultado['error'];
        }
	}
	public function lista_ajax(){
		$valor = $_POST['valor'];
		$resultado = $this->modelo->filtra_campos_base($valor, $_GET['seccion']);
		$this->registros = $resultado['registros'];
	}

    public function modifica(){
        $breadcrumbs = array('alta','lista');
        $this->breadcrumbs = $this->directiva->nav_breadcumbs_modifica(8, 2, $breadcrumbs);

        $tabla = $_GET['seccion'];
        $this->registro_id = $_GET['registro_id'];
        $resultado = $this->modelo->obten_por_id($tabla, $this->registro_id);

        if($resultado['n_registros'] == 0){
            header("Location: ./index.php?seccion=$tabla&accion=lista&mensaje=Ya no existe registro&tipo_mensaje=error");
            exit;
        }
        $this->registro = $resultado['registros'][0];
    }

    public function modifica_bd(){
        $tabla = $_GET['seccion'];
        $this->registro_id = $_GET['registro_id'];

        if(!empty($_FILES['archivo']['tmp_name'])){

            $archivo = $this->upload_file('archivo');
            $_POST['archivo'] = $archivo;
        }

        if(!empty($_FILES['imagen_derecha']['tmp_name'])){

            $archivo = $this->upload_file('imagen_derecha');
            $_POST['imagen_derecha'] = $archivo;
        }

        if(!empty($_FILES['imagen_izquierda']['tmp_name'])){

            $archivo = $this->upload_file('imagen_izquierda');
            $_POST['imagen_izquierda'] = $archivo;
        }

        $resultado = $this->modelo->modifica_bd($_POST, $tabla, $this->registro_id);


        if($resultado['error']){
            $mensaje = $resultado['mensaje'];
           header("Location: ./index.php?seccion=$tabla&accion=modifica&mensaje=$mensaje&tipo_mensaje=error&registro_id=$this->registro_id");
            exit;
        }
        header("Location: ./index.php?seccion=$tabla&accion=lista&mensaje=Registro modificado con éxito&tipo_mensaje=exito");
        exit;
    }

	public function resultado($registro){
        echo $registro['mensaje'];
        if($registro['error']){
            http_response_code(404);
        }
        else{
            http_response_code(200);
        }
    }


}
