<?php

require __DIR__ . '/../../../vendor/autoload.php';

use adarshdec23\Koinex\LogicKoinexAPI;
use adarshdec23\Config\Accepted_Crypto;

class LogicKoinexAPITest extends \PHPUnit\Framework\TestCase{

    private static $koinexReturnJSONCorrect = '
    {
        "prices": {
          "BTC": "1220000.0",
          "XRP": "201.0",
          "ETH": "63100.0",
          "BCH": "212001.0",
          "LTC": "20350.0",
          "MIOTA": 217.56,
          "OMG": 922.13,
          "GNT": 46.24
        },
        "stats": {
          "ETH": {
            "last_traded_price": "63100.0",
            "lowest_ask": "63100.0",
            "highest_bid": "63001.0",
            "min_24hrs": "60000.0",
            "max_24hrs": "73100.0",
            "vol_24hrs": "5739.641"
          },
          "BTC": {
            "last_traded_price": "1220000.0",
            "lowest_ask": "1222000.0",
            "highest_bid": "1220000.0",
            "min_24hrs": "1180500.0",
            "max_24hrs": "1285000.0",
            "vol_24hrs": "668.8021"
          },
          "LTC": {
            "last_traded_price": "20350.0",
            "lowest_ask": "20350.0",
            "highest_bid": "20351.0",
            "min_24hrs": "19000.0",
            "max_24hrs": "27500.0",
            "vol_24hrs": "28594.176"
          },
          "XRP": {
            "last_traded_price": "201.0",
            "lowest_ask": "198.0",
            "highest_bid": "201.0",
            "min_24hrs": "115.0",
            "max_24hrs": "270.0",
            "vol_24hrs": "57359594.1"
          },
          "BCH": {
            "last_traded_price": "212001.0",
            "lowest_ask": "213000.0",
            "highest_bid": "212001.0",
            "min_24hrs": "202000.0",
            "max_24hrs": "252000.0",
            "vol_24hrs": "2072.28"
          }
        }
      }
    ';

    private static $koinexReturnJSONIncorrect = '
    {
        "prices": {
          "BTC": "1220000.0",
          "XRP": "201.0",
          "Notice ETH is missing": "63100.0",
          "BCH": "212001.0",
          "LTC": "20350.0",
          "MIOTA": 217.56,
          "OMG": 922.13,
          "GNT": 46.24
        },
        "stats": {
          "ETH": {
            "last_traded_price": "63100.0",
            "lowest_ask": "63100.0",
            "highest_bid": "63001.0",
            "min_24hrs": "60000.0",
            "max_24hrs": "73100.0",
            "vol_24hrs": "5739.641"
          },
          "BTC": {
            "last_traded_price": "1220000.0",
            "lowest_ask": "1222000.0",
            "highest_bid": "1220000.0",
            "min_24hrs": "1180500.0",
            "max_24hrs": "1285000.0",
            "vol_24hrs": "668.8021"
          },
          "LTC": {
            "last_traded_price": "20350.0",
            "lowest_ask": "20350.0",
            "highest_bid": "20351.0",
            "min_24hrs": "19000.0",
            "max_24hrs": "27500.0",
            "vol_24hrs": "28594.176"
          },
          "XRP": {
            "last_traded_price": "201.0",
            "lowest_ask": "198.0",
            "highest_bid": "201.0",
            "min_24hrs": "115.0",
            "max_24hrs": "270.0",
            "vol_24hrs": "57359594.1"
          },
          "BCH": {
            "last_traded_price": "212001.0",
            "lowest_ask": "213000.0",
            "highest_bid": "212001.0",
            "min_24hrs": "202000.0",
            "max_24hrs": "252000.0",
            "vol_24hrs": "2072.28"
          }
        }
      }
    ';

    /**
     * @param array $apiReturnData The data returned by Koinex
     * @param string $inputCryptoToken The token to extract
     * @dataProvider providerExtractCryptoValueSuccess
     */
    function testExtractCryptoValueSuccess($apiReturnData, $inputCryptoToken, $expectedOutput){
        $this->koinexApi = new LogicKoinexAPI();
        $result = $this->koinexApi->extractCryptoValue($apiReturnData, $inputCryptoToken);
        $this->assertSame($expectedOutput, $result);
    }

    function providerExtractCryptoValueSuccess(){
        return array(
            array(self::$koinexReturnJSONCorrect, Accepted_Crypto::ETHEREUM, "63100.0"),
            array(self::$koinexReturnJSONCorrect, Accepted_Crypto::BITCOINCASH, "212001.0")
        );
    }

    /**
     * @param array $apiReturnData The data returned by Koinex
     * @param string $inputCryptoToken The token to extract
     * @param mixed $expectedOutput The expected output value
     * @dataProvider providerExtractCryptoValueFail
     */
    function testExtractCryptoValueFail($apiReturnData, $inputCryptoToken, $expectedOutput){
        // Warning: notice, strict. We handle this ourselves
        PHPUnit\Framework\Error\Warning::$enabled = FALSE;
        PHPUnit\Framework\Error\Notice::$enabled = FALSE;
        error_reporting(E_ALL & ~E_NOTICE);
        
        $this->koinexApi = new LogicKoinexAPI();
        $result = $this->koinexApi->extractCryptoValue($apiReturnData, $inputCryptoToken);
        $this->assertSame($expectedOutput, $result);
    }

    function providerExtractCryptoValueFail(){
        return array(
            //Wrong API data with correct Crypto value
            array(self::$koinexReturnJSONIncorrect, Accepted_Crypto::ETHEREUM, false),
            //Correct API data with wrong Crypto value
            array(self::$koinexReturnJSONCorrect, Accepted_Crypto::UNKNOWN, false)
        );
    }
}