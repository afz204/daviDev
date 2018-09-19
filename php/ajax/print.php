<?php 
require '../../config/config.php';

// require_once("../../assets/vendors/dompdf/dompdf_config.inc.php");
// menyertakan autoloader
require_once '../../assets/vendors/dompdf/autoload.inc.php';

// mengacu ke namespace DOMPDF
use Dompdf\Dompdf;

// menggunakan class dompdf
$dompdf = new Dompdf();

$html = file_get_contents("print_invoice.php");
// $html = file_get_contents("print_amplop.php");
$dompdf->loadHtml($html);

// (Opsional) Mengatur ukuran kertas dan orientasi kertas
// $dompdf->setPaper(array(0, 0, 400, 700), 'landscape'); //amplop
$dompdf->setPaper(array(0, 0, 670, 450), 'potrait');

// Menjadikan HTML sebagai PDF
$dompdf->render();

// Output akan menghasilkan PDF (1 = download dan 0 = preview)
$dompdf->stream("Codingan",array("Attachment"=>0));

?>