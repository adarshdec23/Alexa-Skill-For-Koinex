<?php

namespace adarshdec23\Config;

//Crypto token config
abstract class Accepted_Crypto{
    const UNKNOWN       = -1;
    const ETHEREUM      = 0;
    const BITCOIN       = 1;
    const RIPPLE        = 2;
    const BITCOINCASH   = 3;
    const LITECOIN      = 4;
}