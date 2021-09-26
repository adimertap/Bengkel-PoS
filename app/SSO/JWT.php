<?php

//------------------------------------------//
// JSON Web Token Class 					//
// Version 2018 Build 180705				//
// Copyright (C) 2018 Nyoman Piarsa			//
// All right reserved						//
//------------------------------------------//

namespace App\SSO; // for Laravel users

class JWT{
	private $secret = "SSO-JWT-SECRET-KEY";			// JWT Secret
	private $header = [
	    "alg"     => "HS256",
	    "typ"     => "JWT"
	];		 										// JWT Header
	private $payload;								// JWT Data
	private $jwtString;								// JWT encapsulated String

	//
	public function __construct(){

	}	

	private function base64UrlEncode(string $data): string{
	    $urlSafeData = strtr(base64_encode($data), '+/', '-_');
	    return rtrim($urlSafeData, '='); 
	} 
	private function base64UrlDecode(string $data): string{
	    $urlUnsafeData = strtr($data, '-_', '+/');
	    $paddedData = str_pad($urlUnsafeData, strlen($data) % 4, '=', STR_PAD_RIGHT);
	    return base64_decode($paddedData);
	}

	// generate JWT encapsulated String
	private function generateJWT(
	    string $algo,
	    array $header,
	    array $payload,
	    string $secret
	):string {
	    $headerEncoded = $this->base64UrlEncode(json_encode($header));
	    $payloadEncoded = $this->base64UrlEncode(json_encode($payload));
	    // Delimit with period (.)
	    $dataEncoded = "$headerEncoded.$payloadEncoded";
	    $rawSignature = hash_hmac($algo, $dataEncoded, $secret, true);
	    $signatureEncoded = $this->base64UrlEncode($rawSignature);
	    // Delimit with second period (.)
	    $this->jwtString = "$dataEncoded.$signatureEncoded";
	    return $this->jwtString;
	}

	// verify / decode encapsulated JWT String
	private function verifyJWT(string $algo, string $jwt, string $secret){
	    list($headerEncoded, $payloadEncoded, $signatureEncoded) = explode('.', $jwt);
	    $dataEncoded = "$headerEncoded.$payloadEncoded";
	    $signature = $this->base64UrlDecode($signatureEncoded);
	    $rawSignature = hash_hmac($algo, $dataEncoded, $secret, true);
	    if(hash_equals($rawSignature, $signature)){
	    	$this->payload = json_decode($this->base64UrlDecode($payloadEncoded));
	    	return $this->payload;
	    }else{
	    	return false;
	    }
	}		
	
	//
	public function setPayloadJWT($payload){
		$this->payload = (array) $payload;
	}
	
	//
	public function getPayloadJWT(){
		return $this->payload;
	}
	
	//
	public function setJWTString($jwt){
		$this->jwtString = $jwt;
	}
	
	//
	public function getJWTString(){
		return $this->jwtString;
	}
	
	//
	public function encodeJWT(){
		return $this->generateJWT('sha256', $this->header ,$this->payload, $this->secret);
	}
	
	//
	public function decodeJWT(){
		return $this->verifyJWT('sha256', $this->jwtString, $this->secret);
	}
}
// $jwt = new JWT();
// $jwt->setPayload(array('data'=>"Hello 12345",'key'=>'254789'));
// echo $jwt->encode();
// $jwt->setJWTString($jwt->getJWTString());
// $jwt->decode();
// print_r($jwt->getPayload());
