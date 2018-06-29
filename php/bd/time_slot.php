<?php 
	
	$slots = $config->Products('id, dateFrom, dateTo, created_date, value', 'time_slots');

?>
<div id="btnSlots">
	<button type="button" class="btn btn-primary" onclick="formSlotShow()">add time_slots</button>
</div>
</br>
<div id="formSlot" class="card card-body col-12 col-sm-6 col-lg-6 hidden">
	<form id="time_slotForm" data-parsley-validate="">
			<div class="form-group">
		    <label for="date_ranges">date range</label>
		    		<div class="form-group">
                        <div id="range_slot" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                            <i class="fa fa-calendar"></i>&nbsp;
                            <span></span> <i class="fa fa-caret-down"></i>
                        </div>
                        <input type="hidden" id='hidde_date_field'>
                    </div>
		  	</div>
		  <div class="custom-control custom-checkbox my-1 mr-sm-2">
		    <input type="checkbox" name="time_slot" class="custom-control-input" id="slot_1" value="9am to 2pm">
		    <label class="custom-control-label" for="slot_1">9am to 2pm</label>
		  </div>
		  <div class="custom-control custom-checkbox my-1 mr-sm-2">
		    <input type="checkbox" name="time_slot" class="custom-control-input" id="slot_2" value="3pm to 7pm">
		    <label class="custom-control-label" for="slot_2">3pm to 7pm</label>
		  </div>
		  <div class="custom-control custom-checkbox my-1 mr-sm-2">
		    <input type="checkbox" name="time_slot" class="custom-control-input" id="slot_3" value="8pm to 11pm <small>+ rp. 20.000</small>">
		    <label class="custom-control-label" for="slot_3">8pm to 11pm <small>+ rp. 20.000</small></label>
		  </div>
		  <button type="submit" class="btn btn-sm btn-block btn-primary">Submit</button>
	</form>
</div>
<div class="row" id="listSlot">
	<?php while ($data = $slots->fetch(PDO::FETCH_LAZY)) {

	$time = json_decode($data['value']);
		?>
	<div class="col-12 col-sm-3 col-lg-3" style="margin-bottom: 1%;">
		<div class="card">
		  <div class="card-header bg-info text-white text-center">
		    <?=Date('d M Y', strtotime($data['dateFrom']))?> to <?=Date('d M Y', strtotime($data['dateTo']))?>
		  </div>
		  <div class="card-body text-center" style="height: 170px;">
		  	<div class="text-center">
			    <div class="btn-group " role="group" aria-label="Basic example">
				  <button type="button" class="btn btn-primary" data-toggle="tooltip" data-placement="bottom" title="Edit" ><span class="fa fa-pencil-square"></span></button>
				  <button type="button" class="btn btn-danger" data-toggle="tooltip" data-placement="bottom" title="Delete" ><span class="fa fa-trash"></span></button>
				</div>
			</div>
		</br>
		    <div class="form-group">
		    	<?php foreach ($time as $slot) {
		    		?>
		    	<span class="badge badge-pill badge-primary" style="font-size: 14px;"><?=$slot?></span>
		    	<?php } ?>
		    </div>
		    
		  </div>
		</div>
	</div>
<?php } ?>
</div>