<?php

namespace Validator;

use DB\MySQL;
use InvalidArgumentException;
use Repository\{
    EmpresasRepository,
};
use Util\ConstantesGenericasUtil;

date_default_timezone_set('America/Fortaleza');
class AuthValidator
{
    private object $MySQL;
    private object $EmpresasRepository;


    private static $keys = [
        'empresa' => "empresa_key",
    ]; //Application Key

    public function __construct()
    {
        $this->MySQL = new MySQL();
        $this->EmpresasRepository = new EmpresasRepository();
    }

    /*========================================================*/
        
    public function login($usuario, $login, $senha){
        
        ($usuario == 'informacoes_empresa') && $usuario = 'empresa';

        if ($dados = $this->MySQL->validarLogin($usuario.'s', $login, $senha)) {
            $dados = $this->EmpresasRepository->ids($dados['id']);            

            //Header Token
            $header = [
                'alg' => 'HS256',
                'typ' => 'JWT'
            ];

            //Payload - Content
            $payload = [
                'iss' => 'projeto-venda',
                'sub' => time(),
                'dados' => $dados
            ];

            //JSON
            $header = json_encode($header);
            $payload = json_encode($payload);

            //Base 64
            $header = self::base64UrlEncode($header);
            $payload = self::base64UrlEncode($payload);

            $user_sing = self::$keys[$usuario].date("d/m/Y");

            //Sign
            $sign = hash_hmac('sha256', $header . "." . $payload, $user_sing, true);
            $sign = self::base64UrlEncode($sign);

            //Token
            $token = $header . '.' . $payload . '.' . $sign;

            return [ 'JWT_token' => $token ];
        }
        
        throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_DADOS_INVALIDOS);
    }

    /*===================================================*//*===================================================*/

    public static function checkAuth($usuario, $auth = false)
    {
        if (!$auth) {
            $http_header = apache_request_headers();
            $auth = $http_header['Authorization'];
        } else {
            $auth = 'Bearer '.$auth;
        }

        if (isset($auth) && $auth != null) {
            $bearer = explode (' ', $auth);
            //$bearer[0] = 'bearer';
            //$bearer[1] = 'token jwt';

            $token = explode('.', $bearer[1]);
            $header = $token[0];
            $payload = $token[1];
            $sign = $token[2];

            $data_payload = json_decode(base64_decode($payload));
            
            ($usuario == 'informacoes_empresa') && $usuario = 'empresa';
            $tempo_token = 60 * 60 / 2;

            if ($data_payload->sub+$tempo_token < time())                 
                throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_TOKEN_NAO_AUTORIZADO); 
            
            $user_sing = self::$keys[$usuario].date("d/m/Y");

            //Conferir Assinatura
            $valid = hash_hmac('sha256', $header . "." . $payload, $user_sing, true);
            $valid = self::base64UrlEncode($valid);

            if ($sign === $valid) return 'Token Válido';
        }

        throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_TOKEN_NAO_AUTORIZADO);
    }
    
    
    /*Criei os dois métodos abaixo, pois o jwt.io agora recomenda o uso do 'base64url_encode' no lugar do 'base64_encode'*/
    private static function base64UrlEncode($data)
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

    /*=============================================*//*=============================================*/
}
