<?php 
require '../../config/config.php';
require_once '../../assets/vendors/dompdf/autoload.inc.php';
use Dompdf\Dompdf;
$dompdf = new Dompdf();

$transactionID = $_GET['transactionID'];

$data = $config->getData('*', 'transaction', "transactionID = '". $transactionID ."' ");

$content = '
<html>
<head>
</head>
<style type="text/css">
    *{
        margin:0;
    }
    body{
        margin:0 auto;
        width:470px;
        height: 0 auto;
        border:0px solid black;
        clear: both;
    }
    p { float: left; clear: left; margin: 1em auto; border: 0px solid #888; }

.wb { word-break: break-all; width:400px; }

.ww { word-wrap: break-word; }

.h { -ms-hyphens: auto; hyphens: auto; }

.mw { max-width: 100%; }
.content {
   margin-top: 480px; 
   vertical-align: bottom;
   }
.isi {
   display: block;
   padding: 12px;
   /* margin: 0px 6px; */
   font-size: 15px;
   line-height: 1.42857143;
   color: #333;
   word-break: break-all;
   word-wrap: break-word;
   text-align: center;
   border: 0px solid #ccc;
   border-radius: 4px;
   font-family: Arial, "sans-serif";
   text-transform: capitalize;
}
.fromcard {
   display: block;
   padding: 12px;
   /* margin: 0px 10px; */
   font-size: 16px;
   line-height: 1.42857143;
   color: #333;
   word-break: break-all;
   word-wrap: break-word;
   border: 0px solid #ccc;
   border-radius: 4px;
   font-weight: 600;
   font-family: Arial, "sans-serif";
   text-align: center;
   text-transform: capitalize;
}
</style>
<body>
   <div class="content">
       <!-- <div class="fromcard">
       To : Arfan Azhari
       </div> -->
       <div class="isi">
       '.$data['card_isi'] .'
       </div>
   </div>
</body>
</html>
';


// $html = file_get_contents("print_do.php");
// $dompdf->loadHtml($html);
$dompdf->loadHtml($content);

// (Opsional) Mengatur ukuran kertas dan orientasi kertas
$dompdf->setPaper(array(0, 0, 419.53, 595.28), 'portrait');

// Menjadikan HTML sebagai PDF
$dompdf->render();

// Output akan menghasilkan PDF (1 = download dan 0 = preview)
$dompdf->stream("Card-Messages". $transactionID,array("Attachment"=>0));

?>