<?php 

$card_level1 = $config->Products('id, level1', "card_messages WHERE level2 ='NULL'");
$card = $config->runQuery("SELECT cat.level1, subcat.id AS SubID, subcat.level1 AS SubCategory, subcat.level2 as categoriesID, 
subcat.level3 as isi
FROM card_messages AS cat
RIGHT OUTER JOIN card_messages AS subcat
ON cat.id = subcat.level2
WHERE cat.level2 = 'NULL'");
$card->execute();
?>

<div class="card hidden" id="formCard">
   <div class="card-header">
      form card messages
   </div>
   <div class="card-body">
      <form id="formCardMessages" method="post" data-parsley-validate="" class="needs-validation" novalidate="" autocomplete="off">
          <div class="form-group">
            <label for="usernameAdmin">head template</label>
            <select class="form-control" name="level_1" id="level_1" required="">
               <option value="">:: level_1 ::</option>
               <?php while ($row = $card_level1->fetch(PDO::FETCH_LAZY)){ ?>
               <option value="<?=$row->id?>"><?=$row->level1?></option>
               <?php } ?>
            </select>
         </div>
         <div class="row">
            <div class="col-md-12 mb-12">
               <label for="lastName">template</label>
               <input type="text" class="form-control" id="level_2" placeholder="" value="" required="">
            </div>
         </div>
         <div class="form-group">
            <label for="usernameAdmin">isi_template</label>
            <textarea style="text-transform: capitalize;" data-parsley-minLength="5" name="isi_template" id="isi_template" class="form-control" cols="5" required></textarea>
         </div>
         <br>
         <div id="btn_submit_card">
           <button class="btn btn-success btn-sm btn-block" type="submit">submit card</button>
         </div>
      </form>
   </div>
</div>
<br>
<div class="d-flex justify-content-center" id="btn_add_card">
   <button class="btn btn-info " onclick="formCard()">New Card Messages</button>
</div>
<br>
<div class="row">
  <?php while ($row = $card->fetch(PDO::FETCH_LAZY)) {
    ?>
   <div class="col-12 col-sm-4 col-lg-4" style="<?=$device['device']=='MOBILE' ? 'margin-bottom: 1%;' : ''?>">
      <div class="card" style="height: 250px;">
         <div class="card-body">
            <h5 class="card-title" style="text-transform: capitalize;"><?=$row->level1?></h5>
            <h6 class="card-subtitle mb-2 text-muted" style="text-transform: capitalize;"><?=$row->SubCategory?></h6>
            <p class="card-text" style="text-transform: capitalize;"><?=$row->isi?></p>
            
         </div>
         <div class="card-footer bg-warning text-muted">
           <button type="button" class="btn btn-sm btn-secondary" disabled><span class="fa fa-pencil"></span> Edit</button>
          </div>
      </div>
   </div>

 <?php } ?>
</div>