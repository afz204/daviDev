<?php
/**
 * Created by PhpStorm.
 * User: small-project
 * Date: 01/04/2018
 * Time: 20.00
 */

require '../../config/config.php';
require '../../config/Mail.php';
$config = new Admin();

$admin = $config->adminID();

if($_GET['type'] == 'formresonbox'){
    $transactionID = $_POST['transactionID'];
    $notes = $_POST['notes'];
    $Types = $_POST['Types'];

    if(in_array($Types, [4, 5, 6])) {
        $a  = $_POST['transactionID'];
        $b  = $_POST['notes'];
        
        $updatekurirjobs = $config->runQuery("UPDATE kurir_jobs SET StatusKirim = 3, Notes ='". $b ."' WHERE TransactionNumber ='". $a ."' ");
        $updatekurirjobs->execute();

        if($updatekurirjobs) {
            
            $logs = $config->saveLogs($a, $admin, 'u', 'kurir_jobs');

            $updatetransaction = $config->runQuery("UPDATE transaction SET statusOrder = '4', notes = '". $b ."'   WHERE transactionID ='". $a ."' ");
            $updatetransaction->execute();

            if($updatetransaction) {
                echo $config->actionMsg('u', 'transaction');
                $logs = $config->saveLogs($a, $admin, 'u', 'transaction');

                $transactionID = $a;
        
                $data = $config->getData('transaction.*, corporate_pics.name as CorporateName, corporate_pics.email as CorporateEmail, corporate_pics.nomor as CorporatePhone, customer.FullName as OrganicName, customer.Email as OrganicEmail, customer.Mobile as OrganicPhone, provinces.name as ProvinsiName, regencies.name as KotaName, districts.name as Kecamatan, villages.name as Kelurahan', 'transaction 
                LEFT JOIN corporate_pics ON corporate_pics.id = transaction.PIC LEFT JOIN customer on customer.CustomerUniqueID = transaction.CustomerID LEFT JOIN provinces ON provinces.id = transaction.provinsi_id LEFT JOIN regencies on regencies.id = transaction.kota_id LEFT JOIN districts ON districts.id = transaction.kecamata_id LEFT JOIN villages on villages.id = transaction.kelurahan_id', "transactionID = '". $transactionID ."' ");
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
                        <img style="border:1px solid #FFFFFF; padding:1px; " src="'.URL.'assets/images/product/'. str_replace(' ', '_', $row['product_name']) .'.jpg" width="100" height="95" align=center>
                        </td>
                        <td style="padding: 3px;font-size: 14px;font-weight: 600; border-bottom: 0.5px solid; text-transform: capitalize;">'. strtoupper($row['id_product']) .' '. $row['product_name'] .'</td>
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
                $subject = 'Notification Delivery Bunga Davi-'.$data['transactionID'];
                $arraypaid = 'UNPAID';
                    if($data['statusPaid']) $arraypaid = $arrpaid[$data['statusPaid']];
                
                if($data['statusOrder'] == 3) {
                    $SendStatus = '<tr>
                    <td width="600" align="center" class="w640">
                    <span class="article-content" style="font-family:Arial; font-size:24px;color:#333333; font-weight:bold; line-height:26px; color: green;">Has Been Send! <br> Thank you!</span>
                    <br /><br />
                    </td>
                </tr>';
                } elseif($data['statusOrder'] == 4) {
                    $SendStatus = '<tr>
                    <td width="600" align="center" class="w640">
                    <span class="article-content" style="font-family:Arial; font-size:24px;color:#333333; font-weight:bold; line-height:26px; color: orange;">Has Return! <br> Reason: '.$data['notes'].'.</span>
                    <br /><br />
                    </td>
                </tr>';
                }
                
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
                        body{font-family: Arial; font-size:12px}
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
                                        <td width="640" height="20" class="w640"></td>
                                        </tr>
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
                                                            NOTIFICATION DELIVERY
                                                        <!-- <span><a class="menu" style="color:#FFFFFF; font-size:12px; font-weight:bold; text-decoration:none; font-family: Arial;" href=""> Birthday &nbsp;&nbsp;&nbsp;</a> </span>
                                                            <span> <a class="menu" style="color:#FFFFFF; font-size:12px; font-weight:bold; text-decoration:none; font-family: Arial;" href=""> Anniversary </a> &nbsp;&nbsp;&nbsp;</span>
                                                            <span> <a class="menu" style="color:#FFFFFF; font-size:12px; font-weight:bold; text-decoration:none; font-family: Arial;" href=""> Romance </a> &nbsp;&nbsp;&nbsp;</span>
                                                            <span> <a class="menu" style="color:#FFFFFF; font-size:12px; font-weight:bold; text-decoration:none; font-family: Arial;" href=""> Get Well Soon </a> &nbsp;&nbsp;&nbsp;</span>
                                                            <span> <a class="menu" style="color:#FFFFFF; font-size:12px; font-weight:bold; text-decoration:none; font-family: Arial;" href=""> Sympathy </a> &nbsp;&nbsp;&nbsp;</span><br /> -->
                                                    </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <br /><br />
                                        </td>
                                        </tr>
                                        <tr>
                                        <td width="640" bgcolor="#FFFFFF" align="center" id="header" class="w640">
                                            <table width="600" cellspacing="0" cellpadding="0" border="0" bgcolor="#FFFFFF" class="w640" style=" -webkit-font-smoothing: antialiased;">
                                                <tbody>
                                                    <tr>
                                                    <td width="600" align="center" class="w640">
                                                        <span class="article-content" style="font-family:Arial; font-size:24px;color:#333333; font-weight:bold; line-height:26px">Your Order Transacation: </span>
                                                        <br /><br />
                                                    </td>
                                                    </tr>
                                                    <tr>
                                                    <td width="600" align="center" class="w640">
                                                        <span class="tittle-dis" style="font-size:18px;color:#333333; line-height:20px; font-family:Arial; background-color: #7b7878; padding: 5px; border-rounded: 5px; color: #ffffff;">
                                                        #'.strtoupper($data['transactionID']).' </span> <br /><br />
                                                    </td>
                                                    </tr>
                                                    '.$SendStatus.'
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
                                                                <td align="center" width="200" class="w325"><span align="center" class="content-body">'. $receivedName.'</span></td>
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
                                                        <div style="background-color: yellow; width: 120px;padding: 8px;border: 1px solid yellow;border-radius: 5px; margin-left: 40%;">
                                                            <span>'.$arraypaid.'</span>
                                                        </div>
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
                                                                            <td width="170" class="w170" style="vertical-align: top;"><span class="content-body" style="font-family:Arial;">'. $data['email'].'</span></td>
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
                                                                <td width="280" height="15" class="w160"></td>
                                                                </tr>
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
                                                                            <td width="170" class="w170" style="vertical-align: top;"><span class="content-body" style="font-family:Arial;">'. $config->_formatdate($data['delivery_date']). '</span> <span style="color: red; font-size: 12px; font-weight: 600;">'.$arraypaid.'</span></td>
                                                                            </tr>
                                                                            <tr>
                                                                            <td width="110" class="w170" style="vertical-align: top;"><span class="content-body1" style="font-family:Arial;">Delivery Note :</span></td>
                                                                            <td width="170" class="w170" style="vertical-align: top;"><span class="content-body" style="font-family:Arial;">'.$data['delivery_marks'].'</span></td>
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
                                                        '.$data['card_to'].' <br>
                                                        " '.$data['card_isi'].' " <br>
                                                        '.$data['card_from'].'
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
                                                        <p align="center" class="footer-content-left"><a style="font-family:Arial;" href="">About Us</a> |
                                                            <a href="" style="font-family:Arial;">Testimonial</a> |
                                                            <a style="font-family:Arial;" href="">Policy</a> |
                                                            <a style="font-family:Arial;" href="">Contact Us</a> |
                                                            <a style="font-family:Arial;" href="">Corporate Sign Up</a> |
                                                            <a style="font-family:Arial;" href="">T&C</a>
                                                        <p align="center" class="footer-content-left" style="font-family:Arial;">Call us : <br /> Cilegon: +62818433612  || Jakarta: +62811133364 || Serang: +62816884292 <br /> Tangerang: +62811133364 || Area Lain +62811133365  <br /> (24 Hours Hotline) <br /><br /></p>
                                                        <p align="center" class="footer-content-left" style="font-family:Arial;">Copyright &copy; 2007 - 2017 Bunga Davi</p>
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
                
                $cc = 'fiki@bungadavi.co.id';
                $config = new Mail();
                $email = $config->Mailler('ardinirianti@gmail.com', $receivedName, $cc, $subject, $content);
            } else {
                echo 'Failed Transaction!';
            }

        } else {
            echo 'Failed Kurir Jobs!';
        }
    }

    $update = $config->runQuery("UPDATE transaction SET statusOrder = '".$Types."', notes = '".$notes."' WHERE transactionID = '".$transactionID."' ");
    $update->execute();

    if($update) {
        echo 'Success!';

        $logs = $config->saveLogs($transactionID, $admin, 'u', 'Update Status Order');
    } else {
        echo 'Failed!';
    }
}
if($_GET['type'] == 'getcodecustomproduct'){
    $data = $_POST['data'];
    
    $tgl = $config->getDate('ymhms');
    $dd = $config->getDate('hs');
    $new_code = $tgl;
    $tilte = 'CUSTOMIZEPRODUCT'.$dd;

    die(json_encode(['msg' => 'OK', 'code' => $new_code, 'title' => $tilte], JSON_FORCE_OBJECT));
}
if($_GET['type'] == 'tableSearch'){
    $request = $_REQUEST;
    $search = $_POST['is_date_search'];

    $daterange ='';
    $corporate ='';
    $admin = '';
    
    if(isset($_POST['date_range'])) {
        $daterange = $_POST['date_range'];
    }

    $databox = '';
    if(isset($_POST['invoicenomor']) || isset($_POST['sendername']) || isset($_POST['address']) || isset($_POST['typeReport'])) {
        // echo $_POST['search']['value'];
        $databox = '(transaction.transactionID LIKE "%'. $_POST['invoicenomor'] . '%" OR transaction.CustomerName LIKE "%'. $_POST['sendername'] . '%" OR users.name LIKE "%'. $_POST['search']['value'] . '%") OR (transaction_details.product_name LIKE "%'.$_POST['search']['value'].'%") ';
    }

    $colom = array(
        0   => 'transaction.transactionID',
        1   => 'transaction_details.product_name',
        2   => 'transaction.delivery_date',
        3   => 'villages.name',
        4   => 'transaction.statusOrder',
        5   => 'transaction.notes',
        6   => 'transaction.delivery_date',
        7   => 'transaction.created_date',
        8   => 'transaction.id_florist',
        9   => 'transaction.id_kurir',
        10   => 'FloristName',
        11   => 'Action',
        12   => 'Color',
    );

    $orderby = 'ORDER BY transaction.delivery_date ASC';
    if(isset($_POST['order'][0]['column'])) {
        $column     = $_POST['order'][0]['column'];
        $typesort   = $_POST['order'][0]['dir'];

        $orderby = 'ORDER BY '.$colom[$column].' '. $typesort;
    }

    $limitstart = $_POST['start'];
    $limitend = $_POST['length'];
    $limit = 'LIMIT '.$limitstart.','.$limitend;

    $Query = '
    SELECT 
        (select GROUP_CONCAT(transaction_details.product_name SEPARATOR ",") from transaction_details where transaction_details.id_trx = transaction.transactionID) as product, 
        (select GROUP_CONCAT(transaction_details.product_price SEPARATOR ",") from transaction_details where transaction_details.id_trx = transaction.transactionID) as price, 
        (select GROUP_CONCAT(transaction_details.product_cost SEPARATOR ",") from transaction_details where transaction_details.id_trx = transaction.transactionID) as cost, 
        (select GROUP_CONCAT(transaction_details.product_qty SEPARATOR ",") from transaction_details where transaction_details.id_trx = transaction.transactionID) as quantity, 
        transaction.*, transaction_details.*, villages.name as kelurahan, users.name as admin
        FROM transaction 
        LEFT JOIN transaction_details ON transaction_details.id_trx = transaction.transactionID 
        LEFT JOIN villages ON villages.id = transaction.kelurahan_id 
        LEFT JOIN users ON users.id = transaction.created_by ';

    $QueryTotal = '
    SELECT 
        (select GROUP_CONCAT(transaction_details.product_name SEPARATOR ",") from transaction_details where transaction_details.id_trx = transaction.transactionID) as product, 
        (select GROUP_CONCAT(transaction_details.product_price SEPARATOR ",") from transaction_details where transaction_details.id_trx = transaction.transactionID) as price, 
        (select GROUP_CONCAT(transaction_details.product_cost SEPARATOR ",") from transaction_details where transaction_details.id_trx = transaction.transactionID) as cost, 
        (select GROUP_CONCAT(transaction_details.product_qty SEPARATOR ",") from transaction_details where transaction_details.id_trx = transaction.transactionID) as quantity, 
        transaction.*, transaction_details.*, villages.name as kelurahan, users.name as admin
        FROM transaction 
        LEFT JOIN transaction_details ON transaction_details.id_trx = transaction.transactionID 
        LEFT JOIN villages ON villages.id = transaction.kelurahan_id 
        LEFT JOIN users ON users.id = transaction.created_by ';

    $Query .= $databox;
    $QueryTotal .= $databox;
    if($search == 'yes') { 
        $rangeArray = explode("_",$daterange); 

        $startDate = $rangeArray[0];
        $endsDate = $rangeArray[1];

        $daterangequery = "transaction.delivery_date BETWEEN '". $startDate ."' AND '". $endsDate ."'";

        $Query .= $daterangequery." GROUP BY transaction.transactionID ". $orderby. ' '. $limit;
        $QueryTotal .= $daterangequery." GROUP BY transaction.transactionID ". $orderby;
        
        $stmt2 = $config->runQuery($QueryTotal);
        $stmt2->execute();
        $totalData = $stmt2->rowCount();
        $totalFilter = $totalData;
        
        $Data = $config->runQuery($Query);
        $Data->execute();
        
    } else {
        $Query .=" GROUP BY transaction.transactionID ". $orderby. ' '. $limit;
        $QueryTotal .=" GROUP BY transaction.transactionID ". $orderby;
        
        // var_dump($QueryTotal);
        $stmt2 = $config->runQuery($QueryTotal);
        $stmt2->execute();
        $totalData = $stmt2->rowCount();
        $totalFilter = $totalData;

        $Data = $config->runQuery($Query);
        $Data->execute();
    }
    // var_dump($Data);

   
    $data = [];
    $arrstatusorder = array(
        0 => 'New order',
        1 => 'On Production',
        2 => 'On Delivery',
        3 => 'Success',
        4 => 'Return',
        5 => 'Complain',
        6 => 'Cancel',
        99 => 'not ready'
    );
    $arrstatuspaid = array(
        0 => 'UNPAID',
        1 => 'PAID'
    );
    $arrtime = [
        0 => '9am - 1pm',
        1 => '2pm - 5pm',
        2 => '6pm - 8pm',
        3 => '9pm - 0am',
        4 => '1am - 5am',
        5 => '6am - 8am'
    ];

    $tampung = array();
    // print_r($Data);
    if($totalData > 0 ) {
        while ($row = $Data->fetch(PDO::FETCH_LAZY)){

            $product = explode(',', $row['product']);
            $price = explode(',', $row['price']);
            $quantity = explode(',', $row['quantity']);
            // print_r($product);
            // echo $row['product'];
            $dataproduct = [];
            foreach($product as $key => $val) {
                $dataproduct[] = '<span class="badge badge-info">'.$val.'</span></br>';
            }
            $dataprice = [];
            foreach($price as $key => $val) {
                $dataprice[] = '<span class="badge badge-info">'.$config->formatprice($val).'</span></br>';
            }
            $dataquantity = [];
            foreach($quantity as $key => $val) {
                $dataquantity[] = '<span class="badge badge-info">'.$val.'</span></br>';
            }
            
            $type = [ 'nama' => 'ORGANIC' ]; 
            if($row['type'] == 'BD_CP'){
                $type = $config->getData('*', 'corporates', "CorporateUniqueID = '". $row['CustomerID'] ."'");
            }
            if(empty($row['id_florist'])){
                $florist = '<button class="btn btn-sm btn-primary" onclick="selectFlorist(\''. $row['transactionID'] .'\')" style="font-size: 12px;">select florist</button>';
            }else{
                $data = $config->getData('ID, FloristName', 'florist', "ID = '". $row['id_florist'] ."'");
                $florist = '<a href="javascript:;" onclick="selectFlorist(\''. $row['transactionID'] .'\')"><span class="badge badge-sm badge-success">'. $data['FloristName'] .'</span></a>';
            }
            if(empty($row['id_kurir'])){
                $kurir = '<button class="btn btn-sm btn-primary" onclick="pilihKurir(\''. $row['transactionID'] .'\')" style="font-size: 12px;">select kurir</button>';
            }else{
                $data = $config->getData('id, nama_kurir', 'kurirs', "id = '". $row['id_kurir'] ."'");
                $kurir = '<a href="javascript:;" onclick="pilihKurir(\''. $row['transactionID'] .'\')"><span class="badge badge-sm badge-success">'. $data['nama_kurir'] .'</span></a>';
            }
            $btnchangestatus = '<button class="btn btn-sm btn-primary" onclick="chagestatusordermodal(\''. $row['transactionID'] .'\')" style="font-size: 12px;">'. $arrstatusorder[$row['statusOrder']] .'</button>';
            $grandTotal = '0';
            if(!empty($row['grandTotal'])){
                $grandTotal = $row['grandTotal'];
            }

            $cancelorder = '<a href="javascript:;" onclick="cancelOrder(\''. $row['transactionID'] .'\')"><span class="badge badge-sm badge-info">Cancel</span></a>';
            $Kirim = Date('d-M-Y', strtotime($row['delivery_date']));
            $createorder = Date('d F Y', strtotime($row['created_date']));
            $statuspaid = $row['statusPaid'] == 1 ? 'success' : 'warning';
            
            $delivarydatess = $row['delivery_date'];
            $datenow = Date('Y-m-d');
            if($delivarydatess <= $datenow) {
                $color = $delivarydatess.' = '.$datenow;
            } else {
                $color =   '';
            }

            $deliverytime = 'unset';
            if($row['delivery_time']) $deliverytime = $arrtime[$row['delivery_time']];

            // echo $delivarydatess .' '. $datenow . '    -' .$row['delivery_date'];
            $btnchangestatus = '<button class="btn btn-sm btn-primary" onclick="chagestatusordermodal(\''. $row['transactionID'] .'\')" style="font-size: 12px;">'. $arrstatusorder[$row['statusOrder']] .'</button>';

            $subdata = array();
            
            $subdata[]  = '<a href="'.$config->url().'order/?p=detailtrx&trx='. $row['transactionID'] .'" target="_blank">'.$row['transactionID'].'</a>';
            $subdata[]  = $dataproduct;
            $subdata[]  = $row['CustomerName'] .'<small class="badge badge-sm badge-info">'.$type['nama'].'</small>';
            $subdata[]  = $dataprice;
            $subdata[]  = $dataquantity;
            $subdata[]  = $config->formatprice($row['grandTotal']);
            $subdata[]  = $Kirim . '<span class="small" style="color: red;"> '.$deliverytime.'</span>';
            // $subdata[]  = $row['kelurahan'];
            // $subdata[]  = '<span class="badge badge-sm badge-info">'.$arrstatusorder[$row['statusOrder']].'</span>';
            $subdata[]  = '<span class="badge badge-sm badge-'.$statuspaid.'">'.$arrstatuspaid[$row['statusPaid']].'</span>';
            $subdata[]  = $createorder;
            $subdata[]  = $row['admin'];
            $subdata[]  = $florist;
            $subdata[]  = $btnchangestatus;
            $subdata[]  = $color;
            array_push($tampung, $subdata);
         }
    }

    $json_data = array(
        'draw'              => intval($request['draw']),
        'recordsTotal'      => intval($totalData),
        'recordsFiltered'   => intval($totalFilter),
        'data'              => $tampung
    );
    echo json_encode($json_data);
}
if($_GET['type'] == 'tableNewOrder'){
    $request = $_REQUEST;
    $search = $_POST['is_date_search'];

    $daterange ='';
    $corporate ='';
    $admin = '';
    
    if(isset($_POST['date_range'])) {
        $daterange = $_POST['date_range'];
    }

    $databox = '';
    if(isset($_POST['search']['value']) && $_POST['search']['value'] != '') {
        // echo $_POST['search']['value'];
        $databox = '(transaction.transactionID LIKE "%'. $_POST['search']['value'] . '%" OR transaction.CustomerName LIKE "%'. $_POST['search']['value'] . '%" OR users.name LIKE "%'. $_POST['search']['value'] . '%") OR (transaction_details.product_name LIKE "%'.$_POST['search']['value'].'%") AND ';
    }

    $colom = array(
        0   => 'transaction.transactionID',
        1   => 'transaction_details.product_name',
        2   => 'transaction.delivery_date',
        3   => 'villages.name',
        4   => 'transaction.statusOrder',
        5   => 'transaction.notes',
        6   => 'transaction.delivery_date',
        7   => 'transaction.created_date',
        8   => 'transaction.id_florist',
        9   => 'transaction.id_kurir',
        10   => 'FloristName',
        11   => 'Action',
        12   => 'Color',
    );

    $orderby = 'ORDER BY transaction.delivery_date ASC';
    if(isset($_POST['order'][0]['column'])) {
        $column     = $_POST['order'][0]['column'];
        $typesort   = $_POST['order'][0]['dir'];

        $orderby = 'ORDER BY '.$colom[$column].' '. $typesort;
    }

    $limitstart = $_POST['start'];
    $limitend = $_POST['length'];
    $limit = 'LIMIT '.$limitstart.','.$limitend;

    $Query = '
    SELECT 
        (select GROUP_CONCAT(transaction_details.product_name SEPARATOR ",") from transaction_details where transaction_details.id_trx = transaction.transactionID) as product, 
        (select GROUP_CONCAT(transaction_details.product_price SEPARATOR ",") from transaction_details where transaction_details.id_trx = transaction.transactionID) as price, 
        (select GROUP_CONCAT(transaction_details.product_cost SEPARATOR ",") from transaction_details where transaction_details.id_trx = transaction.transactionID) as cost, 
        (select GROUP_CONCAT(transaction_details.product_qty SEPARATOR ",") from transaction_details where transaction_details.id_trx = transaction.transactionID) as quantity, 
        transaction.*, transaction_details.*, villages.name as kelurahan, users.name as admin
        FROM transaction 
        LEFT JOIN transaction_details ON transaction_details.id_trx = transaction.transactionID 
        LEFT JOIN villages ON villages.id = transaction.kelurahan_id 
        LEFT JOIN users ON users.id = transaction.created_by WHERE ';

    $QueryTotal = '
    SELECT 
        (select GROUP_CONCAT(transaction_details.product_name SEPARATOR ",") from transaction_details where transaction_details.id_trx = transaction.transactionID) as product, 
        (select GROUP_CONCAT(transaction_details.product_price SEPARATOR ",") from transaction_details where transaction_details.id_trx = transaction.transactionID) as price, 
        (select GROUP_CONCAT(transaction_details.product_cost SEPARATOR ",") from transaction_details where transaction_details.id_trx = transaction.transactionID) as cost, 
        (select GROUP_CONCAT(transaction_details.product_qty SEPARATOR ",") from transaction_details where transaction_details.id_trx = transaction.transactionID) as quantity, 
        transaction.*, transaction_details.*, villages.name as kelurahan, users.name as admin
        FROM transaction 
        LEFT JOIN transaction_details ON transaction_details.id_trx = transaction.transactionID 
        LEFT JOIN villages ON villages.id = transaction.kelurahan_id 
        LEFT JOIN users ON users.id = transaction.created_by WHERE ';

    $Query .= $databox;
    $QueryTotal .= $databox;
    if($search == 'yes') { 
        $rangeArray = explode("_",$daterange); 

        $startDate = $rangeArray[0];
        $endsDate = $rangeArray[1];

        $daterangequery = "transaction.delivery_date BETWEEN '". $startDate ."' AND '". $endsDate ."'";

        $Query .= $daterangequery." AND transaction.statusOrder = '0' GROUP BY transaction.transactionID ". $orderby. ' '. $limit;
        $QueryTotal .= $daterangequery." AND transaction.statusOrder = '0' GROUP BY transaction.transactionID ". $orderby;
        
        $stmt2 = $config->runQuery($QueryTotal);
        $stmt2->execute();
        $totalData = $stmt2->rowCount();
        $totalFilter = $totalData;
        
        $Data = $config->runQuery($Query);
        $Data->execute();
        
    } else {
        $Query .=" transaction.statusOrder = '0' GROUP BY transaction.transactionID ". $orderby. ' '. $limit;
        $QueryTotal .=" transaction.statusOrder = '0' GROUP BY transaction.transactionID ". $orderby;
        
        // var_dump($QueryTotal);
        $stmt2 = $config->runQuery($QueryTotal);
        $stmt2->execute();
        $totalData = $stmt2->rowCount();
        $totalFilter = $totalData;

        $Data = $config->runQuery($Query);
        $Data->execute();
    }
    // var_dump($Data);

   
    $data = [];
    $arrstatusorder = array(
        0 => 'New order',
        1 => 'On Production',
        2 => 'On Delivery',
        3 => 'Success',
        4 => 'Return',
        5 => 'Complain',
        6 => 'Cancel',
        99 => 'not ready'
    );
    $arrstatuspaid = array(
        0 => 'UNPAID',
        1 => 'PAID'
    );
    $arrtime = [
        0 => '9am - 1pm',
        1 => '2pm - 5pm',
        2 => '6pm - 8pm',
        3 => '9pm - 0am',
        4 => '1am - 5am',
        5 => '6am - 8am'
    ];

    $tampung = array();
    // print_r($Data);
    if($totalData > 0 ) {
        while ($row = $Data->fetch(PDO::FETCH_LAZY)){

            $product = explode(',', $row['product']);
            $price = explode(',', $row['price']);
            $quantity = explode(',', $row['quantity']);
            // print_r($product);
            // echo $row['product'];
            $dataproduct = [];
            foreach($product as $key => $val) {
                $dataproduct[] = '<span class="badge badge-info">'.$val.'</span></br>';
            }
            $dataprice = [];
            foreach($price as $key => $val) {
                $dataprice[] = '<span class="badge badge-info">'.$config->formatprice($val).'</span></br>';
            }
            $dataquantity = [];
            foreach($quantity as $key => $val) {
                $dataquantity[] = '<span class="badge badge-info">'.$val.'</span></br>';
            }
            
            $type = [ 'nama' => 'ORGANIC' ]; 
            if($row['type'] == 'BD_CP'){
                $type = $config->getData('*', 'corporates', "CorporateUniqueID = '". $row['CustomerID'] ."'");
            }
            if(empty($row['id_florist'])){
                $florist = '<button class="btn btn-sm btn-primary" onclick="selectFlorist(\''. $row['transactionID'] .'\')" style="font-size: 12px;">select florist</button>';
            }else{
                $data = $config->getData('ID, FloristName', 'florist', "ID = '". $row['id_florist'] ."'");
                $florist = '<a href="javascript:;" onclick="selectFlorist(\''. $row['transactionID'] .'\')"><span class="badge badge-sm badge-success">'. $data['FloristName'] .'</span></a>';
            }
            if(empty($row['id_kurir'])){
                $kurir = '<button class="btn btn-sm btn-primary" onclick="pilihKurir(\''. $row['transactionID'] .'\')" style="font-size: 12px;">select kurir</button>';
            }else{
                $data = $config->getData('id, nama_kurir', 'kurirs', "id = '". $row['id_kurir'] ."'");
                $kurir = '<a href="javascript:;" onclick="pilihKurir(\''. $row['transactionID'] .'\')"><span class="badge badge-sm badge-success">'. $data['nama_kurir'] .'</span></a>';
            }
            $btnchangestatus = '<button class="btn btn-sm btn-primary" onclick="chagestatusordermodal(\''. $row['transactionID'] .'\')" style="font-size: 12px;">'. $arrstatusorder[$row['statusOrder']] .'</button>';
            $grandTotal = '0';
            if(!empty($row['grandTotal'])){
                $grandTotal = $row['grandTotal'];
            }

            $cancelorder = '<a href="javascript:;" onclick="cancelOrder(\''. $row['transactionID'] .'\')"><span class="badge badge-sm badge-info">Cancel</span></a>';
            $Kirim = Date('d-M-Y', strtotime($row['delivery_date']));
            $createorder = Date('d F Y', strtotime($row['created_date']));
            $statuspaid = $row['statusPaid'] == 1 ? 'success' : 'warning';
            
            $delivarydatess = $row['delivery_date'];
            $datenow = Date('Y-m-d');
            if($delivarydatess <= $datenow) {
                $color = $delivarydatess.' = '.$datenow;
            } else {
                $color =   '';
            }

            $deliverytime = 'unset';
            if($row['delivery_time']) $deliverytime = $arrtime[$row['delivery_time']];

            // echo $delivarydatess .' '. $datenow . '    -' .$row['delivery_date'];
            $btnchangestatus = '<button class="btn btn-sm btn-primary" onclick="chagestatusordermodal(\''. $row['transactionID'] .'\')" style="font-size: 12px;">'. $arrstatusorder[$row['statusOrder']] .'</button>';

            $subdata = array();
            
            $subdata[]  = '<a href="'.$config->url().'order/?p=detailtrx&trx='. $row['transactionID'] .'" target="_blank">'.$row['transactionID'].'</a>';
            $subdata[]  = $dataproduct;
            $subdata[]  = $row['CustomerName'] .'<small class="badge badge-sm badge-info">'.$type['nama'].'</small>';
            $subdata[]  = $dataprice;
            $subdata[]  = $dataquantity;
            $subdata[]  = $config->formatprice($row['grandTotal']);
            $subdata[]  = $Kirim . '<span class="small" style="color: red;"> '.$deliverytime.'</span>';
            // $subdata[]  = $row['kelurahan'];
            // $subdata[]  = '<span class="badge badge-sm badge-info">'.$arrstatusorder[$row['statusOrder']].'</span>';
            $subdata[]  = '<span class="badge badge-sm badge-'.$statuspaid.'">'.$arrstatuspaid[$row['statusPaid']].'</span>';
            $subdata[]  = $createorder;
            $subdata[]  = $row['admin'];
            $subdata[]  = $florist;
            $subdata[]  = $btnchangestatus;
            $subdata[]  = $color;
            array_push($tampung, $subdata);
         }
    }

    $json_data = array(
        'draw'              => intval($request['draw']),
        'recordsTotal'      => intval($totalData),
        'recordsFiltered'   => intval($totalFilter),
        'data'              => $tampung
    );
    echo json_encode($json_data);
}
if($_GET['type'] == 'tableOnProccess'){
    $request = $_REQUEST;
    $search = $_POST['is_date_search'];

    $daterange ='';
    $corporate ='';
    $admin = '';
    
    if(isset($_POST['date_range'])) {
        $daterange = $_POST['date_range'];
    }
    $databox = '';
    if(isset($_POST['search']['value']) && $_POST['search']['value'] != '') {
        // echo $_POST['search']['value'];
        $databox = '(transaction.transactionID LIKE "%'. $_POST['search']['value'] . '%" OR transaction.CustomerName LIKE "%'. $_POST['search']['value'] . '%" OR users.name LIKE "%'. $_POST['search']['value'] . '%") OR (transaction_details.product_name LIKE "%'.$_POST['search']['value'].'%") AND ';
    }

    $colom = array(
        0   => 'transaction.transactionID',
        1   => 'transaction_details.product_name',
        2   => 'transaction.delivery_date',
        3   => 'villages.name',
        4   => 'transaction.statusOrder',
        5   => 'transaction.notes',
        6   => 'transaction.delivery_date',
        7   => 'transaction.created_date',
        8   => 'transaction.id_florist',
        9   => 'transaction.id_kurir',
        10   => 'FloristName',
        11   => 'Action',
        12   => 'Color',
    );

    $orderby = 'ORDER BY transaction.delivery_date DESC';
    if(isset($_POST['order'][0]['column'])) {
        $column     = $_POST['order'][0]['column'];
        $typesort   = $_POST['order'][0]['dir'];

        $orderby = 'ORDER BY '.$colom[$column].' '. $typesort;
    }

    $limitstart = $_POST['start'];
    $limitend = $_POST['length'];
    $limit = 'LIMIT '.$limitstart.','.$limitend;

    $Query = '
    SELECT 
    (select GROUP_CONCAT(transaction_details.product_name SEPARATOR ",") from transaction_details where transaction_details.id_trx = transaction.transactionID) as product, 
    (select GROUP_CONCAT(transaction_details.product_price SEPARATOR ",") from transaction_details where transaction_details.id_trx = transaction.transactionID) as price, 
    (select GROUP_CONCAT(transaction_details.product_cost SEPARATOR ",") from transaction_details where transaction_details.id_trx = transaction.transactionID) as cost, 
    (select GROUP_CONCAT(transaction_details.product_qty SEPARATOR ",") from transaction_details where transaction_details.id_trx = transaction.transactionID) as quantity, 
    transaction.*, transaction_details.*, villages.name as kelurahan, users.name as admin
    FROM transaction 
    LEFT JOIN transaction_details ON transaction_details.id_trx = transaction.transactionID 
    LEFT JOIN villages ON villages.id = transaction.kelurahan_id 
    LEFT JOIN users ON users.id = transaction.created_by WHERE ';

     $QueryTotal = '
    SELECT 
        (select GROUP_CONCAT(transaction_details.product_name SEPARATOR ",") from transaction_details where transaction_details.id_trx = transaction.transactionID) as product, 
        (select GROUP_CONCAT(transaction_details.product_price SEPARATOR ",") from transaction_details where transaction_details.id_trx = transaction.transactionID) as price, 
        (select GROUP_CONCAT(transaction_details.product_cost SEPARATOR ",") from transaction_details where transaction_details.id_trx = transaction.transactionID) as cost, 
        (select GROUP_CONCAT(transaction_details.product_qty SEPARATOR ",") from transaction_details where transaction_details.id_trx = transaction.transactionID) as quantity, 
        transaction.*, transaction_details.*, villages.name as kelurahan, users.name as admin
        FROM transaction 
        LEFT JOIN transaction_details ON transaction_details.id_trx = transaction.transactionID 
        LEFT JOIN villages ON villages.id = transaction.kelurahan_id 
        LEFT JOIN users ON users.id = transaction.created_by WHERE ';

    $Query .= $databox;
        $QueryTotal .= $databox;

    if($search == 'yes') {
        $rangeArray = explode("_",$daterange); 

        $startDate = $rangeArray[0];
        $endsDate = $rangeArray[1];

        $daterangequery = "transaction.delivery_date BETWEEN '". $startDate ."' AND '". $endsDate ."'";

       $Query .= $daterangequery." AND transaction.statusOrder = '1' GROUP BY transaction.transactionID ". $orderby. ' '. $limit;
        $QueryTotal .= $daterangequery." AND transaction.statusOrder = '1' GROUP BY transaction.transactionID ". $orderby;

        $stmt2 = $config->runQuery($QueryTotal);
        $stmt2->execute();
        $totalData = $stmt2->rowCount();
        $totalFilter = $totalData;

        $Data = $config->runQuery($Query);
        $Data->execute();

    } else {
        $Query .=" transaction.statusOrder = '1' GROUP BY transaction.transactionID ". $orderby. ' '. $limit;
        $QueryTotal .=" transaction.statusOrder = '1' GROUP BY transaction.transactionID ". $orderby;
        
         $stmt2 = $config->runQuery($QueryTotal);
        $stmt2->execute();
        $totalData = $stmt2->rowCount();
        $totalFilter = $totalData;

        $Data = $config->runQuery($Query);
        $Data->execute();
    }
    // var_dump($Data);

    $colom = array(
        0   => 'transactionID',
        1   => 'ProductName',
        2   => 'SenderName',
        3   => 'Price',
        4   => 'Quantity',
        5   => 'GrandTotal',
        6   => 'DeliveryDate',
        7   => 'StatusPaid',
        8   => 'CreatedOrder',
        9   => 'CreatedBy',
        10   => 'FloristName',
        10   => 'KurirName',
        11   => 'Color',
        12   => 'Color',
        13   => 'Color',
    );

    $data = [];
    $arrstatusorder = array(
        0 => 'New order',
        1 => 'On Production',
        2 => 'On Delivery',
        3 => 'Success',
        4 => 'Return',
        5 => 'Complain',
        6 => 'Cancel',
        99 => 'not ready'
    );
    $arrstatuspaid = array(
        0 => 'UNPAID',
        1 => 'PAID'
    );
    $arrtime = [
        0 => '9am - 1pm',
        1 => '2pm - 5pm',
        2 => '6pm - 8pm',
        3 => '9pm - 0am',
        4 => '1am - 5am',
        5 => '6am - 8am'
    ];

    $tampung = array();
    // print_r($Data);
    if($totalData > 0 ) {
        while ($row = $Data->fetch(PDO::FETCH_LAZY)){

            $product = explode(',', $row['product']);
            $price = explode(',', $row['price']);
            $quantity = explode(',', $row['quantity']);
            // print_r($product);
            // echo $row['product'];
            $dataproduct = [];
            foreach($product as $key => $val) {
                $dataproduct[] = '<span class="badge badge-info">'.$val.'</span></br>';
            }
            $dataprice = [];
            foreach($price as $key => $val) {
                $dataprice[] = '<span class="badge badge-info">'.$config->formatprice($val).'</span></br>';
            }
            $dataquantity = [];
            foreach($quantity as $key => $val) {
                $dataquantity[] = '<span class="badge badge-info">'.$val.'</span></br>';
            }
            
            $type = [ 'nama' => 'ORGANIC' ]; 
            if($row['type'] == 'BD_CP'){
                $type = $config->getData('*', 'corporates', "CorporateUniqueID = '". $row['CustomerID'] ."'");
            }
            if(empty($row['id_florist'])){
                $florist = '<button class="btn btn-sm btn-primary" onclick="selectFlorist(\''. $row['transactionID'] .'\')" style="font-size: 12px;">select florist</button>';
            }else{
                $data = $config->getData('ID, FloristName', 'florist', "ID = '". $row['id_florist'] ."'");
                $florist = '<a href="javascript:;" onclick="selectFlorist(\''. $row['transactionID'] .'\')"><span class="badge badge-sm badge-success">'. $data['FloristName'] .'</span></a>';
            }
            if(empty($row['id_kurir'])){
                $kurir = '<button class="btn btn-sm btn-primary" onclick="pilihKurir(\''. $row['transactionID'] .'\')" style="font-size: 12px;">select kurir</button>';
            }else{
                $data = $config->getData('id, nama_kurir', 'kurirs', "id = '". $row['id_kurir'] ."'");
                $kurir = '<a href="javascript:;" onclick="pilihKurir(\''. $row['transactionID'] .'\')"><span class="badge badge-sm badge-success">'. $data['nama_kurir'] .'</span></a>';
            }

            if(empty($row['id_kurir'])){
                $kurir = '<button class="btn btn-sm btn-primary" onclick="pilihKurir(\''. $row['transactionID'] .'\')" style="font-size: 12px;">select kurir</button>';
            }else{
                $data = $config->getData('id, nama_kurir', 'kurirs', "id = '". $row['id_kurir'] ."'");
                $kurir = '<a href="javascript:;" onclick="pilihKurir(\''. $row['transactionID'] .'\')"><span class="badge badge-sm badge-success">'. $data['nama_kurir'] .'</span></a>';
            }

            $grandTotal = '0';
            if(!empty($row['grandTotal'])){
                $grandTotal = $row['grandTotal'];
            }
            $Kirim = Date('d-M-Y', strtotime($row['delivery_date']));
            $createorder = Date('d F Y', strtotime($row['created_date']));
            $statuspaid = $row['statusPaid'] == 1 ? 'success' : 'warning';
            
            $delivarydatess = strtotime(Date('Y-m-d', strtotime($row['delivery_date'])));
            
            $datenow = strtotime($config->getdate('Y-m-d'));
            if($delivarydatess <= $datenow) {
                $color = $delivarydatess.' = '.$datenow;
            } else {
                $color =   '';
            }
            $deliverytime = 'unset';
            if($row['delivery_time']) $deliverytime = $arrtime[$row['delivery_time']];
            // var_dump(strtotime(Date('Y-m-d', strtotime($row['delivery_date']))));
            // var_dump(strtotime($config->getdate('Y-m-d')));
            $subdata = array();

            $subdata[]  = '<a href="'.$config->url().'order/?p=detailtrx&trx='. $row['transactionID'] .'" target="_blank">'.$row['transactionID'].'</a>';
            $subdata[]  = $dataproduct;
            $subdata[]  = $row['CustomerName'] .'<small class="badge badge-sm badge-info">'.$type['nama'].'</small>';
            $subdata[]  = $dataprice;
            $subdata[]  = $dataquantity;
            $subdata[]  = $config->formatprice($row['grandTotal']);
            $subdata[]  = $Kirim . '<span class="small" style="color: red;"> '.$deliverytime.'</span>';
            // $subdata[]  = $row['kelurahan'];
            // $subdata[]  = '<span class="badge badge-sm badge-info">'.$arrstatusorder[$row['statusOrder']].'</span>';
            $subdata[]  = '<span class="badge badge-sm badge-'.$statuspaid.'">'.$arrstatuspaid[$row['statusPaid']].'</span>';
            $subdata[]  = $createorder;
            $subdata[]  = $row['admin'];
            $subdata[]  = $florist;
            $subdata[]  = $kurir;
            $subdata[]  = $color;
            array_push($tampung, $subdata);
         }
    }

    $json_data = array(
        'draw'              => intval($request['draw']),
        'recordsTotal'      => intval($totalData),
        'recordsFiltered'   => intval($totalFilter),
        'data'              => $tampung
    );
    echo json_encode($json_data);
}
if($_GET['type'] == 'tableOnDelivery'){
    $request = $_REQUEST;
    $search = $_POST['is_date_search'];

    $daterange ='';
    $corporate ='';
    $admin = '';
    
    if(isset($_POST['date_range'])) {
        $daterange = $_POST['date_range'];
    }

    $databox = '';
    if(isset($_POST['search']['value']) && $_POST['search']['value'] != '') {
        // echo $_POST['search']['value'];
        $databox = '(transaction.transactionID LIKE "%'. $_POST['search']['value'] . '%" OR transaction.CustomerName LIKE "%'. $_POST['search']['value'] . '%" OR users.name LIKE "%'. $_POST['search']['value'] . '%") OR (transaction_details.product_name LIKE "%'.$_POST['search']['value'].'%") AND ';
    }

    $colom = array(
        0   => 'transaction.transactionID',
        1   => 'transaction_details.product_name',
        2   => 'transaction.delivery_date',
        3   => 'villages.name',
        4   => 'transaction.statusOrder',
        5   => 'transaction.notes',
        6   => 'transaction.delivery_date',
        7   => 'transaction.created_date',
        8   => 'transaction.id_florist',
        9   => 'transaction.id_kurir',
        10   => 'FloristName',
        11   => 'Action',
        12   => 'Color',
    );

    $orderby = 'ORDER BY transaction.delivery_date ASC';
    if(isset($_POST['order'][0]['column'])) {
        $column     = $_POST['order'][0]['column'];
        $typesort   = $_POST['order'][0]['dir'];

        $orderby = 'ORDER BY '.$colom[$column].' '. $typesort;
    }

    $limitstart = $_POST['start'];
    $limitend = $_POST['length'];
    $limit = 'LIMIT '.$limitstart.','.$limitend;

    $Query = '
    SELECT 
    (select GROUP_CONCAT(transaction_details.product_name SEPARATOR ",") from transaction_details where transaction_details.id_trx = transaction.transactionID) as product, 
    (select GROUP_CONCAT(transaction_details.product_price SEPARATOR ",") from transaction_details where transaction_details.id_trx = transaction.transactionID) as price, 
    (select GROUP_CONCAT(transaction_details.product_cost SEPARATOR ",") from transaction_details where transaction_details.id_trx = transaction.transactionID) as cost, 
    (select GROUP_CONCAT(transaction_details.product_qty SEPARATOR ",") from transaction_details where transaction_details.id_trx = transaction.transactionID) as quantity, 
    transaction.*, transaction_details.*, villages.name as kelurahan, users.name as admin
    FROM transaction 
    LEFT JOIN transaction_details ON transaction_details.id_trx = transaction.transactionID 
    LEFT JOIN villages ON villages.id = transaction.kelurahan_id 
    LEFT JOIN users ON users.id = transaction.created_by WHERE ';

    $QueryTotal = '
    SELECT 
    (select GROUP_CONCAT(transaction_details.product_name SEPARATOR ",") from transaction_details where transaction_details.id_trx = transaction.transactionID) as product, 
    (select GROUP_CONCAT(transaction_details.product_price SEPARATOR ",") from transaction_details where transaction_details.id_trx = transaction.transactionID) as price, 
    (select GROUP_CONCAT(transaction_details.product_cost SEPARATOR ",") from transaction_details where transaction_details.id_trx = transaction.transactionID) as cost, 
    (select GROUP_CONCAT(transaction_details.product_qty SEPARATOR ",") from transaction_details where transaction_details.id_trx = transaction.transactionID) as quantity, 
    transaction.*, transaction_details.*, villages.name as kelurahan, users.name as admin
    FROM transaction 
    LEFT JOIN transaction_details ON transaction_details.id_trx = transaction.transactionID 
    LEFT JOIN villages ON villages.id = transaction.kelurahan_id 
    LEFT JOIN users ON users.id = transaction.created_by WHERE ';

    $Query .= $databox;
    $QueryTotal .= $databox;
    if($search == 'yes') {
        $rangeArray = explode("_",$daterange); 

        $startDate = $rangeArray[0];
        $endsDate = $rangeArray[1];

        $daterangequery = "transaction.delivery_date BETWEEN '". $startDate ."' AND '". $endsDate ."'";
        $corporatequery = " AND transaction.CustomerID = '".$corporate."'";
        $adminquery = " AND transaction.created_by = '".$admin."'";

        $Query .= $daterangequery." AND transaction.statusOrder = '2' GROUP BY transaction.transactionID ". $orderby. ' '. $limit;
        $QueryTotal .= $daterangequery." AND transaction.statusOrder = '2' GROUP BY transaction.transactionID ". $orderby;

        $stmt2 = $config->runQuery($QueryTotal);
        $stmt2->execute();
        $totalData = $stmt2->rowCount();
        $totalFilter = $totalData;
        
        $Data = $config->runQuery($Query);
        $Data->execute();
        
    } else {
        $Query .=" transaction.statusOrder = '2' GROUP BY transaction.transactionID ". $orderby. ' '. $limit;
        $QueryTotal .=" transaction.statusOrder = '2' GROUP BY transaction.transactionID ". $orderby;
        
        // var_dump($Query);
        $stmt2 = $config->runQuery($QueryTotal);
        $stmt2->execute();
        $totalData = $stmt2->rowCount();
        $totalFilter = $totalData;

        $Data = $config->runQuery($Query);
        $Data->execute();
    }
    // var_dump($Data);

    $colom = array(
        0   => 'transactionID',
        1   => 'ProductName',
        2   => 'SenderName',
        3   => 'Price',
        4   => 'Quantity',
        5   => 'GrandTotal',
        6   => 'DeliveryDate',
        7   => 'StatusPaid',
        8   => 'CreatedOrder',
        9   => 'CreatedBy',
        10   => 'FloristName',
        10   => 'KurirName',
        10   => 'KurirName',
        11   => 'Color',
    );

    $data = [];
    $arrstatusorder = array(
        0 => 'New order',
        1 => 'On Production',
        2 => 'On Delivery',
        3 => 'Success',
        4 => 'Return',
        5 => 'Complain',
        6 => 'Cancel',
        99 => 'not ready'
    );
    $arrstatuspaid = array(
        0 => 'UNPAID',
        1 => 'PAID'
    );
    $arrtime = [
        0 => '9am - 1pm',
        1 => '2pm - 5pm',
        2 => '6pm - 8pm',
        3 => '9pm - 0am',
        4 => '1am - 5am',
        5 => '6am - 8am'
    ];

    $tampung = array();
    // print_r($Data);
    if($totalData > 0 ) {
        while ($row = $Data->fetch(PDO::FETCH_LAZY)){

            $product = explode(',', $row['product']);
            $price = explode(',', $row['price']);
            $quantity = explode(',', $row['quantity']);
            // print_r($product);
            // echo $row['product'];
            $dataproduct = [];
            foreach($product as $key => $val) {
                $dataproduct[] = '<span class="badge badge-info">'.$val.'</span></br>';
            }
            $dataprice = [];
            foreach($price as $key => $val) {
                $dataprice[] = '<span class="badge badge-info">'.$config->formatprice($val).'</span></br>';
            }
            $dataquantity = [];
            foreach($quantity as $key => $val) {
                $dataquantity[] = '<span class="badge badge-info">'.$val.'</span></br>';
            }
            
            $type = [ 'nama' => 'ORGANIC' ]; 
            if($row['type'] == 'BD_CP'){
                $type = $config->getData('*', 'corporates', "CorporateUniqueID = '". $row['CustomerID'] ."'");
            }
            if(empty($row['id_florist'])){
                $florist = '<button class="btn btn-sm btn-primary" onclick="selectFlorist(\''. $row['transactionID'] .'\')" style="font-size: 12px;">select florist</button>';
            }else{
                $data = $config->getData('ID, FloristName', 'florist', "ID = '". $row['id_florist'] ."'");
                $florist = '<a href="javascript:;" onclick="selectFlorist(\''. $row['transactionID'] .'\')"><span class="badge badge-sm badge-success">'. $data['FloristName'] .'</span></a>';
            }
            if(empty($row['id_kurir'])){
                $kurir = '<button class="btn btn-sm btn-primary" onclick="pilihKurir(\''. $row['transactionID'] .'\')" style="font-size: 12px;">select kurir</button>';
            }else{
                $data = $config->getData('id, nama_kurir', 'kurirs', "id = '". $row['id_kurir'] ."'");
                $kurir = '<a href="javascript:;" onclick="pilihKurir(\''. $row['transactionID'] .'\')"><span class="badge badge-sm badge-success">'. $data['nama_kurir'] .'</span></a>';
            }

            if(empty($row['id_kurir'])){
                $kurir = '<button class="btn btn-sm btn-primary" onclick="pilihKurir(\''. $row['transactionID'] .'\')" style="font-size: 12px;">select kurir</button>';
            }else{
                $data = $config->getData('id, nama_kurir', 'kurirs', "id = '". $row['id_kurir'] ."'");
                $kurir = '<a href="javascript:;" onclick="pilihKurir(\''. $row['transactionID'] .'\')"><span class="badge badge-sm badge-success">'. $data['nama_kurir'] .'</span></a>';
            }

            $btnchangestatus = '<button class="btn btn-sm btn-primary" onclick="chagestatusordermodal(\''. $row['transactionID'] .'\')" style="font-size: 12px;">'. $arrstatusorder[$row['statusOrder']] .'</button>';
            $grandTotal = '0';
            if(!empty($row['grandTotal'])){
                $grandTotal = $row['grandTotal'];
            }
            $Kirim = Date('d-M-Y', strtotime($row['delivery_date']));
            $createorder = Date('d F Y', strtotime($row['created_date']));
            $statuspaid = $row['statusPaid'] == 1 ? 'success' : 'warning';
            
            $deliverytime = 'unset';
            if($row['delivery_time']) $deliverytime = $arrtime[$row['delivery_time']];

            $delivarydatess = $row['delivery_date'];
            $datenow = Date('Y-m-d');
            if($delivarydatess <= $datenow) {
                $color = $delivarydatess.' = '.$datenow;
            } else {
                $color =   '';
            }

            $subdata = array();
            $subdata[]  = '<a href="'.$config->url().'order/?p=detailtrx&trx='. $row['transactionID'] .'" target="_blank">'.$row['transactionID'].'</a>';
            $subdata[]  = $dataproduct;
            $subdata[]  = $row['CustomerName'] .'<small class="badge badge-sm badge-info">'.$type['nama'].'</small>';
            $subdata[]  = $dataprice;
            $subdata[]  = $dataquantity;
            $subdata[]  = $config->formatprice($row['grandTotal']);
            $subdata[]  = $Kirim . '<span class="small" style="color: red;"> '.$deliverytime.'</span>';
            // $subdata[]  = $row['kelurahan'];
            // $subdata[]  = '<span class="badge badge-sm badge-info">'.$arrstatusorder[$row['statusOrder']].'</span>';
            $subdata[]  = '<span class="badge badge-sm badge-'.$statuspaid.'">'.$arrstatuspaid[$row['statusPaid']].'</span>';
            $subdata[]  = $createorder;
            $subdata[]  = $row['admin'];
            $subdata[]  = $florist;
            $subdata[]  = $kurir;
            $subdata[]  = $btnchangestatus;
            $subdata[]  = $color;
            array_push($tampung, $subdata);
         }
    }

    $json_data = array(
        'draw'              => intval($request['draw']),
        'recordsTotal'      => intval($totalData),
        'recordsFiltered'   => intval($totalFilter),
        'data'              => $tampung
    );
    echo json_encode($json_data);
}
if($_GET['type'] == 'tableHistory'){
    $request = $_REQUEST;
    $search = $_POST['is_date_search'];

    $daterange ='';
    $corporate ='';
    $admin = '';
    
    if(isset($_POST['date_range'])) {
        $daterange = $_POST['date_range'];
    }

    $databox = '';
    if(isset($_POST['search']['value']) && $_POST['search']['value'] != '') {
        // echo $_POST['search']['value'];
        $databox = '(transaction.transactionID LIKE "%'. $_POST['search']['value'] . '%" OR transaction.CustomerName LIKE "%'. $_POST['search']['value'] . '%" OR users.name LIKE "%'. $_POST['search']['value'] . '%")  OR (transaction_details.product_name LIKE "%'.$_POST['search']['value'].'%") AND ';
    }

     $colom = array(
        0   => 'transaction.transactionID',
        1   => 'transaction_details.product_name',
        2   => 'transaction.delivery_date',
        3   => 'villages.name',
        4   => 'transaction.statusOrder',
        5   => 'transaction.notes',
        6   => 'transaction.delivery_date',
        7   => 'transaction.created_date',
        8   => 'transaction.id_florist',
        9   => 'transaction.id_kurir',
        10   => 'FloristName',
        11   => 'Action',
        12   => 'Color',
    );

    $orderby = 'ORDER BY transaction.delivery_date ASC';
    if(isset($_POST['order'][0]['column'])) {
        $column     = $_POST['order'][0]['column'];
        $typesort   = $_POST['order'][0]['dir'];

        $orderby = 'ORDER BY '.$colom[$column].' '. $typesort;
    }

    $limitstart = $_POST['start'];
    $limitend = $_POST['length'];
    $limit = 'LIMIT '.$limitstart.','.$limitend;

    $Query = '
    SELECT 
    (select GROUP_CONCAT(transaction_details.product_name SEPARATOR ",") from transaction_details where transaction_details.id_trx = transaction.transactionID) as product, 
    (select GROUP_CONCAT(transaction_details.product_price SEPARATOR ",") from transaction_details where transaction_details.id_trx = transaction.transactionID) as price, 
    (select GROUP_CONCAT(transaction_details.product_cost SEPARATOR ",") from transaction_details where transaction_details.id_trx = transaction.transactionID) as cost, 
    (select GROUP_CONCAT(transaction_details.product_qty SEPARATOR ",") from transaction_details where transaction_details.id_trx = transaction.transactionID) as quantity, 
    transaction.*, transaction_details.*, villages.name as kelurahan, users.name as admin, kurirs.nama_kurir as NamaKurir
    FROM transaction 
    LEFT JOIN transaction_details ON transaction_details.id_trx = transaction.transactionID 
    LEFT JOIN villages ON villages.id = transaction.kelurahan_id 
    LEFT JOIN users ON users.id = transaction.created_by 
    LEFT JOIN kurirs ON kurirs.id = transaction.id_kurir
    WHERE ';
    
    $QueryTotal = '
    SELECT 
    (select GROUP_CONCAT(transaction_details.product_name SEPARATOR ",") from transaction_details where transaction_details.id_trx = transaction.transactionID) as product, 
    (select GROUP_CONCAT(transaction_details.product_price SEPARATOR ",") from transaction_details where transaction_details.id_trx = transaction.transactionID) as price, 
    (select GROUP_CONCAT(transaction_details.product_cost SEPARATOR ",") from transaction_details where transaction_details.id_trx = transaction.transactionID) as cost, 
    (select GROUP_CONCAT(transaction_details.product_qty SEPARATOR ",") from transaction_details where transaction_details.id_trx = transaction.transactionID) as quantity, 
    transaction.*, transaction_details.*, villages.name as kelurahan, users.name as admin, kurirs.nama_kurir as NamaKurir
    FROM transaction 
    LEFT JOIN transaction_details ON transaction_details.id_trx = transaction.transactionID 
    LEFT JOIN villages ON villages.id = transaction.kelurahan_id 
    LEFT JOIN users ON users.id = transaction.created_by 
    LEFT JOIN kurirs ON kurirs.id = transaction.id_kurir
    WHERE ';

    $Query .= $databox;
    $QueryTotal .= $databox;
    if($search == 'yes') {
        $rangeArray = explode("_",$daterange); 

        $startDate = $rangeArray[0];
        $endsDate = $rangeArray[1];

        $daterangequery = "transaction.delivery_date BETWEEN '". $startDate ."' AND '". $endsDate ."'";
        $corporatequery = " AND transaction.CustomerID = '".$corporate."'";
        $adminquery = " AND transaction.created_by = '".$admin."'";

        $Query .= $daterangequery." AND transaction.statusOrder IN (3, 4, 5) GROUP BY transaction.transactionID ". $orderby. ' '. $limit;
        $QueryTotal .= $daterangequery." AND transaction.statusOrder IN (3, 4, 5) GROUP BY transaction.transactionID ". $orderby;

        $stmt2 = $config->runQuery($QueryTotal);
        $stmt2->execute();
        $totalData = $stmt2->rowCount();
        $totalFilter = $totalData;
        
        $Data = $config->runQuery($Query);
        $Data->execute();
    } else {
        $Query .=" transaction.statusOrder IN (3, 4, 5) GROUP BY transaction.transactionID ". $orderby. ' '. $limit;
        $QueryTotal .=" transaction.statusOrder IN (3, 4, 5) GROUP BY transaction.transactionID ". $orderby;
        
        $stmt2 = $config->runQuery($QueryTotal);
        $stmt2->execute();
        $totalData = $stmt2->rowCount();
        $totalFilter = $totalData;

        $Data = $config->runQuery($Query);
        $Data->execute();
    }
    // var_dump($Data);

    $colom = array(
        0   => 'transactionID',
        1   => 'ProductName',
        2   => 'SenderName',
        3   => 'Price',
        4   => 'Quantity',
        5   => 'GrandTotal',
        6   => 'DeliveryDate',
        7   => 'StatusPaid',
        8   => 'CreatedOrder',
        9   => 'CreatedBy',
        10   => 'CreatedBy',
        11   => 'Color',
    );

    $data = [];
    $arrstatusorder = array(
        0 => 'New order',
        1 => 'On Production',
        2 => 'On Delivery',
        3 => 'Success',
        4 => 'Return',
        5 => 'Complain',
        6 => 'Cancel',
        99 => 'not ready'
    );
    $arrstatuspaid = array(
        0 => 'UNPAID',
        1 => 'PAID'
    );
    $arrtime = [
        0 => '9am - 1pm',
        1 => '2pm - 5pm',
        2 => '6pm - 8pm',
        3 => '9pm - 0am',
        4 => '1am - 5am',
        5 => '6am - 8am'
    ];

    $tampung = array();
    // print_r($Data);
    if($totalData > 0 ) {
        while ($row = $Data->fetch(PDO::FETCH_LAZY)){

            $product = explode(',', $row['product']);
            $price = explode(',', $row['price']);
            $quantity = explode(',', $row['quantity']);
            // print_r($product);
            // echo $row['product'];
            $dataproduct = [];
            foreach($product as $key => $val) {
                $dataproduct[] = '<span class="badge badge-info">'.$val.'</span></br>';
            }
            $dataprice = [];
            foreach($price as $key => $val) {
                $dataprice[] = '<span class="badge badge-info">'.$config->formatprice($val).'</span></br>';
            }
            $dataquantity = [];
            foreach($quantity as $key => $val) {
                $dataquantity[] = '<span class="badge badge-info">'.$val.'</span></br>';
            }
            
            $type = [ 'nama' => 'ORGANIC' ]; 
            if($row['type'] == 'BD_CP'){
                $type = $config->getData('*', 'corporates', "CorporateUniqueID = '". $row['CustomerID'] ."'");
            }
            if(empty($row['id_florist'])){
                $florist = '<span class="badge badge-secondary">unset</span>';
            }else{
                $data = $config->getData('ID, FloristName', 'florist', "ID = '". $row['id_florist'] ."'");
                $florist = '<a href="javascript:;" onclick="selectFlorist(\''. $row['transactionID'] .'\')"><span class="badge badge-sm badge-success">'. $data['FloristName'] .'</span></a>';
            }
            if(empty($row['id_kurir'])){
                $kurir = '<button class="btn btn-sm btn-primary" onclick="pilihKurir(\''. $row['transactionID'] .'\')" style="font-size: 12px;">select kurir</button>';
            }else{
                $data = $config->getData('id, nama_kurir', 'kurirs', "id = '". $row['id_kurir'] ."'");
                $kurir = '<a href="javascript:;" onclick="pilihKurir(\''. $row['transactionID'] .'\')"><span class="badge badge-sm badge-success">'. $data['nama_kurir'] .'</span></a>';
            }

            if(empty($row['id_kurir'])){
                $kurir = '<button class="btn btn-sm btn-primary" onclick="pilihKurir(\''. $row['transactionID'] .'\')" style="font-size: 12px;">select kurir</button>';
            }else{
                $datakurir = $config->getData('id, nama_kurir', 'kurirs', "id = '". $row['id_kurir'] ."'");
                $kurir = '<a href="javascript:;" onclick="pilihKurir(\''. $row['transactionID'] .'\')"><span class="badge badge-sm badge-success">'.$row['NamaKurir'].'</span></a>';
            }

            $btnchangestatus = '<button class="btn btn-sm btn-primary" onclick="chagestatusordermodal(\''. $row['transactionID'] .'\')" style="font-size: 12px;">'. $arrstatusorder[$row['statusOrder']] .'</button>';
            $grandTotal = '0';
            if(!empty($row['grandTotal'])){
                $grandTotal = $row['grandTotal'];
            }
            $Kirim = Date('d-M-Y', strtotime($row['delivery_date']));
            $createorder = Date('d F Y', strtotime($row['created_date']));
            $statuspaid = $row['statusPaid'] == 1 ? 'PAID' : 'UNPAID';
            
            $delivarydatess = strtotime(Date('Y-m-d', strtotime($row['delivery_date'])));
            $datenow = strtotime($config->getdate('Y-m-d'));
            if($delivarydatess <= $datenow) {
                $color = '';
            } else {
                $color =   '';
            }

            $subdata = array();

            $subdata[]  = '<a href="'.$config->url().'order/?p=detailtrx&trx='. $row['transactionID'] .'" target="_blank">'.$row['transactionID'].'</a>';
            $subdata[]  = $dataproduct;
            $subdata[]  = Date('d-M-Y', strtotime($row['delivery_date']));
            $subdata[]  = $row['kelurahan'];
            $subdata[]  = $btnchangestatus;
            $subdata[]  = $row['notes'] != '' ? $row['notes'] : '<span class="badge badge-sm badge-info">unset</span>';
            $subdata[]  = $statuspaid;
            $subdata[]  = Date('d-M-Y', strtotime($row['created_date']));;
            $subdata[]  = $florist;
            $subdata[]  = $row['NamaKurir'];
            // $subdata[]  = $florist;
            // $subdata[]  = $kurir;
            $subdata[]  = $color;

            array_push($tampung, $subdata);
         }
    }

    $json_data = array(
        'draw'              => intval($request['draw']),
        'recordsTotal'      => intval($totalData),
        'recordsFiltered'   => intval($totalFilter),
        'data'              => $tampung
    );
    echo json_encode($json_data);
}
if($_GET['type'] == 'tableCancelOrder'){
    $request = $_REQUEST;
    $search = $_POST['is_date_search'];

    $daterange ='';
    $corporate ='';
    $admin = '';
    
    if(isset($_POST['date_range'])) {
        $daterange = $_POST['date_range'];
    }

    $databox = '';
    if(isset($_POST['search']['value']) && $_POST['search']['value'] != '') {
        // echo $_POST['search']['value'];
        $databox = '(transaction.transactionID LIKE "%'. $_POST['search']['value'] . '%" OR transaction.CustomerName LIKE "%'. $_POST['search']['value'] . '%" OR users.name LIKE "%'. $_POST['search']['value'] . '%")  OR (transaction_details.product_name LIKE "%'.$_POST['search']['value'].'%") AND ';
    }

    $colom = array(
        0   => 'transaction.transactionID',
        1   => 'transaction_details.product_name',
        2   => 'transaction.delivery_date',
        3   => 'villages.name',
        4   => 'transaction.statusOrder',
        5   => 'transaction.notes',
        6   => 'transaction.delivery_date',
        7   => 'transaction.created_date',
        8   => 'transaction.id_florist',
        9   => 'transaction.id_kurir',
        10   => 'FloristName',
        11   => 'Action',
        12   => 'Color',
    );

    $orderby = 'ORDER BY transaction.delivery_date ASC';
    if(isset($_POST['order'][0]['column'])) {
        $column     = $_POST['order'][0]['column'];
        $typesort   = $_POST['order'][0]['dir'];

        $orderby = 'ORDER BY '.$colom[$column].' '. $typesort;
    }

    $limitstart = $_POST['start'];
    $limitend = $_POST['length'];
    $limit = 'LIMIT '.$limitstart.','.$limitend;

    $Query = '
    SELECT 
    (select GROUP_CONCAT(transaction_details.product_name SEPARATOR ",") from transaction_details where transaction_details.id_trx = transaction.transactionID) as product, 
    (select GROUP_CONCAT(transaction_details.product_price SEPARATOR ",") from transaction_details where transaction_details.id_trx = transaction.transactionID) as price, 
    (select GROUP_CONCAT(transaction_details.product_cost SEPARATOR ",") from transaction_details where transaction_details.id_trx = transaction.transactionID) as cost, 
    (select GROUP_CONCAT(transaction_details.product_qty SEPARATOR ",") from transaction_details where transaction_details.id_trx = transaction.transactionID) as quantity, 
    transaction.*, transaction_details.*, villages.name as kelurahan, users.name as admin, kurirs.nama_kurir as NamaKurir
    FROM transaction 
    LEFT JOIN transaction_details ON transaction_details.id_trx = transaction.transactionID 
    LEFT JOIN villages ON villages.id = transaction.kelurahan_id 
    LEFT JOIN users ON users.id = transaction.created_by 
    LEFT JOIN kurirs ON kurirs.id = transaction.id_kurir
    WHERE ';

    $QueryTotal = '
    SELECT 
    (select GROUP_CONCAT(transaction_details.product_name SEPARATOR ",") from transaction_details where transaction_details.id_trx = transaction.transactionID) as product, 
    (select GROUP_CONCAT(transaction_details.product_price SEPARATOR ",") from transaction_details where transaction_details.id_trx = transaction.transactionID) as price, 
    (select GROUP_CONCAT(transaction_details.product_cost SEPARATOR ",") from transaction_details where transaction_details.id_trx = transaction.transactionID) as cost, 
    (select GROUP_CONCAT(transaction_details.product_qty SEPARATOR ",") from transaction_details where transaction_details.id_trx = transaction.transactionID) as quantity, 
    transaction.*, transaction_details.*, villages.name as kelurahan, users.name as admin, kurirs.nama_kurir as NamaKurir
    FROM transaction 
    LEFT JOIN transaction_details ON transaction_details.id_trx = transaction.transactionID 
    LEFT JOIN villages ON villages.id = transaction.kelurahan_id 
    LEFT JOIN users ON users.id = transaction.created_by 
    LEFT JOIN kurirs ON kurirs.id = transaction.id_kurir
    WHERE ';

    $Query .= $databox;
    $QueryTotal .= $databox;
    if($search == 'yes') {
        $rangeArray = explode("_",$daterange); 

        $startDate = $rangeArray[0];
        $endsDate = $rangeArray[1];

        $daterangequery = "transaction.delivery_date BETWEEN '". $startDate ."' AND '". $endsDate ."'";
        $corporatequery = " AND transaction.CustomerID = '".$corporate."'";
        $adminquery = " AND transaction.created_by = '".$admin."'";

        // $Query .= $daterangequery." AND transaction.statusOrder = 6 ORDER BY transaction.delivery_date DESC";
        $Query .= $daterangequery." AND transaction.statusOrder = 6 GROUP BY transaction.transactionID ". $orderby. ' '. $limit;
        $QueryTotal .= $daterangequery." AND transaction.statusOrder = 6 GROUP BY transaction.transactionID ". $orderby;

        $stmt2 = $config->runQuery($QueryTotal);
        $stmt2->execute();
        $totalData = $stmt2->rowCount();
        $totalFilter = $totalData;
        
        $Data = $config->runQuery($Query);
        $Data->execute();

    } else {
        $Query .=" transaction.statusOrder = '6' GROUP BY transaction.transactionID ". $orderby. ' '. $limit;
        $QueryTotal .=" transaction.statusOrder = '6' GROUP BY transaction.transactionID ". $orderby;
        
        // var_dump($QueryTotal);
        $stmt2 = $config->runQuery($QueryTotal);
        $stmt2->execute();
        $totalData = $stmt2->rowCount();
        $totalFilter = $totalData;

        $Data = $config->runQuery($Query);
        $Data->execute();
    }
    // var_dump($Data);

    $colom = array(
        0   => 'transactionID',
        1   => 'ProductName',
        2   => 'SenderName',
        3   => 'Price',
        4   => 'Quantity',
        5   => 'GrandTotal',
        6   => 'DeliveryDate',
        7   => 'StatusPaid',
        8   => 'CreatedOrder',
        9   => 'CreatedBy',
        10   => 'CreatedBy',
        11   => 'Color',
    );

    $data = [];
    $arrstatusorder = array(
        0 => 'New order',
        1 => 'On Production',
        2 => 'On Delivery',
        3 => 'Success',
        4 => 'Return',
        5 => 'Complain',
        6 => 'Cancel',
        99 => 'not ready'
    );
    $arrstatuspaid = array(
        0 => 'UNPAID',
        1 => 'PAID'
    );
    $arrtime = [
        0 => '9am - 1pm',
        1 => '2pm - 5pm',
        2 => '6pm - 8pm',
        3 => '9pm - 0am',
        4 => '1am - 5am',
        5 => '6am - 8am'
    ];

    $tampung = array();
    // print_r($Data);
    if($totalData > 0 ) {
        while ($row = $Data->fetch(PDO::FETCH_LAZY)){

            $product = explode(',', $row['product']);
            $price = explode(',', $row['price']);
            $quantity = explode(',', $row['quantity']);
            // print_r($product);
            // echo $row['product'];
            $dataproduct = [];
            foreach($product as $key => $val) {
                $dataproduct[] = '<span class="badge badge-info">'.$val.'</span></br>';
            }
            $dataprice = [];
            foreach($price as $key => $val) {
                $dataprice[] = '<span class="badge badge-info">'.$config->formatprice($val).'</span></br>';
            }
            $dataquantity = [];
            foreach($quantity as $key => $val) {
                $dataquantity[] = '<span class="badge badge-info">'.$val.'</span></br>';
            }
            
            $type = [ 'nama' => 'ORGANIC' ]; 
            if($row['type'] == 'BD_CP'){
                $type = $config->getData('*', 'corporates', "CorporateUniqueID = '". $row['CustomerID'] ."'");
            }
            if(empty($row['id_florist'])){
                $florist = '<button class="btn btn-sm btn-primary" onclick="selectFlorist(\''. $row['transactionID'] .'\')" style="font-size: 12px;">select florist</button>';
            }else{
                $data = $config->getData('ID, FloristName', 'florist', "ID = '". $row['id_florist'] ."'");
                $florist = '<a href="javascript:;" onclick="selectFlorist(\''. $row['transactionID'] .'\')"><span class="badge badge-sm badge-success">'. $data['FloristName'] .'</span></a>';
            }
            if(empty($row['id_kurir'])){
                $kurir = '<button class="btn btn-sm btn-primary" onclick="pilihKurir(\''. $row['transactionID'] .'\')" style="font-size: 12px;">select kurir</button>';
            }else{
                $data = $config->getData('id, nama_kurir', 'kurirs', "id = '". $row['id_kurir'] ."'");
                $kurir = '<a href="javascript:;" onclick="pilihKurir(\''. $row['transactionID'] .'\')"><span class="badge badge-sm badge-success">'. $data['nama_kurir'] .'</span></a>';
            }

            if(empty($row['id_kurir'])){
                $kurir = '<button class="btn btn-sm btn-primary" onclick="pilihKurir(\''. $row['transactionID'] .'\')" style="font-size: 12px;">select kurir</button>';
            }else{
                $datakurir = $config->getData('id, nama_kurir', 'kurirs', "id = '". $row['id_kurir'] ."'");
                $kurir = '<a href="javascript:;" onclick="pilihKurir(\''. $row['transactionID'] .'\')"><span class="badge badge-sm badge-success">'.$row['NamaKurir'].'</span></a>';
            }

            $btnchangestatus = '<button class="btn btn-sm btn-primary" onclick="chagestatusordermodal(\''. $row['transactionID'] .'\')" style="font-size: 12px;">'. $arrstatusorder[$row['statusOrder']] .'</button>';
            $grandTotal = '0';
            if(!empty($row['grandTotal'])){
                $grandTotal = $row['grandTotal'];
            }
            $Kirim = Date('d-M-Y', strtotime($row['delivery_date']));
            $createorder = Date('d F Y', strtotime($row['created_date']));
            $statuspaid = $row['statusPaid'] == 1 ? 'PAID' : 'UNPAID';
            
            $delivarydatess = strtotime(Date('Y-m-d', strtotime($row['delivery_date'])));
            $datenow = strtotime($config->getdate('Y-m-d'));
            if($delivarydatess <= $datenow) {
                $color ='';
            } else {
                $color =   '';
            }

            $subdata = array();

            $subdata[]  = '<a href="'.$config->url().'order/?p=detailtrx&trx='. $row['transactionID'] .'" target="_blank">'.$row['transactionID'].'</a>';
            $subdata[]  = $dataproduct;
            $subdata[]  = Date('d-M-Y', strtotime($row['delivery_date']));
            $subdata[]  = $row['kelurahan'];
            $subdata[]  = $arrstatusorder[$row['statusOrder']].' By: '. $row['admin'];
            $subdata[]  = $row['notes'] != '' ? $row['notes'] : '<span class="badge badge-sm badge-info">unset</span>';
            $subdata[]  = $statuspaid;
            $subdata[]  = Date('d-M-Y', strtotime($row['created_date']));;
            $subdata[]  = $florist;
            $subdata[]  = $row['NamaKurir'];
            // $subdata[]  = $florist;
            // $subdata[]  = $kurir;
            $subdata[]  = $color;

            array_push($tampung, $subdata);
         }
    }

    $json_data = array(
        'draw'              => intval($request['draw']),
        'recordsTotal'      => intval($totalData),
        'recordsFiltered'   => intval($totalFilter),
        'data'              => $tampung
    );
    echo json_encode($json_data);
}
if($_GET['type'] == 'generate'){
    $type = $_POST['type'];
    if($type == '1'){
        $field = 'id_trx';
        $table = 'detail_trxs';
        $kode = 'BD_CP';
        $tgl = $config->getDate('Ydmhms');

        $new_code = $kode. $tgl;
    }else{
        $field = 'id_trx';
        $table = 'detail_trxs';
        $kode = 'BD_OG';
        $tgl = $config->getDate('Ydmhms');

        $new_code = $kode. $tgl;
    }
        echo $new_code;
        $logs = $config->saveLogs($new_code, $admin, 'f', 'Generate trx Code');
}

if($_GET['type'] == 'deliveryCharges')
{
    $id = $_POST['id'];

    $stmt = $config->runQuery("SELECT delivery_charges.price, villages.id, villages.name FROM delivery_charges LEFT JOIN villages 
        ON villages.id = delivery_charges.id_kelurahan WHERE villages.id = :id ");
    $stmt->execute(array(':id' => $id));
    header('Content-Type: application/json');
    $data = array();
    while ($row = $stmt->fetch(PDO::FETCH_LAZY)) {
        # code...
        $data['id'] = $row['id'];
        $data['price'] = '('. $config->formatPrice($row['price']) .')';
        $data['kelurahan'] = $row['name'];
        $data['delivery_charges'] = $row['price'];
    }

    $data = json_encode($data);
    echo $data;
}

if($_GET['type'] == 'cardTemplate')
{
    $id = $_POST['id'];

    $stmt = $config->runQuery("SELECT id, level1, level3 FROM card_messages WHERE level2 = :id ");
    $stmt->execute(array(':id' => $id));
    header('Content-Type: application/json');
    echo json_encode($stmt->fetchAll());
}

if($_GET['type'] == 'addProducts')
{
    $id = $_POST['id'];
    $trx = $_POST['trx'];

    //cek di product
    $stmt = $config->runQuery("SELECT * FROM products WHERE product_id = :id ");
    $stmt->execute(array(':id' => $id));

    if($stmt->rowCount() > 0){
        $info = $stmt->fetch(PDO::FETCH_LAZY);

        $cek = $config->runQuery("INSERT INTO transaction_details (id_trx, id_product, product_name, product_price, product_cost, product_qty) VALUES (:a, :b, :c, :d, :e, :f) ");
        $cek->execute(array(
            ':a' => $trx,
            ':b' => $id,
            ':c' => $info['name_product'],
            ':d' => $info['selling_price'],
            ':e' => $info['cost_price'],
            ':f' => '1'
        ));

        $reff = $config->lastInsertId();
        $logs = $config->saveLogs($reff, $admin, 'c', 'add product checkout');
        if($cek){
            //echo $config->actionMsg('c', 'detail_trxs');

            //insert to transaction total 
            $trxd = $config->getData('grandTotal', 'transaction', " transactionID = '". $trx ."'");

            $grandTotal = $trxd['grandTotal'] + $info['selling_price'];

            $transaction = $config->runQuery("UPDATE transaction SET grandTotal = :a WHERE transactionID = :b ");
            $transaction->execute(array(':a' => $grandTotal, ':b' => $trx));
            //

            $prod = $config->ProductsJoin('transaction_details.id, transaction_details.id_product,  transaction_details.product_price, transaction_details.product_cost,  transaction_details.product_qty, transaction_details.florist_remarks, products.product_id, products.name_product,
      products.cost_price, products.selling_price, products.note, products.images, products.permalink',
      'transaction_details', 'LEFT JOIN products ON products.product_id = transaction_details.id_product', "WHERE transaction_details.id_trx = '". $trx ."'");

            $data = ''; $proQty = '';
            $images = ''; $title = ''; $id = ''; $qty = ''; $cost = ''; $selling = ''; $price =''; $remarks='';
            while ($row = $prod->fetch(PDO::FETCH_LAZY)) {
                $images = $row['images'];
                $title = $row['name_product'];
                $id = $row['id'];
                $qty = $row['qty'];
                $cost = $config->formatPrice($row['cost_price']);
                $selling = $config->formatPrice($row['selling_price']);
                $costprice = $row['product_cost'];
                $price = $row['product_price'];
                $remarks = $row['florist_remarks'];

                if($qty >= 1){
                    $proQty = 'disabled';
                }

                //bawa data
                //totalBarang
                $barang =  $config->runQuery("SELECT id FROM transaction_details WHERE id_trx = :trx");
                $barang->execute(array(':trx' => $trx));
                $totalBarang = $barang->rowCount();

                //total transaction
                $transaction = $config->getData('SUM(product_price) as price, SUM(product_qty) as qty', 'transaction_details', " id_trx = '". $trx ."' ");
            
                $total = $config->formatPrice($transaction['price'] * $transaction['qty']);

                 $data = '<li class="list-group-item" id="ListProduct-'. $id .'">
                  <div class="checkout-content">
                     <div class="chekcout-img">
                        <picture>
                         <a href="'. $config->url() .'assets/images/product/'. $images .'" data-toggle="lightbox" data-gallery="example-gallery">
                               <img src="'. $config->url() .'assets/images/product/'. $images .'" class="img-fluid img-thumbnail" width="50%">
                           </a>
                       </picture>
                     </div>
                     <div class="checkout-sometext" style="width: 120%">
                        <div class="title">'. $title .' <div class="pull-right"><button class="btn btn-sm btn-danger deleteListProduct" type="button" data-id="'. $id .'"><span class="fa fa-trash"></span></button></div></div>
                        <div class="count-product">
                           
                           <div class="center">
                              <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                  <button class="btn btn-sm btn-outline-secondary btn-number-count" type="button" data-type="minus" data-field="count-product-number['. $id .']" data-id="selling_price_product['. $id .']" data-trx="'. $trx .'" disabled="disabled"><span class="fa fa-minus"></span></button>
                                </div>
                                <input style="text-align: center;" type="text" value="1" id="count-product-number['. $id .']" name="count-product-number['. $id .']" min="1" max="10" class="input-number form-control form-control-sm" placeholder="" aria-label="" aria-describedby="basic-addon1" data-field="count-product-number['. $id .']" data-qty="'. $qty .'" data-transactionid="'. $trx .'">
                                <div class="input-group-append">
                                  <button class="btn btn-sm btn-outline-secondary btn-number-count" type="button" data-type="plus" data-field="count-product-number['. $id .']" data-id="selling_price_product['. $id .']" data-trx="'. $trx .'"><span class="fa fa-plus"></span></button>
                                </div>
                              </div>
                            
                           </div>
                        </div>
                        <div class="text-info" style="font-size: 13px; font-weight: 600;">Cost_price: '. $cost .'</div>
                        <div class="price" style="width: 50%">
                          
                              <div class="input-group mb-3">
                                 <div class="input-group-prepend">
                                     <span class="input-group-text">Rp.</span>
                                   </div>
                                <input type="text" data-parsley-type="number" class="form-control" name="cost_price_product['. $id .']" id="cost_price_product['. $id .']" value="'.$costprice.'" aria-describedby="basic-addon2">
                                <div class="input-group-append">
                                  <button class="btn btn-outline-info cost_price_btn" type="button" data-id="cost_price_product['. $id .']" data-trx="'. $trx .'">Cost Price</button>
                                </div>
                              </div>

                              <div class="input-group mb-3">
                                 <div class="input-group-prepend">
                                     <span class="input-group-text">Rp.</span>
                                   </div>
                                <input type="text" data-parsley-type="number" class="form-control" name="selling_price_product['. $id .']" id="selling_price_product['. $id .']" value="'.$price.'" aria-describedby="basic-addon2">
                                <div class="input-group-append">
                                  <button class="btn btn-outline-info selling_price_btn" type="button" data-id="selling_price_product['. $id .']" data-trx="'. $trx .'">Selling Price</button>
                                </div>
                              </div>
                           
                        </div>
                        
                        <div class="important-notes">
                           <div class="note">
                              <form id="remarks_florist" data-parsley-validate="" novalidate="">
                                 <div class="form-group">
                                    <textarea class="form-control remarks-florist-tambahan" name="isi_remarks['. $id .']" row="5" required="" placeholder="remarks florist" data-id="'. $id .'"></textarea>
                                 </div>
                              </form>
                           </div>
                        </div>
                     </div>
                  </div>
              </li>';
              $checkoutData = '
              <li class="list-group-item d-flex justify-content-between lh-condensed">
                    <div>
                       <h6 class="my-0">Total Harga Barang</h6>
                    </div>
                    <span class="text-muted" id="subTotal">'. $total .'</span>
                 </li>
                 <li class="list-group-item d-flex justify-content-between lh-condensed">
                    <div>
                       <h6 class="my-0">Biaya Kirim</h6>
                    </div>
                    <span class="text-muted" id="deliveryCharges">00</span>
                 </li>
                 <!-- <li class="list-group-item d-flex justify-content-between bg-light">
                    <div class="text-danger">
                       <h6 class="my-0">Promo code</h6>
                       <small class="badge badge-danger">#BULANBERKAH</small>
                    </div>
                    <span class="text-danger">-Rp. 100.000.00</span>
                 </li> -->
                 <li class="list-group-item d-flex justify-content-between">
                    <strong>Total Belanja</strong>
                    <strong id="totalTransaction">'. $total .'</strong>
                 </li>
              ';

              $content = array(
                'data' => $data,
                'qty' => $totalBarang,
                'checkout' => $checkoutData
              );
                
            
            }
            echo json_encode($content, true);
        }else{
            echo 'Failed!';
        }
    }else{
        echo 'Product Not Found!';
    }
    
}

if($_GET['type'] == 'changePriceProduct'){
    $a = $_POST['id'];
    $b = $_POST['new_price'];

    $a = explode('[', $a);
    $a = explode(']', $a[1]);

    $id = $a[0];
    
    $update = $config->runQuery("UPDATE transaction_details SET product_price ='". $b ."' WHERE id = '". $id ."' ");
    $update->execute();

    if($update)
    {
        $logs = $config->saveLogs($id, $admin, 'u', 'update price checkout!');
        
        $data = array(
            'msg' => $config->actionMsg('u', 'transaction_details'),
            'price' => $b
        );
        $data = json_encode($data, true);
        echo $data;
    }else{
        echo 'Failed!';
    }
}

if($_GET['type'] == 'changeCostPriceProduct'){
    $a = $_POST['id'];
    $b = $_POST['new_price'];

    $a = explode('[', $a);
    $a = explode(']', $a[1]);

    $id = $a[0];
    
    $update = $config->runQuery("UPDATE transaction_details SET product_cost ='". $b ."' WHERE id = '". $id ."' ");
    $update->execute();

    if($update)
    {
        $logs = $config->saveLogs($id, $admin, 'u', 'update cost price checkout!');
        
        $data = array(
            'msg' => $config->actionMsg('u', 'transaction_details'),
            'price' => $b
        );
        $data = json_encode($data, true);
        echo $data;
    }else{
        echo 'Failed!';
    }
}

if($_GET['type'] == 'addRemarksProduct'){
    $a = $_POST['id'];
    $b = $_POST['remarks'];

    $id = $a;
    
    $update = $config->runQuery("UPDATE transaction_details SET florist_remarks ='". $b ."' WHERE id = '". $id ."' ");
    $update->execute();

    if($update)
    {
        $logs = $config->saveLogs($id, $admin, 'u', 'update price checkout!');
        echo $config->actionMsg('u', 'transaction_details');
    }else{
        echo 'Failed!';
    }
}

if($_GET['type'] == 'changeQty'){
    $a = $_POST['id'];
    $b = $_POST['types'];
    $c = $_POST['count'];

    $a = explode('[', $a);
    $a = explode(']', $a[1]);

    $id = $a[0];
    
    $cek = $config->getData('id_product, product_price, product_qty', 'transaction_details', " id = '". $id ."' ");

    $newPrice = $cek['product_price'] * $c;
    if($b == 'minus'){

        //echo 'types: minus, newPrice: '. $newPrice . ' id: '.$id; 
        $update = $config->runQuery("UPDATE transaction_details SET product_qty = '". $c ."' WHERE id = '". $id ."' ");
        $update->execute();

        $logs = $config->saveLogs($id, $admin, 'u', 'Mengurangi Qty checkout product!');
        $data = array(
            'price' => $newPrice,
            'id' => $id
        );
        $data = json_encode($data, true);
        echo $data;

        //echo $config->actionMsg('u', 'transaction_details');
    }else{
        
        //echo 'types: plus, newPrice: '. $newPrice . ' id: '.$id; 
        $update = $config->runQuery("UPDATE transaction_details SET product_qty = '". $c ."' WHERE id = '". $id ."' ");
        $update->execute();

        $logs = $config->saveLogs($id, $admin, 'u', 'Menambah Qty checkout product!');
        $data = array(
            'price' => $newPrice,
            'id' => $id
        );
        $data = json_encode($data, true);
        echo $data;
        //echo $config->actionMsg('u', 'transaction_details');
    }

}

if($_GET['type'] == 'listCheckout'){
    $a = $_POST['transctionID'];

    $product = $config->runQuery("SELECT transaction_details.id FROM transaction_details WHERE transaction_details.id_trx = '". $a ."' ");
    $product->execute();
    
    $totalRow = $product->rowCount();

    $delivery = $config->getData('delivery_charge, delivery_charge_time', '  transaction', " transaction.transactionID = '". $a ."'");
    $deliveryCharge = $delivery['delivery_charge'];
    $timeslot = $delivery['delivery_charge_time'];
    if(empty($delivery['delivery_charge'])) $deliveryCharge = '0';
    if(empty($delivery['delivery_charge_time'])) $timeslot = '0';
    
    $total = $config->getData('SUM(detail.product_qty * detail.product_price) as subtotal', '  transaction_details as detail', " detail.id_trx = '". $a ."'");
    $totalTransaction = $total['subtotal'];

    $grandTotal = $totalTransaction + $deliveryCharge + $timeslot;
        $dataContent = '
    <li class="list-group-item d-flex justify-content-between lh-condensed">
            <div>
               <h6 class="my-0">Total Harga Barang</h6>
            </div>
            <span class="text-muted" id="subTotal">'. $config->formatPrice($totalTransaction) .'</span>
         </li>
         <li class="list-group-item d-flex justify-content-between lh-condensed">
            <div>
               <h6 class="my-0">Biaya Kirim</h6>
            </div>
            <span class="text-muted" id="deliveryCharges">'. $config->formatPrice($deliveryCharge).'</span>
         </li>
         <li class="list-group-item d-flex justify-content-between lh-condensed">
            <div>
               <h6 class="my-0">Biaya Time Slot</h6>
            </div>
            <span class="text-muted" id="deliveryCharges">'. $config->formatPrice($timeslot).'</span>
         </li>
         <li class="list-group-item d-flex justify-content-between">
            <strong>Total Belanja</strong>
            <strong id="totalTransaction">'. $config->formatPrice($grandTotal) .'</strong>
         </li>
    ';
    
    $data = array(
        'totalRow' => $totalRow,
        'product' => $dataContent,
        'subtotal' => $totalTransaction,
        'delivery_charge' => $deliveryCharge,
        'grandtotal' => $grandTotal
    );

    $data = json_encode($data, true);
    echo $data;
}

if($_GET['type'] == 'deleteProduct'){
    $a = $_POST['dataID'];

    $hapus = $config->delRecord('transaction_details', 'id', $a);

    if($hapus)
    {
        echo $config->actionMsg('d', 'transaction_details');
        $logs = $config->saveLogs($a, $admin, 'd', 'hapus list product checkout');
    }else{
        echo 'Failed';
    }
}
if($_GET['type'] == 'step1'){
    $Types = $_POST['Types'];

    if($Types == 'organic') {
        $a = $_POST['TransactionID'];
        $b = $_POST['OrganicFirstName'];
        $c = $_POST['OrganicLastName'];
        $d = $_POST['OrganicEmail'];
        $e = $_POST['OrganicMobileNumber'];

        $cekemail = $config->getData('Email', 'customer',"Email LIKE '% ". $d ." %'");
        if($cekemail['Email']) {
            die(json_encode(['response' => 'ERROR', 'msg' => 'Duplicated Email']));
        } else {
            $kode = 'BDO';
            $tgl = $config->getDate('Ydmhms');
            $new_code = $kode. $tgl;
            $email = explode('@', $d);
            $password = $config->newPassword($email[0]);
            //create customer
            $insert = "INSERT INTO customer (CustomerUniqueID, FirstName, LastName, FullName, Email, Mobile, Username, Password, IsActive, CreatedDate, CreatedBy, permalink) VALUES (:CustomerUniqueID, :FirstName, :LastName, :FullName, :Email, :Mobile, :Username, :Password, :IsActive, :CreatedDate, :CreatedBy, :permalink) ";
            $stmt = $config->runQuery($insert);
            $stmt->execute(array(
                ':CustomerUniqueID' => $new_code,
                ':FirstName' => $b,
                ':LastName' => $c,
                ':FullName' => $b.' '.$c,
                ':Email' => $d,
                ':Mobile' => $e,
                ':Username' => $d,
                ':Password' => $password,
                ':IsActive' => 1,
                ':CreatedDate' => $config->getDate("Y-m-d H:m:s"),
                ':CreatedBy' => $admin,
                ':permalink' => strtolower($b.'-'.$email[0])
            ));

            if($stmt) {
                $logs = $config->saveLogs($new_code, $admin, 'c', 'New Customer');

                $data = $config->getDataTable('transactionID', 'transaction', " transactionID = '". $a ."' ");
                if($data->rowCount() > 0 ){
                    //edit
                    $namecustomer = $b. ' ' . $c;
                    $update = $config->runQuery("UPDATE transaction SET CustomerID = '". $new_code ."', CustomerName = '". $namecustomer ."' WHERE transactionID = '". $a ."' ");
                    $update->execute();

                    if($update) {
                        $logs = $config->saveLogs($a, $admin, 'u', 'Customer');
                        die(json_encode(['response' => 'OK', 'msg' => $config->actionMsg('u', 'transaction')]));
                    } else {
                        die(json_encode(['response' => 'ERROR', 'msg' => 'Failed!']));
                    }
                }else{
                    //new
                    $namecustomer = $b. ' ' . $c;

                    $input = $config->runQuery("INSERT INTO transaction (transactionID, type, CustomerID, CustomerName, statusOrder) VALUES (:a, :b, :c, :d, :e)");
                    $input->execute(array(
                        ':a'    => $a,
                        ':b'    => 'BD_OG',
                        ':c'    => $new_code,
                        ':d'    => $namecustomer,
                        ':e'    => 99
                    ));
                    $reff = $config->lastInsertId();
                    $logs = $config->saveLogs($reff, $admin, 'c', 'add transactionID');
                    if($input)
                    {
                        die(json_encode(['response' => 'OK', 'msg' => $config->actionMsg('c', 'transaction')]));
                    }else{
                        die(json_encode(['response' => 'ERROR', 'msg' => 'Failed!']));
                    }
                }
            } else {
                die(json_encode(['response' => 'ERROR', 'msg' => 'Failed!']));
            }
        }
    } else {
        $a = $_POST['TransactionID'];
        $b = $_POST['CustomerID'];
        $c = $_POST['picID'];
        $d = $_POST['namePic'];

        $type = substr($a, 0, 5);

        $data = $config->getDataTable('transactionID', 'transaction', " transactionID = '". $a ."' ");
        if($data->rowCount() > 0 ){
            //edit
            $update = $config->runQuery("UPDATE transaction SET PIC = '". $c ."', CustomerID = '". $b ."', CustomerName = '". $d ."' WHERE transactionID = '". $a ."' ");
            $update->execute();

            if($update) {
                
                $logs = $config->saveLogs($a, $admin, 'u', 'Customer');
                die(json_encode(['response' => 'OK', 'msg' => $config->actionMsg('u', 'transaction')]));
            } else {
                die(json_encode(['response' => 'ERROR', 'msg' => 'Failed!']));
            }
        }else{
            //new
            $input = $config->runQuery("INSERT INTO transaction (PIC, transactionID, type, CustomerID, CustomerName, statusOrder) VALUES (:pic, :a, :b, :c, :d, :e)");
            $input->execute(array(
                ':pic'    => $c,
                ':a'    => $a,
                ':b'    => $type,
                ':c'    => $b,
                ':d'    => $d,
                ':e'    => 99
            ));
            $reff = $config->lastInsertId();
            $logs = $config->saveLogs($reff, $admin, 'c', 'add transactionID');
            if($input)
            {
                die(json_encode(['response' => 'OK', 'msg' => $config->actionMsg('c', 'transaction')]));
            }else{
                die(json_encode(['response' => 'ERROR', 'msg' => 'Failed!']));
            }
        }
    }
}
if($_GET['type'] == 'step2'){
    $a = $_POST['Name'];
    $b = $_POST['Email'];
    $c = $_POST['Provinsi'];
    $d = $_POST['Kota'];
    $e = $_POST['Kec'];
    $f = $_POST['Kel'];
    $g = $_POST['Alamat'];
    $hh = $_POST['hp_penerima'];
    $trx = $_POST['TransactionID'];

   
    $data = $config->getDataTable('transactionID', 'transaction', " transactionID = '". $trx ."' ");
    if($data->rowCount() > 0 ){
        //edit
        $update = $config->runQuery("UPDATE transaction SET nama_penerima = :a, hp_penerima = :hh, email = :b, provinsi_id = :c, kota_id = :d, kecamata_id = :e, kelurahan_id = :f, alamat_penerima = :g WHERE transactionID = :trx");
        $update->execute(array(
            ':a'    => $a,
            ':hh'    => $hh,
            ':b'    => $b,
            ':c'    => $c,
            ':d'    => $d,
            ':e'    => $e,
            ':f'    => $f,
            ':g'    => $g,
            ':trx'  => $trx
        ));
        $logs = $config->saveLogs($trx, $admin, 'u', 'update detail transaction');
        if($update)
        {
            $charge = $config->getData('price', '  delivery_charges', " delivery_charges.id_kelurahan = '". $f ."'");

            if($charge) {
                die(json_encode(['response' => 'OK', 'msg' => $charge['price']], JSON_FORCE_OBJECT));
            } else {
                die(json_encode(['response' => 'ERROR', 'msg' => $config->actionMsg('u', 'transaction')], JSON_FORCE_OBJECT));
            }
        }else{
            echo 'Failed!';
        }
    }else{
        //new
        echo 'NEwQ';
    }
}
if($_GET['type'] == 'step3'){
    $a = $_POST['TransactionID'];
    $b = $_POST['deliverCharge'];
    $c = $_POST['deliveryDate'];
    $d = $_POST['deliveryTimes'];
    $e = $_POST['deliveryRemarks'];

   
    $data = $config->getDataTable('TransactionID', 'transaction', " TransactionID = '". $a ."' ");
    if($data->rowCount() > 0 ){
        //edit
        $update = $config->runQuery("UPDATE transaction SET delivery_date = :c, delivery_time = :d, delivery_marks = :e WHERE TransactionID = :trx");
        $update->execute(array(
            ':c'    => $c,
            ':d'    => $d,
            ':e'    => $e,
            ':trx'  => $a
        ));
        $logs = $config->saveLogs($a, $admin, 'u', 'update detail transaction');
        if($update)
        {
            echo $config->actionMsg('u', 'transaction');
        }else{
            echo 'Failed!';
        }
    }else{
        //new
        echo 'NEwQ';
    }
}
if($_GET['type'] == 'step4'){
    $a = $_POST['TransactionID'];
    $b = $_POST['from'];
    $c = $_POST['to'];
    $d = $_POST['msg'];
    $e = $_POST['level1'];
    $f = $_POST['level2'];

   
    $data = $config->getDataTable('transactionID', 'transaction', " transactionID = '". $a ."' ");
    if($data->rowCount() > 0 ){
        //edit
        $update = $config->runQuery("UPDATE transaction SET card_from = :a, card_to = :b, card_template1 = :c, card_template2 = :d, card_isi = :e WHERE transactionID = :trx");
        $update->execute(array(
            ':a'    => $b,
            ':b'    => $c,
            ':c'    => $e,
            ':d'    => $f,
            ':e'    => $d,
            ':trx'  => $a
        ));
        $logs = $config->saveLogs($a, $admin, 'u', 'update messages transaction');
        if($update)
        {
            echo $config->actionMsg('u', 'transaction');
        }else{
            echo 'Failed!';
        }
    }else{
        //new
        echo 'NEwQ';
    }
}
if($_GET['type'] == 'PaymentSelected'){
    $a = $_POST['transctionID'];
    $b = $_POST['paymentID'];


    $stmt = "UPDATE transaction SET paymentID = :pay WHERE transactionID = :trx";
    $stmt = $config->runQuery($stmt);
    $stmt->execute(array(':pay' => $b, ':trx' => $a));

    if($stmt){
        echo $config->actionMsg('u', 'transaction');
        $logs = $config->saveLogs($a, $admin, 'u', 'update payment order');
    }else{
        echo 'Failed!';
    }
}
if($_GET['type'] == 'proccessOrder'){
    $a = $_POST['transactionID'];
    $b = $_POST['InvoiceName'];

    $type = substr($b, 0, 4);

    $delivery = $config->getData('delivery_charge, delivery_charge_time, CustomerID, PIC', '  transaction', " transaction.transactionID = '". $a ."'");
    $price = $config->getData('SUM(transaction_details.product_cost * transaction_details.product_qty) as costprice, SUM(transaction_details.product_price * transaction_details.product_qty) as sellingprice', 'transaction_details', " transaction_details.id_trx = '". $a ."'");

    $deliveryCharge = 0;
    if($delivery['delivery_charge'] > 0) { $deliveryCharge = $delivery['delivery_charge']; }

    $timeslotcharges = 0;
    if($delivery['delivery_charge_time'] > 0) { $timeslotcharges = $delivery['delivery_charge_time']; }

    $total = $config->getData('SUM(detail.product_qty * detail.product_price) as subtotal', '  transaction_details as detail', " detail.id_trx = '". $a ."'");
    $totalTransaction = $total['subtotal'];

    $grandTotal = $totalTransaction + $deliveryCharge + $timeslotcharges;

    $stmt = "UPDATE transaction SET statusOrder = '0', invoice_name = '". $b ."', statusOrder = '0', TotalCostPrice = '". $price['costprice'] ."', TotalSellingPrice = '". $price['sellingprice'] ."', grandTotal = '". $grandTotal ."', created_by = '". $admin ."' WHERE transactionID = :trx";
    $stmt = $config->runQuery($stmt);
    $stmt->execute(array(
        ':trx' => $a
    ));

    if($stmt){
        echo $config->actionMsg('u', 'transaction'); 
        $logs = $config->saveLogs($a, $admin, 'u', 'proccess order');
    }else{
        echo 'Failed!';
    }

    $Point = 0;
    if($grandTotal > 0 && $grandTotal <= 500000){ $Point = 2; }  
    if($grandTotal > 500001 && $grandTotal <= 750000) { $Point = 3; }
    if($grandTotal > 750001 && $grandTotal <= 1000000) { $Point = 4; }
    if($grandTotal > 1000001 && $grandTotal <= 1250000) { $Point = 5; }
    if($grandTotal > 1250001 && $grandTotal <= 1500000) { $Point = 6; }
    if($grandTotal > 1500001 && $grandTotal <= 1750000) { $Point = 7; }
    if($grandTotal > 1750001 && $grandTotal <= 2000000) { $Point = 10;} 
    if($grandTotal > 2000001 && $grandTotal <= 2250000) { $Point = 11;} 
    if($grandTotal > 2250001 && $grandTotal <= 2500000) { $Point = 12;} 
    if($grandTotal > 2500001 && $grandTotal <= 2750000) { $Point = 13;} 
    if($grandTotal > 2750001 && $grandTotal <= 3000000) { $Point = 20;} 
    if($grandTotal > 3000001 && $grandTotal <= 3250000) { $Point = 21;} 
    if($grandTotal > 3250001 && $grandTotal <= 3500000) { $Point = 22;} 
    if($grandTotal > 3500001 && $grandTotal <= 3750000) { $Point = 23;} 
    if($grandTotal > 3750001 && $grandTotal <= 4000000) { $Point = 26;} 
    if($grandTotal > 4000001 && $grandTotal <= 4250000) { $Point = 27;} 
    if($grandTotal > 4250001 && $grandTotal <= 4500000) { $Point = 28;} 
    if($grandTotal > 4500001 && $grandTotal <= 4750000) { $Point = 29;} 
    if($grandTotal > 4750001 && $grandTotal <= 5000000) { $Point = 30;} 
    $customerID = $delivery['CustomerID'];
    if($type == 'BD_CP') {
        $oldpoint = $config->getData('Point', 'corporate_pics', "id = '".$delivery['PIC']."' AND corporate_id = '". $customerID ."' ");

        $newPoint = $oldpoint + $Point;
        $update = $config->runQuery("UPDATE corporate_pics SET corporate_pics.Point = :a WHERE id = :id and corporate_id = :cp");
        $update->execute(array(
            ':a'    => $newPoint,
            ':id'    => $delivery['PIC'],
            ':cp'  => $customerID
        ));
    } else {
        $oldpoint = $config->getData('Point', 'customer', " CustomerUniqueID = '". $customerID ."' ");

        $newPoint = $oldpoint + $Point;
        $update = $config->runQuery("UPDATE customer SET customer.Point = :a WHERE CustomerUniqueID = :id ");
        $update->execute(array(
            ':a'    => $newPoint,
            ':id'    => $customerID
        ));
    }
}

if($_GET['type'] == 'changeOrderStatus'){
    $a = $_POST['status'];
    $b = $_POST['transctionID'];
	$c = $_POST['types'];
	
	if($c == 'florist'){
		$cek = $config->getData('id_florist, id_kurir', 'transaction', "transactionID ='". $b ."' ");
		
		if(empty($cek['id_florist']) && $c == 'florist')
		{
			echo 'Pilih Florist Terlebih dahulu!';
        }
        else { 
            $stmt = "UPDATE transaction SET statusOrder = '". $a ."', updated_by = '".$admin."' WHERE transactionID = '". $b ."'";
			$stmt = $config->runQuery($stmt);
			$stmt->execute();

			if($stmt) {
				echo $config->actionMsg('u', 'transaction');
				$logs = $config->saveLogs($a, $admin, 'u', 'update statusOrder');
			} else {
				echo 'Failed!';
			}
		}
	} else {

        $cek = $config->runQuery("select transaction.id_kurir, transaction.transactionID, delivery_charges.id, delivery_charges.price from transaction
        left join delivery_charges on delivery_charges.id_kelurahan = transaction.kelurahan_id WHERE transaction.transactionID ='". $b ."' ");
        $cek->execute();
        $data = $cek->fetch(PDO::FETCH_LAZY);
		
		if(empty($data['id_kurir']) && $c == 'kurir')
		{
			echo 'Pilih Kurir Terlebih dahulu!';
        }
        else { 
            $paykurir = $config->runQuery("INSERT INTO pay_kurirs (no_trx, kurir_id, charge_id, created_at, admin_id) VALUES (:a, :b, :c, :d, :e)");
            $paykurir->execute(array(
                ':a' => $b,
                ':b' => $data['id_kurir'],
                ':c' => $data['id'],
                ':d' => $config->getDate('Y-m-d H:m:s'),
                ':e' => $admin
            ));
            $reff = $config->lastInsertId();
            if($paykurir) {
                echo $config->actionMsg('c', 'payment kurir');
                $logs = $config->saveLogs($reff, $admin, 'c', 'add payment kurir');

                $stmt = "UPDATE transaction SET statusOrder = '". $a ."' WHERE transactionID = '". $b ."'";
                $stmt = $config->runQuery($stmt);
                $stmt->execute();

                if($stmt) {
                    echo $config->actionMsg('u', 'transaction');
                    $logs = $config->saveLogs($a, $admin, 'u', 'update statusOrder');
                } else {
                    echo 'Failed!';
                }
                
            } else {
                echo 'Failed !';
            }
        }
        
    }
}

if($_GET['type'] == 'addDeliveryCharges'){
    $a = $_POST['transctionID'];
    $b = $_POST['transctionPrice'];


    $stmt = "UPDATE transaction SET delivery_charge = '". $b ."' WHERE transactionID = '". $a ."'";
    $stmt = $config->runQuery($stmt);
    $stmt->execute();

    if($stmt){
        echo $config->actionMsg('u', 'transaction');
        $logs = $config->saveLogs($a, $admin, 'u', 'update delivery_charge');
    }else{
        echo 'Failed!';
    }
}

if($_GET['type'] == 'selectFlorist'){
    $a = $_POST['transctionID'];
    $b = $_POST['floristID'];

    $stmt = "UPDATE transaction SET id_florist = '". $b ."', statusOrder = '1', updated_date = '". $config->getDate('Y-m-d H:m:s') ."', updated_by = '". $admin."' WHERE transactionID = '". $a ."'";
    $stmt = $config->runQuery($stmt);
    $stmt->execute();

    if($stmt){
        echo $config->actionMsg('u', 'transaction_details');
        $logs = $config->saveLogs($a, $admin, 'u', 'update florist!');

        $transaction = "UPDATE transaction SET statusOrder = '1' WHERE transactionID = '". $b ."'";
        $transaction = $config->runQuery($transaction);
        $transaction->execute();

        if($transaction) {
            echo $config->actionMsg('u', 'transaction');
            $logs = $config->saveLogs($a, $admin, 'u', 'update statusOrder');
        } else {
            echo 'Failed!';
        }
    }else{
        echo 'Failed!';
    }
}
if($_GET['type'] == 'selectKurir'){
    $a = $_POST['transctionID'];
    $b = $_POST['KurirID'];

    $stmt = "UPDATE transaction SET id_kurir = '". $b ."', statusOrder = '2' WHERE transactionID = '". $a ."'";
    $stmt = $config->runQuery($stmt);
    $stmt->execute();
    $tanggall = $config->getDate("Y-m-d H:m:s");
    
    $cekdata = $config->getData('COUNT(*) as data', 'kurir_jobs', "TransactionNumber = '". $a ."'");
    if($cekdata['data'] > 0) {
        $updatejobs = "UPDATE kurir_jobs SET Status = 1 WHERE TransactionNumber = :a";
        $updatejobs = $config->runQuery($updatejobs);
        $updatejobs->execute(array(':a' => $a));

        if($updatejobs) {
            $insert = "INSERT INTO kurir_jobs (TransactionNumber, KurirID, Created_date, Created_by) VALUES ('". $a ."', '". $b ."', '".$tanggall ."', '". $admin ."')";
            $insert = $config->runQuery($insert);
            $insert->execute();
            echo $config->actionMsg('u', 'transaction_details');
            $logs = $config->saveLogs($a, $admin, 'u', 'update kurir!');
        }
    } else {
        if($stmt){
            $insert = "INSERT INTO kurir_jobs (TransactionNumber, KurirID, Created_date, Created_by) VALUES ('". $a ."', '". $b ."', '".$tanggall ."', '". $admin ."')";
            $insert = $config->runQuery($insert);
            $insert->execute();
            echo $config->actionMsg('u', 'transaction_details');
            $logs = $config->saveLogs($a, $admin, 'u', 'update kurir!');
        }else{
            echo 'Failed!';
        }
    }
}
if($_GET['type'] == 'removecharges'){
    $a = $_POST['transctionID'];

    $stmt = "UPDATE transaction SET delivery_charge = '', updated_date = '". $config->getDate('Y-m-d H:m:s') ."', updated_by = '". $admin." ' WHERE transactionID = '". $a ."'";
    $stmt = $config->runQuery($stmt);
    $stmt->execute();

    if($stmt){
        echo $config->actionMsg('u', 'transaction');
        $logs = $config->saveLogs($a, $admin, 'u', 'hapus delivery charge!');
    }else{
        echo 'Failed!';
    }
}
if($_GET['type'] == 'getTime'){
    $a = $_POST['Tanggal'];
    $arrtime = [
		0 => '9am - 1pm',
		1 => '2pm - 5pm',
		2 => '6pm - 8pm',
		3 => '9pm - 0am',
		4 => '1am - 5am',
		5 => '6am - 8am'
	];

	$arrcharge = [
		0 => $config->formatPrice('0'),
		1 => $config->formatPrice('0'),
		2 => $config->formatPrice('0'),
		3 => $config->formatPrice('100000'),
		4 => $config->formatPrice('200000'),
		5 => $config->formatPrice('50000')
	];

	$arrdescription = [
		0 => '-',
		1 => '-',
		2 => '-',
		3 => 'JABODETABEK',
		4 => 'JABODETABEK',
		5 => 'JABODETABEK'
    ];
    
    $data = $config->getData('*', 'time_slots', "DateSlots = '".$a."'");
    if($data['ID'] != '') {
        $time = [];
        
        foreach(json_decode($data['TimeSlots'], true) as $key => $value) {
            # code...
            $time[] = $arrtime[$key].' '.$arrcharge[$key].' '.$arrdescription[$key];
        }
        die(json_encode(['response' => 'OK', 'msg' => $time]));
    } else {
        die(json_encode(['response' => 'ERROR', 'msg' => 'Time Slot Not Available !']));
    }
}
if($_GET['type'] == 'timeslotcharge'){
    $a = $_POST['transctionID'];
    $b = $_POST['ID'];
    $arrcharge = [
		0 => 0,
		1 => 0,
		2 => 0,
		3 => 100000,
		4 => 200000,
		5 => 50000
    ];
    
    $newcharge = $arrcharge[$b];

    $stmt = "UPDATE transaction SET delivery_charge_time = :a, updated_date = :b, updated_by = :c WHERE transactionID = :d";
    $stmt = $config->runQuery($stmt);
    $stmt->execute(array(
        ':a' => $newcharge,
        ':b' => $config->getDate('Y-m-d H:m:s'),
        ':c' => $admin,
        ':d' => $a
    ));

    if($stmt){
        die(json_encode(['response' => 'OK', 'msg' => $config->actionMsg('u', 'transaction')]));
        $logs = $config->saveLogs($a, $admin, 'u', 'delivery time charge!');
    }else{
        die(json_encode(['response' => 'ERROR', 'msg' => $stmt]));
    }
}
if($_GET['type'] == 'sendInvoiceEmail' || $_GET['type'] == 'proccessOrder'){
    
    $arrtime = [
        0 => '9am - 1pm',
        1 => '2pm - 5pm',
        2 => '6pm - 8pm',
        3 => '9pm - 0am',
        4 => '1am - 5am',
        5 => '6am - 8am'
    ];
    
    $arrpaid = [
        0 => 'UNPAID',
        1 => 'PAID'
    ];
    
    $transactionID = $_POST['transactionID'];
    
    $data = $config->getData('transaction.*, customer.FullName as OrganicName, customer.Email as OrganicEmail, customer.Mobile as OrganicPhone, provinces.name as ProvinsiName, regencies.name as KotaName, districts.name as Kecamatan, villages.name as Kelurahan, corporate_pics.name as CorporateName, corporate_pics.nomor as CorporatePhone, corporate_pics.email as CorporateEmail', 
    'transaction 
    LEFT JOIN customer on customer.CustomerUniqueID = transaction.CustomerID LEFT JOIN provinces ON provinces.id = transaction.provinsi_id LEFT JOIN regencies on regencies.id = transaction.kota_id LEFT JOIN districts ON districts.id = transaction.kecamata_id 
    LEFT JOIN villages on villages.id = transaction.kelurahan_id
    LEFT JOIN corporate_pics on corporate_pics.id = transaction.PIC', 
    "transactionID = '". $transactionID ."' ");
    // $config->_debugvar($data);
    $subtotal = $config->getData('SUM(product_price * product_qty) as Subtotal', 'transaction_details', "id_trx = '". $transactionID ."'");
    
    $product = $config->runQuery("SELECT * FROM transaction_details WHERE id_trx = '". $transactionID ."'");
    $product->execute();
    $subtotal = $config->getData('SUM(product_price * product_qty) as Subtotal', 'transaction_details', "id_trx = '". $transactionID ."'");
    $color = 'yellow';
    if($data['statusPaid'] == 1) {
        $color = 'green';
    }
    // die($arrpaid[$data['statusPaid']]);
    $dataproduct = [];
        while($row = $product->fetch(PDO::FETCH_LAZY)) {
        $dataproduct[] = '
        <tr style="background-color: #ffffff;">
            <td style="padding: 5px; border-bottom: 0.5px solid;">
            <img style="border:1px solid #FFFFFF; padding:1px; " src="'.URL.'assets/images/product/'. str_replace(' ', '_', strtolower($row['product_name'])) .'.jpg" width="100" height="95" align=center>
            </td>
            <td style="padding: 3px;font-size: 14px;font-weight: 600; border-bottom: 0.5px solid; text-transform: capitalize;">'. strtoupper($row['id_product']) .' '. $row['product_name'] .'</td>
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
    
    $arraypaid = 'UNPAID';
    if($data['statusPaid'] == 1) $arraypaid = 'PAID';
    $receivedEmail = $CustomerEmail;
    $receivedName = $CustomerName;
    $receivedPhone = $CustomerPhone;
    $subject = 'Order Confirmation Bunga Davi-'.$data['transactionID'];
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
             body{font-family: Arial; font-size:12px}
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
                               <td width="640" height="20" class="w640"></td>
                            </tr>
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
                                  <br /><br />
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
                                                       <td align="center" width="200" class="w325"><span align="center" class="content-body">'. $receivedName.'</span></td>
                                                       <td align="center" width="200" class="w325"><span align="center" class="content-body">'. $receivedEmail.'</span></td>
                                                       <td align="center" width="200" class="w325"><span align="center" class="content-body">'.$receivedPhone.'</span></td>
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
                                  <table width="640" cellspacing="0" cellpadding="0" border="0" class="w640" bgcolor="#444444">
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
                                            <div style="background-color: '.$color.'; width: 120px;padding: 8px;border: 1px solid '.$color.';border-radius: 5px; margin-left: 40%;">
                                                <span>'.$arraypaid.'</span>
                                            </div>
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
                                                                   <td width="170" class="w170" style="vertical-align: top;"><span class="content-body" style="font-family:Arial;">'. $data['email'].'</span></td>
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
                                                       <td width="280" height="15" class="w160"></td>
                                                    </tr>
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
                                                                   <td width="170" class="w170" style="vertical-align: top;"><span class="content-body" style="font-family:Arial;">'. $config->_formatdate($data['delivery_date']). '</span> <span style="color: red; font-size: 12px; font-weight: 600;">'.$arraypaid.'</span></td>
                                                                </tr>
                                                                <tr>
                                                                   <td width="110" class="w170" style="vertical-align: top;"><span class="content-body1" style="font-family:Arial;">Delivery Note :</span></td>
                                                                   <td width="170" class="w170" style="vertical-align: top;"><span class="content-body" style="font-family:Arial;">'.$data['delivery_marks'].'</span></td>
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
                                              '.$data['card_to'].' <br>
                                              " '.$data['card_isi'].' " <br>
                                              '.$data['card_from'].'
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
                                              <p align="center" class="footer-content-left"><a style="font-family:Arial;" href="">About Us</a> |
                                                 <a href="" style="font-family:Arial;">Testimonial</a> |
                                                 <a style="font-family:Arial;" href="">Policy</a> |
                                                 <a style="font-family:Arial;" href="">Contact Us</a> |
                                                 <a style="font-family:Arial;" href="">Corporate Sign Up</a> |
                                                 <a style="font-family:Arial;" href="">T&C</a>
                                              <p align="center" class="footer-content-left" style="font-family:Arial;">Call us : <br /> Cilegon: +62818433612  || Jakarta: +62811133364 || Serang: +62816884292 <br /> Tangerang: +62811133364 || Area Lain +62811133365  <br /> (24 Hours Hotline) <br /><br /></p>
                                              <p align="center" class="footer-content-left" style="font-family:Arial;">Copyright &copy; 2007 - 2017 Bunga Davi</p>
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
   
    $cc = 'fiki@bungadavi.co.id';
    $config = new Mail();
    $email = $config->Mailler($receivedEmail, $receivedName, $cc, $subject, $content);

        die(json_encode(['response' => $email['response'], 'msg' => $email['msg']]));
        $logs = $config->saveLogs($a, $admin, 'f', 'send email!');
}
if($_GET['type'] == 'cancelOrder'){
    $a = $_POST['transactionID'];

    $update = $config->runQuery("UPDATE transaction SET statusOrder = 6 WHERE transactionID = '".$a."' ");
    $update->execute();

    if($update)
    {
        echo $config->actionMsg('u', 'transaction');
        $logs = $config->saveLogs($a, $admin, 'u', 'cancel order');
    }else{
        echo 'Failed';
    }
}