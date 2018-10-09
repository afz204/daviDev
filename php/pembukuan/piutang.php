<div class="card card-body">
<div class="card-body" style="padding: 1%;">
<br>
        <form id="Piutang" methods="post" data-parsley-validate="">
            <div class="row">
                <div class="form-group mx-sm-3 mb-2">
                    <div id="datepiutang" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                        <i class="fa fa-calendar"></i>&nbsp;
                        <span></span> <i class="fa fa-caret-down"></i>
                    </div>
                    <input type="hidden" id='daterangepiutang'>
                </div>
                <div class="form-group">
                    <select class="form-control" name="StatusPaid" id="StatusPaid" required>
                        <option value="">:: status order ::</option>
                        <option value="0">UNPAID</option>
                        <option value="1">PAID</option>
                        <option value="2">ALL</option>
                    </select>
                </div>
                <div class="col-12 col-sm-3 col-lg-3">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="submit"><span class="fa fa-search"></span> filter</button>
                    </div>
                </div>
            </div>
        </form>
        
        <table id="TablePiutang" class="table table-bordered  <?=$device['device']=='MOBILE' ? 'table-responsive' : ''?> table-condensed table-hover" style="text-transform: capitalize;">
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
                <th scope="col" width="10%">action</th>
            </tr> 
            </thead>
            <tbody>
            </tbody>
        </table>
        <div class="col-auto my-1">
          <div class="custom-control custom-checkbox mr-sm-2">
            <input type="checkbox" class="custom-control-input" id="allpaid">
            <label class="custom-control-label" for="allpaid">For All Paid</label>
            <button type="button" onClick="allpaid()" class="btn btn-sm btn-primary">PAID</button>
          </div>
        </div>
            <div class="col-6 col-sm-3 col-lg-3 text-right">
                <ul class="list-group mb-3">
                    
                    <li class="list-group-item d-flex justify-content-between" style="padding: .25rem .75rem !important;">
                        <span>Total Selling </span>
                        <strong id="totalPayment">$20</strong>
                    </li>
                </ul>
            </div>
  
  </div>
</div>
</div>

<!-- Modal -->
<div class="modal fade" id="generatepushtoken" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Password</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
            <div class="form-group">
                <input type="text" name="paidDate" class="form-control" placeholder="tgl lunas" required>
            </div>
            <div class="form-group">
                <input type="password" name="passwordpushtoken" autocomplete="off" data-parsley-minlength="2" class="form-control" id="exampleInputEmail1" placeholder="token" required>
                <input type="hidden" name="transactionIDpush" data-parsley-minlength="2" class="form-control" id="exampleInputEmail2" required>
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="generatepushtokenmultiple" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Password</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
            <div class="form-group">
                <input type="text" name="paidDatemultiple" class="form-control" placeholder="tgl lunas" required>
            </div>
            <div class="form-group">
                <input type="password" name="passwordpushtokenmultiple" autocomplete="off" data-parsley-minlength="2" class="form-control" id="exampleInputEmail1" placeholder="token" required>
                <input type="hidden" name="transactionIDpush[]" data-parsley-minlength="2" class="form-control" id="exampleInputEmail2" required>
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>