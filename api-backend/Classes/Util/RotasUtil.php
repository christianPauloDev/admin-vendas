<?php

namespace Util;

use InvalidArgumentException;

class RotasUtil
{

    public static function getRotas() 
    {
        $request = [];
        $urls = self::getUrls();
        $indices = ['tipo_usuario', 'id_usuario', 'rota', 'recurso', 'id', 'id_fk'];

        if ((count($urls) == 2 || count($urls) == 3) && ($urls[1] == 'login' || $urls[1] == 'validar_token')) {
            $request = [
                'tipo_usuario' => $urls[0],
                'recurso' => $urls[1],
                'token' => $urls[2]
            ];
        } else {
            if ( !(count($urls) == 5 || count($urls) == 6) )
                throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_URL_OBRIGATORIA);

            for ($i=count($urls); $i > 0; $i--) { 
                $request[ $indices[$i-1] ] = $urls[$i-1] ?? null;
            }

            $request['rota'] = strtoupper($request['rota']);
        }
        $request['metodo'] = $_SERVER['REQUEST_METHOD'];
        // echo '<pre>';var_dump($request);exit;
        
        return $request;
    } 

    /*=====================================================================*/

    public static function getUrls() 
    {
        $uri = str_replace('/' . DIR_PROJETO, '', $_SERVER['REQUEST_URI']);

        $url = explode('/', $uri);
        
        if ($url[0] == '')
            array_shift($url);

        return $url;
    } 

}