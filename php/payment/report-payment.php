<?php 
    $listAdmin = $config->Products('id, name', 'users');
    $kurir     = $config->Products('id, nama_kurir', 'kurirs');

    if(isset($_GET['type']) || isset($_GET['tgl']) || isset($_GET['admin'])){
        $type = $_GET['type'];
        $tgl = $_GET['range'];
        $admin = $_GET['admin'];

        $componen = array($type, $tgl, $admin);
        //print_r($componen);
        $formR = 'hidden';
        $reportH = 'display: block;';

        if($type == 'kasBesar'){

        }
        if($type == 'kasIn'){

        }
        if($type == 'kasOut'){

        }
        if($type == 'kurir'){

        }
    }
?>
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
<div id="hasilReport" class="hidden" style = "margin-top:2%; <?=$reportH?>">
    <div class="card">
        <div class="card-header">
            <h5>Hasil Report
            <div class="float-right">
                <a href="?p=report-payment">
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