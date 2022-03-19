<?php

class ApiHandler
{
    private $userAgent;

    public function __construct($userAgent = "curl/7.79.1"){
        $this->userAgent = $userAgent;
    }

    public function makeRequest($url){
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response);
    }
}