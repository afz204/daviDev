<?php
$outKas = $config->ProductsJoin('kas_ins.id, kas_ins.title, kas_ins.total, kas_ins.ket, kas_ins.admin_id, kas_ins.status, kas_ins.created_at, users.name', 'kas_ins',
    'INNER JOIN users ON users.id = kas_ins.admin_id', "WHERE kas_ins.status ='' ");
$totalKas   = $config->Products('created_at, SUM(total) as totalDana', "kas_ins WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE()) AND status = ''");
$totalKas   = $totalKas->fetch(PDO::FETCH_LAZY);

$kasOut     = $config->Products('SUM(qty * harga) as totalOut', "kas_outs WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE()) AND status = '1'");
$kasOut     = $kasOut->fetch(PDO::FETCH_LAZY);

$sql = "SELECT pay_kurirs.id, pay_kurirs.total FROM pay_kurirs WHERE pay_kurirs.total != '' AND MONTH(pay_kurirs.created_at) = MONTH(CURRENT_DATE()) AND YEAR(pay_kurirs.created_at) = YEAR(CURRENT_DATE())";
$kurir   = $config->runQuery($sql);
$kurir->execute();

$payKurir = $kurir->fetch(PDO::FETCH_LAZY);

    if(empty($totalKas['totalDana'])){
        $danaKas = 0;
    }else{
        $danaKas = $totalKas['totalDana'];
    }
    if(empty($kasOut['totalOut'])){
        $kasOut = 0;
    }else{
        $kasOut = $kasOut['totalOut'];
    }

    if(empty($payKurir['total'])){
        $payKurir = 0;
    }else{
        $payKurir = $payKurir['total'];
    }

$total = $danaKas - ($kasOut + $payKurir);
$totalDanaKas = $config->formatPrice($total);

if($totalKas > 0 ){
        $style = 'success';
    }else{
        $style = 'danger';
    }
?>
<div id="listKasInHeader" <?=$access['read']?>>
    <div class="row">
        <div class="col-12 col-sm-12 col-lg-12" id="listPemasukanKas">
            <div class="card">
                <div class="card-header">
                    List Pemasukkan <div class="pull-right">
                        <button class="btn btn-sm btn-primary addInKas" <?=$access['create']?> type="button"><span class="fa fa-fw fa-plus"></span> pemasukan</button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="form-kasIn" class="hidden">
                        <div class="card border-dark mb-3">
                            <div class="card-header bg-transparent border-dark">Form Tambah Dana Kas</div>
                            <div class="card-body">
                                <form id="kasIn-form" method="post" data-parsley-validate="" autocomplete="off">
                                    <div class="form-group">
                                        <input type="hidden" value="<?=$admin[0]['user_id']?>" id="adminIn">
                                        <input type="text"
                                               data-parsley-minLength="3" data-parsley-maxLength="255"
                                               class="form-control" placeholder="nama dana kas" id="nameIn" required>
                                    </div>
                                    <div class="form-group">
                                        <input type="text"
                                               data-parsley-type="number"
                                               class="form-control" placeholder="total biaya" id="biayaIn" required>
                                    </div>
                                    <div class="form-group">
                                        <textarea class="form-control" rows="5" id="ketIn" required placeholder="keterangan kas masuk"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-block btn-primary">submit pemasukan</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div id="monitoringKasIn">
                        <div class="card text-center border-success mb-3">
                            <div class="card-body">
                                <h3 class="card-title">Your Kas Balance</h3>
                                <p class="card-text">Update every time.</p>
                                <button class="btn btn-lg btn-<?=$style?> showListKasIn">
                        <?=$totalDanaKas?>
                                </button>
                            </div>
                            <div class="card-footer text-muted">
                                Updated at: <span class="badge badge-danger"><?=$config->timeAgo($totalKas['created_at'])?></span>
                            </div>
                        </div>
                    </div>
                    <div id="listKasIn">

                        <table id="kasMasuk" class="table table-bordered  <?=$device['device']=='MOBILE' ? 'table-responsive' : ''?> table-condensed table-hover" style="text-transform: capitalize;">
                            <thead class="thead-light">
                            <tr style="text-transform: lowercase;">
                                <th scope="col">#</th>
                                <th scope="col">Nama Pengeluaran</th>
                                <th scope="col">Total Biaya</th>
                                <th scope="col">Keterangan</th>
                                <th scope="col">Admin id</th>
                                <th scope="col">Created_at</th>
                                <th scope="col">action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $i = 1; while ($row = $outKas->fetch(PDO::FETCH_LAZY)){ ?>
                                <tr style="text-transform: lowercase;">
                                    <td><?=$i++?></td>
                                    <td><?=$row['title']?></td>
                                    <td style="text-align: right;"><?=number_format($row['total'], '2', ',', '.')?></td>
                                    <td><?=$row['ket']?></td>
                                    <td><?=$row['name']?></td>
                                    <td><i class="small"><?=$row['created_at']?></i></td>
                                    <td>
                                        <!--                                        <a href="--><?//=PAYMENT?><!--?p=koDetail&id=--><?//=$row['id']?><!--" --><?//=$access['read']?><!-->
                                        <!--                                            <button class="btn btn-sm btn-primary" style="text-transform: uppercase; font-size: 10px; font-weight: 500;">details</button>-->
                                        <!--                                        </a>-->
                                        <button class="btn btn-sm btn-danger delKasIn" style="text-transform: uppercase; font-size: 10px; font-weight: 500;"  <?=$access['delete']?> data-id="<?=$row['id']?>" data-admin="<?=$admin[0]['user_id']?>" >delete</button>

                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                        <button class="btn btn-sm btn-success reportKasIn" <?=$access['update']?> data-admin="<?=$admin[0]['user_id']?>">report</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>