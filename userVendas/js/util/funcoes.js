function redirectLogout() 
{
    window.location = 'entrar';
    localStorage.removeItem('projeto_venda_token_JWT');
}

/*=====================================================*//*=====================================================*/

function moeda(valor)
{
    return (valor).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
}

/*=====================================================*//*=====================================================*/

function mudancasAoFazerRequisicao(i, button) 
{
    var classe = i.attr('class');

    button.prop('disabled', true);
    i.removeClass().addClass('fas fa-sync-alt fa-spin');

    return classe;
}