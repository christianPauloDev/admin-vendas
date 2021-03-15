function cadastrar() {
    var dados = {
        titulo : $("#titulo").val(),
        tipo_desconto : $("#tipo_desconto").val(),
        valor_desconto : $("#valor_desconto").val(),
        valor_minimo : $("#valor_minimo").val(),
        data_validade : $("#data_validade").val()
    };

    var url = base_url + usuario + id + 'cupons/cadastrar/';
    
    var i = $(`#icone_cadastrar`);
    var button = $(`#cadastrar_cupom`);
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
                $("#data_validade").val(""),
                $("#valor_desconto").val(""),
                $("#valor_minimo").val("")
                break;
        }

    });
}

$('#cadastrar_cupom').click(function() {
    cadastrar();
});

validarEnter();