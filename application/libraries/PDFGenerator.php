<?php

class PDFGenerator
{
  public function generate($html,$filename, $kertas = 'a4', $type = 'portrait')
  {
    // include autoloader
    define('DOMPDF_ENABLE_AUTOLOAD', true);
    require_once("./vendor/dompdf/dompdf_config.inc.php");

    $dompdf = new DOMPDF();

    $dompdf->load_html($html);
    // $dompdf->set_paper($kertas, $type);
    $dompdf->render();
    // $dompdf->stream($filename.'.pdf',array("Attachment"=>0));
    $output = $dompdf->output();
    if ( !is_dir( './uploads/'.$filename ) ) {
        mkdir('./uploads/'.$filename);
    }
    file_put_contents('./uploads/'.$filename.'/' . $filename.'.pdf', $output);
    // $pdf->save('./uploads/'.$filename.'/' . $filename.'.pdf');
  }
}
