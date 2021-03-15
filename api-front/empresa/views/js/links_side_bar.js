// function para redirecionar as paginas
function redirect(event, page, title) {
    event.preventDefault();
    document.title = "Empresa - "+title;
    $("#main-content").load(prefixo+'loads/'+page+'.html');
}

$(document).ready(function() {    
    $(".link_categorias").click(function (event) {
        redirect(event, "categorias", "Categorias");  
    });

    $(".link_produtos").click(function (event) {
        redirect(event, "produtos", "Produtos");  
    });

    $(".link_cupons").click(function (event) {
        redirect(event, "cupons", "Cupons");  
    });
})