var logado = false;
var i = "/";

var dados_usuario = { valor_frete : 0 };

if (localStorage.getItem("projeto_venda_token_JWT") != null) {
    const payload = (localStorage.getItem("projeto_venda_token_JWT")).split('.')[1];
    
    var dados_token = JSON.parse(atob(payload));
    
    if (dados_token.dados.app) {
        logado = true;

        var dados_usuario = dados_token.dados;
        dados_usuario.valor_frete = Number(dados_usuario.valor_frete);
        i = dados_usuario.id+'/';
        
        $("#div-frete").removeClass("hide");
        $("#frete").text( moeda( Number(dados_usuario.valor_frete) ) );
        $("#vdd_frete").val(dados_usuario.valor_frete);
    }
}

const base_url = "../api-backend/";
const id = '/'+i;
const usuario = "usuario";

const id_informacao_empresa = "/1";

const sucesso = 'sucesso';
const erro = 'erro';
const atencao = 'warning';

const headers = {
    'Authorization': 'Bearer ' + localStorage.getItem('projeto_venda_token_JWT')
};