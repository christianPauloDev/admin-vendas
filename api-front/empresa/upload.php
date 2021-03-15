<?php

date_default_timezone_set('America/Fortaleza');

$erro_token = 'Sessão Expirada! Realize o login novamente!';

$key = "empresa_key";

$http_header = apache_request_headers();

if (isset($http_header['Authorization']) && $http_header['Authorization'] != null) {
    $bearer = explode(' ', $http_header['Authorization']);
    //$bearer[0] = 'bearer';
    //$bearer[1] = 'token jwt';

    $token = explode('.', $bearer[1]);
    $header = $token[0];
    $payload = $token[1];
    $sign = $token[2];

    $data_payload = json_decode(base64_decode($payload));

    $tempo_token = 60 * 60 / 2;

    if ($data_payload->sub + $tempo_token < time()) {
        $tipo = 'erro';
        $resposta = $erro_token;
    } else {
        //Conferir Assinatura
        $valid = hash_hmac('sha256', $header . "." . $payload, $key.date("d/m/Y"), true);
        $valid = base64UrlEncode($valid);

        if ($sign === $valid) {

            if (isset($_FILES['img'])) {
                $arquivo = $_FILES["img"]; 
            
                if (!empty($arquivo)) {
                    $config = array();
            
                    $tamanho = 3 * 1024 * 1024; 
            
                    if ($arquivo["size"] > $tamanho) {
                        $tipo = 'erro';
                        $resposta = "A imagem deve ser de no máximo " . $tamanho/1024 . " mb!";
                    } else {
                        list($largura, $altura) = getimagesize($arquivo['tmp_name']);
            
                        $extensao = explode(".", $arquivo['name']);
                        $arquivo_extensao = strtolower(end($extensao));
            
                        $lista_extensoes = array('jpeg', 'jpg', 'png');
                        $lista_tipos = array('image/jpeg', 'image/jpg', 'image/png');
            
                        if (in_array($arquivo_extensao, $lista_extensoes) || in_array($arquivo['type'], $lista_tipos) 
                            || $largura != '' || $altura != '') 
                        {
                            $novo_nome = md5(time() . uniqid());
            
                            $nome_completo = $novo_nome . '.' . $arquivo_extensao;
            
                            if (move_uploaded_file($arquivo['tmp_name'], 'views/img/produtos/' . $nome_completo)) {
                                $tipo = 'sucesso';
                                $resposta = $nome_completo;
                            } else {
                                $tipo = 'erro';
                                $resposta = "Não foi possível realizar o upload da imagem!";
                            }
                        } else {
                            $tipo = 'erro';
                            $resposta = "Arquivo Inválido!";
                        }
                    }
                    
                } else {
                    $tipo = 'erro';
                    $resposta = "Não foi possível encontrar a imagem!";
                }
            }
            
            ////////////////////////////////////////////////////////////////////////////////////////////
            
            if (isset($_GET['apagar_img'])) {
                $nome_img = $_GET['img'];

                if (unlink('views/img/produtos/' . $nome_img)) {
                    $tipo = 'sucesso';
                    $resposta = 'Img deletada';
                } else {
                    $tipo = 'erro';
                    $resposta = $erro_token;
                }
            }
        } else {
            $tipo = 'erro';
            $resposta = $erro_token;
        }
    }
} else {
    $tipo = 'erro';
    $resposta = $erro_token;
}

echo json_encode([
    'tipo' => $tipo,
    'resposta' => $resposta,
]);


function base64UrlEncode($data)
{
    // First of all you should encode $data to Base64 string
    $b64 = base64_encode($data);

    // Make sure you get a valid result, otherwise, return FALSE, as the base64_encode() function do
    if ($b64 === false) {
        return false;
    }

    // Convert Base64 to Base64URL by replacing “+” with “-” and “/” with “_”
    $url = strtr($b64, '+/', '-_');

    // Remove padding character from the end of line and return the Base64URL result
    return rtrim($url, '=');
}