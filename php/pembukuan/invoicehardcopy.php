
<div class="card card-body">
<div class="card-body" style="padding: 1%;">
<br>
        <form id="HardCopy" methods="post" data-parsley-validate="">
            <div class="row">
                <div class="form-group mx-sm-3 mb-2">
                    <div id="daterevenue" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                        <i class="fa fa-calendar"></i>&nbsp;
                        <span></span> <i class="fa fa-caret-down"></i>
                    </div>
                    <input type="hidden" id='daterangerevenue'>
                </div>
                <div class="col-12 col-sm-3 col-lg-3">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="submit"><span class="fa fa-search"></span> filter</button>
                        <button class="btn btn-outline-success" onClick="exportrevenue('exportrevenue')" type="button"><span class="fa fa-download"></span> export</button>
                    </div>
                </div>
            </div>
        </form>
        <table id="TableHardCopy" class="table table-bordered  <?=$device['device']=='MOBILE' ? 'table-responsive' : ''?> table-condensed table-hover" style="text-transform: capitalize;">
            <thead class="thead-light">
            <tr style="text-transform: lowercase;">
                <th scope="col" width="10%">invoice</th>
                <th scope="col" width="10%">Corporate</th>
                <th scope="col" width="10%">PIC</th>
                <th scope="col" width="10%">GrandTotal</th>
                <th scope="col" width="10%">order date</th>
                <th scope="col" width="10%">delivery date</th>
                <th scope="col" width="10%">status paid</th>
                <th scope="col" width="10%">no. resi</th>
                <th scope="col" width="10%">tanggal kirim resi</th>
                <th scope="col" width="10%">action</th>
            </tr> 
            </thead>
            <tbody>
            </tbody>
        </table>  
  </div>
</div>
</div>

<div class="modal fade" id="modalinputresi" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Nomor Resi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
            <div class="form-group">
                <input type="text" name="residate" class="form-control" placeholder="tanggal kirim" required>
            </div>
            <div class="form-group">
                <input type="text" name="nomorresi" autocomplete="off" data-parsley-minlength="2" class="form-control" id="exampleInputEmail1" placeholder="nomor resi" required>
                <input type="hidden" name="transactionIDpush" data-parsley-minlength="2" class="form-control" id="exampleInputEmail2" required>
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>