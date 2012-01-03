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

function textChange(areaTexto, areaCount) {
    $(areaCount).text(
    	555 - $(areaTexto).val().length
    );
}

function getDataVideo(idVideo) {
	var respuesta = new Object();
	urlApiYoutube = "http://gdata.youtube.com/feeds/api/videos?v=2&alt=jsonc&q=" + idVideo;
	
	$.ajax({
		  url: urlApiYoutube,
		  async: false,
		  success: function(response) {			  
			  if ($.type(response) == 'string') {
				  response = $.parseJSON(response);
			  }			  
			  if (response.data != null) {				  				  
				  if (response.data.items != null && response.data.items.length > 0) {
					  datosVideo = response.data.items[0];
					  respuesta.titulo = datosVideo.title;
					  respuesta.descripcion = datosVideo.description;
					  respuesta.thumbnail = datosVideo.thumbnail.sqDefault;
					  respuesta.urlVideo = 'http://www.youtube.com/watch?v=' + idVideo;
				  }
			  }	    
		  }
	});
	return respuesta;
} 

function getHtmlDataVideoPost(data){
	var id = 0;
	var regexYoutube = /www\.youtube\.com\/watch\?[^v]*v=([^&]{11})/ig;	
	var urlYoutube = regexYoutube.exec(data.urlVideo);
	if (urlYoutube != null)
		id = urlYoutube[1];
		
	return "<div class='TituloVideo'><a target='_blank' href='" + data.urlVideo + "' alt='" + data.titulo + "'>" + data.titulo + "</a></div>" +
	"<div class='ThumbnailVideo'><a href='javascript:void(0)'><img src='" + data.thumbnail + "' alt='" + data.titulo + "' /></a>" +
	"</div><div class='DescripcionVideo'>" + data.descripcion + "</div><input type='hidden' id='youtubeI' value='" + id + "' />";
}

function getHtmlDataVideo(data){
	return "<div class='TituloVideo'>" + data.titulo + "</div><div id='eliminarVideo'>X</div>" +
	"<div class='ThumbnailVideo'><img src='" + data.thumbnail + "' alt='" + data.titulo + "' />" +
	"</div><div class='DescripcionVideo'>" + data.descripcion + "<div class='UrlVideo'>" + data.urlVideo + "</div></div>";
}

function findVideo() {
	if ($('#video').html() == '') {		
		var areaText = $('#contenido_texto').val();
		var regexYoutube = /www\.youtube\.com\/watch\?[^v]*v=([^&]{11})/ig;
		
		var urlYoutube = regexYoutube.exec(areaText);
		if (urlYoutube != null) {
			var data = getDataVideo(urlYoutube[1]);
			var html = getHtmlDataVideo(data);
			
			$('#video').html(html);
			$('#video').show();
			$('#contenido_url_video').val(data.urlVideo);
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
	$('#contenido_url_video').removeAttr('value');
	$('#contenido_categoria option').each(function() {  
		if ($(this).attr("disabled")) {				
			$(this).removeAttr("disabled");
		}
	});
	$('#contenido_categoria').val("");
}

function getHtmlEmbedVideo(idVideo) {
	var urlVideoYoutube = "http://www.youtube.com/embed/" + idVideo + '?fs=1&autoplay=1';
	var htmlYoutube = '<div><iframe width="560" height="250" src="' + urlVideoYoutube + '" frameborder="0" allowfullscreen></iframe></div>';
	return htmlYoutube;
}

function renderizarVideosPost($html) {
	var posts = $html;
	if ($html == null) {
		$html = $(document);
		posts = $html.find('.Post');
	}
	posts.each(function() {
		var contenido = $(this).find('.Contenido');
		var urlVideo = $(this).find('.UrlVideo').val();
		if (urlVideo != null && urlVideo != '') {
			var regex = /[\?&]+v=([^&]{11})/i;
			var idVideoR = regex.exec(urlVideo);
			if (idVideoR.length == 2) {
				var idVideo = idVideoR[1];					
				var data = getDataVideo(idVideo);
				contenido.html(
						contenido.html() + 
						'<div class="VideoData" style="float:left;">' + 
						getHtmlDataVideoPost(data) +
						'</div>'
				);
				contenido.find('.ThumbnailVideo a').click(function(){				
					contenido.find('.VideoData').fadeOut(
						300, 
						function() {
							$(this).remove();
							contenido.html(contenido.html() + getHtmlEmbedVideo(idVideo));
						}
					);
				});
			}
		}
	});	
} 

function renderizarVideos() {
	$('.Post').each(function(){
		if ($(this).find('.TituloTexto').text().indexOf("Video") != '-1') {
			var contenido = $(this).children('.Contenido');
			var regexYoutube = /h?t?t?p?:?\/?\/?www\.youtube\.com\/watch\?[^v]*v=([^&]{11}).*/i;
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
    //$('.PostLoading').html('<img src="bundles/mondelcuentalo/img/ajax-loader.gif"/>');
    $('#masContenidos').hide();
    var cid = $("#cid").val();
    var lastId = $(".Post:last").attr("id");
    var urlContenidos = "contenido/" + cid + "/" + lastId + "/5";
    var response = '';    
    $.ajax({
    	url: urlContenidos,
    	//dataType: 'json', //todavia no esta pronto para recibir json
    	async: true,
    	success: function(response) {
    		if (response != "") {
    			var lastPost = $(".Post:last");
    			lastPost.after(response);
    			var newPosts = lastPost.nextAll();
			  	asignarOnClickVerComentarios(newPosts);
			  	actualizarBotones(newPosts);
			  	renderizarVideosPost(newPosts);
		  	}
	        //$('.PostLoading').empty();
	        $('#masContenidos').show();
	        //window.isScrolling = false;
    	}
	});
};

function actualizarBotones($response) {
	
	if( $response.length < 1 ) return;
	
	$response.each(function(){
		gapi.plusone.go(
				$(this).attr('id')		
		);
		FB.XFBML.parse(
				$(this).get(0)
		);
		twttr.widgets.load(
				$(this).find('a.twitter-share-button').eq(0).get(0)
		);
	});
}

function comentarioEliminar() {
	return confirm('Esta seguro que desea eliminar este comentario ?');
}

function asignarOnHoverComentarioEliminar(selComentario, selItem) {
	$(selComentario).hover(
    		function() {
				$(this).find(selItem).css("display", "block");    	
    		},
			function() {
    			$(this).find(selItem).css("display", "none");    	
    		}
	);   	
}

function asignarOnClickVerComentarios($html) {	
	if ($html == null) {
		$html = $(document);
	}
	$html.find('.VerComentarios').click(
		function (){
			$(this).parents('.Comentarios').children('.Comentario').removeClass('Hidden');
			$(this).remove();
		}
	);
}