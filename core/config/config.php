<?php

//Monolog config
abstract class Monolog_Config{
    const MAX_FILES = 7;
    const LOG_FILE  = 'alex_koinex.log';
}

//Crypto token config
abstract class Accepted_Crypto{
    const ETHEREUM      = 0;
    const BITCOIN       = 1;
    const RIPPLE        = 2;
    const BITCOINCASH   = 3;
    const LITECOIN      = 4;
}

abstract class Crypto_Skopen_Values{
    const ETHEREUM = [
        "ether",
        "ethereum",
        "ETH",
        "E.T.H"
    ];
    const BITCOIN = [
        "bitcoin",
        "BTC",
        "bit",
        "B.T.C"
    ];
    const LITECOIN = [
        "litecoin",
        "LTC",
        "lite",
        "L.T.C" 
    ];
    const RIPPLE = [
        "ripple",
        "XRP",
        "X.R.P"
    ];
    const BITCOINCASH = [
        "bitcoin cash",
        "bitcoincash",
        "BCH",
        "B.C.H"
    ];
}

$ACCEPTED_CRYPTO_ARRAY = [
    "Ether" => Accepted_Crypto::ETHEREUM,
    "Litecoin" => Accepted_Crypto::LITECOIN,
    "Ripple" => Accepted_Crypto::RIPPLE,
    "Bitcoin Cash" => Accepted_Crypto::BITCOINCASH,
    "Bitcoin" => Accepted_Crypto::BITCOIN
];