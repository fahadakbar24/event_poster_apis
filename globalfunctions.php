<?php

function printVars($var, $message=""){
    echo $message;
    echo "<pre>";
    print_r($var);
    echo "<pre/><br/><br/>";
}

function makeApiReq($method = "get",$uri, $payload = "", $headers = []){
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $uri);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

    if($method == "delete"){
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    } else {
        curl_setopt($ch, CURLOPT_POST, $method == "post");
    }

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}