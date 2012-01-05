$(document).ready(function(){   
	
    /*
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
	*/

    var searcher = new Worker('js/searcher.js');    

    function search(query) {
        searcher.postMessage(query);
    }

     searcher.onmessage = function (event) {
        var lastPost = $(".Post:last");
        lastPost.after(event.data);
        $('html,body').animate({scrollTop : lastPost.position().top}, 'slow');
        var newPosts = lastPost.nextAll();        
        
        asignarOnClickVerComentarios(newPosts);
        //actualizarBotones(newPosts);
        //renderizarVideosPost(newPosts);
    }


    $('#masContenidos').click(function(){
        //obtenerContenidos();
        var cid = $("#cid").val();
        var lastId = $(".Post:last").attr("id");
        var urlContenidos = "contenido/" + cid + "/" + lastId + "/5";

        search('../' + urlContenidos);
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