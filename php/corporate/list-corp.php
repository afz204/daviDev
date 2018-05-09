<?php
$stmt = $config->runQuery("SELECT * FROM corporates ORDER BY created_at DESC");
$stmt->execute();
?>

<div class="card" <?=$access['read']?>>
    <div class="card-header">
        List Corporate
    </div>
    <div class="card-body">

        <table id="listCorporate" class="table table-bordered <?=$device['device']=='MOBILE' ? 'table-responsive' : ''?> table-condensed table-hover" style="text-transform: capitalize;">
            <thead class="thead-light">
            <tr>
                <th scope="col">#</th>
                <th scope="col">Nama Perusahaan</th>
                <th scope="col">Telphone</th>
                <th scope="col">Handphone</th>
                <th scope="col">Email</th>
                <th scope="col">contact person</th>
                <th scope="col">join at</th>
                <th scope="col">action</th>
            </tr>
            </thead>
            <tbody>
            <?php $i = 1; while ($row = $stmt->fetch(PDO::FETCH_LAZY)){ ?>
                <tr style="text-transform: lowercase;">
                    <td><?=$i++?></td>
                    <td><?=$row['nama']?></td>
                    <td><?=$row['telp']?></td>
                    <td><?=$row['handphone']?></td>
                    <td><?=$row['email']?></td>
                    <td><?=$row['cp']?></td>
                    <td><?=date('d M Y H:m', strtotime($row['created_at']))?></td>
                    <td >
                        <a href="<?=CORPORATE?>?p=profile&id=<?=$row['id']?>" <?=$access['read']?>>
                            <button class="btn btn-sm btn-primary" style="text-transform: uppercase; font-size: 10px; font-weight: 500;">details</button>
                        </a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>