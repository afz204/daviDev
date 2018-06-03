<?php
$listAdmin = $config->Products('id, name', 'users');

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
<div id="formLogs" class="<?=$formR?>">
    <div class="col-12 col-sm-12 col-lg-12">
        <div class="card">
            <div class="card-header">
                form logs users
            </div>
            <div class="card-body d-flex justify-content-center" >
                <form class="form-inline" id="form-logs" method="post" action="" data-parsley-validate="" autocomplete="off">
                    <div class="form-group mx-sm-3 mb-2">
                        <div id="logsRange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                            <i class="fa fa-calendar"></i>&nbsp;
                            <span></span> <i class="fa fa-caret-down"></i>
                        </div>
                        <input type="hidden" id='hidde_date_field'>
                    </div>
                    <div>
                        <div class=" form-group mx-sm-3 mb-2" >
                            <select name="adminLogs" id="adminLogs" class="form-control">
                                <option value="">:: admin-list ::</option>
                                <?php
                                while($row = $listAdmin->fetch(PDO::FETCH_LAZY)){
                                    ?>
                                    <option value="<?=$row['id']?>"><?=$row['name']?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary mb-2">cek logs..</button>
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

    
    $adminID = " AND users.id = '". $admin. "' ";
    if(empty($admin)){
        $adminID = '';
    }

    $reportH = 'display: block;';

    
        $sql = "SELECT logs_users.id, logs_users.user_id, logs_users.reff_id, logs_users.methode, logs_users.ket, logs_users.created_at, users.name
        FROM logs_users
        INNER JOIN users ON users.id = logs_users.user_id
WHERE logs_users.created_at BETWEEN '". $dateFrom ." 00:00:00' AND '". $dateTo . " 23:59:59' ". $adminID ."
ORDER BY logs_users.created_at DESC";

        $stmt = $config->runQuery($sql);
        $stmt->execute();
       
        ?>
<div id="hasilLogs" class="hidden" style = "margin-top:2%; <?=$reportH?>">
    <div class="card">
        <div class="card-header">
            <h5>Report Logs
                <div class="float-right">
                    <a href="?p=log_user">
                        <button class="btn btn-sm btn-primary">logs..</button>
                    </a>
                </div>
            </h5>

        </div>
        <div class="card-body">
            
                <hr>
                <table id="tableLogs" class="table table-bordered table-condensed table-hover <?=$device['device']=='MOBILE' ? 'table-responsive' : ''?>" style="text-transform: capitalize;">
                    <thead class="thead-light">
                    <tr style="text-transform: lowercase;">
                        <th scope="col" width="5%">TYPES</th>
                        <th scope="col" width="35%">Keterangan</th>
                        <th scope="col" width="15%">admin</th>
                        <th class="text-right" scope="col" width="10%">date_logs</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php while ($row = $stmt->fetch(PDO::FETCH_LAZY)){
                       
                        $label = '<label class="badge badge-secondary">'.$row['methode'].'</label>';
                        if($row['methode'] == 'update'){
                            $label = '<label class="badge badge-info">UPDATE</label>';
                        }elseif($row['methode'] == 'delete'){
                            $label = '<label class="badge badge-danger">DELETE</label>';
                        }elseif($row['methode'] == 'function'){
                            $label = '<label class="badge badge-warning">FUNCTION</label>';
                        }
                        elseif($row['methode'] == 'create'){
                            $label = '<label class="badge badge-primary">CREATE</label>';
                        }
                        ?>
                        <tr>
                            <td><?=$label?></td>
                            <td><?=$row['ket']?></td>
                            <td><?=$row['name']?></td>
                            <td class="text-right" style="font-weight: 600; font-size: 12px; font-style: italic;"><?=date('d M Y h:m:s', strtotime($row['created_at']))?></td>
                        </tr>
                <?php } ?>

                    </tbody>
                </table>
        </div>
    </div>
</div>
   <?php  }?>
    