<?php
$listAdmin = $config->Products('id, name', 'users');
$kurir     = $config->Products('id, nama_kurir', 'kurirs');

$formR = '';
if(isset($_GET['type'])) $formR = 'hidden';

?>
<style type="text/css">
    .dataTables_wrapper{
        padding-left: unset !important;
        padding-right:  unset !important;
    }
    .infoReport{
        font-weight: 600;
        padding: 1%;
    }
</style>
<div id="formReport" class="<?=$formR?>">
    <div class="col-12 col-sm-12 col-lg-12">
        <div class="card">
            <div class="card-header">
                form report
            </div>
            <div class="card-body d-flex justify-content-center" >
                <form class="form-inline" id="form-report" method="post" action="" data-parsley-validate="" autocomplete="off">
                    <div class="form-group mb-2">
                        <select name="typeReport" id="typeReport" class="form-control" required>
                            <option value="">apa yang mau direport...</option>
                            <option value="1">report kas besar</option>
                            <option value="2">report kas masuk</option>
                            <option value="3">report kas keluar (belanja)</option>
                            <option value="4">payment-kurir</option>
                        </select>
                    </div>

                    <div class="form-group mx-sm-3 mb-2">
                        <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                            <i class="fa fa-calendar"></i>&nbsp;
                            <span></span> <i class="fa fa-caret-down"></i>
                        </div>
                        <input type="hidden" id='hidde_date_field'>
                    </div>
                    <div class="col-auto my-1">
                        <div class="form-check">
                            <input class="form-check-input position-static" type="checkbox" id="selectAdminR" value="option1" aria-label="..."> admin/kurir
                        </div>
                    </div>
                    <div class="hidden" id="pilihAdminReport">
                        <div class=" form-group mx-sm-3 mb-2" >
                            <select name="adminReport" id="adminReport" class="form-control">
                                <option value="">:: admin-list ::</option>
                                <?php
                                while($row = $listAdmin->fetch(PDO::FETCH_LAZY)){
                                    ?>
                                    <option value="<?=$row['id']?>"><?=$row['name']?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="hidden" id="pilihKurirReport">
                        <div class=" form-group mx-sm-3 mb-2" >
                            <select name="adminReport" id="kurirReport" class="form-control">
                                <option value="">:: kurir-list ::</option>
                                <?php
                                while($row = $kurir->fetch(PDO::FETCH_LAZY)){
                                    ?>
                                    <option value="<?=$row['id']?>"><?=$row['nama_kurir']?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary mb-2">confirm report</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php

if(isset($_GET['type']) || isset($_GET['tgl']) || isset($_GET['admin'])){
    $type = $_GET['type'];
    $tgl = $_GET['range'];
    $admin = $_GET['admin'];
    $tgl = explode('_', $tgl);
    $dateFrom = $tgl[0];
    $dateTo = $tgl[1];

    $adminType = 'kas_besar';
    if($type == 'kasIn'){
        $adminType = 'kas_ins';
    }elseif($type == 'kasOut'){
        $adminType = 'kas';
    }elseif($type == 'kurir'){
        $adminType = 'payment_kurir';
    }
    $adminID = " AND ".$adminType.".admin_id = '". $admin. "' ";
    if(empty($admin)){
        $adminID = '';
    }

    $reportH = 'display: block;';

    if($type == 'kasBesar'){
        $sql = "SELECT kas_besar.id, kas_besar.type, kas_besar.total, kas_besar.title, kas_besar.ket, kas_besar.status, 
users.name, kas_besar.created_at FROM kas_besar 
INNER JOIN users ON users.id = kas_besar.admin_id
WHERE kas_besar.created_at BETWEEN :dateFrom AND :dateTo ". $adminID ."
ORDER BY kas_besar.type";

        $stmt = $config->runQuery($sql);
        $stmt->execute(array(
            ':dateFrom' => $dateFrom,
            ':dateTo'   => $dateTo
        ));
        $stmtDebit = $config->runQuery("SELECT SUM(total) as total FROM kas_besar WHERE type = 'debit' AND created_at BETWEEN '". $dateFrom ."' AND '". $dateTo ."' ". $adminID);
        $stmtDebit->execute(); $stmtDebit = $stmtDebit->fetch(PDO::FETCH_LAZY);
        $totalDebit = $stmtDebit['total'];

        $stmtKredit = $config->runQuery("SELECT SUM(total) as total FROM kas_besar WHERE type = 'kredit' AND created_at BETWEEN '". $dateFrom ."' AND '". $dateTo ."' ". $adminID);
        $stmtKredit->execute(); $stmtKredit = $stmtKredit->fetch(PDO::FETCH_LAZY);
        $totalKredit = $stmtKredit['total'];

        ?>
<div id="hasilReport" style = "margin-top:2%; <?=$reportH?>">
    <div class="card">
        <div class="card-header">
            <h5>Hasil Report Kas Besar
                <div class="float-right">
                    <a href="?p=reportPayment">
                        <button class="btn btn-sm btn-primary">report again..</button>
                    </a>
                </div>
            </h5>

        </div>
        <div class="card-body">
            <div class="row infoReport justify-content-center">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="firstName">Total Debit</label>
                            <input type="text" class="form-control" value="<?=$config->formatPrice($totalDebit)?>" id="firstName" placeholder="" readonly="readonly">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="lastName">Total Kredit</label>
                            <input type="text" class="form-control" value="<?=$config->formatPrice($totalKredit)?>" id="lastName" placeholder="" readonly="readonly">
                        </div>

                    </div>
                    <div class="row" style="<?=$device['device'] == 'MOBILE' ? ''  : 'padding-left : 2.555555%;'?>">
                        <div class="col-md-6 mb-3">
                            <label for="firstName">Selisih Total</label>
                            <input type="text" class="form-control" value="<?=$config->formatPrice($totalDebit-$totalKredit)?>" id="firstName" placeholder="" readonly="readonly">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="lastName">Tanggal Report</label>
                            <input type="text" class="form-control" value="<?=Date('d M', strtotime($dateFrom))?> s.d <?=Date('d M Y', strtotime($dateTo))?>" id="lastName" placeholder="" readonly="readonly">
                        </div>

                    </div>

            </div>
                <hr>
                <table id="tableReporKasBesar" class="table table-bordered table-condensed table-hover <?=$device['device']=='MOBILE' ? 'table-responsive' : ''?>" style="text-transform: capitalize;">
                    <thead class="thead-light">
                    <tr style="text-transform: lowercase;">
                        <th scope="col" width="5%">TRANSAKSI</th>
                        <th scope="col" width="15%">Nama Transaksi</th>
                        <th scope="col" width="25%">Keterangan</th>
                        <th class="text-right" scope="col" width="8%">Total Trx.</th>
                        <th scope="col" width="5%">Label Trx</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php while ($row = $stmt->fetch(PDO::FETCH_LAZY)){
                        $trx = '<label class="badge badge-success">'.$row['type'].'</label>';
                        if($row['type'] == 'kredit'){
                            $trx = '<label class="badge badge-danger">kredit</label>';
                        }
                        $label = '<label class="badge badge-secondary">UNSET</label>';
                        if($row['status'] == '1'){
                            $label = '<label class="badge badge-primary">PRODUKSI</label>';
                        }elseif($row['status'] == '2'){
                            $label = '<label class="badge badge-primary">KURIR</label>';
                        }elseif($row['status'] == '3'){
                            $label = '<label class="badge badge-primary">DLL</label>';
                        }
                        ?>
                        <tr>
                            <td><?=$trx?></td>
                            <td><?=$row['title']?></td>
                            <td><?=$row['ket']?></td>
                            <td class="text-right" style="font-weight: 600;"><?=$config->formatPrice($row['total'])?></td>
                            <td><?=$label?></td>
                        </tr>
                <?php } ?>

                    </tbody>
                </table>
        </div>
    </div>
</div>
   <?php }
    if($type == 'kasIn'){
        $sql = "SELECT kas_ins.id, kas_ins.types, kas_ins.total, kas_ins.title, kas_ins.ket, kas_ins.status, 
users.name, kas_ins.created_at FROM kas_ins 
INNER JOIN users ON users.id = kas_ins.admin_id
WHERE kas_ins.status !='0' AND kas_ins.created_at BETWEEN :dateFrom AND :dateTo ". $adminID ."
ORDER BY kas_ins.types";

        $stmt = $config->runQuery($sql);
        $stmt->execute(array(
            ':dateFrom' => $dateFrom,
            ':dateTo'   => $dateTo
        ));
        $stmtDebit = $config->runQuery("SELECT SUM(total) as total FROM kas_ins WHERE types = 'debit' AND status != '0' AND created_at BETWEEN '". $dateFrom ."' AND '". $dateTo ."' ". $adminID);
        $stmtDebit->execute(); $stmtDebit = $stmtDebit->fetch(PDO::FETCH_LAZY);
        $totalDebit = $stmtDebit['total'];

        $stmtKredit = $config->runQuery("SELECT SUM(total) as total FROM kas_ins WHERE types = 'kredit' AND status != '0' AND created_at BETWEEN '". $dateFrom ."' AND '". $dateTo ."' ". $adminID);
        $stmtKredit->execute(); $stmtKredit = $stmtKredit->fetch(PDO::FETCH_LAZY);
        $totalKredit = $stmtKredit['total'];

        ?>
<div id="hasilReport" style = "margin-top:2%; <?=$reportH?>">
    <div class="card">
        <div class="card-header">
            <h5>Hasil Report Kas Operasional
                <div class="float-right">
                    <a href="?p=reportPayment">
                        <button class="btn btn-sm btn-primary">report again..</button>
                    </a>
                </div>
            </h5>

        </div>
        <div class="card-body">
            <div class="row infoReport justify-content-center">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="firstName">Total Debit</label>
                        <input type="text" class="form-control" value="<?=$config->formatPrice($totalDebit)?>" id="firstName" placeholder="" readonly="readonly">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="lastName">Total Kredit</label>
                        <input type="text" class="form-control" value="<?=$config->formatPrice($totalKredit)?>" id="lastName" placeholder="" readonly="readonly">
                    </div>

                </div>
                <div class="row" style="<?=$device['device'] == 'MOBILE' ? ''  : 'padding-left : 2.555555%;'?>">
                    <div class="col-md-6 mb-3">
                        <label for="firstName">Selisih Total</label>
                        <input type="text" class="form-control" value="<?=$config->formatPrice($totalDebit-$totalKredit)?>" id="firstName" placeholder="" readonly="readonly">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="lastName">Tanggal Report</label>
                        <input type="text" class="form-control" value="<?=Date('d M', strtotime($dateFrom))?> s.d <?=Date('d M Y', strtotime($dateTo))?>" id="lastName" placeholder="" readonly="readonly">
                    </div>

                </div>

            </div>
            <hr>
            <table id="tablePayKurir" class="table table-bordered table-condensed table-hover" style="text-transform: capitalize;">
                <thead class="thead-light">
                <tr style="text-transform: lowercase;">
                    <th scope="col" width="5%">TRANSAKSI</th>
                    <th scope="col" width="15%">Nama Transaksi</th>
                    <th scope="col" width="25%">Keterangan</th>
                    <th class="text-right" scope="col" width="8%">Total Trx.</th>
                    <th scope="col" width="5%">Label Trx</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($row = $stmt->fetch(PDO::FETCH_LAZY)){
                    $trx = '<label class="badge badge-success">'.$row['types'].'</label>';
                    if($row['types'] == 'kredit'){
                        $trx = '<label class="badge badge-danger">kredit</label>';
                    }
                    $label = '<label class="badge badge-secondary">UNSET</label>';
                    if($row['status'] == '1'){
                        $label = '<label class="badge badge-primary">PRODUKSI</label>';
                    }elseif($row['status'] == '2'){
                        $label = '<label class="badge badge-secondary">KURIR</label>';
                    }elseif($row['status'] == '3'){
                        $label = '<label class="badge badge-warning">DLL</label>';
                    }
                    ?>
                    <tr>
                        <td><?=$trx?></td>
                        <td><?=$row['title']?></td>
                        <td><?=$row['ket']?></td>
                        <td class="text-right" style="font-weight: 600;"><?=$config->formatPrice($row['total'])?></td>
                        <td><?=$label?></td>
                    </tr>
                <?php } ?>

                </tbody>
            </table>
        </div>
    </div>
</div>
    <?php }
    if($type == 'kasOut'){
        $dateFrom = str_replace('-', ':', $dateFrom);
        $dateTo = str_replace('-', ':', $dateTo);

        $sql = "SELECT kas.id, kas.nama, kas.qty, kas.harga, kas.satuan, kas.ket, kas.created_at, 
kas.status, kas.admin_id, users.name, cat.content as category, subcat.category as subCategory 
FROM kas_outs AS kas
INNER JOIN users ON users.id = kas.admin_id
        LEFT OUTER JOIN satuans AS cat ON cat.id = kas.type
        LEFT OUTER JOIN satuans AS subcat ON subcat.id = kas.sub_type
        WHERE kas.created_at BETWEEN '". $dateFrom ." 00:00:00' AND '". $dateTo ." 23:59:59' 
        ". $adminID;

        $stmt = $config->runQuery($sql);
        $stmt->execute();

        $stmtDebit = $config->runQuery("SELECT SUM(kas.harga * kas.qty) as total FROM kas_outs AS kas WHERE kas.created_at BETWEEN '". $dateFrom ."' AND '". $dateTo ."' ". $adminID);
        $stmtDebit->execute(); $stmtDebit = $stmtDebit->fetch(PDO::FETCH_LAZY);
        $totalDebit = $stmtDebit['total'];

//        $stmtKredit = $config->runQuery("SELECT SUM(total) as total FROM kas_besar WHERE type = 'kredit' AND created_at BETWEEN '". $dateFrom ."' AND '". $dateTo ."' ". $adminID);
//        $stmtKredit->execute(); $stmtKredit = $stmtKredit->fetch(PDO::FETCH_LAZY);
//        $totalKredit = $stmtKredit['total'];
        ?>
<div id="hasilReport" style = "margin-top:2%; <?=$reportH?>">
    <div class="card">
        <div class="card-header">
            <h5>Hasil Report Belanja
                <div class="float-right">
                    <a href="?p=reportPayment">
                        <button class="btn btn-sm btn-primary">report again..</button>
                    </a>
                </div>
            </h5>

        </div>
        <div class="card-body">
            <div class="row infoReport justify-content-center">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="firstName">Total Belanja</label>
                        <input type="text" class="form-control" value="<?=$config->formatPrice($totalDebit)?>" id="firstName" placeholder="" readonly="readonly">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="lastName">Report Tanggal</label>
                        <input type="text" class="form-control" value="<?=Date('d M', strtotime($dateFrom))?> s.d <?=Date('d M Y', strtotime($dateTo))?>" id="lastName" placeholder="" readonly="readonly">
                    </div>
                </div>

            </div>
            <hr>
            <table id="tableReporKasBesar" class="table table-bordered table-condensed table-hover <?=$device['device']=='MOBILE' ? 'table-responsive' : ''?>" style="text-transform: capitalize;">
                <thead class="thead-light">
                <tr style="text-transform: lowercase;">
                    <th scope="col" width="10%">Category</th>
                    <th scope="col" width="15%">TRANSAKSI</th>
                    <th scope="col" width="25%">Keterangan</th>
                    <th scope="col" width="10%">Qty</th>
                    <th scope="col" width="10%">Harga</th>
                    <th scope="col" width="10%">Total</th>
                    <th scope="col" width="5%">By</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($row = $stmt->fetch(PDO::FETCH_LAZY)){

                    ?>
                    <tr>
                        <td><?=$row['category']?> > <?=$row['subCategory']?></td>
                        <td><?=$row['nama']?></td>
                        <td><?=$row['ket']?></td>
                        <td><?=$row['qty']?> <label class="badge badge-pill badge-info"><?=$row['satuan']?></label></td>
                        <td><?=$config->formatPrice($row['harga'])?></td>
                        <td><?=$config->formatPrice($row['harga']*$row['qty'])?></td>
                        <td><?=$row['name']?></td>
                    </tr>
                <?php } ?>

                </tbody>
            </table>
        </div>
    </div>
</div>
    <?php }
    if($type == 'kurir'){ ?>
<div id="hasilReport" class="hidden" style = "margin-top:2%; <?=$reportH?>">
    <div class="card">
        <div class="card-header">
            <h5>Hasil Report
                <div class="float-right">
                    <a href="?p=reportPayment">
                        <button class="btn btn-sm btn-primary">report again..</button>
                    </a>
                </div>
            </h5>

        </div>
        <div class="card-body">
            <div id="listReport"></div>
            <table id="tablePayKurir" class="table table-bordered table-condensed table-hover" style="text-transform: capitalize;">
                <thead class="thead-light">
                <tr style="text-transform: lowercase;">
                    <th scope="col">Nama Kurir</th>
                    <th scope="col">Nomor Transaksi</th>
                    <th scope="col">Kirim ke</th>
                    <th scope="col">Delivery Charge</th>
                    <th scope="col">Admin id</th>
                    <th scope="col">Created_at</th>
                    <th scope="col">action</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>
 <?php   }
}
?>
