$("#editar_produto").click(function() {    
    var i = $(`#icone_edicao`);
    var button = $(this);
    var class_icone = mudancasAoFazerRequisicao(i, button);

    if ($(`#novo_img`)[0].files[0]) {
        var data = new FormData();
        data.append('img', $(`#novo_img`)[0].files[0]);
        
        $.ajax({
            headers,
            url: 'upload.php',
            data: data,
            processData: false,
            contentType: false,
            type: 'POST',
            
            success: function(retorno) 
            {   
                var retorno = JSON.parse(retorno);
                var img = null;
                
                switch (retorno.tipo) {
                    case sucesso:
                        img = retorno.resposta;
                        editar(img, button, i, class_icone, true)
                        break;

                    default:
                        editar(img, button, i, class_icone, true)
                        break;
                }
            },
            error: function()
            {
                editar(null, button, i, class_icone, true)
            }
        });
    } else {
        editar(null, button, i, class_icone)
    }

});


function editar(img, button, i, class_icone, upload = false) {
    var dados = {
        nome_produto : $(`#novo_nome_produto`).val(),
        preco : $(`#novo_preco`).val(),
        id_categoria : $(`#novo_id_categoria_fk`).val(),
        descricao : $(`#novo_descricao`).val(),
        status : $(`#novo_status`).val(),
        img : (img) ? img : img_antiga,
    };

    var url = base_url + usuario + id + 'produtos/atualizar/' + id_produto;

    $.ajax({
        headers,
        url,
        type: 'PUT',
        data: JSON.stringify(dados),
        
        success: function(retorno) {
            var message = retorno.resposta;

            button.prop('disabled', false);
            i.removeClass().addClass(class_icone);  

            switch (retorno.tipo) {
                case erro:
                    $.growl.error( {message} );
                    if (img) apagar_img(img);
                    break;

                case atencao:
                    $.growl.warning( {message} );
                    if (img) apagar_img(img);
                    break;

                case sucesso:
                    $.growl.notice( {message} );                

                    $(`#produto_${id_produto} td:nth-child(2) img`).attr('src', `views/img/produtos/${dados.img}`);
                    $(`#produto_${id_produto} td:nth-child(3)`).text(dados.nome_produto)
                    $(`#produto_${id_produto} td:nth-child(4)`).text($("#novo_id_categoria_fk option:selected").text())
                    $(`#produto_${id_produto} td:nth-child(5)`).text((Number(dados.preco)).toLocaleString('pt-br', {style: 'currency', currency: 'BRL'}))
                    $(`#produto_${id_produto} td:nth-child(6)`).text((dados.status).toUpperCase())
                    $(`#novo_img`).val('');
                    
                    $("#modal_edicao").modal('hide');

                    if (upload && !img) $.growl.warning( {message : "Somente não foi possível salvar a nova imagem!"} );
                    else if (upload) apagar_img(img_antiga);

                    break;
            }
        }
    });
}

function apagar_img(img)
{
    $.ajax({
        headers,
        url: 'upload.php',
        type: 'GET',
        data: {apagar_img:1, img},
        
        success: function(retorno) {
        }
    });
}