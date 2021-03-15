var id_cupom;

/*=========================================*//*=========================================*/

function listar() 
{
    $(".box-icone-carregando").show();

    var url = base_url + usuario + id + 'cupons/listar/';

    if ($("#filtro_tipo").val() != "") 
        url = base_url + usuario + id + 'cupons/listar//' + $("#filtro_tipo").val();

    $.ajax({
        headers,
        url,
        method: 'GET',
    }) 
    .done(function( retorno ) {
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
                $("#tbody_cupons").html("");

                var cupons = retorno.resposta;
                
                $.each(cupons, function (idx, cupom) {
                    $("#tbody_cupons").append(cuponsTabela(cupom));
                });

                $('#dataTable').DataTable();
                break;
        }
    });
}

/*=========================================*//*=========================================*/

function consultar(id_consulta) 
{
    $(".box-icone-carregando").show();

    var url = base_url + usuario + id + 'cupons/listar/' + id_consulta;

    $.ajax({
        headers,
        url,
        method: 'GET', 
    }) 
    .done(function( retorno ) {       
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
                id_cupom = id_consulta;

                var cupom = retorno.resposta;
                
                $.each(cupom, function (idx, informacao) {
                    $(`#novo_${idx}`).val(informacao);
                });

                if(cupom.tipo_desconto == "porcentagem")
                    $('#novo_span_desconto').text('%');
                else
                    $('#novo_span_desconto').text('R$');

                $("#modal_edicao").modal('show');
                break;
        }
    });
}

listar();

/*=========================================*//*=========================================*/

$('#tipo_desconto').change(function() {
    if($(this).val() == "dinheiro")
        $('#span_desconto').text('R$');
    else
        $('#span_desconto').text('%');
});

$('#novo_tipo_desconto').change(function() {
    if($(this).val() == "dinheiro")
        $('#novo_span_desconto').text('R$');
    else
        $('#novo_span_desconto').text('%');
});

/*================================================*//*================================================*/

$("#btn-filtros").click(function() {
    if ($(this).val() == 'mostrar') {
        $(this).val('esconder').addClass('btn-outline-danger').removeClass('btn-danger');
        $("#i-filtros").addClass('fa-reply').removeClass('fa-filter');
        $("#filtros").show('fast');
    } else {
        $(this).val('mostrar').removeClass('btn-outline-danger').addClass('btn-danger');
        $("#i-filtros").removeClass('fa-reply').addClass('fa-filter');
        $("#filtros").hide('fast');
    }
});

$('#filtro_tipo').change(function() {
    $('#dataTable').DataTable().destroy();
    listar();
});