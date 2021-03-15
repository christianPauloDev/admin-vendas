function produtosTabela(dados) {
    var img = (dados.img) ? dados.img : img_default;

    return `
        <tr id="produto_${dados.id}">
            <td class='text-right'>${dados.id}</td>
            <td>
                <img class="img-fluid img-thumbnail" style="max-height: 80px;" src="views/img/produtos/${img}" alt="">
            </td>
            <td>${dados.titulo}</td>
            <td>${dados.nome_produto}</td>
            <td class='text-right'>${(Number(dados.preco)).toLocaleString('pt-br', { style: 'currency', currency: 'BRL' })}</td>
            <td>${(dados.status).toUpperCase()}</td>
            <td>
                <a href="#" class="btn btn-warning btn-circle btn-md" title="Editar" onClick="consultar(${dados.id})">
                    <i class="fas fa-pen"></i>
                </a>
                <a href="#" class="btn btn-danger btn-circle btn-md" title="Deletar" onClick="deletar(${dados.id})" >
                    <i class="fas fa-trash"></i>
                </a>
            </td>
        </tr>
    `;
}

/*=====================================================*//*=====================================================*/

function categoriasTabela(dados) {
    return `
        <tr id="categoria_${dados.id}">
            <td class='text-right'>${dados.id}</td>
            <td>${dados.titulo}</td>
            <td>
                <a href="#" class="btn btn-warning btn-circle btn-md" title="Editar" onClick="consultar(${dados.id})">
                    <i class="fas fa-pen"></i>
                </a>
                <a href="#" class="btn btn-danger btn-circle btn-md" title="Deletar" onClick="deletar(${dados.id})" >
                    <i class="fas fa-trash"></i>
                </a>
            </td>
        </tr>
    `;
}

/*=====================================================*//*=====================================================*/

function cuponsTabela(dados) {
    var data = new Date(dados.data_validade);
    var desconto = dados.tipo_desconto == "porcentagem" ? dados.valor_desconto + "%" : moeda(parseFloat(dados.valor_desconto));
    var valor_minimo = dados.valor_minimo == null ? '-' : dados.valor_minimo;
    var status = (dados.status == '1') ? ['#0fd9a7', 'VÁLIDO'] : ['#d90429', 'INVÁLIDO'];
    status = verificarData(data, status);

    return `
        <tr id="cupom_${dados.id}">
            <td class='text-right'>${dados.id}</td>
            <td>${dados.titulo}</td>
            <td>${desconto}</td>
            <td>${valor_minimo}</td>
            <td>${data.toLocaleDateString()}</td>
            <td style="color:${status[0]};">${status[1]}</td>
            <td>
                <a href="#" class="btn btn-warning btn-circle btn-md" title="Editar" onClick="consultar(${dados.id})">
                    <i class="fas fa-pen"></i>
                </a>
                <a href="#" class="btn btn-danger btn-circle btn-md" title="Deletar" onClick="deletar(${dados.id})" >
                    <i class="fas fa-trash"></i>
                </a>
            </td>
        </tr>
    `;
}

function verificarData(data, atual) {
    var data_cupom = data.toLocaleDateString();
    var array_data = data_cupom.split('/');
    var format_cupom = new Date(array_data[2], array_data[1] - 1, array_data[0]);

    if (format_cupom < new Date())
        return ['#F6C23E', 'EXPIRADO'];
    else
        return atual;
}