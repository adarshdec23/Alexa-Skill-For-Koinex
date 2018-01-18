<?php

namespace adarshdec23;

class OutputUtils{
    public static function buildOutputResponse($responseMessage){
        //Our job here is done. Nothing more to do ლ(▀̿̿Ĺ̯̿̿▀̿ლ)
        $response = new \Alexa\Response\Response;
        $response->respond($responseMessage);
        $response->shouldEndSession = true;
        return $response;
    }

    public static function buildOutputReprompt($responseMessage, $promptMessage){
        $response = new \Alexa\Response\Response;
        $response->reprompt($promptMessage);
        $response->respond($responseMessage);
        $response->shouldEndSession = false;
        return $response;
    }

    public static function sendResponse($response){
        header('Content-Type: application/json');
        echo json_encode($response->render());
    }
}