const auth_url = '../../api-backend/';

function validar_login(login = false)
{    
    $.ajax({
        url : auth_url.concat(`empresa/validar_token/${localStorage.getItem('projeto_venda_token_JWT')}`),
        method: 'GET',   
    }) 
    .done(function( retorno ) {
        if (retorno.tipo == 'erro') {
            if (!login) {
                redirectLogout();
            }
        } else if (login) {
            window.location = 'home';
        }
    });
}

function redirectLogout() 
{
    window.location = 'entrar';
    localStorage.removeItem('projeto_venda_token_JWT');
}


function logar() 
{
    var button = $("#btn-login");

    var dados = {
        login : $("#email_usuario").val(),
        senha : $("#senha_usuario").val()
    };

    var i = $(`#icon-login`);

    button.prop('disabled', true);
    i.removeClass().addClass('fas fa-sync-alt fa-spin');

    $.post(auth_url.concat("empresa/login"), JSON.stringify(dados), function(retorno) {
        button.prop('disabled', false);
        i.removeClass().addClass(`fas fa-sign-in-alt`);

        if (retorno.tipo == 'erro') {
            $.growl.error( {message : retorno.resposta} );
        } else {
            $("#email_usuario").val('');
            $("#senha_usuario").val('');

            var dados = retorno.resposta;

            localStorage.setItem("projeto_venda_token_JWT", dados.JWT_token);

            $.growl.notice( {message : 'Login realizado!'} );

            window.location = 'home';
        }
    });
}

$("#btn-login").click(function() { logar(); });

document.addEventListener('keyup', (e) => {
    if (e.keyCode === 13) {
        logar();
    } 
});