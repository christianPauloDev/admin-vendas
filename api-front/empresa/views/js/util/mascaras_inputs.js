$(document).ready(function(){
    //--------MÁSCARA DE CEP--------//
    $('.input_cep').mask('00000-000');    
    
    //--------MÁSCARA DE NÚMERO--------//
    $('.input_num').mask('00000');

    //--------MÁSCARA DE MINUTOS--------//
    $('.input_minutos').mask('000');

    //--------MÁSCARA DE CPF --------//
    $('.input_cpf').mask('000.000.000-00');

    //--------MÁSCARA DE CNPJ --------//
    $('.input_cnpj').mask('00.000.000/0000-00');

    //--------MÁSCARA DE FONE/WHATS --------// 
    $('.input_fone').mask('(00) 00000-0000');

    $('.input_minutos').on({
        click: function (){ if ($(this).val() < 0) $(this).val(0); },
        change: function (){ if ($(this).val() < 0) $(this).val(0); },
        keyup: function (){ if ($(this).val() < 0) $(this).val(0); },
        keypress: function (){ if ($(this).val() < 0) $(this).val(0); },
        keydown: function (){ if ($(this).val() < 0) $(this).val(0); },
    });
});