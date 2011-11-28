$(document).ready(function(){

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
});