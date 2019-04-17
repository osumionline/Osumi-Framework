<?php
class OCrypt {
  private $key = null;
  private $method = 'aes-256-cbc';

  function __construct($key=null){
    if (!is_null($key)){
      $this->setKey($key);
    }
  }

  public function setKey($key){
    $this->key = $key;
  }
  public function setMethod($method){
    $this->method = $method;
  }

  public function generateKey(){
    $this->key = base64_encode(openssl_random_pseudo_bytes(32));
    return $this->key;
  }

  function encrypt($data, $key=null) {
    if (is_null($key)){
      $key = $this->key;
    }
    $encryption_key = base64_decode($key);
    $iv             = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->method));
    $encrypted      = openssl_encrypt($data, $this->method, $encryption_key, 0, $iv);
    return base64_encode($encrypted . '::' . $iv);
  }

  public function decrypt($data, $key=null) {
    if (is_null($key)){
      $key = $this->key;
    }
    $encryption_key            = base64_decode($key);
    list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);
    return openssl_decrypt($encrypted_data, $this->method, $encryption_key, 0, $iv);
  }
}