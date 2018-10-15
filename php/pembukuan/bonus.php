<?php 
$kurir = $config->Products('*', 'users where status = 1');
?>
<div class="card card-body">
<div class="card-body" style="padding: 1%;">
<br>
    <form id="Bonus" methods="post" data-parsley-validate="">
            <div class="row">
                <div class="form-group mx-sm-3 mb-2">
                    <div id="datebonus" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                        <i class="fa fa-calendar"></i>&nbsp;
                        <span></span> <i class="fa fa-caret-down"></i>
                    </div>
                    <input type="hidden" id='daterangebonus'>
                </div>
                <div class="form-group">
                    <select class="form-control" name="adminbonus" id="adminbonus" required>
                        <option value="">:: Admin ::</option>
                        <option value="0">:: All Admin ::</option>
                        <?php while ($row = $kurir->fetch(PDO::FETCH_LAZY)){ ?>
                        <option value="<?=$row->id?>"><?=$row->name?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-12 col-sm-3 col-lg-3">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="submit"><span class="fa fa-search"></span> filter</button>
                        <button class="btn btn-outline-success" onClick="exportrevenue('exportbonus')" type="button"><span class="fa fa-download"></span> export</button>
                    </div>
                </div>
            </div>
        </form>
        <table id="tableBonus" class="table table-bordered  <?=$device['device']=='MOBILE' ? 'table-responsive' : ''?> table-condensed table-hover" style="text-transform: capitalize;">
            <thead class="thead-light">
            <tr style="text-transform: lowercase;">
                <th scope="col" width="10%">invoice</th>
                <th scope="col" width="10%">pengirim</th>
                <th scope="col" width="10%">admin</th>
                <th scope="col" width="10%">tgl order</th>
                <th scope="col" width="10%">tgl kirim</th>
                <th scope="col" width="10%">status paid</th>
                <th scope="col" width="10%">tgl lunas</th>
                <th scope="col" width="10%">cost price</th>
                <th scope="col" width="10%">selling price</th>
                <th scope="col" width="10%">mp</th>
            </tr> 
            </thead>
            <tbody>
            </tbody>
        </table>
            <div class="col-6 col-sm-3 col-lg-3 text-right">
                <ul class="list-group mb-3">
                    
                    <li class="list-group-item d-flex justify-content-between" style="padding: .25rem .75rem !important;">
                        <span>Total Cost Price </span>
                        <strong id="totalPayment">$20</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between" style="padding: .25rem .75rem !important;">
                        <span>Total Selling Price </span>
                        <strong id="totalPerKurir">$20</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between" style="padding: .25rem .75rem !important;">
                        <span>Total MP </span>
                        <strong id="selisih">$20</strong>
                    </li>
                </ul>
            </div>
  
  </div>
</div>
</div>