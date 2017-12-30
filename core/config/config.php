<?php

namespace Alexa_Koinex;

//Monolog config
abstract class Monolog_Config{
    const MAX_FILES = 7;
    const LOG_FILE  = 'alex_koinex.log';
}

//Crypto token config
abstract class Accepted_Crypto{
    const UNKNOWN       = -1;
    const ETHEREUM      = 0;
    const BITCOIN       = 1;
    const RIPPLE        = 2;
    const BITCOINCASH   = 3;
    const LITECOIN      = 4;
}

abstract class Crypto_Spoken_Values{
    const ALL_CRYPTOS = [
        Accepted_Crypto::ETHEREUM => [
            "ether",
            "ethereum",
            "ETH",
            "E.T.H"
        ],
        Accepted_Crypto::BITCOIN => [
            "bitcoin",
            "BTC",
            "bit",
            "B.T.C"
        ],
        Accepted_Crypto::LITECOIN => [
            "litecoin",
            "LTC",
            "lite",
            "L.T.C" 
        ],
        Accepted_Crypto::RIPPLE => [
            "ripple",
            "XRP",
            "X.R.P"
        ],
        Accepted_Crypto::BITCOINCASH => [
            "bitcoin cash",
            "bitcoincash",
            "BCH",
            "B.C.H"
        ]
    ];
}

abstract class Alexa_Constants{
    const CRYPTO_SLOT = "CryptoCurrencyIntentSlot";
}