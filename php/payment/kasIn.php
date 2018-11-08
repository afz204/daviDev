<?php
$stylePHP = 'hidden';
$tipes = '';
if(isset($_GET['types'])){
    if($_GET['types'] == '1'){
        $outKas = $config->ProductsJoin('kas_ins.id, kas_ins.types, kas_ins.title, kas_ins.total, kas_ins.ket, kas_ins.admin_id, kas_ins.status, kas_ins.created_at, users.name', 'kas_ins',
    'INNER JOIN users ON users.id = kas_ins.admin_id', " WHERE kas_ins.id NOT IN ('1', '2', '3') AND kas_ins.status = '1' ORDER BY kas_ins.created_at DESC ");
    $stylePHP = 'showContent';
    $tipes = "1";
    }elseif($_GET['types'] == '2'){
        $outKas = $config->ProductsJoin('kas_ins.id, kas_ins.types, kas_ins.title, kas_ins.total, kas_ins.ket, kas_ins.admin_id, kas_ins.status, kas_ins.created_at, users.name', 'kas_ins',
    'INNER JOIN users ON users.id = kas_ins.admin_id', " WHERE kas_ins.id NOT IN ('1', '2', '3') AND kas_ins.status = '2' ORDER BY kas_ins.created_at DESC ");
    $stylePHP = 'showContent';
    $tipes = "2";
    }else{
        $outKas = $config->ProductsJoin('kas_ins.id, kas_ins.types, kas_ins.title, kas_ins.total, kas_ins.ket, kas_ins.admin_id, kas_ins.status, kas_ins.created_at, users.name', 'kas_ins',
    'INNER JOIN users ON users.id = kas_ins.admin_id', " WHERE kas_ins.id NOT IN ('1', '2', '3') AND kas_ins.status = '3' ORDER BY kas_ins.created_at DESC ");
    $stylePHP = 'showContent';
    $tipes = "3";
    }
}else{
    $outKas = $config->ProductsJoin('kas_ins.id, kas_ins.types, kas_ins.title, kas_ins.total, kas_ins.ket, kas_ins.admin_id, kas_ins.status, kas_ins.created_at, users.name', 'kas_ins',
    'INNER JOIN users ON users.id = kas_ins.admin_id', " WHERE kas_ins.id NOT IN ('1', '2', '3') ORDER BY kas_ins.created_at DESC ");
}

    $rec = $config->Products('MAX(id) as new, created_at', 'kas_ins');
    $newRecord = $rec->fetch(PDO::FETCH_LAZY);
    
    $product = $config->Products('SUM(total) AS totalProduksi, created_at', "kas_ins WHERE id = '1'");
    $product = $product->fetch(PDO::FETCH_LAZY);

    $kurir = $config->Products('SUM(total) AS totalProduksi, created_at', "kas_ins WHERE id = '2'");
    $kurir = $kurir->fetch(PDO::FETCH_LAZY);

    $dll = $config->Products('SUM(total) AS totalProduksi, created_at', "kas_ins WHERE id = '3'");
    $dll = $dll->fetch(PDO::FETCH_LAZY);
    

$prod = $config->formatPrice($product['totalProduksi']);
$kur = $config->formatPrice($kurir['totalProduksi']);
$dl = $config->formatPrice($dll['totalProduksi']);
    if($product['totalProduksi'] > 0 ){
        $styleProd = 'info';
    }else{
        $styleProd = 'danger';
    }
    if($kurir['totalProduksi'] > 0 ){
        $styleKur = 'success';
    }else{
        $styleKur = 'danger';
    }
    if($dll['totalProduksi'] > 0 ){
        $styleDl = 'warning';
    }else{
        $styleDl = 'danger';
    }
    
?>
<div id="listKasInHeader" <?=$access['read']?>>
    <div class="row">
        <div class="col-12 col-sm-12 col-lg-12" id="listPemasukanKas">
            <div class="card">
                <div class="card-header">
                    List Pemasukkan <div class="pull-right">
                        <!-- <button class="btn btn-sm btn-primary addInKas" <?=$access['create']?> type="button"><span class="fa fa-fw fa-plus"></span> pemasukan</button> -->
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
                            <h3 class="card-title" style="margin-bottom: 2%; border-bottom: 2px dashed #cdcdcd;">Your Kas Balance</h3>
                            
                            <div class="row justify-content-center">
                                <div class="col-12 col-md-6 col-lg-4">
                                    <p class="card-text text-<?=$styleProd?>" style="font-weight: 600;"><a href="#" data-nilai="" onclick="returnKas(1, <?=$product['totalProduksi']?>, <?=$admin[0]['user_id']?>)">PRODUKSI</a></p>
                                            <button class="btn btn-xs btn-<?=$styleProd?>" onclick="showListKasIn(1)">
                                    <?=$prod?>
                                            </button>
                                </div>
                                <!-- <div class="col-12 col-md-6 col-lg-4">
                                    <p class="card-text text-<?=$styleKur?>" style="font-weight: 600;"><a href="#" data-nilai="" onclick="returnKas(2, <?=$kurir['totalProduksi']?>, <?=$admin[0]['user_id']?>)">KURIR</a></p>
                                            <button class="btn btn-xs btn-<?=$styleKur?>" onclick="showListKasIn(2)">
                                    <?=$kur?>
                                            </button>
                                </div> -->
                                <!-- <div class="col-12 col-md-6 col-lg-4">
                                    <p class="card-text text-<?=$styleDl?>" style="font-weight: 600;"><a href="#" data-nilai="" onclick="returnKas(3, <?=$dll['totalProduksi']?>, <?=$admin[0]['user_id']?>)">DLL</a></p>
                                            <button class="btn btn-xs btn-<?=$styleDl?>" onclick="showListKasIn(3)">
                                    <?=$dl?>
                                            </button>
                                </div> -->
                            </div>
                                        
                            </div>
                            <div class="card-footer text-muted">
                            <p>
                                             Updated at: <span class="badge badge-danger"><?=$config->timeAgo($newRecord['created_at'])?></span>
                                        </p>
                            </div>
                        </div>
                    </div>
                    <div id="listKasIn" class="<?=$stylePHP?>">

                        <table id="kasMasuk" class="table table-bordered  <?=$device['device']=='MOBILE' ? 'table-responsive' : ''?> table-condensed table-hover" style="text-transform: capitalize;">
                            <thead class="thead-light">
                            <tr style="text-transform: lowercase;">
                                <th scope="col">#</th>
                                <th scope="col">Type</th>
                                <th scope="col">Nama Pengeluaran</th>
                                <th scope="col">Total Biaya</th>
                                <th scope="col">Keterangan</th>
                                <th scope="col">Admin id</th>
                                <th scope="col">Created_at</th>
                                <th scope="col">action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $i = 1; while ($row = $outKas->fetch(PDO::FETCH_LAZY)){ 
                                if($row['types'] == 'debit'){
                                    $types = '<label class="badge badge-success">debit</label>';
                                }else{
                                    $types = '<label class="badge badge-warning">kredit</label>';
                                }
                                ?>
                                <tr style="text-transform: lowercase;">
                                    <td><?=$i++?></td>
                                    <td><?=$types?></td>
                                    <td><?=$row['title']?></td>
                                    <td style="text-align: right;"><?=number_format($row['total'], '2', ',', '.')?></td>
                                    <td><?=$row['ket']?></td>
                                    <td><?=$row['name']?></td>
                                    <td><i class="small"><?=$row['created_at']?></i></td>
                                    <td>
                                        <!--                                        <a href="--><?//=PAYMENT?><!--?p=koDetail&id=--><?//=$row['id']?><!--" --><?//=$access['read']?><!-->
                                        <!--                                            <button class="btn btn-sm btn-primary" style="text-transform: uppercase; font-size: 10px; font-weight: 500;">details</button>-->
                                        <!--                                        </a>-->
                                        <?php if($row['types'] == 'kredit'){ ?>
                                            <button class="btn btn-sm btn-danger" style="text-transform: uppercase; font-size: 10px; font-weight: 500;"  <?=$access['delete']?> onclick="delKasIns(<?=$row['id']?>, '<?=$row['types']?>', <?=$tipes?>, <?=$row['total']?>, <?=$admin[0]['user_id']?>)" data-id="" data-admin="" >delete</button>

                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                        <!-- <button class="btn btn-sm btn-success reportKasIn" <?=$access['update']?> data-admin="<?=$admin[0]['user_id']?>">report</button> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>