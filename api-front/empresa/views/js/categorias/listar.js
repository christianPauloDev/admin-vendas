var id_categoria;

/*=========================================*//*=========================================*/

function listar() 
{
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
                $("#tbody_categorias").html("");

                var categorias = retorno.resposta;
                
                $.each(categorias, function (idx, categoria) {
                    $("#tbody_categorias").append(categoriasTabela(categoria));
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

    var url = base_url + usuario + id + 'categorias/listar/' + id_consulta;

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
                id_categoria = id_consulta;

                var categoria = retorno.resposta;
                
                $.each(categoria, function (idx, informacao) {
                    $(`#novo_${idx}`).val(informacao);
                });

                $("#modal_edicao").modal('show');
                break;
        }
    });
}

listar();