<?php

namespace Validator;

use InvalidArgumentException;
use Util\ConstantesGenericasUtil;

class XSSSecurityAccessOrigin
{

    private $permission_domains = array();
    private $request;
    private $secureProtocol = false;

    public function __construct($request, $secureProtocol = false)
    {
        $this->permission_domains = array(
            'localhost',
            '192.168.0.115',
            'adminvendas.2dmedia.com.br'
            // 'www.subdominio.seusite.com.br'
        );
        $this->request = $request;
        $this->secureProtocol = $secureProtocol;
    }

    public function accessControlAllowOrigin()
    {
        if (in_array($this->request, $this->permission_domains)) {
            $security = ($this->secureProtocol) ? 's' : '';
            
            $this->request = "http{$security}://" . $this->request;
            
            $this->getJSON();
        } else {
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_TIPO_ROTA);
        }
    }

    private function getJSON()
    {
        header("Access-Control-Allow-Origin: {$this->request}");
        header("X-Frame-Options: SAMEORIGIN");
        header("X-XSS-Protection: 1; mode=block");
        header("X-Content-Type-Options: nosniff");
        header("Strict-Transport-Security: max-age=31536000; includeSubDomains");  
        header('Content-Type: application/json');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    }
}
