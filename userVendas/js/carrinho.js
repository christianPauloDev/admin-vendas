if (localStorage.getItem('projeto_venda_carrinho') != null) {
    $.each(JSON.parse(localStorage.getItem('projeto_venda_carrinho')), function(idx, produtos) {
        exibirProdutoCarrinho(produtos);
    });
}


var numero_produto = 0;

var carrinho_usuario = {
    id_informacao_empresa : id_informacao_empresa.replace("/", ""), 
	id_usuario : id.replace("/", ""), 
    id_cupom : 0,
	desconto : 0, 
	id_forma_pagamento : 0,
	frete : "retirada", 
	valor_frete : 0,
	troco : 0,
	produtos : []
};

function buttonAlterarQuantidade(id_input, opc) {
    $(".btn-subtotal").prop("disabled", true);

    var qtd = Number($(id_input).text());

    if (opc == 'add') {        
        if (qtd <= 0) $(id_input).text(1);
        else $(id_input).text(qtd + 1);
    } else {
        if (qtd > 0) $(id_input).text(qtd - 1);
        else $(id_input).text(0);
    }

    var nova_qtd = Number($(id_input).text());
    
    produto_cart.quantidade = nova_qtd;

    atualizarSubTotal();
}


function atualizarSubTotal() {
    $("#preco_add_selecionados").text("0,00");

    var subtotal = Number(produto_cart.produto.preco);

    $.each(produto_cart.adicionais, function (idx, adicional) {
        subtotal += Number(adicional.quantidade * adicional.preco);
    });

    produto_cart.subtotal = subtotal * produto_cart.quantidade;    

    $("#preco_add_selecionados").text(moeda(produto_cart.subtotal));

    $(".btn-subtotal").prop("disabled", false);
}


function addProdutoCarrinho()
{    
    if (produto_cart.quantidade > 0) {
        produto_cart.quantidade = $("#qtd_produto").text();
        produto_cart.observacao = $("#observacao").val();
        
        exibirProdutoCarrinho(produto_cart);
        atualizarStorageCarrinho(false);
    
        $("#modal_adicional").modal('hide');

        alert('Produto adicionado ao carrinho!')
    }
}

$("#btn-add_produto_carrinho").click(function() {
    addProdutoCarrinho();
});

function removerProduto(numero_produto, subtotal)
{
    $(`#produto_${numero_produto}`).fadeOut('fast');

    $("#vdd_subtotal_pedido").val(Number($("#vdd_subtotal_pedido").val()) - Number(subtotal));

    var total = Number($("#vdd_subtotal_pedido").val());
    var frete = Number($("#vdd_frete").val());

    $("#subtotal_pedido").text( moeda(total) );

    $("#total_pedido").text( moeda( totalDesconto(total, frete, $("#vdd_desconto_pedido").val())) );

    setTimeout(() => {
        $(`#produto_${numero_produto}`).remove();
        atualizarStorageCarrinho(false);
    }, 500);
}

function removerTodosProdutos()
{
    numero_produto = 0;

    $("#vdd_subtotal_pedido").val(0);
    $("#subtotal_pedido").text( moeda(0) );
    $("#total_pedido").text( moeda(0) );

    $(`.produtos-carrinho`).fadeOut('fast');

    removerCupom();

    setTimeout(() => {
        $(`.produtos-carrinho`).remove();
        atualizarStorageCarrinho();
    }, 500);
}

function atualizarStorageCarrinho(limpar = true)
{
    if (limpar) {
        localStorage.removeItem('projeto_venda_carrinho');
    } else {
        var carrinho = [];

        $(".produtosCarrinho").each(function(idx, dados) {
            var dados_produto = JSON.parse(dados.value.replaceAll("&", `"`));
    
            (carrinho).push(dados_produto);
        });
        
        if (carrinho.length > 0)
            localStorage.setItem("projeto_venda_carrinho", JSON.stringify(carrinho));
        else 
            localStorage.removeItem('projeto_venda_carrinho');
    }
}


function totalDesconto(total, desconto)
{
    total = Number(total);
    
    if (total == 0 || total < valor_minimo_cupom) {
        removerCupom();
        $("#erro_cupom").text("Valor Insuficiente");

        return 0.00;
    }
    
    if (String(desconto).indexOf("%") == -1) {
        return total - Number(desconto);
    } else {
        desconto = Number(desconto.replace("%", ""));

        var novo_total = Number((total) * (100 - desconto)/100);

        $("#desconto_pedido").text('-'+moeda(total - novo_total));

        return novo_total;
    }
}