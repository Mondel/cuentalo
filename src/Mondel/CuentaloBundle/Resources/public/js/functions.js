function validaContenido(input, min_length, max_length, errors, form) {
    var error = '';
    if (input.val().length < min_length)
        error = 'El contenido de tu mensaje debe ser mayor a ' + min_length;
    else if (input.val().length > max_length)
        error = 'El contenido de tu mensaje debe ser menor a ' + max_length;

    if(error == '') {
        form.submit();
    	return false;
    } else {
        errors.text(error);
    }
}

function textChange() {
    var area = $('#contenido_texto');
    var num = $('#count_caracteres');
    var valor = 555 - area.val().length;
    num.text(valor);
}

function obtenerContenidos() {    
    $('.PostLoading').html('<img src="bundles/mondelcuentalo/img/ajax-loader.gif"/>');
	$.get("contenido/" + $("#cid").val() + "/" + $(".Post:last").attr("id") + "/5",   
 
    function(data){
        if (data != "") {
        	$(".Post:last").after(data);        	
        }
        $('.PostLoading').empty();
    });
};

function comentarioEliminar() {
	return confirm('Esta seguro que desea eliminar este comentario ?');
}

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
    });

    $('#contenido_texto').keydown(function() {
        textChange();
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
});