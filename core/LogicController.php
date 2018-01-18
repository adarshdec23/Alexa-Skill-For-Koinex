<?php


namespace adarshdec23;

use adarshdec23\Config\Alexa_Constants;


class LogicController{
    var $logger;
    function __construct(){
        try{
            $this->logger = new \Monolog\Logger('app_log'); 
            $this->logger->pushHandler( new \Monolog\Handler\RotatingFileHandler(
                /* file name: */        __DIR__.'/../logs/'.Config\Monolog_Config::LOG_FILE,
                /* max day to keep*/    Config\Monolog_Config::MAX_FILES,
                /* max log level*/      \Monolog\Logger::INFO
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
            $returnObject['requestType'] = 'IntentRequest';
            $returnObject['intentType'] = $alexaRequest->intentName;
            $returnObject['alexaRequest'] = $alexaRequest;
            return $returnObject;
        }
        if($alexaRequest instanceof \Alexa\Request\LaunchRequest){
            $returnObject['requestType'] = 'LaunchRequest';
            $returnObject['alexaRequest'] = $alexaRequest;
            return $returnObject;
        }
        if($alexaRequest instanceof \Alexa\Request\SessionEndedRequest){
            $returnObject['requestType'] = 'SessionEndedRequest';
            $returnObject['alexaRequest'] = $alexaRequest;
            return $returnObject;
        }
        $this->logger->error("Unknown Request: ".print_r($inputData));
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

    function executeKoinex($alexaRequest){
        $inputCryptoToken = $alexaRequest->slots[Config\Alexa_Constants::CRYPTO_SLOT];
        $inputCryptoToken = $this->getCryptoToken($inputCryptoToken);
        if($inputCryptoToken === Config\Accepted_Crypto::UNKNOWN){
            OutputUtils::sendResponse(OutputUtils::buildOutputReprompt(Config\Alexa_Constants::ERROR_TOKEN_NOT_FOUND, Config\Alexa_Constants::LAUNCH_MESSAGE));
            return false;
        }
        $koinexApi = new Koinex\LogicKoinexAPI();
        $koinexValue = $koinexApi->getValueFor($inputCryptoToken);
        if($koinexValue == false){
            OutputUtils::sendResponse(OutputUtils::buildOutputReprompt(Config\Alexa_Constants::ERROR_TOKEN_NOT_FOUND, Config\Alexa_Constants::LAUNCH_MESSAGE));
            return false;
        }
        $outputMessage = "The price of ". $inputCryptoToken. " is ". $koinexValue." rupees.";
        OutputUtils::sendResponse(OutputUtils::buildOutputResponse($outputMessage));
        return true;
    }

    function executeHelp(){
        $finalResponseToSend = OutputUtils::buildOutputReprompt(Config\Alexa_Constants::HELP_EXAMPLE, Config\Alexa_Constants::LAUNCH_MESSAGE);
        OutputUtils::sendResponse($finalResponseToSend);
    }

    function findAndExecuteIntentType($compositeObject){
        switch ($compositeObject['intentType']) {
            case 'EtherIntent':
                $this->executeKoinex($compositeObject['alexaRequest']);
                break;
            case 'AMAZON.HelpIntent':
                $this->executeHelp();
                break;
            case 'AMAZON.CancelIntent':
            case 'AMAZON.StopIntent':
                OutputUtils::sendResponse(OutputUtils::buildOutputResponse(Config\Alexa_Constants::SESSION_END_MESSAGE));
                break;
            default:
                //Nothing to do, so call help
                $this->executeHelp();
                break;
        }
    }

    private function findAndExecuteRequestType($compositeObject)
    {
        if(!isset($compositeObject['requestType'])){
            OutputUtils::sendResponse(OutputUtils::buildOutputReprompt(Config\Alexa_Constants::ERROR_WITH_REQUEST, Config\Alexa_Constants::LAUNCH_MESSAGE));
            return false;
        }
        switch ($compositeObject['requestType']) {
            case 'IntentRequest':
                $this->logger->info("Got an intent request");
                $this->findAndExecuteIntentType($compositeObject);
                break;
            case 'LaunchRequest':
                $this->logger->info("Got a launch request");
                OutputUtils::sendResponse(OutputUtils::buildOutputReprompt(Config\Alexa_Constants::LAUNCH_MESSAGE, Config\Alexa_Constants::LAUNCH_MESSAGE));
                break;
            case 'SessionEndedRequest':
                $this->logger->info("Got a session end request");
                OutputUtils::sendResponse(OutputUtils::buildOutputResponse(Config\Alexa_Constants::SESSION_END_MESSAGE));
                break;
            default:
                $this->logger->error("Got an unknown request: ".print_r($compositeObject, true));
                OutputUtils::sendResponse(OutputUtils::buildOutputReprompt(Config\Alexa_Constants::ERROR_WITH_REQUEST, Config\Alexa_Constants::LAUNCH_MESSAGE));
                break;
        }
    }

    function execute(){
        $finalResponseToSend = null;
        $compositeObject = $this->parseInput();
        if($compositeObject == false){
            //Error already logged
            $finalResponseToSend =OutputUtils::buildOutputReprompt(Config\Alexa_Constants::ERROR_UNABLE_TO_PARSE, Config\Alexa_Constants::LAUNCH_MESSAGE);
            OutputUtils::sendResponse($finalResponseToSend);
            return false;
        }
        $this->findAndExecuteRequestType($compositeObject);
    }
}