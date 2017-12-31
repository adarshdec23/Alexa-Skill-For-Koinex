<?php

namespace adarshdec23\Config;

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