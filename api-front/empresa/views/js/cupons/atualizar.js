$("#editar").click(function () {
    var url = base_url + usuario + id + 'cupons/atualizar/' + id_cupom;

    var i = $(`#icone_edicao`);
    var button = $(this);
    var class_icone = mudancasAoFazerRequisicao(i, button);

    var dados = {
        titulo : $("#novo_titulo").val(),
        tipo_desconto : $("#novo_tipo_desconto").val(),
        valor_desconto : $("#novo_valor_desconto").val(),
        valor_minimo : $("#novo_valor_minimo").val(),
        data_validade : $("#novo_data_validade").val(),
        status : $("#novo_status").val()
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

                    var data = new Date(dados.data_validade);
                    var desconto = dados.tipo_desconto == "porcentagem" ? dados.valor_desconto + "%" : moeda(parseFloat(dados.valor_desconto));
                    var status = (dados.status == '1') ? ['#0fd9a7', 'VÁLIDO'] : ['#d90429', 'INVÁLIDO'];

                    $(`#cupom_${id_cupom} td:nth-child(2)`).text(dados.titulo);
                    $(`#cupom_${id_cupom} td:nth-child(3)`).text(desconto);
                    $(`#cupom_${id_cupom} td:nth-child(4)`).text(dados.valor_minimo);
                    $(`#cupom_${id_cupom} td:nth-child(5)`).text(data.toLocaleDateString());
                    $(`#cupom_${id_cupom} td:nth-child(6)`).text(status[1]).css('color', status[0]);

                    $("#modal_edicao").modal('hide');
                    break;
            }
        }
    });
})