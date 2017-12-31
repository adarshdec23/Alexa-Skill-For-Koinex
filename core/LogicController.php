<?php


/*require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../core/config/config.php';*/

use Alexa\Request\Response;

namespace adarshdec23;

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
        $alexaRequest = Alexa\Request\Request::fromData($inputData);
        if($alexaRequest instanceof Alexa\Request\IntentRequest){
            $inputCryptoToken = $alexaRequest->slots[Alexa_Constants::CRYPTO_SLOT];
            $cryptoToken = $this->getCryptoToken($inputData);
            return $cryptoToken;
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

    function execute(){
        $inputData = $this->parseInput();
        if($inputData == false){
            //Error already logged
            return false;
        }
        echo "The token is: ".$inputData;
    }
}