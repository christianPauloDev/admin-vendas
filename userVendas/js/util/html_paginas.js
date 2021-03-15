var time = -250;

function cardProduto(card, icon_rotate = '', collapse_status = '') {
    var produtos = card.produtos;
    var detalhes = "";

    $.each(produtos, function (idx, value) {
        detalhes += cardDetalhes(value, card.nome_categoria);
    });

    time += 250;

    setTimeout(() => {
        $(`#accordion_${card.id_categoria}`).show("slow");
    }, time);

    return `
        <div class="coupon-accordion hide" id="accordion_${card.id_categoria}">   
            <div type='button' class="categoria" data-toggle="collapse" data-target="#collapse${card.id_categoria}" 
            aria-expanded="false" aria-controls="collapse${card.id_categoria}" onClick="$('#icon-collapse${card.id_categoria}').toggleClass('fa-rotate-90')">
                <div class="row justify-content-between">
                    <div class="col">
                        <span>${card.nome_categoria}</span>
                    </div>
                    <div class="col text-right">
                        <button type="button" class="cat-chevron">
                            <i class="fa fa-chevron-right ${icon_rotate}" id="icon-collapse${card.id_categoria}" style="transition: 0.5s;"></i>
                        </button> 
                    </div>
                </div>
            </div>
            
            <div class="collapse ${collapse_status}" id="collapse${card.id_categoria}">
                ${detalhes}
            </div>		
        </div>
    `;
}

/*=====================================================*//*=====================================================*/

function cardDetalhes(produto, nome_categoria) {
    var titulo = nome_categoria + " - " + produto.nome_produto;

    var img = (produto.img) ? `../api-front/empresa/views/img/produtos/${produto.img}` : 'assets/img/cart/4.jpg';

    return `
        <div class="card shadow-sm card-body mb-2" style="padding: 1.2em;">
            <div class="row">
                <div class="col-lg-2 col-md-2 col-sm-2 col-3 text-center">
                    <img src="${img}" class='img-fluid img-thumbnail img-product-max' alt="">
                </div>
                <div class="col-lg-10 col-md-10 col-sm-10 col-9">
                    <div class="row">
                        <div class="col-lg-8 col-md-8 col-sm-6 col-6 text-left">
                            <h5 class="text-uppercase" id="produto">
                                ${produto.nome_produto}
                            </h5>
                            <p id="descricao_${produto.id}">${produto.descricao}</p>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-6 col-6 text-right">
                            <h5 id="preco">${moeda(Number(produto.preco))}</h5>
                            <button class="btn btn-danger btn-sm mt-2" onclick="consultar(${produto.id}, '${titulo}', '${produto.preco}')">
                            Adicionar
                        </button>
                        </div>
                    </div>   
                </div>
            </div>            
        </div>
    `;
}



function exibirProdutoCarrinho(produto_car) {
    var produto = produto_car.produto;

    var dados_produtos = (JSON.stringify(produto_car)).replaceAll(`"`, '&');

    var div_obs = ``;

    if (produto_car.observacao != '') {
        div_obs = `
            <div class="font-weight-boldi mt-1">
                OBS: ${produto_car.observacao}
            </div>
        `;
    }

    var subtotal = Number(produto.preco);

    subtotal = produto_car.quantidade * subtotal;

    $("#produtos_carrinho").append(`
        <div class="border-bottom produtos-carrinho mt-3" id="produto_${numero_produto}">
            <div class="row">
                <div class="col-lg-8 col-md-8 col-sm-6 col-6 font-weight-boldi">
                    ${produto_car.quantidade}x ${produto.titulo}
                </div>
                <input type="hidden" class="produtosCarrinho" value="${dados_produtos}">
                <div class="col-lg-4 col-md-4 col-sm-6 col-6 text-right text-danger font-weight-boldi">
                    ${moeda(Number(produto.preco))}
                </div>
            </div>
            ${div_obs}
            <div class="row mt-1">
                <div class="col-lg-8 col-md-8 col-sm-6 col-6 font-weight-boldi text-danger">
                    Subtotal
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6 col-6 text-right text-danger font-weight-boldi">
                    ${moeda(subtotal)}
                </div>
            </div>
            <div class="text-center mb-2">
                <button class="btn btn-link text-danger btn-sm font-weight-boldi" 
                    onclick="removerProduto(${numero_produto}, ${subtotal})">
                    Remover
                </button>
            </div>
        </div>
    `);

    var subtotalAntigo = Number($("#vdd_subtotal_pedido").val());
    var desconto = $("#vdd_desconto_pedido").val();

    $("#vdd_subtotal_pedido").val(subtotalAntigo + subtotal);
    $("#subtotal_pedido").text(moeda(subtotalAntigo + subtotal));
    $("#total_pedido").text(moeda(totalDesconto(subtotalAntigo + subtotal, desconto)));
    numero_produto++;
}
