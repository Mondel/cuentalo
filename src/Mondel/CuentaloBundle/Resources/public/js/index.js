$(document).ready(function(){

    //$('#contenido_form').submit(function () { 
    	//return false;
    	//validaContenido($('#contenido_texto'), 50, 555, $('#message'), $('#contenido_form')) 
	//});
	
	$(window).scroll(function(){
        if  ($(window).scrollTop() == $(document).height() - $(window).height()){
           obtenerContenidos();
        }
	});
	
    textChange();

    $('#contenido_texto').keyup(function() {
        textChange();
        findVideo();
    });

    $('#contenido_texto').keydown(function() {
        textChange();
        findVideo();
    });

    $('input[name="comentario[texto]"]').each(function(index) {
        $(this).keypress(function(event) {
            if ( event.which == 13 ) {
                $(this).parent('form').submit();
            }
        });
    });

    $('.Comentario').hover(
    		function() {
				$(this).find('.EliminarItem').css("display", "block");    	
    		},
			function() {
    			$(this).find('.EliminarItem').css("display", "none");    	
    		}
	);
    
    asignarOnClickVerComentarios();
    
    renderizarVideosPost();
});