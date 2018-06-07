(function($) {
    $.get = function(key)   {
        key = key.replace(/[\[]/, '\\[');
        key = key.replace(/[\]]/, '\\]');
        var pattern = "[\\?&]" + key + "=([^&#]*)";
        var regex = new RegExp(pattern);
        var url = unescape(window.location.href);
        var results = regex.exec(url);
        if (results === null) {
            return null;
        } else {
            return results[1];
        }
    }
})(jQuery);
$(document).ready(function () {
    var seccion = $.get("seccion");
    var accion = $.get("accion");

    $('#cliente-ve-facturas').DataTable();

    $('.nombre_representante_legal').keyup(function (){
        var valor = $(this).val();
        if(valor != ''){
            $(this).removeClass('error_input');
        }
        else{
            $(this).addClass('error_input');
        }
    });

    $('.pagina_web').keyup(function (){
        var valor = $(this).val();
        if(valor != ''){
            $(this).removeClass('error_input');
        }
        else{
            $(this).addClass('error_input');
        }
    });

    $('.cp').keyup(function (){
        var valor = $(this).val();
        if(valor != ''){
            $(this).removeClass('error_input');
        }
        else{
            $(this).addClass('error_input');
        }
    });



    $( ".nombre_representante_legal" ).each(function( index ) {
        var valor = $(this).val();
        if(valor == ''){
            $(this).addClass('error_input');
        }
    });

    $( ".pagina_web" ).each(function( index ) {
        var valor = $(this).val();
        if(valor == ''){
            $(this).addClass('error_input');
        }
    });

    $( ".cp" ).each(function( index ) {
        var valor = $(this).val();
        if(valor == ''){
            $(this).addClass('error_input');
        }
    });



    $('.busca_registros').keyup(function (){
        var url = new URL(window.location.href);
        var seccion = url.searchParams.get("seccion");
        var valor_consulta = $(this).val();
        $.ajax({
            url: "./index_ajax.php?seccion="+seccion+"&accion=lista_ajax",
            type: "POST", //send it through get method
            data: {valor: valor_consulta},
            success: function(data) {
                $('#contenido_lista').empty();
                $('#contenido_lista').append(data);

                $('.desactiva').unbind('click');

                $('.desactiva').click(function (){
                    activa_desactiva($(this), 'desactivar');
                });

                $('.activa').unbind('click');

                $('.activa').click(function (){
                    activa_desactiva($(this), 'activar');
                });

                $('.elimina').click(function (){
                    elimina($(this));
                });
            },
            error: function(xhr, status) {
                //Do Something to handle error
                //alert("no insertado correctamente");
            }
        });
    });



    $('.agrega_accion_bd').on("click", function () {
        agrega_accion_bd($(this));
    });

    $('.actualiza_cliente').on("click", function () {
        var elemento = $(this);
        var cliente_id = elemento.parents().siblings('.cliente_id').val();
        var rfc_envio = elemento.parents().parents().siblings('.td_rfc').children('.rfc').val();
        var cp_envio = elemento.parents().parents().siblings('.td_cp').children('.cp').val();
        var nombre_representante_legal_envio = elemento.parents().parents().siblings('.td_nombre_representante_legal').children('.nombre_representante_legal').val();
        var pagina_web_envio = elemento.parents().parents().siblings('.td_pagina_web').children('.pagina_web').val();

        $.ajax({
            url: "./index_ajax.php?seccion=cliente&accion=actualiza_masiva_bd&cliente_id="+cliente_id,
            type: "POST", //send it through get method
            data: {
                rfc: rfc_envio, cp: cp_envio,
                nombre_representante_legal: nombre_representante_legal_envio,
                pagina_web: pagina_web_envio
            },
            success: function(data) {
                alert('Actualizado con éxito');
            },
            error: function(xhr, status) {
                //Do Something to handle error
                //alert("no insertado correctamente");
            }
        });
    });


    $('.busca_elemento').keyup(function (){
        var valor_consulta = $(this).val().toUpperCase();
        $(".elemento_accion").each(function (index) {
            var contenido = $(this).html().toUpperCase();
            var existe = contenido.indexOf(valor_consulta);
            if(existe > -1){
                $(this).show('slow');
            }
            else{
                $(this).hide('slow');
            }
        });
        $(".panel").each(function (index)
        {
            var contenido = $(this).html().toUpperCase();

            var existe = contenido.indexOf(valor_consulta);

            if(existe > -1){
                $(this).show('slow');
            }
            else{
                $(this).hide('slow');
            }
        });

    });

    $('.elimina_accion_bd').on("click", function () {
        elimina_accion_bd($(this));
    });


    $('.ve_tipo_cambio').click(function (){
        var moneda_id_enviar = $(this).children('.moneda_id').val();

        $(".modal-title").empty();
        $(".modal-title").append('Moneda Id: '+moneda_id_enviar);

        $.ajax({
            url: "./index_ajax.php?seccion=moneda&accion=obten_tipo_cambio",
            type: "POST", //send it through get method
            data: {moneda_id: moneda_id_enviar},
            success: function(data) {
                $('.modal-body').empty();
                $('.modal-body').append(data);
            },
            error: function(xhr, status) {
                //Do Something to handle error
                //alert("no insertado correctamente");
            }
        });
    });
    $('.navbar a.dropdown-toggle').unbind('click');

    $('.navbar a.dropdown-toggle').on('click', function(e) {
        var $el = $(this);
        var $parent = $(this).offsetParent(".dropdown-menu");
        $(this).parent("li").toggleClass('open');

        if(!$parent.parent().hasClass('nav')) {
            $el.next().css({"top": $el[0].offsetTop, "left": $parent.outerWidth() - 4});
        }
        $('.nav li.open').not($(this).parents("li")).removeClass("open");
        return false;
    });


    $('.elimina').click(function (){
        elimina($(this));
    });

    $('.desactiva').click(function (){
        activa_desactiva($(this), 'desactivar');
    });

    $('.activa').click(function (){
        activa_desactiva($(this), 'activar');
    });

    if ($("#select_pais").length) {

        if ($("#contenedor_select_estado").length) {
            $('#select_pais').change(function (){
                var id = $(this).val();
                genera_option($(this),'estado','pais_id',$('.estado_id'),'','');
            });
        }
    }

    if ($("#select_estado").length) {

        if ($("#contenedor_select_municipio").length) {
            $('#select_estado').change(function (){
                var id = $(this).val();
                genera_option($(this),'municipio','estado_id',$('.municipio_id'),'','');
            });
        }
    }

    if ($("#producto_sat_id").length) {
        $('#producto_sat_id').keyup(function (){
            var descripcion_busqueda = $(this).val();
            var url_ejecucion = "./index_ajax.php?seccion=insumo&accion=genera_lista_producto_sat";
            $("#producto_sat_datos").empty();
            $("#producto_sat_datos" ).load( url_ejecucion ,{valor:descripcion_busqueda}, function() {
                $(".producto_sat_id").unbind('click');
                $('.producto_sat_id').click(function (){
                    $('.producto_sat_id').parent().hide();
                    $(this).parent().show();
                });
            });
        });
    }

    if ($("#select_grupo_insumo").length) {

        if ($("#contenedor_select_tipo_insumo").length) {

            $('#select_grupo_insumo').change(function () {
                genera_option($(this),'tipo_insumo','grupo_insumo_id',$('#select_tipo_insumo'),'','tipo_insumo_descripcion');
            });
        }
    }

    if ($(".btn_impuestos").length) {
        $('.btn_impuestos').click(function (){

            $.ajax({
                url: "./index_ajax.php?seccion=insumo&accion=carga_registro_lista",
                type: "POST", //send it through get method
                data: {},
                success: function(data) {
                    $('.contenedor_registros').append(data);
                    $(".btn_elimina_registro_lista").unbind('click');
                    $('.btn_elimina_registro_lista').click(function (){
                        var elemento = $(this).parent();
                        elemento.empty();
                    });
                },
                error: function(xhr, status) {
                    //Do Something to handle error
                    //alert("no insertado correctamente");
                }
            });

        });
    }

    if ($(".btn_elimina_registro_lista").length) {
        $('.btn_elimina_registro_lista').click(function (){
            var elemento = $(this).parent();
            elemento.empty();
        });
    }

    if(seccion == 'insumo'){
        if(accion == 'alta'){
            $('.btn-signin').click(function () {
                var impuesto_retenido_id = $('#select_impuesto_retenido').val();
                if(impuesto_retenido_id!=''){
                    var tipo_factor_retenido_id = $('#select_tipo_factor_retenido').val();
                    var factor = $('#factor_retenido').val();
                    if(tipo_factor_retenido_id == ''){
                        alert('Asigne una tasa o cuota de retencion');
                        $('#select_tipo_factor_retenido').focus();
                        return false;
                    }
                    if(factor == ''){
                        alert('Asigne un factor de retencion');
                        $('#factor_retenido').focus();
                        return false;
                    }
                }


                var impuesto_id = $('#select_impuesto').val();
                if(impuesto_id!=''){
                    var tipo_factor_id = $('#select_tipo_factor').val();
                    var factor = $('#factor').val();
                    if(tipo_factor_id == ''){
                        alert('Asigne una tasa o cuota');
                        $('#select_tipo_factor').focus();
                        return false;
                    }
                    if(factor == ''){
                        alert('Asigne un factor');
                        $('#factor').focus();
                        return false;
                    }
                }
            });
        }
    }

});
