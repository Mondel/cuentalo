$(document).ready(function(){
    
    asignarOnSubmitComentarAjax('/comentar/ajax/0');

    asignarOnHoverComentarioEliminar('.Comentario', '.EliminarItem');

    renderizarVideosPost();
    
});