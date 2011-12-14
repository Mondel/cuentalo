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

function findVideo() {
	if ($('#video').html() == '') {
		var titulo = "";
		var thumbnail = "";
		var descripcion = "";
		var urlVideo = "";	
		
		var areaText = $('#contenido_texto').val();
		var regexYoutube = /www\.youtube\.com\/watch\?v=([^&]*)/ig;
		
		var urlYoutube = regexYoutube.exec(areaText);
		if (urlYoutube != null) {
			idVideoYoutube = urlYoutube[1];
			urlApiYoutube = "http://gdata.youtube.com/feeds/api/videos?v=2&alt=jsonc&q=" + idVideoYoutube;
			
			$.ajax({
				  url: urlApiYoutube,
				  async: false,
				  success: function(response){
					  if (response.data != null) {
							if (response.data.items != null && response.data.items.length > 0) {
								datosVideo = response.data.items[0];
								titulo = datosVideo.title;
								descripcion = datosVideo.description;
								thumbnail = datosVideo.thumbnail.sqDefault;
								urlVideo = 'http://www.youtube.com/watch?v=' + idVideoYoutube;
							}
					  }	    
				  }
			});
			
			html = "<div class='TituloVideo'>" + titulo + "</div><div id='eliminarVideo'>X</div>" +
				"<div class='ThumbnailVideo'><img src='" + thumbnail + "' alt='" + titulo + "' />" +
				"</div><div class='DescripcionVideo'>" + descripcion + "<div class='UrlVideo'>" + urlVideo + "</div></div>";
			$('#video').html(html);
			$('#video').show();
			$('#contenido_categoria option').each(function() {  
				if ($(this).text() == 'Video'){				
					$('#contenido_categoria').val($(this).val());				
				} else {
					$(this).attr("disabled", "disabled");
				}
			});		
			$('#eliminarVideo').click(function(){ eliminarVideo(); });
		}
	}
}

function eliminarVideo() {	
	$('#video').html("");
	$('#video').hide();
	$('#contenido_categoria option').each(function() {  
		if ($(this).attr("disabled")) {				
			$(this).removeAttr("disabled");
		}
	});
}

function renderizarVideos() {
	$('.Post').each(function(){
		if ($(this).find('.TituloTexto').text().indexOf("Video") != '-1') {
			var contenido = $(this).children('.Contenido');
			var regexYoutube = /h?t?t?p?:?\/?\/?www\.youtube\.com\/watch\?v=([^&]*).*/i;
			var contenidoTexto = contenido.text().trim();
			var idVideoYoutube = regexYoutube.exec(contenidoTexto);
			if (idVideoYoutube != null) {
				var urlVideoYoutube = "http://www.youtube.com/embed/" + idVideoYoutube[1];
				var htmlYoutube = '<div><iframe width="560" height="455" src="' + urlVideoYoutube + '" frameborder="0" allowfullscreen></iframe></div>';
				contenido.html(
					contenidoTexto.replace(regexYoutube, htmlYoutube)
				);
			}
		}
	});
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
	
	$response.find('a.twitter-share-button').each(function() {
		twttr.widgets.load($(this).get(0));		
	});
}

function comentarioEliminar() {
	return confirm('Esta seguro que desea eliminar este comentario ?');
}