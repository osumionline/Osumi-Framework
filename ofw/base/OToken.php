<?php
class OToken{
  private $secret = null;
  private $params = [];
  private $token = null;

  function __construct($secret) {
    $this->secret = $secret;
  }

  public function setParams($params){
    $this->params = $params;
  }
  public function addParam($key, $value){
    $this->params[$key] = $value;
  }
  public function getParams(){
    return $this->params;
  }
  public function getParam($key){
    if (array_key_exists($key, $this->params)){
      return $this->params[$key];
    }
    else{
      return false;
    }
  }

  public function getToken(){
    if (!is_null($this->token)){
      return $this->token;
    }
    $header = ["alg"=> "HS256", "typ"=>"JWT"];
    $header_64 = base64_encode(json_encode($header));
    $payload = $this->params;
    $payload_64 = base64_encode(json_encode($payload));

    $signature = hash_hmac('sha256', $header_64.'.'.$payload_64, $this->secret);

    $this->token = $header_64.'.'.$payload_64.'.'.$signature;

    return $this->token;
  }

  public function checkToken($token){
    $pieces = explode('.', $token);
    $header_64  = $pieces[0];
    $payload_64 = $pieces[1];
    $signature  = $pieces[2];

    $signature_check = hash_hmac('sha256', $header_64.'.'.$payload_64, $this->secret);

    if ($signature === $signature_check){
      $this->params = json_decode(base64_decode($payload_64), true);
      return true;
    }
    return false;
  }
}
