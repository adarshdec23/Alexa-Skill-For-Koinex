<?php


namespace adarshdec23;

use PHPUnit\Runner\Exception;


class LogicController{
    var $logger;
    function __construct(){
        try{
            $this->logger = new \Monolog\Logger('app_log'); 
            $this->logger->pushHandler( new \Monolog\Handler\RotatingFileHandler(
                /* file name: */        __DIR__.'/../logs/'.Config\Monolog_Config::LOG_FILE,
                /* max day to keep*/    Config\Monolog_Config::MAX_FILES,
                /* max log level*/      \Monolog\Logger::ERROR
            ));
        }
        catch (\Exception $e){
            echo "How do you log when your logger fails? ".$e->getMessage();
            //Its time to stop.
            exit(0);
        }   
    }

    function parseInput(){
        $inputData = $this->parseRawData();
        if(!$inputData){
            return false;
        }
        $alexaRequest = \Alexa\Request\Request::fromData($inputData);
        try{
            $alexaRequest->validate();
        }
        catch(\Exception $exception){
            /**
             * This means that there is a problem with the request. 
             * Ideally we should have a separate view for this, but
             * considering that we only need to throw a 400 error 
             * this should suffice.
             */
            http_response_code(400);
		    $this->logger->error($exception);
		    die();
        }
        if($alexaRequest instanceof \Alexa\Request\IntentRequest){
            $returnObject['intentType'] = $alexaRequest->intentName;
            $returnObject['alexaRequest'] = $alexaRequest;
            return $returnObject;
        }
        $this->logger->error("Unknown Intent");
        return false;            
    }

    function getCryptoToken($inputData){
        foreach(Config\Crypto_Spoken_Values::ALL_CRYPTOS as $cryptoToken => $spokenValues){
            foreach($spokenValues as $spokenValue){
                if(strcasecmp($inputData, $spokenValue) == 0){
                    return $cryptoToken;
                }
            }
        }
        return Config\Accepted_Crypto::UNKNOWN;
    }

    function parseRawData(){
        //Read the incoming JSON. Bad PHP, bad bad PHP.
        $rawJSONInput = file_get_contents('php://input');
        if($rawJSONInput === false){
            $this->logger->error('Unable to read input');
            return false;
        }
        $arrayInput = json_decode($rawJSONInput, true);
        //No need to hard check.
        if($arrayInput == NULL){
            $this->logger->error('Unable to parse JSON');
            return false;
        }
        return $arrayInput;
    }

    function buildOutputResponse($inputCryptoToken, $koinexValue){
        $response = new \Alexa\Response\Response;
        $response->respond("The price of ". $inputCryptoToken. " is ". $koinexValue." rupees.");
        //Our job here is done. Nothing more to do ლ(▀̿̿Ĺ̯̿̿▀̿ლ)
        $response->shouldEndSession = true;
        return $response;
    }

    function buildOutputReprompt($promptMessage){
        $response = new \Alexa\Response\Response;
        $response->reprompt($promptMessage);
        $response->shouldEndSession = false;
        return $response;
    }

    function sendResponse($response){
        header('Content-Type: application/json');
        echo json_encode($response->render());
    }

    function executeKoinex($alexaRequest){
        $inputCryptoToken = $alexaRequest->slots[Config\Alexa_Constants::CRYPTO_SLOT];
        $inputCryptoToken = $this->getCryptoToken($inputCryptoToken);
        if($inputCryptoToken === Config\Accepted_Crypto::UNKNOWN){
            $this->sendResponse($this->buildOutputReprompt(Config\Alexa_Constants::ERROR_TOKEN_NOT_FOUND));
            return false;
        }
        $koinexApi = new Koinex\LogicKoinexAPI();
        $koinexValue = $koinexApi->getValueFor($inputCryptoToken);
        if($koinexValue == false){
            $this->sendResponse($this->buildOutputReprompt(Config\Alexa_Constants::ERROR_TOKEN_NOT_FOUND));
            return false;
        }
        $this->sendResponse($this->buildOutputResponse($inputCryptoToken, $koinexValue));
        return true;
    }

    function executeHelp(){
        $finalResponseToSend = $this->buildOutputReprompt(Config\Alexa_Constants::HELP_EXAMPLE);
        $this->sendResponse($finalResponseToSend);
    }

    function findAndExecuteIntentType($compositeObject){
        switch ($compositeObject['intentType']) {
            case 'EtherIntent':
                $this->executeKoinex($compositeObject['alexaRequest']);
                break;
            case 'AMAZON.HelpIntent':
                $this->executeHelp();
                break;
            default:
                //Nothing to do, so call help
                $this->executeHelp();
                break;
        }
    }

    function execute(){
        $finalResponseToSend = null;
        $compositeObject = $this->parseInput();
        if($compositeObject == false){
            //Error already logged
            $finalResponseToSend =$this->buildOutputReprompt(Config\Alexa_Constants::ERROR_UNABLE_TO_PARSE);
            $this->sendResponse($finalResponseToSend);
            return false;
        }
        $this->findAndExecuteIntentType($compositeObject);
    }
}