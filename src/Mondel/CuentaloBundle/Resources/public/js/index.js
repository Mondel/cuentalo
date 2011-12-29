$(document).ready(function(){   
	
    $('#contenido_form').submit(function(){ validarPost() });

	window.isScrolling = false;
	
	$(window).scroll(function(){
        if  (($(window).scrollTop() == $(document).height() - $(window).height() || 
        $(window).scrollTop() + 1 == $(document).height() - $(window).height() ||
        $(window).scrollTop() - 1 == $(document).height() - $(window).height())
        && !window.isScrolling){
        	window.isScrolling = true;
        	obtenerContenidos();        	
        }
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