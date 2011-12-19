$(document).ready(function(){

	$('.Comentario').hover(
    		function() {
				$(this).find('.EliminarItem').css("display", "block");    	
    		},
			function() {
    			$(this).find('.EliminarItem').css("display", "none");    	
    		}
	);   
    
    renderizarVideosPost();
    
});