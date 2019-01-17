<?php
class OPDF{
  private $creator  = '';
  private $author   = '';
  private $title    = '';
  private $subject  = '';
  private $keywords = '';
  private $font     = 'helvetica';
  private $pages    = [];
  private $pdf_obj  = null;
  private $pdf_dir  = '';

  function __construct($data=null){
    if (!is_null($data) && is_array($data)){
      $this->setCreator(  array_key_exists('creator',  $data)?$data['creator']  : '' );
      $this->setAuthor(   array_key_exists('author',   $data)?$data['author']   : '' );
      $this->setTitle(    array_key_exists('title',    $data)?$data['title']    : '' );
      $this->setSubject(  array_key_exists('subject',  $data)?$data['subject']  : '' );
      $this->setKeywords( array_key_exists('keywords', $data)?$data['keywords'] : '' );
      $this->setFont(     array_key_exists('font',     $data)?$data['font']     : 'helvetica' );
      $this->setPdfDir(   array_key_exists('ruta_pdf', $data)?$data['ruta_pdf'] : '' );
    }
  }

  public function setCreator($c){
    $this->creator = $c;
  }
  public function getCreator(){
    return $this->creator;
  }

  public function setAuthor($a){
    $this->author = $a;
  }
  public function getAuthor(){
    return $this->author;
  }

  public function setTitle($t){
    $this->title = $t;
  }
  public function getTitle(){
    return $this->title;
  }

  public function setSubject($s){
    $this->subject = $s;
  }
  public function getSubject(){
    return $this->subject;
  }

  public function setKeywords($k){
    $this->keywords = $k;
  }
  public function getKeywords(){
    return $this->keywords;
  }

  public function setFont($f){
    $this->font = $f;
  }
  public function getFont(){
    return $this->font;
  }

  public function setPages($p){
    $this->pages = $p;
  }
  public function getPages(){
    return $this->pages;
  }

  public function addPage($p){
    $pages = $this->getPages();
    array_push($pages,$p);
    $this->setPages($pages);
  }

  public function setPdfObj($po){
    $this->pdf_obj = $po;
  }
  public function getPdfObj(){
    return $this->pdf_obj;
  }

  public function setPdfDir($pd){
    $this->pdf_dir = $pd;
  }
  public function getPdfDir(){
    return $this->pdf_dir;
  }

  public function render(){
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // set document information
    $pdf->SetCreator($this->getCreator());
    $pdf->SetAuthor($this->getAuthor());
    $pdf->SetTitle($this->getTitle());
    $pdf->SetSubject($this->getSubject());
    $pdf->SetKeywords($this->getKeywords());

    // set default header data
    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 061', PDF_HEADER_STRING);

    // set header and footer fonts
    $pdf->setHeaderFont([PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN]);
    $pdf->setFooterFont([PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA]);

    // set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    // set margins
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    // set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    // set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    // set font
    $pdf->SetFont($this->getFont(), '', 10);

    $pages = $this->getPages();
    foreach ($pages as $p){
      // add a page
      $pdf->AddPage();

      // output the HTML content
      $pdf->writeHTML($p, true, false, true, false, '');
    }

    // reset pointer to the last page
    $pdf->lastPage();

    $this->setPdfObj($pdf);
  }

  public function getPdf(){
    $this->render();
    $pdf = $this->getPdfObj();
    $pdf->Output($this->getPdfDir(), 'I');
  }
}
