<?php

namespace adarshdec23\Koinex;
use \adarshdec23\Config\Monolog_Config;
use \adarshdec23\Config\Koinex_Config;

class LogicKoinexAPI{
    function __construct(){
        try{
            $this->logger = new \Monolog\Logger('koinex_log');
            $this->logger->pushHandler( new \Monolog\Handler\RotatingFileHandler(
                /* file name: */        __DIR__.'/../../logs/'.Monolog_Config::LOG_FILE,
                /* max day to keep*/    Monolog_Config::MAX_FILES,
                /* max log level*/      \Monolog\Logger::ERROR
            ));
        }
        catch (\Exception $e){
            echo "How do you log when your logger fails? ".$e->getMessage();
            //Its time to stop.
            exit(0);
        }   
    }

    function getValueFor($inputCryptoToken){
        $apiReturnData = $this->makeApiCall();
        if($apiReturnData == false){
            $this->logger->error("API call failed.");
            return false;
        }

        //Error already logged.
        return $this->extractCryptoValue($apiReturnData, $inputCryptoToken);
    }

    function makeApiCall(){
        //Set up our contex. Without this we get a 403. 
        $context = stream_context_create(
            array(
                'http' => array(
                'header'  =>	"User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64; rv:44.0) Gecko/20100101 Firefox/44.0"."\r\n".
								"Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8"."\r\n",
                'method'  => 'GET'
                )
            )
        );
        $apiReturnData = file_get_contents(Koinex_Config::API_URL, false, $context);
        return $apiReturnData;
    }

    function extractCryptoValue($apiReturnData, $inputCryptoToken){
        $arrayReturnData = json_decode($apiReturnData, true);
        if($arrayReturnData == null){
            $this->logger->error("Invalid response by Koinex.");
            return false;
        }
        $cryptoValue = $arrayReturnData["prices"][
            //Map internal data to Koinex Expected Values
            Koinex_Config::Koinex_Accepted_Crypto[$inputCryptoToken]
        ];
        if($cryptoValue == null){
            $this->logger->error($inputCryptoToken);
            $this->logger->error("Data missing for ".Koinex_Config::Koinex_Accepted_Crypto[$inputCryptoToken], $arrayReturnData);
            return false;
        }
        /* We don't check whether the value returned by Koinex is valid.
        *  Hell, we don't even care if it's a number. Just return it.
        */
        return $cryptoValue;
    }
}