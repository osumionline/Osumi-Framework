<?php
class OEmail{
  private $debug_mode   = false;
  private $log          = null;
  private $recipients   = array();
  private $subject      = '';
  private $message      = '';
  private $is_html      = true;
  private $from         = '';
  private $result_ok    = array();
  private $result_error = array();
  
  function __construct() {
    global $c, $where;
    $this->setDebugMode($c->getDebugMode());
    $l = new OLog();
    $this->setLog($l);
    $this->getLog()->setSection($where);
    $this->getLog()->setModel('OEmail');
  }

  public function setDebugMode($dm){
    $this->debug_mode = $dm;
  }

  public function getDebugMode(){
    return $this->debug_mode;
  }

  public function setLog($l){
    $this->log = $l;
  }

  public function getLog(){
    return $this->log;
  }

  public function setRecipients($r){
    $this->recipients = $r;
  }

  public function getRecipients(){
    return $this->recipients;
  }
  
  public function addRecipient($r){
    $list = $this->getRecipients();
    array_push($list, $r);
    $this->setRecipients($list);
  }

  public function setSubject($s){
    $this->subject = $s;
  }

  public function getSubject(){
    return $this->subject;
  }
  
  public function setMessage($m){
    $this->message = $m;
  }

  public function getMessage(){
    return $this->message;
  }
  
  public function setIsHtml($ih){
    $this->is_html = $ih;
  }

  public function getIsHtml(){
    return $this->is_html;
  }
  
  public function setFrom($f){
    $this->from = $f;
  }

  public function getFrom(){
    return $this->from;
  }
  
  public function setResultOk($ro){
    $this->result_ok = $ro;
  }

  public function getResultOk(){
    return $this->result_ok;
  }
  
  public function addResultOk($ro){
    $list = $this->getResultOk();
    array_push($list, $ro);
    $this->setResultOk($list);
  }
  
  public function setResultError($re){
    $this->result_error = $re;
  }

  public function getResultError(){
    return $this->result_error;
  }
  
  public function addResultError($re){
    $list = $this->getResultError();
    array_push($list, $re);
    $this->setResultError($list);
  }
  
  public function send(){
    $ret = array('status'=>'ok','mens'=>'');
    
    // Si no hay destinatarios fuera
    if (count($this->getRecipients())==0){
      $ret['status'] = 'error';
      $ret['mens'] = 'No hay destinatarios!';
    }
    else{
    
      // Cojo lista de destinatarios y la recorro
      $list = $this->getRecipients();
      foreach ($list as $item){
        $headers = '';
        // Si es html tiene cabeceras especiales
        if ($this->getIsHtml()){
          $headers .= 'MIME-Version: 1.0' . "\r\n";
          $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        }
        $headers .= 'To: ' . $item . "\r\n";
        $headers .= 'From: ' . $this->getFrom() . "\r\n";
        
        // Lo envio
        if (mail($item, $this->getSubject(), $this->getMessage(), $headers)){
          $this->addResultOk($item);
        }
        else{
          $this->addResultError($item);
          $ret['status'] = 'error';
          $ret['mens'] .= 'Error al enviar email a: '.$item.' - ';
        }
      }
    }
    
    return $ret;
  }
}