<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;


class CorsFilter implements FilterInterface
{
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {


    }

    public function before(RequestInterface $request, $arguments = null)
    {
        // get origins
        if (array_key_exists('HTTP_ORIGIN', $_SERVER)) {
            $origin = $_SERVER['HTTP_ORIGIN'];
        } else if (array_key_exists('HTTP_REFERER', $_SERVER)) {
            $origin = $_SERVER['HTTP_REFERER'];
        } else {
            $origin = $_SERVER['REMOTE_ADDR'];
        }
        $allowed_domains = array(
            'http://localhost:8080',
            'https://testing.getdaabo.com',
            'https://www.testing.getdaabo.com',
        );

        // this below code work on local server localhost:8080

        if (in_array($origin, $allowed_domains)) {
            header('Access-Control-Allow-Origin: ' . $origin);
        }

        header("Access-Control-Allow-Headers: Origin, X-API-KEY, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Access-Control-Allow-Headers, Authorization, observe, enctype, Content-Length, X-Csrf-Token");
        header("Access-Control-Allow-Methods: GET, PUT, POST, DELETE, PATCH, OPTIONS");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Max-Age: 3600");
        header('content-type: application/json; charset=utf-8');

        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == "OPTIONS") {
            header("HTTP/1.1 200 OK");
            header("Access-Control-Allow-Headers: X-API-KEY");
            die();
        }

    }


} 