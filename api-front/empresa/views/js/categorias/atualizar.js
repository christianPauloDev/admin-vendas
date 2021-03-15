$("#editar").click(function () {
    var url = base_url + usuario + id + 'categorias/atualizar/' + id_categoria;

    var i = $(`#icone_edicao`);
    var button = $(this);
    var class_icone = mudancasAoFazerRequisicao(i, button);

    var dados = {
        titulo : $("#novo_titulo").val()
    };

    $.ajax({
        headers,
        url,
        type: 'PUT',
        data: JSON.stringify(dados),
        
        success: function(retorno) {
            button.prop('disabled', false);
            i.removeClass().addClass(class_icone);

            var message = retorno.resposta;

            switch (retorno.tipo) {
                case erro:
                    $.growl.error( {message} );
                    break;
    
                case atencao:
                    $.growl.warning( {message} );
                    break;
    
                case sucesso:
                    $.growl.notice( {message} );

                    $(`#categoria_${id_categoria} td:nth-child(2)`).text(dados.titulo);

                    $("#modal_edicao").modal('hide');
                    break;
            }
        }
    });
})