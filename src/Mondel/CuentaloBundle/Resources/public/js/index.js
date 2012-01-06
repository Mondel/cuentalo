$(document).ready(function(){

    $('#masContenidos').click(function(){
        obtenerContenidos();                
    });    

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