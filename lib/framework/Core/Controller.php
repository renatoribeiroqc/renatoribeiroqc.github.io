<?php

/* 
 * Copyright (C) conexperience.com.br, Inc - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Renato Ribeiro <renato.ribeiro@conexperience.com.br>, June 2023
 */

namespace Lib\Framework\Core;

use Areas\Pessoa\Client;
use Areas\Pessoa\Professional;
use ReflectionClass;
use ReflectionMethod;

/**
 * Controller is the base class to all Controllers
 *
 * @author renato
 */

class Controller
{
    const UID_LEN = 250;
    const HTTP_ERROR_CODES = array(
        200 => '200 OK',
        201 => '201 Created',
        202 => '202 Accepted',
        203 => '203 Non-Authoritative Information',
        204 => '204 No Content',
        205 => '205 Reset Content',
        206 => '206 Partial Content',
        207 => '207 Multi-Status',
        208 => '208 Already Reported',
        226 => '226 IM Used',
        400 => '400 Bad Request',
        401 => '401 Unauthorized',
        402 => '402 Payment Required',
        403 => '403 Forbidden',
        404 => '404 Not Found',
        405 => '405 Method Not Allowed',
        406 => '406 Not Acceptable',
        407 => '407 Proxy Authentication Required',
        408 => '408 Request Timeout',
        409 => '409 Conflict',
        410 => '410 Gone',
        411 => '411 Length Required',
        412 => '412 Precondition Failed',
        413 => '413 Payload Too Large',
        414 => '414 URI Too Long',
        415 => '415 Unsupported Media Type',
        416 => '416 Range Not Satisfiable',
        417 => '417 Expectation Failed',
        421 => '421 Misdirected Request',
        422 => '422 Unprocessable Entity',
        423 => '423 Locked',
        424 => '424 Failed Dependency',
        426 => '426 Upgrade Required',
        428 => '428 Precondition Required',
        429 => '429 Too Many Requests',
        431 => '431 Request Header Fields Too Large',
        451 => '451 Unavailable For Legal Reasons',
        500 => '500 Internal Server Error',
        501 => '501 Not Implemented',
        502 => '502 Bad Gateway',
        503 => '503 Service Unavailable',
        504 => '504 Gateway Timeout',
        505 => '505 HTTP Version Not Supported',
        506 => '506 Variant Also Negotiates',
        507 => '507 Insufficient Storage',
        508 => '508 Loop Detected',
        510 => '510 Not Extended',
        511 => '511 Network Authentication Required'
     );
    protected $route = array();
    //actions x urls, methods
    public $user;
    public $person;
    public $base_url = BASE_URI;
    public $view;
    public $model;
    public $service;

    public function __construct()
    {
        $this->view = new View();
        $this->autoloadRoutes();
        $this->User();
    }

    private function autoloadRoutes()
    {
        $reflection = new ReflectionClass($this);
        if ($reflection->isInstantiable()) {
            $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
            $array = explode('\\', get_class($this));
            $self = end($array);
            foreach ($methods as $method) {
                $array = explode('\\', $method->class);
                $reflectionShortClassName = end($array);
                //Exclude parent methods
                if ($self !== $reflectionShortClassName) {
                    continue;
                }

                $classShortName = str_replace('controller', '', strtolower($reflection->getShortName()));
                $methodName = strtolower($method->getName());

                $route = BASE_URI . $classShortName . '/' . $methodName;
                $this->route[$methodName] = array("url" => $route, "method" => $method->getName());
            }
        }
    }

    public function addRoute($route)
    {
        array_push($this->route, $route);
    }

    public function addAllRoutes($routes)
    {
        $this->route = $routes;
    }

    public function getAllRoutes()
    {
        return $this->route;
    }

    public function readRoute($route, $type = 'url')
    {

        if ($route != '' && !is_null($route)) {
            $allowedRoutes = array_keys($this->route);
            if (array_search($route, $allowedRoutes) === false) {
                return false;
            }
            if ($type == 'url') {
                return $this->route[$route]['url'];
            }
            return $this->route[$route]['method'];
        }
    }

    public function generateHash()
    {
        return bin2hex(openssl_random_pseudo_bytes($this::UID_LEN));
    }

    public function AsJson($output)
    {
        header('Content-type:application/json;charset=utf-8');
        echo json_encode($output);
    }

    public function baseURL()
    {
        $baseUrl = BASE_URI;
        return $baseUrl;
    }

    function sendResponse($status, $data = null, $message = null, $statusCode = null) 
    {
        $responseCode = $status === 'success' ? 200 : 400;
        $responseCode = $statusCode !== null ? $statusCode : $responseCode;

        $response = array(
            'status' => $status,
            'message' => $message,
            'data' => $data
        );

        if ($responseCode !== 400) {
            echo json_encode($response, JSON_PRETTY_PRINT);
        }

        http_response_code($responseCode);
    }

    public function showHttpMsg($code)
    {
        http_response_code($code);
        echo "HTTP/1.1 " . $this::HTTP_ERROR_CODES[$code];
    }

    public function view($viewBag = array())
    {
        if ($this->view && !is_null($this->view->partial())) {
            $this->view->render($viewBag);
        }
    }

    public function owner()
    {
        //TODO : recover the data from pessoa, colaborador and users using guid from users.
        if (isset($_SESSION['uid'])) {
            return array(
                "id"                => "",
                "name"              => "",
                "email"             => "",
                "status"            => "",
                "level"             => "",
                "type"              => "",
                "company"           => "",
                "employeeType"      => "",
                "employeeStatus"    => ""
            );
        }
        return null;
    }

    protected function User()
    {
        if (isset($_SESSION['user'])) {
            $userInfo = unserialize($_SESSION['user']);
            if ($userInfo['t'] === 1) {
                $this->person = new Professional(CONNECTION_STRING);
            } else {
                $this->person = new Client(CONNECTION_STRING);
            }
            $this->user = $userInfo;
        }
    }

    public function userId()
    {
        $this->User();
        $modelData = $this->person->Find(array("pes_uidativacao = '" . $this->user['i'] . "'"));
        if ($modelData !== false) {
            $modelData = $modelData->fetchObject();
            return $modelData->idpessoa;
        }
        return -1;
    }

    public function isPost()
    {
        return strtoupper($_SERVER['REQUEST_METHOD']) == 'POST';
    }

    public function isPut()
    {
        return strtoupper($_SERVER['REQUEST_METHOD']) == 'PUT';
    }

    public function isDelete()
    {
        return strtoupper($_SERVER['REQUEST_METHOD']) == 'DELETE';
    }

    public function isGet()
    {
        return strtoupper($_SERVER['REQUEST_METHOD']) == 'GET';
    }
}
