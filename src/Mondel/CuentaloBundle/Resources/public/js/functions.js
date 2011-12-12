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
        	actualizarBotones(data);
        }
        $('.PostLoading').empty();
    });
};

function actualizarBotones(data) {
	var $response = $(data);
	
	if( $response.length < 1 ) return;
	
	gapi.plusone.go();
	
	FB.XFBML.parse();
	
	getTaringaButtons();
	
	$response.find('a.twitter-share-button').each(function() {
		twttr.widgets.load($(this).get(0));		
	});
}

function comentarioEliminar() {
	return confirm('Esta seguro que desea eliminar este comentario ?');
}