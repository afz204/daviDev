<?php 
$ListAdmin = $config->Products('*', 'users where status = 1');
$Listcorporate = $config->Products('*', 'corporates');
$Listflorist = $config->Products('*', 'florist');
$listkurir = $config->Products('id, nama_kurir', 'kurirs WHERE status = 1');
?>
<style>
    .card-body {
        padding: unset !important;
        padding-top: 1% !important;
    }
</style>
<div class="card">
    <div class="card-header" <?=$access['read']?>>
        List Order
    </div>
    <div class="card-body">
            <form id="OnDelivery" class="form-inline" methods="post" data-parsley-validate="" style="    padding-left: 1.2222222%;">
                <div class="row">
                    <div class="form-group mx-sm-3">
                    <label for="dateneworder">Created Date</label>
                        <div id="dateneworder" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%;">
                            <i class="fa fa-calendar"></i>&nbsp;
                            <span></span> <i class="fa fa-caret-down"></i>
                        </div>
                        <input type="hidden" id='daterangeneworder'>
                    </div>
                    <div class="form-group" style="padding-left: 3px; padding-top: 24px">
                    <button class="btn btn-outline-secondary" type="submit"><span class="fa fa-search"></span> filter</button>
                    </div>
                </div>
            </form>
        <div id="listOrder">
            <table id="tableOnDelivery" class="table table-hover<?=$device['device']=='MOBILE' ? 'table-responsive' : ''?> table-condensed table-hover">
                <thead class="thead-light">
                <tr style="text-transform: lowercase;">
                    <th scope="col">Invoice</th>
                    <th scope="col">Product Name</th>
                    <th scope="col">Sender Name</th>
                    <th scope="col">Price</th>
                    <th scope="col">Qty</th>
                    <th scope="col">Total Payment</th>
                    <!-- <th scope="col">customer_name</th> -->
                    <th scope="col">delivery_date</th>
                    <th scope="col">status_paid</th>
                    <th scope="col">created order</th>
                    <th scope="col">created by</th>
                    <th scope="col">florist</th>
                    <th scope="col">kurir</th>
                    <!-- <th scope="col">ACTION</th> -->
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="selectFlorist" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

            <div class="modal-body">
                <form id="formSelectFlorist" method="post" data-parsley-validate="" class="needs-validation" novalidate="" autocomplete="off">
                    <div class="form-group">
                        <select class="form-control" name="ListSelectedFlorist" id="ListSelectedFlorist" required>
                            <option value="">:: florist ::</option>
                            <?php while ($row = $Listflorist->fetch(PDO::FETCH_LAZY)){ ?>
                            <option value="<?=$row->ID?>"><?=$row->FloristName?></option>
                            <?php } ?>
                        </select>
                    </div>
					<input type="hidden" name="IDSelectedFlorist">
                    <button class="btn btn-success btn-sm btn-block" type="submit">Pilih Florist</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="chagestatusorder" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
            <div class="modal-body">
                <form id="formChangeStatusOrder" method="post" data-parsley-validate="" class="needs-validation" novalidate="" autocomplete="off">
                    <div class="form-group">
                        <select class="form-control" name="listStatusOrder" id="listStatusOrder" required>
                            <option value="">:: change status ::</option>
                            <?php foreach ($arrstatusorder as $key => $val){ ?>
                            <option value="<?=$key?>"><?=$val?></option>
                            <?php } ?>
                        </select>
                    </div>
					<input type="hidden" name="NomorTransaction">
					<input type="hidden" name="TypeStatus" value="florist">
                    <button class="btn btn-success btn-sm btn-block" type="submit">Change Status</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalselectkurir" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
            <div class="modal-body">
                <form id="formSelectKurir" method="post" data-parsley-validate="" class="needs-validation" novalidate="" autocomplete="off">
                    <div class="form-group">
                        <select class="form-control" name="listKurir" id="listKurir" required>
                            <option value="">:: pilih kurir ::</option>
                            <?php while ($kr = $listkurir->fetch(PDO::FETCH_LAZY)){ ?>
                            <option value="<?=$kr['id']?>"><?=$kr['nama_kurir']?></option>
                            <?php } ?>
                        </select>
                    </div>
					<input type="hidden" name="TransactionNumberKurir">
                    <button class="btn btn-success btn-sm btn-block" type="submit">Pilih Kurir</button>
                </form>
            </div>
        </div>
    </div>
</div>