var id_produto;
var ids_grupos = [];
var img_antiga;

function listarCategorias() {
    $(".box-icone-carregando").show();

    var url = base_url + usuario + id + 'categorias/listar/';

    $.ajax({
        headers,
        url,
        method: 'GET' 
    }) 
    .done(function( retorno ) { 
        $(".box-icone-carregando").fadeOut('slow');

        switch (retorno.tipo) {
            case erro:
                $(".select_categorias").html(
                    `<option value="">${retorno.resposta}</option>`
                );
                break;

            case atencao:
                $(".select_categorias").html(
                    `<option value="">${retorno.resposta}</option>`
                );
                break;

            case sucesso:
                $(".select_categorias").html(`
                    <option value="" disabled selected>Selecione uma Categoria</option>
                `);

                var categorias = retorno.resposta;

                $.each(categorias, function (idx, categoria) {
                    $(".select_categorias").append(`
                        <option value="${categoria.id}">${categoria.titulo}</option>
                    `);
                });

                $("#filtro_categoria option:first").html(`
                    <option value="" selected>Todas</option>
                `).prop('disabled', false);
                break;
        }
    });
}

/*=========================================*/
/*=========================================*/

function listar() {
    $(".box-icone-carregando").show();

    var filtro_status = ($("#filtro_status").val()).toLowerCase();

    var url = base_url + usuario + id + 'produtos/listar/' + filtro_status;

    if ($("#filtro_categoria").val() > 0)
        url = base_url + usuario + id + 'produtos/listarPelaCategoria/' + filtro_status + '/' + $("#filtro_categoria").val();

    $.ajax({
        headers,
        url,
        method: 'GET' 
    }) 
    .done(function( retorno ) { 
        $(".box-icone-carregando").fadeOut('slow');
        $("#tbody_produtos").html("");

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
                var produtos = retorno.resposta;

                $.each(produtos, function (idx, produto) {
                    $("#tbody_produtos").append(produtosTabela(produto));
                });

                $('#dataTable').DataTable();

                break;
        }
    });
}

/*=========================================*/
/*=========================================*/

function consultar(id_consulta) {
    $(".box-icone-carregando").show();

    var url = base_url + usuario + id + 'produtos/listar/' + id_consulta;

    $.ajax({
        headers,
        url,
        method: 'GET' 
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
                $(`#novo_img`).val();

                id_produto = id_consulta;

                var produto = retorno.resposta;

                $.each(produto, function (idx, informacao) {
                    if (idx != 'img') $(`#novo_${idx}`).val(informacao);
                });

                img_antiga = produto.img;

                $("#modal_edicao").modal('show');
                break;
        }
    });
}

listarCategorias();
listar();

/*================================================*/
/*================================================*/

$("#btn-filtros").click(function () {
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

$("#filtro_categoria").change(function () {
    $('#dataTable').DataTable().destroy();
    listar();
    $("#id_categoria").val($(this).val());
});

$("#filtro_status").change(function () {
    $('#dataTable').DataTable().destroy();
    listar();
    $("#status").val($(this).val());
});