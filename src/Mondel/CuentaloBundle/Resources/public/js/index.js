$(document).ready(function(){

    $('#masContenidos').click(function(){
        obtenerContenidos();                
    });    

    $('#contenido_form').submit(function(evt){
        if ($('#contenido_sexo').val() == '') {
            evt.preventDefault();
            $('#contenido_sexo').css('background', 'red');
            $('#options_error').text("Debes seleccionar un sexo");
            $('#contenido_sexo').focus();
        } else if ($('#contenido_categoria').val() == '') {
            evt.preventDefault();
            $('#contenido_categoria').css('background', 'red');
            $('#options_error').text("Debes seleccionar una categoria");
            $('#contenido_categoria').focus();
        }
    });

    asignarOnSubmitComentarAjax();    

    var areaTexto = '#contenido_texto';
    var areaCount = '#count_caracteres';

    textChange(areaTexto, areaCount);

    $(areaTexto).keyup(function() {
        textChange(areaTexto, areaCount);
        findVideo();
    });

    $(areaTexto).keydown(function() {
        textChange(areaTexto, areaCount);
        findVideo();
    });
   
    asignarOnHoverComentarioEliminar('.Comentario', '.EliminarItem');
    
    asignarOnClickVerComentarios();
    
    renderizarVideosPost();
});