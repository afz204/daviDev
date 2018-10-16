<?php 

$arrstatusorder = array(
    0 => 'New order',
    1 => 'On Production',
    2 => 'On Delivery',
    3 => 'Success',
    4 => 'Return',
    5 => 'Complain',
    6 => 'Cancel',
    99 => 'not ready'
);

$formR = '';
if(isset($_GET['type'])) $formR = 'hidden';


?>

<style>
    .widget{
        display : block;
    }
    .widget-icon{
        width   : 40%;
        float   : left;
        color   : #17a2b8;
    }
    .widget-content {
        width   : 60%;
        float   : left;
    }

    .widget-content .card-title{
        color   : #17a2b8;
    }
    .widget-content .card-text{
        color   : #a9b5b7;
        font-size : 14px;
    }
</style>
<div class="row">
    <div class="col-6 col-md-3 col-lg-3">
        <div class="card border-info mb-3">
            <div class="card-body">
                <div class="widget">
                    <div class="widget-icon">
                        <span class="fa fa-5x fa-pie-chart"></span>
                    </div>
                    <div class="widget-content">
                        <h5 class="card-title">Total Order</h5>
                        <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 col-lg-3">
        <div class="card border-info mb-3">
            <div class="card-body">
                <div class="widget">
                    <div class="widget-icon">
                        <span class="fa fa-5x fa-check-square-o"></span>
                    </div>
                    <div class="widget-content">
                        <h5 class="card-title">Order Success</h5>
                        <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3 col-lg-3">
        <div class="card border-info mb-3">
            <div class="card-body">
                <div class="widget">
                    <div class="widget-icon">
                        <span class="fa fa-5x fa-comments-o"></span>
                    </div>
                    <div class="widget-content">
                        <h5 class="card-title">Order Complain</h5>
                        <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3 col-lg-3">
        <div class="card border-info mb-3">
            <div class="card-body">
                <div class="widget">
                    <div class="widget-icon">
                        <span class="fa fa-5x fa-history"></span>
                    </div>
                    <div class="widget-content">
                        <h5 class="card-title">Order Archivement</h5>
                        <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>

<div class="card">
    <div class="card-header" <?=$access['read']?>>
        List Order
    </div>
    <div class="card-body">
        <form id="caridata" class="form-inline" methods="post" data-parsley-validate="" style="    padding-left: 1.2222222%;">
            <div class="form-group mx-sm-3 mb-2">
                    <input type="text" class="form-control" id="invoicenomor" name="invoicenomor" placeholder="Nomor Invoice">
            </div>
            <div class="form-group mx-sm-3 mb-2">
                <input type="text" class="form-control" id="sendername" name="sendername" placeholder="Sender Name">
            </div>
            <div class="form-group mx-sm-3 mb-2">
                <input type="text" class="form-control" id="address" name="address" placeholder="Address">
            </div>
            <div class="form-group mb-2">
                <select name="typeReport" id="typeReport" class="form-control">
                    <option value="">status</option>
                    <?php foreach($arrstatusorder as $key => $val) { ?>
                        <option value="<?=$key?>"><?=$val?></option>
                    <?php } ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary mb-2">cari</button>
        </form>
        <div id="listOrder">
            <table id="tableSearch" class="table table-hover<?=$device['device']=='MOBILE' ? 'table-responsive' : ''?> table-condensed table-hover">
                <thead class="thead-light">
                <tr style="text-transform: lowercase;">
                    <th scope="col">Invoice</th>
                    <th scope="col">Product Name</th>
                    <th scope="col">Sender Name</th>
                    <th scope="col">Price</th>
                    <th scope="col">Qty</th>
                    <th scope="col">Total Payment</th>
                    <th scope="col">delivery_date</th>
                    <th scope="col">status_paid</th>
                    <th scope="col">created order</th>
                    <th scope="col">created by</th>
                    <th scope="col">florist</th>
                    <th scope="col">action</th>
                </tr>
                </thead>
                <tbody>
               
                </tbody>
            </table>
        </div>
    </div>
</div>