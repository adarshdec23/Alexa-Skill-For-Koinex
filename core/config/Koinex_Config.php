<?php

namespace adarshdec23\Config;

abstract class Koinex_Config{
    const API_URL = "https://koinex.in/api/ticker";
    const Koinex_Accepted_Crypto = [
        Accepted_Crypto::BITCOIN => "BTC",
        Accepted_Crypto::ETHEREUM => "ETH",
        Accepted_Crypto::BITCOINCASH => "BCH",
        Accepted_Crypto::LITECOIN => "LTC",
        Accepted_Crypto::RIPPLE => "XRP"
    ];
}