if (localStorage.getItem("projeto_venda_token_JWT") != null) {
    const payload = (localStorage.getItem("projeto_venda_token_JWT")).split('.')[1];

    var key = JSON.parse(atob(payload))['dados']['id'] + '/';
    var key_2 = JSON.parse(atob(payload))['dados']['id_2'] + '/';
} else {
    var key = '0/';
    var key_2 = '0/';
}

const id = key;
const id_2 = key_2;

const base_url = "../../api-backend/";
const prefixo = "views/pages/";
const usuario = "informacoes_empresa/";
const usuario_2 = "empresa/";


// const id = "1/";
// const id_2 = "1/";


const sucesso = 'sucesso';
const erro = 'erro';
const atencao = 'warning';


const headers = {
    'Authorization': 'Bearer ' + localStorage.getItem('projeto_venda_token_JWT')
};


const sem_dados = "Nenhum Registro Encontrado";


const img_default = "default.png";