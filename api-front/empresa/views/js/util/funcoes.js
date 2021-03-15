var horarios_restauracao = {};

function deletarRegistro(registro) 
{
    registro.fadeOut('slow');

    setTimeout(() => { registro.remove(); atualizarInforDataTable(); }, 1000);
}

/*=====================================================*//*=====================================================*/

function corStatus(id) 
{
    var select = $(`#${id}`);
    var icone = $(`#icone_${id}`);
    
    if (select.val() == 'ativo') {
        select.addClass('is-valid').removeClass('is-invalid');
        icone.removeClass().addClass('fas fa-toggle-on');
    } else {
        select.removeClass('is-valid').addClass('is-invalid');
        icone.removeClass().addClass('fas fa-toggle-off');
    }
}

/*=====================================================*//*=====================================================*/

function mudancasAoFazerRequisicao(i, button) 
{
    var classe = i.attr('class');

    button.prop('disabled', true);
    i.removeClass().addClass('fas fa-sync-alt fa-spin');

    return classe;
}

/*=====================================================*//*=====================================================*/

function atualizarInforDataTable()
{
    var infor_datatable = ($("#dataTable_info").text()).split(" ");
        
    if (infor_datatable[3] == '1') {
        $('#dataTable').DataTable().destroy();
        listar();
    } else {
        infor_datatable[3] = parseInt(infor_datatable[3]) - 1;
        infor_datatable[5] = parseInt(infor_datatable[5]) - 1;

        $("#dataTable_info").text(infor_datatable.join(' '));
    }
}

/*=====================================================*//*=====================================================*/

function moeda(valor)
{
    return (valor).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
}
/*=====================================================*//*=====================================================*/

// Caso o nome seja padrão não passar parâmetro
function validarEnter(nome_funcao = 'cadastrar')
{
    $("#modal_cadastro").keypress(function(e) {
        var element = $(":focus")[0];
        if (e.keyCode === 13 && element.tagName != "SELECT" && element.tagName != "TEXTAREA" && element.tagName != "BUTTON"
            && element.type != 'file' && ($("#modal_cadastro").attr("class")).indexOf('show') > -1) {
                window[nome_funcao]();
        } 
    });
}