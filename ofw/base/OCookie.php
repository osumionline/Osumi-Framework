<?php
class OCookie{
  private $cookie_list = [];

  function __construct(){}

  function setCookieList($l){
    $this->cookie_list = $l;
  }

  function getCookieList(){
    return $this->cookie_list;
  }

  function addCookieToList($k,$v){
    global $c;
    $list = $this->getCookieList();
    $list[$k] = $v;

    setcookie ($c->getCookiePrefix().'['.$k.']', $v, time() + (3600*24*31), '/', $c->getCookieUrl());

    $this->setCookieList($list);
  }

  function getCookie($k){
    $list = $this->getCookieList();
    if (array_key_exists($k, $list)){
      return $list[$k];
    }
    else{
      return false;
    }
  }

  function loadCookies(){
    global $c;
    $list = [];

    if (isset($_COOKIE[$c->getCookiePrefix()])) {
      foreach ($_COOKIE[$c->getCookiePrefix()] as $name => $value) {
        $name = htmlspecialchars($name);
        $value = htmlspecialchars($value);

        $list[$name] = $value;
      }
    }

    $this->setCookieList($list);
  }

  function saveCookies(){
    global $c;
    $list = $this->getCookieList();

    foreach ($list as $key => $value){
      setcookie ($c->getCookiePrefix().'['.$key.']', $value, time() + (3600*24*31), '/', $c->getCookieUrl());
    }
  }

  function cleanCookies(){
    global $c;
    $list = $this->getCookieList();

    foreach ($list as $key => $value){
      setcookie ($c->getCookiePrefix().'['.$key.']', $value, 1, '/', $c->getCookieUrl());
    }

    $this->setCookieList([]);
  }
}
