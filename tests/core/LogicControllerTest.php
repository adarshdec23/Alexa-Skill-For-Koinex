<?php


require __DIR__ . '/../../vendor/autoload.php';

use adarshdec23\LogicController;
use adarshdec23\Config\Accepted_Crypto;

class LogicControllerTest extends \PHPUnit_Framework_TestCase{

/**
* @param string $inputData
* @param int $expectedOutput
* @dataProvider providerGetCryptoTokenSuccess
*/
function testGetCryptoTokenSuccess($inputData, $expectedOutput){
        $this->logicController = new LogicController();
        $result = $this->logicController->getCryptoToken($inputData);
        $this->assertEquals($expectedOutput, $result);
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
    $this->assertEquals($expectedOutput, $result);
}

function providerGetCryptoTokenUnknown(){
return array(
    array("ethereu", Accepted_Crypto::UNKNOWN),
    array("EB.T.H", Accepted_Crypto::UNKNOWN),
    array("sadlkfjhsl", Accepted_Crypto::UNKNOWN)
);
}

}