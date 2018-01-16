<?php

namespace Alexa\Request;

use RuntimeException;
use InvalidArgumentException;
use DateTime;

abstract class Request {
	const TIMESTAMP_VALID_TOLERANCE_SECONDS = 15;

	public $requestId;
	public $timestamp;
	public $user;

	public function __construct($data) {
		$this->requestId = $data['request']['requestId'];
		$this->timestamp = new DateTime($data['request']['timestamp']);
		$this->user = new User($data['session']['user']);
	}

	public static function fromData($data) {
		$requestType = $data['request']['type'];

		if (!class_exists('\\Alexa\\Request\\' . $requestType)) {
			throw new RuntimeException('Unknown request type: ' . $requestType);
		}

		$className = '\\Alexa\\Request\\' . $requestType;

		$request = new $className($data);
		return $request;
	}

	public function validate() {
		$this->validateTimestamp();
		$this->validateSignature();
	}


	private function validateCertificateUrl($url){
		$url=parse_url($url);
		$valid=$url["scheme"]=="https";
		$valid&=strtolower($url["host"])==strtolower("s3.amazonaws.com");
		$valid&=(!isset($url["port"])) || (isset($url["port"]) && ($url["port"]==443 || $url["port"]==null));
		$valid&=strpos($url["path"],"/echo.api/")===0;
		if(!$valid)
			throw new InvalidArgumentException('Wrong Signature certificate url');
		
	}

	private function validateSignature() {
		$url=getallheaders()["SignatureCertChainUrl"];
		$this->validateCertificateUrl($url);
		
		$cert=file_get_contents($url);
		$ssl=openssl_x509_parse($cert);
		$key=openssl_pkey_get_public($cert);
		$validFrom=$ssl['validFrom_time_t'];
		$validTo=$ssl['validTo_time_t'];
		if($validFrom>time() || $validTo<time()){
			throw new InvalidArgumentException('Wrong Signature timestamps');
		}
		if($ssl["extensions"]["subjectAltName"]!=self::SAN_NAME){
			throw new InvalidArgumentException('Wrong Subject Alternative Names');
		}
		$signature=base64_decode(getallheaders()["Signature"]);
		$result='';
		$hash=sha1(file_get_contents('php://input'),true);
		openssl_public_decrypt($signature,$result,$key);
		$result=substr($result,15);
		if($result!=$hash){
			throw new InvalidArgumentException('Wrong Signature hash');
		}
	}

	private function validateTimestamp() {
		date_default_timezone_set('UTC');
		$now = new DateTime;
		$differenceInSeconds = $now->getTimestamp() - $this->timestamp->getTimestamp();

		if ($differenceInSeconds > self::TIMESTAMP_VALID_TOLERANCE_SECONDS) {
			throw new InvalidArgumentException('Request timestamp was too old. Possible replay attack.');
		}
	}
}
