function deletar(id_delete)
{
    id_categoria = id_delete;

    $("#modal_delete").modal('show');
}

$("#deletar").click(function () {
    var url = base_url + usuario + id + 'categorias/deletar/' + id_categoria;
    var i = $(`#icone_deletar`);
    var button = $(this);
    var class_icone = mudancasAoFazerRequisicao(i, button);

    $.ajax({
        headers,
        url,
        type: 'DELETE',
        
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
                    deletarRegistro($(`#categoria_${id_categoria}`));
                    break;
            }

            $("#modal_delete").modal('hide');
        }
    });
})