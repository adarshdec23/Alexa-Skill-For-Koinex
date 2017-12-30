<?php


require_once __DIR__."/../../core/LogicController.php";
require_once __DIR__."/../../core/config/config.php";


var_dump(\Accepted_Crypto::ETHEREUM);
class LogicControllerTest extends \PHPUnit_Framework_TestCase{

/**
* @param string $inputData
* @param int $expectedOutput
* @dataProvider providerGetCryptoToken
*/
function testGetCryptoToken($inputData, $expectedOutput){
        $this->logicController = new LogicController();
        $result = $this->logicController->getCryptoToken($inputData);
        $this->assertEquals($expectedOutput, $result);
}

function providerGetCryptoToken(){
    return array(
        array("ethereum", Accepted_Crypto::ETHEREUM),
        array("E.T.H", Accepted_Crypto::ETHEREUM),
        array("bitcoIn cAsh", Accepted_Crypto::BITCOINCASH)
    );
}

}