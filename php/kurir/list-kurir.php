<?php 

    $kurir = $config->Products('kurirs.id, kurirs.nama_kurir, kurirs.email, kurirs.phone, kurirs.wa, kurirs.alamat, kurirs.status,  kurirs.created_at', 'kurirs WHERE kurirs.status = 1 ORDER BY kurirs.created_at');

?>
<div class="card" <?=$access['read']?>>
    <div class="card-header">
        List Kurir
    </div>
    <div class="card-body">
        <div id="listKurir">
            <table id="tableKurir" class="table table-hover<?=$device['device']=='MOBILE' ? 'table-responsive' : ''?> table-condensed table-hover">
            <thead class="thead-light">
                <tr style="text-transform: lowercase;">
                    <th scope="col">nama kurir</th>
                    <th scope="col">email</th>
                    <th scope="col">telp</th>
                    <th scope="col">WA</th>
                    <th scope="col">status</th>
                    <th scope="col">join at</th>
                    <th scope="col">action</th>
                </tr>
            </thead>
            <tbody>
            <?php while($rows = $kurir->fetch(PDO::FETCH_LAZY)){ 
                if($rows['status'] == '1'){
                    $status = '<span class="badge badge-success">active</span>';
                }else{
                    $status = '<span class="badge badge-secondary">deactive</span>';
                }
                ?>
                <tr>
                    <td><?=$rows['nama_kurir']?></td>
                    <td><?=$rows['email']?></td>
                    <td><?=$rows['phone']?></td>
                    <td><?=$rows['wa']?></td>
                    <td><?=$status?></td>
                    <td style="font-size: 12px; font-weight: 600;"><?=$rows['created_at']?></td>                    
                    <td>
                        <button class="btn btn-sm btn-info">details</button>
                    </td>
                </tr> 
            <?php } ?>
            </tbody>
            </table>
        </div>
    </div>
</div>