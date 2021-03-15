var produtos_array;
var valor_minimo_cupom = 0;

const modelo_cart = {
    produto: {
        id: 0,
        titulo: "",
        preco: 0
    },
    observacao: "",
    quantidade: 0,
    subtotal: 0
};

var produto_cart = modelo_cart;

/*=========================================*/
/*=========================================*/

function listar() {
    $(".box-icone-carregando").show();

    var url = base_url + usuario + '//produtos/listar/' + id_informacao_empresa;

    $.ajax({
        url,
        method: 'GET',
    })
        .done(function (retorno) {
            $(".box-icone-carregando").fadeOut('slow');
            $("#card_produtos").html("");

            switch (retorno.tipo) {
                case erro:
                    // $.growl.error({
                    //     message: retorno.resposta
                    // })
                    break;

                case atencao:
                    // $.growl.warning({
                    //     message: retorno.resposta
                    // })
                    break;

                case sucesso:
                    var pedidos = retorno.resposta;
                    produtos_array = pedidos;

                    $.each(pedidos, function (idx, card) {
                        $("#card_produtos").append(cardProduto(card));
                    });
                    break;
            }
        });
}

/*=========================================*/
/*=========================================*/

function consultar(id_consulta, titulo, preco) {
    $(".box-icone-carregando").show();

    $("#modal_adicional").modal('show');

    var descricao = $('#descricao_' + id_consulta).text();

    var img = '';

    var url = base_url + usuario + '//produtos/listar/' + id_consulta;

    $.ajax({
        url,
        method: 'GET'
    })
        .done(function (retorno) {
            $(".box-icone-carregando").fadeOut('slow');

            switch (retorno.tipo) {
                case erro:
                    $.growl.error({
                        message: retorno.resposta
                    })
                    break;

                case atencao:
                    $.growl.warning({
                        message: retorno.resposta
                    })
                    break;

                case sucesso:
                    produto_cart = modelo_cart;

                    produto_cart.produto.id = id_consulta;
                    produto_cart.produto.titulo = titulo;
                    produto_cart.produto.preco = preco;
                    produto_cart.quantidade = 1;

                    img = (retorno.resposta.img) ? `../api-front/empresa/views/img/produtos/${retorno.resposta.img}` : 'assets/img/cart/4.jpg';

                    $('#modal_adicional_titulo').text(titulo);
                    $('#description_modal').text(descricao);
                    $("#observacao").val('');
                    $("#preco_add_selecionados").text(moeda(Number(preco)));
                    $("#qtd_produto").text(1);
                    $("#img_modal").prop('src', img);
                    break;
            }
        });
}

/*================================================*/
/*================================================*/

listar();

$('#pesquisar_produto').keyup(function () {
    var campo_pesquisa = ($('#pesquisar_produto').val()).toUpperCase();
    var pesquisa_local = produtos_array;
    var collapse = campo_pesquisa != "" ? "show" : "";
    var icon_rotate = campo_pesquisa != "" ? "fa-rotate-90" : "";

    $("#card_produtos").html("");

    $.each(pesquisa_local, function (idx, categorias) {
        var resultado = categorias.produtos.filter(function (produto) {
            return (produto.nome_produto).toUpperCase().indexOf(campo_pesquisa) > -1;
        });

        var card = {
            id_categoria: categorias.id_categoria,
            nome_categoria: categorias.nome_categoria,
            produtos: resultado
        }

        if (resultado.length > 0)
            $("#card_produtos").append(cardProduto(card, icon_rotate, collapse));
    });
});

/*=========================================*/
/*=========================================*/

$("#btn-cupom_pesquisa").click(function () {
    if ($(this).val() == 1) {
        $("#btn-cupom_pesquisa").prop('disabled', true).hide();
        $("#carregamento").addClass('lds-ellipsis');

        var titulo = $("#cupom_pesquisa").val();

        var url = base_url + usuario + id + 'cupons/validarParaUsuario' + id_informacao_empresa + '/' + titulo.replace(" ", "&-&");

        $.ajax({
            url,
            method: 'GET',
        })
            .done(function (retorno) {
                $("#erro_cupom").text("");
                $("#carregamento").removeClass('lds-ellipsis');
                $("#btn-cupom_pesquisa").prop('disabled', false).show();

                switch (retorno.tipo) {
                    case erro:
                        $("#btn-cupom_pesquisa").text('Aplicar');
                        $("#erro_cupom").text(retorno.resposta);
                        break;

                    case atencao:
                        $("#btn-cupom_pesquisa").text('Aplicar');
                        $("#erro_cupom").text(retorno.resposta);
                        break;

                    case sucesso:
                        var total = Number($("#vdd_subtotal_pedido").val());
                        var cupom = retorno.resposta;

                        if (total < cupom.valor_minimo) {
                            $("#erro_cupom").text("Valor Insuficiente");
                        } else {
                            valor_minimo_cupom = Number(cupom.valor_minimo);

                            var valor_desconto = cupom.valor_desconto;
                            var desconto = valor_desconto

                            if (cupom.tipo_desconto != 'dinheiro') {
                                valor_desconto += '%';

                                desconto = total - (Number((total) * (100 - Number(desconto)) / 100));
                            }

                            $("#total_pedido").text(moeda(Number(totalDesconto(total, valor_desconto))));

                            $("#div-cupom").removeClass("hide");

                            $("#desconto_pedido").text('-' + moeda(Number(desconto)));
                            $("#vdd_desconto_pedido").val(valor_desconto);

                            $("#cupom_pesquisa").prop("disabled", true);
                            $("#btn-cupom_pesquisa").val(0).text('Remover');

                            carrinho_usuario.id_cupom = cupom.id;
                        }

                        break;
                }
            });
    } else {
        removerCupom();
    }
});

function removerCupom() {
    var total = Number($("#vdd_subtotal_pedido").val());

    $("#total_pedido").text(moeda(total));

    $("#desconto_pedido").text('-' + moeda(0));
    $("#vdd_desconto_pedido").val(0);

    $("#cupom_pesquisa").prop("disabled", false);
    $("#btn-cupom_pesquisa").val(1).text('Aplicar');
    $("#div-cupom").addClass("hide");

    carrinho_usuario.id_cupom = 0;
}