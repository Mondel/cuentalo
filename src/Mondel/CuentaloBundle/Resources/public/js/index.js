$(document).ready(function(){   
	
	window.isScrolling = false;
	
	$(window).scroll(function(){
        if  ($(window).scrollTop() == $(document).height() - $(window).height() && !window.isScrolling){
        	window.isScrolling = true;
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