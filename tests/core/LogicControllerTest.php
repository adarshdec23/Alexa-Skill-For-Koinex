<?php


require __DIR__ . '/../../vendor/autoload.php';

use adarshdec23\LogicController;
use adarshdec23\Config\Accepted_Crypto;
use adarshdec23\Config\Alexa_Constants;

class LogicControllerTest extends \PHPUnit\Framework\TestCase{

/**
* @param string $inputData
* @param int $expectedOutput
* @dataProvider providerGetCryptoTokenSuccess
*/
function testGetCryptoTokenSuccess($inputData, $expectedOutput){
        $this->logicController = new LogicController();
        $result = $this->logicController->getCryptoToken($inputData);
        $this->assertSame($expectedOutput, $result);
}

function providerGetCryptoTokenSuccess(){
    return array(
        array("ethereum", Accepted_Crypto::ETHEREUM),
        array("E.T.H", Accepted_Crypto::ETHEREUM),
        array("bitcoIn cAsh", Accepted_Crypto::BITCOINCASH)
    );
}

/**
* @param string $inputData
* @param int $expectedOutput
* @dataProvider providerGetCryptoTokenUnknown
*/
function testGetCryptoTokenUnknown($inputData, $expectedOutput){
    $this->logicController = new LogicController();
    $result = $this->logicController->getCryptoToken($inputData);
    $this->assertSame($expectedOutput, $result);
}

function providerGetCryptoTokenUnknown(){
    return array(
        array("ethereu", Accepted_Crypto::UNKNOWN),
        array("EB.T.H", Accepted_Crypto::UNKNOWN),
        array("sadlkfjhsl", Accepted_Crypto::UNKNOWN)
    );
}


function testBuildOutputResponse(){
    //Build test data
    $koinexValue = '77780.0';
    $inputCryptoToken = Accepted_Crypto::ETHEREUM;
    // End of data builder

    $this->logicController = new LogicController();
    $resultResponseObject = $this->logicController->buildOutputResponse($inputCryptoToken, $koinexValue);

    //Check only the fields we care about
    $this->assertSame($resultResponseObject->outputSpeech->text, "The price of Ethereum is 77780.0 rupees.");
    $this->assertTrue($resultResponseObject->shouldEndSession);
}

function testBuildOutputRepromptUnableToParse(){
    $this->logicController = new LogicController();
    $resultResponseObject = $this->logicController->buildOutputReprompt(Alexa_Constants::ERROR_UNABLE_TO_PARSE);

    //Check only the fields we care about
    $this->assertSame($resultResponseObject->reprompt->outputSpeech->text, Alexa_Constants::ERROR_UNABLE_TO_PARSE);
    $this->assertFalse($resultResponseObject->shouldEndSession);
}

function testBuildOutputRepromptHelp(){
    $this->logicController = new LogicController();
    $resultResponseObject = $this->logicController->buildOutputReprompt(Alexa_Constants::HELP_EXAMPLE);

    //Check only the fields we care about
    $this->assertSame($resultResponseObject->reprompt->outputSpeech->text, Alexa_Constants::HELP_EXAMPLE);
    $this->assertFalse($resultResponseObject->shouldEndSession);
}

}