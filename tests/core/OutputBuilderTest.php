<?php


require __DIR__ . '/../../vendor/autoload.php';

use adarshdec23\OutputUtils;
use adarshdec23\Config\Accepted_Crypto;
use adarshdec23\Config\Alexa_Constants;

class OutputBuilderTest extends \PHPUnit\Framework\TestCase{
    function testBuildOutputResponse(){
        //Build test data
        $koinexValue = '77780.0';
        $inputCryptoToken = Accepted_Crypto::ETHEREUM;
        // End of data builder
    
        $responseMessage = "The price of ".$inputCryptoToken." is ".$koinexValue." rupees.";
        $resultResponseObject = OutputUtils::buildOutputResponse($responseMessage);
    
        //Check only the fields we care about
        $this->assertSame($resultResponseObject->outputSpeech->text, "The price of Ethereum is 77780.0 rupees.");
        $this->assertTrue($resultResponseObject->shouldEndSession);
    }
    
    function testBuildOutputRepromptUnableToParse(){
        $resultResponseObject = OutputUtils::buildOutputReprompt(Alexa_Constants::ERROR_UNABLE_TO_PARSE, Alexa_Constants::LAUNCH_MESSAGE);
    
        //Check only the fields we care about
        $this->assertSame($resultResponseObject->outputSpeech->text, Alexa_Constants::ERROR_UNABLE_TO_PARSE);
        $this->assertSame($resultResponseObject->reprompt->outputSpeech->text, Alexa_Constants::LAUNCH_MESSAGE);
        $this->assertFalse($resultResponseObject->shouldEndSession);
    }
    
    function testBuildOutputRepromptHelp(){
        $resultResponseObject = OutputUtils::buildOutputReprompt(Alexa_Constants::HELP_EXAMPLE, Alexa_Constants::LAUNCH_MESSAGE);
    
        //Check only the fields we care about
        $this->assertSame($resultResponseObject->outputSpeech->text, Alexa_Constants::HELP_EXAMPLE);
        $this->assertSame($resultResponseObject->reprompt->outputSpeech->text, Alexa_Constants::LAUNCH_MESSAGE);
        $this->assertFalse($resultResponseObject->shouldEndSession);
    }
}