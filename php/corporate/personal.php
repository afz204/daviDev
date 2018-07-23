<?php
$stmt = $config->runQuery("SELECT * FROM customer ORDER BY CreatedDate DESC");
$stmt->execute();
?>

<div <?=$access['delete']?> class="card" >
    <div class="card-header">
        List Customer
    </div>
    <div class="card-body">

        <table id="listPersonal" class="table table-bordered <?=$device['device']=='MOBILE' ? 'table-responsive' : ''?> table-condensed table-hover" style="text-transform: capitalize;">
            <thead class="thead-light">
            <tr>
                <th scope="col">full name</th>
                <th scope="col">L/P</th>
                <th scope="col">Handphone</th>
                <th scope="col">Phone</th>
                <th scope="col">email</th>
                <th scope="col">as guest</th>
                <th scope="col">join at</th>
                <th scope="col">action</th>
            </tr>
            </thead>
            <tbody>
            <?php $i = 1; while ($row = $stmt->fetch(PDO::FETCH_LAZY)){ 
                $jenisKelamin = 'Male'; if($row['Gender'] == '0') { $jenisKelamin = 'Female'; }
                $guest = 'Yes'; if($row['AsGuest'] == '0') {$guest = 'No';} ?>
                <tr style="text-transform: lowercase;">
                    <td><?=$row['FullName']?></td>
                    <td><?=$jenisKelamin?></td>
                    <td><?=$row['Phone']?></td>
                    <td><?=$row['Mobile']?></td>
                    <td><?=$row['Email']?></td>
                    <td><?=$guest?></td>
                    <td><?=date('d M Y H:m', strtotime($row['CreatedDate']))?></td>
                    <td >
                        <a href="<?=CORPORATE?>?p=detailOrganic&id=<?=$row['ID']?>" <?=$access['read']?>>
                            <button class="btn btn-sm btn-primary" style="text-transform: uppercase; font-size: 10px; font-weight: 500;">details</button>
                        </a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>