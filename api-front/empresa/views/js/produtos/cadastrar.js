function limpar_arquivo(novo = '') 
{
    $(`#${novo}label_img`).html("<i class='fas fa-upload'></i> Escolher Arquivo");
    $(`#${novo}img`).val(""); 
    $(`#${novo}btn-limpar`).hide('fast');    
}

function validarImagem(novo = '') {    
    if ($(`#${novo}img`).val() != '') {
        var extensoes = ['png', 'jpg', 'jpeg'];
        var input = document.getElementById(`${novo}img`);
        var nome_quebrado = input.files[0].name.split('.');
        var extension = nome_quebrado[nome_quebrado.length-1];
        
        if (extensoes.indexOf(extension.toLowerCase()) > -1) {
            $(`#${novo}label_img`).html(input.files[0].name);
            $(`#${novo}btn-limpar`).show('fast');  
        } else {
            $.growl.warning( {message : "Arquivo inválido"} );

            limpar_arquivo();
        }
    } else {
        limpar_arquivo();
    }
}

function cadastrarProduto() {
        
    var i = $(`#icone_cadastrar`);
    var button = $("#cadastrar_produto");
    var class_icone = mudancasAoFazerRequisicao(i, button);

    if ($(`#img`)[0].files[0]) {
        var data = new FormData();
        data.append('img', $(`#img`)[0].files[0]);
        
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
                        cadastrar(img, button, i, class_icone, true)
                        break;

                    default:
                        cadastrar(img, button, i, class_icone, true)
                        break;
                }
            },
            error: function()
            {
                cadastrar(null, button, i, class_icone, true)
            }
        });
    } else {
        cadastrar(null, button, i, class_icone)
    }
}


function cadastrar(img, button, i, class_icone, upload = false) {
    var dados = {
        nome_produto : $(`#nome_produto`).val(),
        preco : $(`#preco`).val(),
        id_categoria : $(`#id_categoria`).val(),
        descricao : $(`#descricao`).val(),
        status : $(`#status`).val(),
        img,
    };
    
    var url = base_url + usuario + id + 'produtos/cadastrar/';
    
    $.ajax({            
        headers,
        url,
        method: 'POST',
        data: JSON.stringify(dados) 
    }) 
    .done(function( retorno ) { 
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
                $.growl.notice( {message : "Registro inserido com sucesso!"} );
                
                $('#dataTable').DataTable().destroy();
                listar();
                
                limpar_arquivo();
                $(`#nome_produto`).val("").focus();
                $(`#preco`).val("");
                $(`#descricao`).val("");

                if (upload && !img) $.growl.warning( {message : "Somente não foi possível salvar a imagem!"} );

                break;
        }
    });
}

$("#cadastrar_produto").click(function() {
    cadastrarProduto();
});

validarEnter('cadastrarProduto');