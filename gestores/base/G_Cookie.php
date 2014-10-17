<?php
class G_Cookie{
  private $cookie_list = array();

  function __construct(){}

  function setCookieList($l){
    $this->cookie_list = $l;
  }

  function getCookieList(){
    return $this->cookie_list;
  }

  function addCookieToList($k,$v){
    $list = $this->getCookieList();
    $list[$k] = $v;

    setcookie ("osumifw[".$k."]", $v, time() + (3600*24*31), "/", ".osumi.es");

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
    $list = array();

    if (isset($_COOKIE['photobook'])) {
      foreach ($_COOKIE['photobook'] as $name => $value) {
        $name = htmlspecialchars($name);
        $value = htmlspecialchars($value);

        $list[$name] = $value;
      }
    }

    $this->setCookieList($list);
  }

  function saveCookies(){
    $list = $this->getCookieList();

    foreach ($list as $key => $value){
      setcookie ("photobook[".$key."]", $value, time() + (3600*24*31), "/", ".osumi.es");
    }
  }

  function cleanCookies(){
    $list = $this->getCookieList();

    foreach ($list as $key => $value){
      setcookie ("photobook[".$key."]", $value, 1, "/", ".osumi.es");
    }

    $this->setCookieList(array());
  }
}