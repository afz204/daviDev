<?php 
	
	$payment = $config->Products('ID, PaymentName, AccountName, AccountNumber, PaymentImages, Status', 'payment');

?>
<div id="btnPayment">
	<button type="button" class="btn btn-primary" onclick="formPaymentShow()">new payment</button>
</div>
</br>
<div class="d-flex justify-content-center row">
		<div id="showFormPayment" class="card card-body col-12 col-sm-6 col-lg-6 hidden">
		<form id="formPayment" data-parsley-validate="" enctype="multipart/form-data">
			  <div class="form-group">
			    <label for="paymentName">Payment Name</label>
			    <input type="text" class="form-control" id="paymentName" required="">
			  </div>
			  <div class="form-group">
			    <label for="accountName">Account Name</label>
			    <input type="text" class="form-control" id="accountName" required="">
			  </div>
			  <div class="form-group">
			    <label for="accountNumber">Account Number</label>
			    <input type="text" data-parsley-type="number" class="form-control" id="accountNumber" required="">
			  </div>
			  <div class="custom-file">
				  <input type="file" class="custom-file-input" accept="image/*" id="imagesPayment" required="">
				  <label class="custom-file-label" for="imagesPayment">Choose Images Payment</label>
				</div>

			  <div class="form-group" style="padding-top: 5%">
			  	<button type="submit" class="btn btn-sm btn-block btn-primary">Submit</button>
			  </div>
			  
		</form>
	</div>
</div>

<br>
<div class="row" id="listSlot">
	<?php while ($data = $payment->fetch(PDO::FETCH_LAZY)) {

		if(empty($data['Status'])){
			$status = '<span class="badge badge-lg badge-secondary">unsert</span>';
		}elseif ($data['Status'] == 0) {
			# code...
			$status = '<span class="badge badge-lg badge-danger">disable</span>';
		}else{
			$status = '<span class="badge badge-lg badge-success">active</span>';
		}
		?>
	<div class="col-12 col-sm-3 col-lg-3" style="margin-bottom: 1%;">
		<div class="card">
		  <div class="card-header bg-info text-white text-center">
		    <span style="text-transform: uppercase;"><?=$data['PaymentName']?></span>
		  </div>
		  <div class="card-body text-center" style="height: 300px;">
		  	<div class="text-center">
			    <img src="<?=URL?>assets/images/payment/<?=$data['PaymentImages']?>.jpg" class="rounded mx-auto d-block" alt="<?=$data['PaymentName']?>" width="80%">
			    <footer>
		    	<?=$data['AccountName']?>
		    	<?=$data['AccountNumber']?>
		    	<br>
		    	<?=$status?>
		    </footer>
		    <div class="btn-group mr-2" role="group" aria-label="First group" style="padding-top: 1%">
			    <button type="button" onclick="changePaymentStatus(<?=$data['ID']?>, 1)" class="btn btn-sm btn-outline-success">active</button>
			    <button type="button" onclick="changePaymentStatus(<?=$data['ID']?>, 0)" class="btn btn-sm btn-outline-danger">disable</button>
			  </div>
			</div>
		</br>
		    
		  </div>
		</div>
	</div>
<?php } ?>
</div>