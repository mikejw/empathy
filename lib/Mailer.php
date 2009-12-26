<?php

namespace Empathy;

class Mailer
{
  public $recipients;
  public $subject;
  public $message;
  public $header;
  public $result;
  public $from;

  public function __construct($r, $s, $m, $f)
  {
    $this->header = 'From: '.$f."\r\n"
      .'Reply-To: '.$f."\r\n"
      .'X-Mailer: PHP/'.phpversion();

    $this->recipients = $r;
    $this->subject = $s;
    $this->message = $m;
    $this->result = 0;
    $this->from = $f;
    $this->send();
  }



  public function send()
  {
    $result = array();
    $message = '';
    $i = 0;
    foreach($this->recipients as $index => $r)
      {
	$message = str_replace('___', $r['alias'], $this->message);
	$result[$i] = mail($r['address'], $this->subject, $message, $this->header, '-f'.$this->from);				
	$i++;
      }
    
    if(in_array(0, $result))
      {
	$this->result = 0;
      }
    else
      {
	$this->result = 1;
      }
  }
}
?>