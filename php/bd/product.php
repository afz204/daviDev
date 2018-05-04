<?php

$prod = $config->ProductsJoin('cat.id AS ID, cat.name AS category, subcat.category_id AS subID, subcat.name AS subcategory, prod.id AS idProduct, prod.name_product, prod.cost_price, prod.selling_price, prod.sort_desc, prod.full_desc, prod.note, prod.images, GROUP_CONCAT(prov.name ORDER BY prov.id) AS provinsi',
    'categories cat', 'LEFT JOIN categories subcat
ON subcat.category_id = cat.id
INNER JOIN products prod
ON prod.subcategory_id = subcat.id
LEFT JOIN provinces prov
ON FIND_IN_SET(prov.id, prod.available_on) > 0', 'GROUP BY 1, 2, 3, 4, 5 ORDER BY prod.id DESC');

?>
<div class="card" <?=$access['read']?>>
    <div class="card-header">
        List Products
    </div>
    <div class="card-body">
        <div id="listKurir">
            <table id="tableProduct" class="table table-hover<?=$device['device']=='MOBILE' ? 'table-responsive' : ''?> table-condensed table-hover">
                <thead class="thead-light">
                <tr style="text-transform: lowercase;">
                    <th scope="col" width="20%">PRODUCT</th>
                    <th scope="col" width="10%">CATEGORY</th>
                    <th scope="col" width="10%">SUB_CATEGORY</th>
                    <th scope="col" width="15%">NAMA PRODUCT</th>
                    <th scope="col" width="10%">COST_PRICE</th>
                    <th scope="col" width="10%">SELL_PRICE</th>
                    <th scope="col" width="25%">AVAILABLE_ON</th>
                    <th scope="col" width="15%">ACTION</th>
                </tr>
                </thead>
                <tbody>
                <?php  while($rows = $prod->fetch(PDO::FETCH_LAZY)){
                    $state= explode(',', $rows['provinsi']);
                    $prov = array();
                    foreach ($state as $provinsi){
                        $prov[] = trim($provinsi);
                    }
                    $p = $prov;

                    ?>
                    <tr>
                        <td><img src="<?=URL.'assets/images/product/'.$rows['images']?>" class="img img-responsive" style="width: 40%;"></td>
                        <td><?=$rows['category']?></td>
                        <td><?=$rows['subcategory']?></td>
                        <td><?=str_replace('_', ' ', $rows['name_product'])?></td>
                        <td><?=$config->formatPrice($rows['cost_price'])?></td>
                        <td><?=$config->formatPrice($rows['selling_price'])?></td>
                        <td><?=str_replace(',', ' _ ', $rows['provinsi'])?></td>
                        <td>
                            <a href="<?=BD?>?p=detail&id=<?=$rows['idProduct']?>">
                                <button <?=$access['read']?> class="btn btn-sm btn-warning">DETAILS</button>
                            </a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>