<?php 

require "../../config/config.php";
require '../../config/Mail.php';

$transactionID = $_POST['transactionID'];

if( isset($_GET['type']) && $_GET['type'] == 'savedate') {
    $tgl = $config->getDate("Y-m-d H:m:s");
    $query = "UPDATE transaction SET ResiDate = '".$tgl."' WHERE transactionID = '".$transactionID."' ";
    $stmt = $config->runQuery($query);
    $stmt->execute();
}

$downloadfile = '<tr style="background-color: #ffffff;">
                        <td style="border-bottom: 0.5px solid; font-weight: 600; font-size: 14px; padding: 8px 0px; text-align: center;" colspan="5">
                        <div style="background-color: blue; width: 120px;padding: 3px;border: 1px solid blue;border-radius: 5px; margin-left: 41%; color: #FFF;" >
                            <a href="'.URL.'php/ajax/downloadinvoice.php?transactionID='.$transactionID.'" style="color: #FFF; text-decoration: none;" target="_blank">Download Invoice</a>
                        </div>
                        </td>
                    </tr>';
if(isset($_GET['type'])) {
    $downloadfile = '';
}

$arrtime = [
    0 => '9am - 1pm',
    1 => '2pm - 5pm',
    2 => '6pm - 8pm',
    3 => '9pm - 0am',
    4 => '1am - 5am',
    5 => '6am - 8am'
];

$arrpaid = [
    'UNPAID',
    'PAID'
];


$data = $config->getData('transaction.*, corporate_pics.name as CorporateName, corporate_pics.email as CorporateEmail, corporate_pics.nomor as CorporatePhone, customer.FullName as OrganicName, customer.Email as OrganicEmail, customer.Mobile as OrganicPhone, provinces.name as ProvinsiName, regencies.name as KotaName, districts.name as Kecamatan, villages.name as Kelurahan', 'transaction LEFT JOIN corporate_pics ON corporate_pics.corporate_id = transaction.CustomerID LEFT JOIN customer on customer.CustomerUniqueID = transaction.CustomerID LEFT JOIN provinces ON provinces.id = transaction.provinsi_id LEFT JOIN regencies on regencies.id = transaction.kota_id LEFT JOIN districts ON districts.id = transaction.kecamata_id LEFT JOIN villages on villages.id = transaction.kelurahan_id', "transactionID = '". $transactionID ."' ");
// $config->_debugvar($data);
$subtotal = $config->getData('SUM(product_price * product_qty) as Subtotal', 'transaction_details', "id_trx = '". $transactionID ."'");

$product = $config->runQuery("SELECT * FROM transaction_details WHERE id_trx = '". $transactionID ."'");
$product->execute();
$subtotal = $config->getData('SUM(product_price * product_qty) as Subtotal', 'transaction_details', "id_trx = '". $transactionID ."'");

$dataproduct = [];
    while($row = $product->fetch(PDO::FETCH_LAZY)) {
    $dataproduct[] = '
    <tr style="background-color: #ffffff;">
        <td style="padding: 5px; border-bottom: 0.5px solid;">
        <img style="border:1px solid #FFFFFF; padding:1px; " src="'.URL.'assets/images/product/'. str_replace(' ', '_', strtolower($row['product_name'])) .'.jpg" width="100" height="95" align=center>
        </td>
        <td style="padding: 3px;font-size: 14px;font-weight: 600; border-bottom: 0.5px solid; text-transform: capitalize;">'. strtoupper($row['id_product']) .' '. $config->_parsingproductname(strtolower($row['product_name'])) .'</td>
        <td style="padding: 3px;font-size: 14px;font-weight: 600; text-align: center; border-bottom: 0.5px solid; padding-right: 4px;">'. $row['product_qty'] .'</td>
        <td style="padding: 3px;font-size: 14px;font-weight: 600; text-align: right; border-bottom: 0.5px solid; padding-right: 4px;">'. number_format($row['product_price'], 2, '.', ',') .'</td>
        <td style="padding: 3px;font-size: 14px;font-weight: 600; text-align: right; border-bottom: 0.5px solid; padding-right: 4px;">'. number_format(($row['product_qty'] * $row['product_price']), 2, '.', ',') .'</td>
    </tr>
';
}
$dataproduct = implode(' ', $dataproduct);
$total = ($subtotal['Subtotal'] + $data['delivery_charge'] + $data['delivery_charge_time']) - 0;

$CustomerName = isset($data['CorporateName']) && $data['CorporateName'] == '' ? $data['OrganicName'] : $data['CorporateName'];
$CustomerEmail = isset($data['CorporateEmail']) && $data['CorporateEmail'] == '' ? $data['OrganicEmail'] : $data['CorporateEmail'];
$CustomerPhone = isset($data['CorporatePhone']) && $data['CorporatePhone'] == '' ? $data['OrganicPhone'] : $data['CorporatePhone'];

$receivedEmail = $CustomerEmail;
$receivedName = $CustomerName;
$subject = 'Order Confirmation Bunga Davi-'.$data['transactionID'];

if($data['statusPaid'] == 1) {
    $statusPaid = '<div style="background-color: green; width: 120px;padding: 8px;border: 1px solid green;border-radius: 5px; margin-left: 40%; color: #FFF;">
        <span>PAID</span>
    </div>';
} else {
    $statusPaid = '<div style="background-color: yellow; width: 120px;padding: 8px;border: 1px solid yellow;border-radius: 5px; margin-left: 40%;">
        <span>UNPAID</span>
    </div>';
}

$timedelivery = 'unset';
if($data['delivery_time']) $timedelivery = $arrtime[$data['delivery_time']];

$emailrecipient = 'unset';
if($data['email']) $emailrecipient = $data['email'];

$deliverynote = 'unset';
if($data['delivery_marks']) $deliverynote = $data['delivery_marks'];

$content = '
<html>
   <head></head>
   <body>
      <style type="text/css">
         /* Mobile-specific Styles */
         @media only screen and (max-width: 660px) {
         table[class=w15], td[class=w15], img[class=w15] { width:5px !important; }
         table[class=w30], td[class=w30], img[class=w30] { width:10px !important; }
         table[class=w80], td[class=w80], img[class=w80] { width:20px !important; }
         table[class=w120], td[class=w120], img[class=w120] { width:45px !important; }
         table[class=w135], td[class=w135], img[class=w135] { width:70px !important; }
         table[class=w150], td[class=w150], img[class=w150] { width:105px !important; }
         table[class=w160], td[class=w160], img[class=w160] { width:160px !important; }
         table[class=w170], td[class=w170], img[class=w170] { width:80px !important; }
         table[class=w180], td[class=w180], img[class=w180] { width:70px !important; }
         table[class=w220], td[class=w220], img[class=w220] { width:80px !important; }
         table[class=w240], td[class=w240], img[class=w240] { width:140px !important; }
         table[class=w255], td[class=w255], img[class=w255] { width:185px !important; }
         table[class=w280], td[class=w280], img[class=w280] { width:164px !important; }
         table[class=w315], td[class=w315], img[class=w315] { width:125px !important; }
         table[class=w325], td[class=w325], img[class=w325] { width:95px !important; }
         table[class=w410], td[class=w410], img[class=w410] { width:140px !important; }
         table[class=w520], td[class=w520], img[class=w520] { width:180px !important; }
         table[class=w640], td[class=w640], img[class=w640] { width:330px !important; }
         table[class*=hide], td[class*=hide], img[class*=hide], p[class*=hide], span[class*=hide] { display:none !important; }
         p[class=footer-content-left] { text-align: center !important; }
         img { height: auto; line-height: 100%;}
         .menu{font-size: 11px !important;}
         .article-title { font-size: 9px !important; font-weight:bold; line-height:18px; color: #423640; margin-top:0px; margin-bottom:18px; font-family:Arial; }
         .article-content, #left-sidebar{ -webkit-text-size-adjust: 90% !important; -ms-text-size-adjust: 90% !important; font-size:20px !important }
         .header-content, .footer-content-left, .mail-tittle {-webkit-text-size-adjust: 80% !important; -ms-text-size-adjust: 80% !important; font-size: 10px !important;}
         .tittle-dis{color: #0059B3; font-weight:bold; font-size: 14px !important;}
         .title-content { font: bold 10px Arial !important; color:#888888; line-height: 18px; margin-top: 0px; margin-bottom: 2px;}
         .content-body{font: normal 11px Arial !important; color:#888888;}
         .content-body1{font: bold 11px Arial !important; color:#888888;}
         .article-title1{font-size:9px !important}
         }
         body{font-family: Arial; font-size:11px}
         img { outline: none; text-decoration: none; display: block;}
         #top-bar { border-radius:6px 6px 0px 0px; -moz-border-radius: 6px 6px 0px 0px; -webkit-border-radius:6px 6px 0px 0px; -webkit-font-smoothing: antialiased; color: #4D4D4D; }
         #footer { border-radius:0px 0px 6px 6px; -moz-border-radius: 0px 0px 6px 6px; -webkit-border-radius:0px 0px 6px 6px; -webkit-font-smoothing: antialiased; font:bold 11px Arial}
         td { font-family: Arial; }
         .header-content, .footer-content-left, .footer-content-right { -webkit-text-size-adjust: none; -ms-text-size-adjust: none; }
         .header-content { font-size: 12px; font-weight:bold; }
         .header-content a { color: #0059B3; text-decoration: none; }
         .article-title1 { font-size: 10px; background:#888888; color:#ffffff; padding:4px 2px}
         .mail-tittle {color:#333333}
         .article-content {color:#333333}
         .content-head{color:#f2f2f2; font-family:Arial; font-size:12px; font-weight:bold;}
         .content-body {font-size: 12px; color:#333333;}
         .content-body1 {font-weight: bold; font-size: 12px; color:#333333; white-space:nowrap}
         .footer-content-left { font:bold 10px Arial; line-height: 15px; margin-top: 0px; margin-bottom: 15px; }
         .footer-content-left a { text-decoration: none; }
         .footer-content-right { font-size: 10px; line-height: 16px; color: #ededed; margin-top: 0px; margin-bottom: 15px; }
         .footer-content-right a { color: #ffffff; text-decoration: none; }
         .tittle-dis{color: #333333; font-weight:bold;}
         #footer a {text-decoration: none;color:#000000;}
         .menu{text-decoration:none; color:#eeeeee; font-size:12px; padding:10px 2px 10px 0px; line-height:24px}
         .menu a{color: #ffffff;}
         .promo{color:#FFFFFF; font-weight: bold}
         .promo a{color:#57A3DB ; font-weight: bold}
      </style>
      <table width="100%" cellspacing="0" cellpadding="0" border="0">
         <tbody>
            <tr>
               <td align="center">
                  <table width="640" cellspacing="0" cellpadding="0" border="0" class="w640">
                     <tbody>
                        <tr>
                           <td width="640" bgcolor="#ffffff" class="w640">
                              <table width="640" cellspacing="0" cellpadding="0" border="0" bgcolor="#FFFFFF" id="top-bar" class="w640">
                                 <tbody>
                                    <tr>
                                       <td width="280" align="left" class="w315" style="margin-left: -5px;">
                                          <table width="280" cellspacing="0" cellpadding="0" border="0" class="w315">
                                             <tbody>
                                                <tr>
                                                   <td width="280" height="10" class="w315"></td>
                                                </tr>
                                                <tr>
                                                   <td width="280" class="w315"><a href=""><img width="235" class="w410" src="'.URL.'assets/images/logo.png" alt="Logo Bunga Davi"/></a>
                                                   </td>
                                                </tr>
                                             </tbody>
                                          </table>
                                       </td>
                                       <td width="360" align="right" valign="bottom" class="w240">
                                          <table cellspacing="0" cellpadding="0" border="0" style="margin-right: 3px;">
                                             <tbody>
                                                <tr>
                                                   <td width="360" class="w240" colspan="11" align="right"><span class="article-meta" style=" font-size: 20px; font-weight: bold; line-height: 20px; margin-top: 0;font-family: Arial; color:#333333">Follow Us</span></td>
                                                </tr>
                                                <tr>
                                                   <td width="360" class="w240" height="5"></td>
                                                </tr>
                                                <tr>
                                                   <td width="15"></td>
                                                   <td valign="middle"> 
                                                      <a href="facebook">
                                                      <img width="32" class="w80" src="'.URL.'assets/images/sosmed/facebook.png" alt="Bunga Davi Florist"/>
                                                      </a>
                                                   </td>
                                                   <td width="3"></td>
                                                   <td valign="middle"><span class="header-content"><a href="instagram"><img width="32" class="w80" src="'.URL.'assets/images/sosmed/instagram.png" alt="Bunga Davi Florist"/></a></span></td>
                                                   <td width="3"></td>
                                                   <td valign="middle"><span class="header-content"><a href="mailto:info@bungadavi.co.id" target="_top"><img width="32" class="w80" src="'.URL.'assets/images/sosmed/email.png" alt="Bunga Davi Florist"/></a></span></td>
                                                </tr>
                                             </tbody>
                                          </table>
                                       </td>
                                    </tr>
                                 </tbody>
                              </table>
                           </td>
                        </tr>
                        <tr>
                           <td width="640" bgcolor="#FFFFFF" align="center" id="header" class="w640">
                              <table width="640" cellspacing="0" cellpadding="0" border="0" class="w640" style=" -webkit-font-smoothing: antialiased;">
                                 <tbody>
                                    <tr>
                                       <td width="640" class="w640" align="center" style="padding:5px 0px; background:#383838; color:#eeeeee; font-size:18px; line-height:24px">
                                          ORDER CONFIRMATION
                                          <!-- <span><a class="menu" style="color:#FFFFFF; font-size:12px; font-weight:bold; text-decoration:none; font-family: Arial;" href=""> Birthday &nbsp;&nbsp;&nbsp;</a> </span>
                                             <span> <a class="menu" style="color:#FFFFFF; font-size:12px; font-weight:bold; text-decoration:none; font-family: Arial;" href=""> Anniversary </a> &nbsp;&nbsp;&nbsp;</span>
                                             <span> <a class="menu" style="color:#FFFFFF; font-size:12px; font-weight:bold; text-decoration:none; font-family: Arial;" href=""> Romance </a> &nbsp;&nbsp;&nbsp;</span>
                                             <span> <a class="menu" style="color:#FFFFFF; font-size:12px; font-weight:bold; text-decoration:none; font-family: Arial;" href=""> Get Well Soon </a> &nbsp;&nbsp;&nbsp;</span>
                                             <span> <a class="menu" style="color:#FFFFFF; font-size:12px; font-weight:bold; text-decoration:none; font-family: Arial;" href=""> Sympathy </a> &nbsp;&nbsp;&nbsp;</span><br /> -->
                                       </td>
                                    </tr>
                                 </tbody>
                              </table>
                              <br />
                           </td>
                        </tr>
                        <tr>
                           <td width="640" bgcolor="#FFFFFF" align="center" id="header" class="w640">
                              <table width="600" cellspacing="0" cellpadding="0" border="0" bgcolor="#FFFFFF" class="w640" style=" -webkit-font-smoothing: antialiased;">
                                 <tbody>
                                    <tr>
                                       <td width="600" align="center" class="w640">
                                          <span class="article-content" style="font-family:Arial; font-size:24px;color:#333333; font-weight:bold; line-height:26px">Thank You For Your Order! </span>
                                          <br /><br />
                                       </td>
                                    </tr>
                                    <tr>
                                       <td width="600" align="center" class="w640">
                                          <span class="tittle-dis" style="font-size:18px;color:#333333; line-height:20px; font-family:Arial; background-color: #7b7878; padding: 5px; border-rounded: 5px; color: #ffffff;">
                                          #'.strtoupper($data['transactionID']).' </span> <br />
                                       </td>
                                    </tr>
                                    <tr>
                                       <td height="10" class="w160"></td>
                                    </tr>
                                 </tbody>
                              </table>
                           </td>
                        </tr>
                        <tr>
                           <td  height="5" bgcolor="#ffffff" class="w640"></td>
                        </tr>
                        <tr>
                           <td width="642" bgcolor="#ffffff" class="w642">
                              <table width="642" cellspacing="0" cellpadding="0" border="0" class="w642">
                                 <tbody>
                                    <tr>
                                       <td width="642" class="w642">
                                          <table align="center" width="642" cellspacing="5" cellpadding="0" border="0" class="w642" style="background:#555555">
                                             <tbody>
                                                <tr>
                                                   <td align="center" width="200" class="w325" bgcolor="#444444" style="padding: 7px 5px;"><span align="center" class="content-head" style="font-family:Arial; color: #ffffff">Order By</span></td>
                                                   <td align="center" width="200" class="w325" bgcolor="#444444" style="padding: 7px 5px;"><span align="center" class="content-head" style="font-family:Arial; color: #ffffff">Your Email</span></td>
                                                   <td align="center" width="200" class="w325" bgcolor="#444444" style="padding: 7px 5px;"><span align="center" class="content-head" style="font-family:Arial; color: #ffffff">Your Phone</span></td>
                                                </tr>
                                             </tbody>
                                          </table>
                                       </td>
                                    </tr>
                                 </tbody>
                              </table>
                           </td>
                        </tr>
                        <tr>
                           <td  height="10" bgcolor="#ffffff" class="w640"></td>
                        </tr>
                        <tr>
                           <td width="642" bgcolor="#ffffff" class="w642">
                              <table width="642" cellspacing="0" cellpadding="0" border="0" class="w642">
                                 <tbody>
                                    <tr>
                                       <td width="642" class="w642">
                                          <table align="center" width="642" cellspacing="5" cellpadding="0" border="0" class="w642">
                                             <tbody>
                                                <tr>
                                                   <td align="center" width="200" class="w325"><span align="center" class="content-body">'. $CustomerName.'</span></td>
                                                   <td align="center" width="200" class="w325"><span align="center" class="content-body">'. $CustomerEmail.'</span></td>
                                                   <td align="center" width="200" class="w325"><span align="center" class="content-body">'.$CustomerPhone.'</span></td>
                                                </tr>
                                             </tbody>
                                          </table>
                                       </td>
                                    </tr>
                                 </tbody>
                              </table>
                           </td>
                        </tr>
                        <tr>
                           <td height="15" bgcolor="#ffffff" width="640" class="w640"></td>
                        </tr>
                        <tr>
                           <td width="640" bgcolor="#444444" align="center" id="header" class="w640" style="padding: 7px 5px;">
                              <span align="center" class="content-head" style="font-family:Arial; color: #ffffff">Summary Detail </span>
                           </td>
                        </tr>
                        <tr>
                           <td height="15" bgcolor="#ffffff" width="640" class="w640"></td>
                        </tr>
                        <tr>
                           <td width="640" class="w640">
                              <table width="640" cellspacing="0" cellpadding="0" border="1" class="w640" bgcolor="#444444">
                                 <thead>
                                    <tr>
                                       <td width="80px" align="center" style="font-family:Arial; color: #ffffff;padding:7px 5px"><span align="center" class="content-head">Product Image</span></td>
                                       <td width="150px" align="center" style="font-family:Arial; color: #ffffff;padding:7px 5px"><span align="center" class="content-head">Item Name</span></td>
                                       <td width="20px" align="center" style="font-family:Arial; color: #ffffff;padding:7px 5px"><span align="center" class="content-head">Qty</span></td>
                                       <td width="100px" align="center" style="font-family:Arial; color: #ffffff;padding:7px 5px"><span align="center" class="content-head">Price</span></td>
                                       <td width="100px" align="center" style="font-family:Arial; color: #ffffff;padding:7px 5px"><span align="center" class="content-head">Total</span></td>
                                    </tr>
                                 </thead>
                                <tbody>
                                    '. $dataproduct .'
                                    <tr style="background-color: #ffffff;">
                                        <td style="border-bottom: 0.5px solid; font-weight: 600; font-size: 14px; padding: 8px 0px; text-align: center;" colspan="4">Sub Total</td>
                                        <td style="border-bottom: 0.5px solid; font-weight: 600; font-size: 14px; padding: 8px 0px; text-align: right; padding-right: 2px;" colspan="4">'. number_format($subtotal['Subtotal'], 2, '.', ',') .'</td>
                                    </tr>
                                    <tr style="background-color: #ffffff;">
                                        <td style="border-bottom: 0.5px solid; font-weight: 600; font-size: 14px; padding: 8px 0px; text-align: center;" colspan="4">Delivery Charge + Time slots</td>
                                        <td style="border-bottom: 0.5px solid; font-weight: 600; font-size: 14px; padding: 8px 0px; text-align: right; padding-right: 2px;" colspan="4">'. number_format(($data['delivery_charge'] + $data['delivery_charge_time']), 2, '.', ',') .'</td>
                                    </tr>
                                    <tr style="background-color: #ffffff;">
                                        <td style="border-bottom: 0.5px solid; font-weight: 600; font-size: 14px; padding: 8px 0px; text-align: center;" colspan="4">Grand Total</td>
                                        <td style="border-bottom: 0.5px solid; font-weight: 600; font-size: 14px; padding: 8px 0px; text-align: right; padding-right: 2px;" colspan="4">'. number_format($total, 2, '.', ',') .'</td>
                                    </tr>
                                    <tr style="background-color: #ffffff;">
                                        <td style="border-bottom: 0.5px solid; font-weight: 600; font-size: 14px; padding: 8px 0px; text-align: center;" colspan="5">
                                        '.$statusPaid.'
                                        </td>
                                    </tr>
                                    '.$downloadfile.'
                                </tbody>
                              </table>
                           </td>
                        </tr>
                        <tr>
                           <td height="15" bgcolor="#ffffff" width="640" class="w640"></td>
                        </tr>
                        <tr>
                           <td  height="10" bgcolor="#ffffff" class="w640"></td>
                        </tr>
                        <tr>
                           <td width="640" class="w640" bgcolor="#444444" style="padding: 7px 5px;"><span class="content-head" style="font-family:Arial; color: #ffffff">Recipient Detail</span></td>
                        </tr>
                        <tr>
                           <td width="640" bgcolor="#ffffff" class="w640">
                              <table width="640" cellspacing="0" cellpadding="0" border="0" class="w640">
                                 <tbody>
                                    <tr>
                                       <td width="280" class="w160">
                                          <table width="280" cellspacing="0" cellpadding="0" border="0" class="w160">
                                             <tbody>
                                                <tr>
                                                   <td width="280" height="15" class="w160"></td>
                                                </tr>
                                                <tr>
                                                   <td width="280" class="w160">
                                                      <table width="280" cellspacing="5" cellpadding="0" border="0" class="w160">
                                                         <tbody>
                                                            <tr>
                                                               <td width="110" class="w170" style="vertical-align: top;"><span class="content-body1" style="font-family:Arial;">Recipient Name :</span></td>
                                                               <td width="170" class="w170" style="vertical-align: top;"><span class="content-body" style="font-family:Arial;">'. $data['nama_penerima'].'</span></td>
                                                            </tr>
                                                            <tr>
                                                               <td width="110" class="w170" style="vertical-align: top;"><span class="content-body1" style="font-family:Arial;">Recipient Email :</span></td>
                                                               <td width="170" class="w170" style="vertical-align: top;"><span class="content-body" style="font-family:Arial;">'. $emailrecipient .'</span></td>
                                                            </tr>
                                                            <tr>
                                                               <td width="110" class="w170" style="vertical-align: top;"><span class="content-body1" style="font-family:Arial;">Recipient Adress :</span></td>
                                                               <td width="170" class="w170" style="vertical-align: top;"><span class="content-body" style="font-family:Arial;">'. $data['alamat_penerima'].', '. $data['Kelurahan']. ', '. $data['Kecamatan']. ', '. $data['KotaName']. ', '. $data['ProvinsiName'] .'</span></td>
                                                            </tr>
                                                         </tbody>
                                                      </table>
                                                   </td>
                                                </tr>
                                             </tbody>
                                          </table>
                                       </td>
                                       <td width="280" class="w160">
                                          <table width="280" cellspacing="0" cellpadding="0" border="0" class="w160">
                                             <tbody>
                                                <tr>
                                                   <td width="280" class="w640">
                                                      <table width="280" cellspacing="5" cellpadding="0" border="0" class="w160">
                                                         <tbody>
                                                            <tr>
                                                               <td width="110" class="w170" style="vertical-align: top;"><span class="content-body1" style="font-family:Arial;">Create Date :</span></td>
                                                               <td width="170" class="w170" style="vertical-align: top;"><span class="content-body" style="font-family:Arial;">'. $config->_formatdate($data['created_date']). '</span></td>
                                                            </tr>
                                                            <tr>
                                                               <td width="110" class="w170" style="vertical-align: top;"><span class="content-body1" style="font-family:Arial;">Delivery Date :</span></td>
                                                               <td width="170" class="w170" style="vertical-align: top;"><span class="content-body" style="font-family:Arial;">'. $config->_formatdate($data['delivery_date']). '</span> <span style="color: red; font-size: 12px; font-weight: 600;">'.$timedelivery.'</span></td>
                                                            </tr>
                                                            <tr>
                                                               <td width="110" class="w170" style="vertical-align: top;"><span class="content-body1" style="font-family:Arial;">Delivery Note :</span></td>
                                                               <td width="170" class="w170" style="vertical-align: top;"><span class="content-body" style="font-family:Arial;">'.$deliverynote.'</span></td>
                                                            </tr>
                                                            <tr>
                                                               <td width="110" class="w170" style="vertical-align: top;"><span class="content-body1" style="font-family:Arial;">Payment Type:</span></td>
                                                               <td width="170" class="w170" style="vertical-align: top;"><span class="content-body" style="font-family:Arial;">BCA</span></td>
                                                            </tr>
                                                         </tbody>
                                                      </table>
                                                   </td>
                                                </tr>
                                             </tbody>
                                          </table>
                                       </td>
                                    </tr>
                                 </tbody>
                              </table>
                           </td>
                        </tr>
                        <tr>
                           <td width="640" height="10" bgcolor="#ffffff" class="w640"></td>
                        </tr>
                        <tr>
                           <td width="640" bgcolor="#ffffff" class="w640">
                              <table width="640px" cellspacing="0" cellpadding="0" border="0" class="w640" bgcolor="#444444">
                                 <tbody>
                                    <tr>
                                       <td width="100px" class="w325" bgcolor="#444444" style="padding: 7px 5px;" align="center"><span class="content-head" style="font-family:Arial; color: #ffffff">Card Messege</span></td>
                                    </tr>
                                    <tr>
                                       <td width="100px" class="w325" bgcolor="#ffffff" style="padding: 7px 5px; border-bottom: 1px solid;" align="center">
                                          <span class="content-head" style="font-family:Arial; color:#444444; font-style: italic;">
                                             " '.$data['card_isi'].' "
                                          </span>
                                       </td>
                                    </tr>
                                 </tbody>
                              </table>
                           </td>
                        </tr>
                        <tr>
                           <td width="640" bgcolor="#ffffff" class="w640">
                              <table width="640" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff" id="footer" class="w640">
                                 <tbody>
                                    <tr>
                                       <td width="30" class="w30"></td>
                                       <td width="640" valign="top" class="w640">
                                          <p align="center" class="footer-content-left" style="font-family:Arial;">Call us : <br /> Cilegon: +62818433612  || Jakarta: +62811133364 || Serang: +62816884292 <br /> Tangerang: +62811133364 || Area Lain +62811133365  <br /> (24 Hours Hotline) <br /></p>
                                          <p align="center" class="footer-content-left" style="font-family:Arial;">Copyright &copy; 2007 - 2019 Bunga Davi</p>
                                       </td>
                                       <td width="30" class="w30"></td>
                                    </tr>
                                 </tbody>
                              </table>
                           </td>
                        </tr>
                        <tr>
                           <td width="640" height="60" class="w640"></td>
                        </tr>
                     </tbody>
                  </table>
               </td>
            </tr>
         </tbody>
      </table>
   </body>
</html>';
// echo $content;
$cc = '';
$config = new Mail();
// $email = $config->Mailler('afz60.30@gmail.com', $receivedName, $cc, $subject, $content);
$email = $config->Mailler($receivedEmail, $receivedName, $cc, $subject, $content);