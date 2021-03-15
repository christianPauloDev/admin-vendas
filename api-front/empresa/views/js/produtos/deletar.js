function deletar(id_delete)
{
    id_produto = id_delete;

    $("#modal_delete").modal('show');
}

$("#deletar").click(function () {
    var i = $(`#icone_deletar`);
    var button = $(this);
    var class_icone = mudancasAoFazerRequisicao(i, button);

    var url = base_url + usuario + id + 'produtos/deletar/' + id_produto;

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
                    var url_img = ($(`#produto_${id_produto} img`).attr('src')).split('/');
                    var img = url_img[url_img.length - 1];
                    
                    if (img != img_default) apagar_img(img);

                    $.growl.notice( {message} );    
                    deletarRegistro($(`#produto_${id_produto}`));
                    break;
            }

            $("#modal_delete").modal('hide');
        }
    });
    
})