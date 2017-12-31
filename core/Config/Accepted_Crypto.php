<?php

namespace adarshdec23\Config;

//Crypto token config
abstract class Accepted_Crypto{
    const UNKNOWN       = -1;
    const ETHEREUM      = "Ethereum";
    const BITCOIN       = "Bitcoin";
    const RIPPLE        = "Ripple";
    const BITCOINCASH   = "Bitcoin Cash";
    const LITECOIN      = "Litecoin";
}