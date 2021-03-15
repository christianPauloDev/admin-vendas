function cadastrar() {
    var dados = {
        titulo : $("#titulo").val()
    };

    var url = base_url + usuario + id + 'categorias/cadastrar/';
    
    var i = $(`#icone_cadastrar`);
    var button = $(`#cadastrar_categoria`);
    var class_icone = mudancasAoFazerRequisicao(i, button);

    $.ajax({
        headers,
        url,
        method: 'POST',
        data: JSON.stringify(dados) 
    }) 
    .done(function( retorno ) { 
        var message = retorno.resposta;
        
        button.prop('disabled', false);
        i.removeClass().addClass(class_icone);  

        switch (retorno.tipo) {
            case erro:
                $.growl.error( {message} );
                break;

            case atencao:
                $.growl.warning( {message} );
                break;

            case sucesso:
                $.growl.notice( {message : "Registro inserido com sucesso!"} );
                
                $('#dataTable').DataTable().destroy();
                listar();

                $("#titulo").val("").focus();
                break;
        }

    });
}

$('#cadastrar_categoria').click(function() {
    cadastrar();
});

validarEnter();