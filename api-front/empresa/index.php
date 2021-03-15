<?php

    $prefixo = 'views/pages/';
    $nao_encontrado = 'auth/404.html';
    $login = 'auth/login.html';
    $index = 'index.html';

    if (!isset($_GET['url'])) {
        $page = $index;
    } else {
        $url            = $_GET['url'];
        $url_dividida   = explode('/', $_GET['url']);

        if (count($url_dividida) == 1) {
            switch (strToUpper($url)) {
                case 'HOME' :
                    $page = $index;
                    break;

                case 'ENTRAR' :
                    $page = $login;
                    break;

                default:
                    $page = $nao_encontrado;
                    break;
            }
        } else {
            $page = $nao_encontrado;
        }
    }

    require_once $prefixo.$page;

?>