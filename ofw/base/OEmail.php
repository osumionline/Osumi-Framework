<?php
class OEmail{
  private $debug_mode   = false;
  private $log          = null;
  private $is_smtp      = false;
  private $smtp_data    = [];
  private $recipients   = [];
  private $subject      = '';
  private $message      = '';
  private $is_html      = true;
  private $from         = '';
  private $from_name    = null;
  private $result_ok    = [];
  private $result_error = [];

  function __construct() {
    global $c, $where;
    $this->setDebugMode($c->getDebugMode());
    $l = new OLog();
    $this->setLog($l);
    $this->getLog()->setSection($where);
    $this->getLog()->setModel('OEmail');
    if ($c->getDefaultModule('email_smtp')){
      $this->setIsSMTP(true);
      $this->setSMTPData( $c->getSMTP() );
    }
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
  
  public function setIsSMTP($is){
    $this->is_smtp = $is;
  }
  public function getIsSMTP(){
    return $this->is_smtp;
  }

  public function setSMTPData($sd){
    $this->smtp_data = $sd;
  }
  public function getSMTPData(){
    return $this->smtp_data;
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

  public function setFrom($f, $name=null){
    $this->from = $f;
    if (!is_null($name)){
      $this->from_name = $name;
    }
  }
  public function getFrom(){
    return $this->from;
  }
  
  public function setFromName($n){
    $this->from_name = $n;
  }
  public function getFromName(){
    return $this->from_name;
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
    $ret = ['status'=>'ok','mens'=>''];

    // Si no hay destinatarios fuera
    if (count($this->getRecipients())==0){
      $ret['status'] = 'error';
      $ret['mens'] = 'No hay destinatarios!';
    }
    else{
      $list = $this->getRecipients();

      if (!$this->getIsSMTP()){
        foreach ($list as $item){
          $headers = '';
          // Si es html tiene cabeceras especiales
          if ($this->getIsHtml()){
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
          }
          $headers .= "To: ".$item."\r\n";
          $headers .= "From: ".$this->getFrom().( is_null($this->getFromName()) ? "" : "<".$this->getFromName().">" )."\r\n";
  
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
      else{
        $smtp_data = $this->getSMTPData();
        foreach ($list as $item){
          try{
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            
            $mail->CharSet = 'UTF-8';
            //$mail->SMTPDebug = 1;
            $mail->Host = $smtp_data['host'];
            $mail->Port = $smtp_data['port'];
            $mail->SMTPSecure = $smtp_data['secure'];
            $mail->SMTPAuth = true;
            $mail->Username = $smtp_data['user'];
            $mail->Password = $smtp_data['pass'];
            if (is_null($this->getFromName())){
              $mail->setFrom($this->getFrom());
            }
            else{
              $mail->setFrom($this->getFrom(), $this->getFromName());
            }
            $mail->addAddress($item);
            $mail->Subject = $this->getSubject();
            $mail->msgHTML($this->getMessage());
            
            if ($mail->send()) {
              $this->addResultOk($item);
            }
            else {
              $this->addResultError($item);
              $ret['status'] = 'error';
              $ret['mens'] .= 'Error al enviar email a: '.$item.' - ';
            }
          }
          catch(Exception $e){
            $this->addResultError($item);
            $ret['status'] = 'error';
            $ret['mens'] .= 'Error al enviar email a: '.$item.' - ';
          }

          $mail = null;
        }
      }
      
    }

    return $ret;
  }
}
