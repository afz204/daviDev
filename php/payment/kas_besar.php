<?php
$kas = $config->ProductsJoin('kas_besar.id, kas_besar.type, kas_besar.total, kas_besar.title, kas_besar.ket, kas_besar.status, kas_besar.admin_id, kas_besar.status, kas_besar.created_at, users.name', 'kas_besar',
    'INNER JOIN users ON users.id = kas_besar.admin_id', " WHERE MONTH(kas_besar.created_at) = MONTH(CURRENT_DATE()) AND YEAR(kas_besar.created_at) = YEAR(CURRENT_DATE()) ORDER BY kas_besar.created_at DESC");
$totalKas   = $config->Products('created_at, SUM(total) as totalDana', "kas_besar WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE()) AND type  LIKE 'debit'");
$totalKas   = $totalKas->fetch(PDO::FETCH_LAZY);

$totalKeluar   = $config->Products('created_at, SUM(total) as totalDana', "kas_besar WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE()) AND type  LIKE 'kredit'");
$totalKeluar   = $totalKeluar->fetch(PDO::FETCH_LAZY);

$kasMasuk = $totalKas['totalDana'];
$kasKeluar = $totalKeluar['totalDana'];
$total = $kasMasuk - $kasKeluar;
$totalDanaKas = $config->formatPrice($total);
// var_dump($kas);
if($total > 0 ){
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
                    Kas Besar <div class="pull-right">
                        <button <?=$access['create']?> class="btn btn-sm btn-success" onclick="addKasBesar(<?=$admin[0]['user_id']?>, 'debit')"  type="button"><span class="fa fa-fw fa-plus"></span> debit</button>
                        <button <?=$access['create']?> class="btn btn-sm btn-danger" onclick="addKasBesar(<?=$admin[0]['user_id']?>, 'kredit')" type="button"><span class="fa fa-fw fa-plus"></span> kredit</button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="form_kas_Besar" class="hidden">
                        <div class="card border-dark mb-3">
                            <div class="card-header bg-transparent border-dark">Form Tambah Dana Kas</div>
                            <div class="card-body">
                                <form id="kas_besar_form" method="post" data-parsley-validate="" autocomplete="off">
                                    <div class="form-group hidden" id="kasStatus">
                                        <select name="statusKas" id="statusKas" class="form-control">
                                            <option value="">:: type kredit ::</option>
                                            <option value="1">produksi</option>
                                            <option value="2">kurir</option>
                                            <option value="3">dll</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <input type="hidden" value="<?=$admin[0]['user_id']?>" id="adminKasB">
                                        <input type="text"
                                               data-parsley-minLength="3" data-parsley-maxLength="255"
                                               class="form-control" placeholder="nama dana kas" id="nameKasB" required>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" id="typeKasB" class="form-control" readonly>
                                    </div>
                                    <div class="form-group">
                                        <input type="text"
                                               data-parsley-type="number"
                                               class="form-control" placeholder="total biaya" id="biayaKasB" required>
                                    </div>
                                    <div class="form-group">
                                        <textarea class="form-control" rows="5" id="ketKasB" required placeholder="keterangan kas"></textarea>
                                    </div>
                                    <button type="submit" id="btnKas_besar" class="btn btn-sm btn-block btn-primary">submit pemasukan</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div id="monitoringKasIn">
                        <div class="card text-center border-success mb-3">
                            <div class="card-body">
                                <h3 class="card-title">Your Kas Balance</h3>
                                <p class="card-text">Update every time.</p>
                                <button class="btn btn-lg btn-<?=$style?> " onclick="showKasBesar()">
                        <?=$totalDanaKas?>
                                </button>
                            </div>
                            <div class="card-footer text-muted">
                                Updated at: <span class="badge badge-danger"><?=$config->timeAgo($totalKas['created_at'])?></span>
                            </div>
                        </div>
                    </div>
                    <div id="listKasBesar" class="hidden">
                        <form id="FilterPayKurir" methods="post" data-parsley-validate="">
                            <div class="row">
                                <div class="form-group mx-sm-3 mb-2">
                                    <div id="daterangekasbesar" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                                        <i class="fa fa-calendar"></i>&nbsp;
                                        <span></span> <i class="fa fa-caret-down"></i>
                                    </div>
                                    <input type="hidden" id='datarangekasbesar'>
                                </div>
                                <div class="col-12 col-sm-3 col-lg-3">
                                    <div class="btn-group mr-2" role="group" aria-label="First group" style="padding-top: 1%">
                                        <button type="button" onClick="exportkasbesar('kasbesar')" class="btn btn-sm btn-outline-info"><span class="fa fa-download"></span> export</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <table id="kasMasuk" class="table table-bordered  <?=$device['device']=='MOBILE' ? 'table-responsive' : ''?> table-condensed table-hover" style="text-transform: capitalize;">
                            <thead class="thead-light">
                            <tr style="text-transform: lowercase;">
                                <th scope="col">#</th>
                                <th scope="col">status</th>
                                <th scope="col">Nama Kegiatan</th>
                                <th scope="col">Type</th>
                                <th scope="col">Total Biaya</th>
                                <th scope="col">Keterangan</th>
                                <th scope="col">Admin id</th>
                                <th scope="col">Created_at</th>
                                <th scope="col">action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $i = 1; while ($row = $kas->fetch(PDO::FETCH_LAZY)){ 
                                if($row['type'] == 'debit'){
                                    $types = '<label class="badge badge-success">debit</label>';
                                }else{
                                    $types = '<label class="badge badge-warning">kredit</label>';
                                }
                               if($row['status'] == '1'){
                                   $st12 = 'produksi';
                               }elseif($row['status'] == '2'){
                                   $st12 = 'kurir';
                               }elseif($row['status'] == '3'){
                                   $st12 = 'dll';
                               }else{
                                   $st12 = '';
                               }
                               $tipe = $row['status'];
                               if(empty($row['status'])){
                                   $tipe = '0';
                               }
                               
                                ?>
                                <tr style="text-transform: lowercase;">
                                    <td><?=$i++?></td>
                                    <td><?=$st12?></td>
                                    <td><?=$row['title']?></td>
                                    <td><?=$types?></td>
                                    <td style="text-align: right;"><?=number_format($row['total'], '2', ',', '.')?></td>
                                    <td><?=$row['ket']?></td>
                                    <td><?=$row['name']?></td>
                                    <td><i class="small"><?=$row['created_at']?></i></td>
                                    <td>
                                       
                                        <button class="btn btn-sm btn-danger" onclick="delKasBesar(<?=$row['id']?>, <?=$tipe?>, <?=$row['total']?>, <?=$admin[0]['user_id']?>)" style="text-transform: uppercase; font-size: 10px; font-weight: 500;"  <?=$access['delete']?> data-id="<?=$row['id']?>" data-admin="<?=$admin[0]['user_id']?>" >delete</button>

                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>