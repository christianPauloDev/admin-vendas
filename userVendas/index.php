<?php

    $prefixo = 'pages/';

    $nao_encontrado = 'auth/404.html';
    $index = 'index.html';

    $pages = [
        'HOME' => $index,
    ];

    $page = $nao_encontrado;

    if (!isset($_GET['url'])) {
        $page = $index;
    } else {
        $url            = $_GET['url'];
        $url_dividida   = explode('/', $_GET['url']);

        if (count($url_dividida) == 1)
            (array_key_exists(strToUpper($url), $pages)) && $page = $pages[strToUpper($url)];
    }

    require_once $prefixo.$page;
?>