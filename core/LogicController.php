<?php


require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../core/utils/utils.php';
require_once __DIR__.'/../core/config/config.php';

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Alexa\Request\Request;
use Alexa\Request\Response;

class LogicController{
    var $logger;
    function __construct(){
        try{
            $this->logger = new Logger('app_log'); 
            $this->logger->pushHandler( new RotatingFileHandler(
                /* file name: */        __DIR__.'/../logs/'.Monolog_Config::LOG_FILE,
                /* max day to keep*/    Monolog_Config::MAX_FILES,
                /* max log level*/      Logger::ERROR
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
        $cryptoToken = $this->getCryptoToken($inputData);

        //TODO: after below
        return $cryptoToken;
    }

    function getCryptoToken($inputData){
        $alexaRequest = Request::fromData($inputData);
        if($alexaRequest instanceof IntentRequest){
            echo $alexaRequest;
        }
        echo "Don't know what you are";
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