<?php

namespace Validator;

use InvalidArgumentException;
use Util\ConstantesGenericasUtil;

class DataValidator
{
    private function fone($fone)
    {
        $phoneString = preg_replace('/[()]/', '', $fone);

        $regex = '/^(?:(?:\+|00)?(55)\s?)?(?:\(?([0-0]?[0-9]{1}[0-9]{1})\)?\s?)??(?:((?:9\d|[2-9])\d{3}\-?\d{4}))$/';

        if (preg_match($regex, $phoneString, $matches) === false) {
            return null;
        } else {
            // $ddi = $matches[1] ?? '';
            // $ddd = preg_replace('/^0/', '', $matches[2] ?? '');
            $number = $matches[3] ?? '';
            $number = preg_replace('/-/', '', $number);
        }

        if (!$number)
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_FONE_INVALIDO);
    }

    /*=============================================*//*=============================================*/

    private function cpf($cpf)
    {
        $retorno = true;

        // Extrai somente os números
        $cpf = preg_replace('/[^0-9]/is', '', $cpf);

        // Verifica se foi informado todos os digitos corretamente
        if (strlen($cpf) != 11) {
            $retorno = false;
        } else
            // Verifica se foi informada uma sequência de digitos repetidos. Ex: 111.111.111-11
            if (preg_match('/(\d)\1{10}/', $cpf)) {
                $retorno = false;
            } else {
                // Faz o calculo para validar o CPF
                for ($t = 9; $t < 11; $t++) {
                    for ($d = 0, $c = 0; $c < $t; $c++) {
                        $d += $cpf[$c] * (($t + 1) - $c);
                    }

                    $d = ((10 * $d) % 11) % 10;

                    if ($cpf[$c] != $d)
                        $retorno = false;
                }
            }

        if (!$retorno)
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_CPF_INVALIDO);
    }

    /*=============================================*//*=============================================*/

    private function cnpj($cnpj)
    {
        $cnpj = preg_replace('/[^0-9]/', '', (string) $cnpj);

        // Valida tamanho
        if (strlen($cnpj) != 14)
            $retorno = false;

        // Verifica se todos os digitos são iguais
        if (preg_match('/(\d)\1{13}/', $cnpj))
            $retorno = false;

        // Valida primeiro dígito verificador
        for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++) {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }

        $resto = $soma % 11;

        if ($cnpj[12] != ($resto < 2 ? 0 : 11 - $resto))
            $retorno = false;

        // Valida segundo dígito verificador
        for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++) {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }

        $resto = $soma % 11;

        $retorno = $cnpj[13] == ($resto < 2 ? 0 : 11 - $resto);

        if (!$retorno)
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_CNPJ_INVALIDO);
    }

    /*=============================================*//*=============================================*/

    private function email($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_EMAIL_INVALIDO);
    }

    /*=============================================*//*=============================================*/

    private function login($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_EMAIL_INVALIDO);
    }

    /*=============================================*//*=============================================*/

    public function validarInformacoes($campos, $dados)
    {
        foreach ($campos as $indices => $indices_dados) {
            if ($infor = $dados[$indices_dados]) {
                $metodo = $indices_dados;

                // $this->$metodo($infor);
            }
        }
    }

    /*=============================================*//*=============================================*/
}
