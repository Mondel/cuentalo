function validaContenido(input, min_length, max_length, errors, form) {
    var error = '';
    if (input.val().length < min_length)
        error = 'El contenido de tu mensaje debe ser mayor a ' + min_length;
    else if (input.val().length > max_length)
        error = 'El contenido de tu mensaje debe ser menor a ' + max_length;

    if(error == '')
        form.submit();
    else
        errors.text(error);
}