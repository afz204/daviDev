<?php 
	$arrtime = [
		0 => '9 am - 1 pm',
		1 => '2 pm - 5 pm',
		2 => '6 pm - 8 pm',
		3 => '9 pm - 0 am',
		4 => '1 am - 5 am',
		5 => '6 am - 8 am'
	];

	$arrcharge = [
		0 => $config->formatPrice('0'),
		1 => $config->formatPrice('0'),
		2 => $config->formatPrice('0'),
		3 => $config->formatPrice('100000'),
		4 => $config->formatPrice('200000'),
		5 => $config->formatPrice('50000')
	];

	$arrdescription = [
		0 => '-',
		1 => '-',
		2 => '-',
		3 => 'JABODETABEK',
		4 => 'JABODETABEK',
		5 => 'JABODETABEK'
	];

	$slots = $config->Products('*', 'time_slots WHERE Status = 1 ');

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
				<?php foreach($arrtime as $key => $val){ ?>
		  <div class="custom-control custom-checkbox my-1 mr-sm-2">
		    <input type="checkbox" name="time_slot" class="custom-control-input" id="slot_<?=$key?>" value="<?=$key?>">
		    <label class="custom-control-label" for="slot_<?=$key?>"><?=$val?> <?=$arrcharge[$key]?> <?=$arrdescription[$key]?></label>
		  </div>
				<?php } ?>
		  <button type="submit" class="btn btn-sm btn-block btn-primary">Submit</button>
	</form>
</div>
<div class="row" id="listSlot">
	<div class="card" <?=$access['read']?>>
		<div class="card-header">
				List Products
		</div>
		<div class="card-body">
				<div id="listKurir">
						<table id="tableProduct" class="table table-hover<?=$device['device']=='MOBILE' ? 'table-responsive' : ''?> table-condensed table-hover">
								<thead class="thead-light">
								<tr style="text-align: center;">
										<th scope="col" width="10%">Date Slot</th>
										<th scope="col" width="10%">Time Slots</th>
										<th scope="col" width="10%">Price</th>
										<th scope="col" width="10%">Description</th>
										<th scope="col" width="2%">IsActive</th>
										<th scope="col" width="2%">ACTION</th>
								</tr>
								</thead>
								<tbody>
								<?php  while($rows = $slots->fetch(PDO::FETCH_LAZY)){
										$TimeSlots = json_decode($rows['TimeSlots'], true);
										$Price = json_decode($rows['Price'], true);
										$Description = json_decode($rows['Description'], true);
										
										$timeslot = [];
										foreach($TimeSlots as $key => $val) {
											$timeslot[] = $arrtime[$val];
										}
										$timeprice = [];
										foreach($Price as $key => $val) {
											$timeprice[] = $arrcharge[$val];
										}
										$timedesc = [];
										foreach($Description as $key => $val) {
											$timedesc[] = $arrdescription[$val];
										}
										$btn = '<button class="btn btn-sm btn-secondary" onclick="updatetimeslot('. $rows['ID'] .')">nonactive</button>';
										if($rows['IsActive'] == 0 ){
											$btn = '<button class="btn btn-sm btn-success" onclick="updatetimeslot('. $rows['ID'] .')">active</button>';
										}
										$delete = '<button class="btn btn-sm btn-danger" onclick="deletetimeslot('. $rows['ID'] .')">delete</button>';
										?>
										
										<tr>
												<td><?=$config->_formatdate($rows['DateSlots'])?></td>
												<td><?=implode("<br>", $timeslot)?></td>
												<td><?=implode("<br>", $timeprice)?></td>
												<td><?=implode("<br>", $timedesc)?></td>
												<td style="text-align: center;"><?=$btn?></td>
												<td style="text-align: center;"><?=$delete?></td>
										</tr>
								<?php } ?>
								</tbody>
						</table>
				</div>
		</div>
	</div>
</div>