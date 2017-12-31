<?php
 
 require __DIR__ . '/vendor/autoload.php';
 use adarshdec23\LogicController;

/**
 * You can't have errors if you disable error reporting.
 */
error_reporting(E_ALL & ~E_NOTICE);
$logicController = new LogicController();
$logicController->execute();