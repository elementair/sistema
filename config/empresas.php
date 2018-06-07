<?php
class Empresas{
    public $empresas;
    public $regimenes_fiscales;
    public function __construct(){
        $this->regimenes_fiscales=array('601'=>'LEY GENERAL DE PERSONAS MORALES');
        $empresa99 = array(
            'nombre_empresa'=>'SHIFRA SPA',
            'nombre_base_datos'=>'creactiv_shifra',
            'user'=>'root',
            'pass'=>'',
            'host'=>'localhost',
            // 'user'=>'tdesyxwd_bcagro',
            // 'pass'=>'Moro1983582001.',
            // 'host'=>'localhost',
            'rfc'=>'AAA010101AAA',
            'razon_social'=>'prueba SA de CV',
            'regimen_fiscal'=>'601',
            'regimen_fiscal_descripcion'=>'REGIMEN GENERAL DE PERSONAS MORALES',
            'cp'=>'44770',
            'calle'=>'calle de prueba',
            'exterior'=>'222',
            'interior'=>'',
            'colonia'=>'colonia de prueba',
            'municipio'=>'Guadalajara',
            'estado' =>'Jalisco',
            'pais'=>'Mexico',
            'sufijo_folio'=>'F',
            'folio_inicial'=>'33',
            'serie'=>'A',
            'usuario_integrador'=>'mvpNUXmQfK8=',
            'ruta_pac'=>'https://cfdi33-pruebas.buzoncfdi.mx:1443/Timbrado.asmx?wsdl',
            'telefono'=>'31203312',
            'cuentas'=>array(array('RFC'=>'CFDHGVT','nombre'=>'hsbc','cuenta'=>'000000'))
        );

        $this->empresas = array('99'=>$empresa99);
    }
}