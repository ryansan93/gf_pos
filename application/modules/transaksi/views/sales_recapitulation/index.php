<div class="col-xs-12 no-padding" style="padding: 10px; height: 100%;">
	<div class="col-xs-12 no-padding" style="height: 10%;">
		<div class="col-xs-12 no-padding">
			<div class="col-xs-2 no-padding" style="padding-right: 5px;">
				<div class="input-group date datetimepicker" name="startDate" id="StartDate">
			        <input type="text" class="form-control text-center" placeholder="Start Date" data-required="1" />
			        <span class="input-group-addon">
			            <span class="glyphicon glyphicon-calendar"></span>
			        </span>
			    </div>
			</div>
			<div class="col-xs-2 no-padding" style="padding-left: 5px;">
				<div class="input-group date datetimepicker" name="endDate" id="EndDate">
			        <input type="text" class="form-control text-center" placeholder="End Date" data-required="1" />
			        <span class="input-group-addon">
			            <span class="glyphicon glyphicon-calendar"></span>
			        </span>
			    </div>
			</div>
			<div class="col-xs-2 no-padding" style="padding-left: 10px;">
				<button type="button" class="btn btn-primary" onclick="sr.getLists();"><i class="fa fa-search"></i> Tampilkan</button>
			</div>
		</div>
		<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
	</div>
	<div class="col-xs-12 no-padding" style="height: 90%; overflow-y: auto;">
		<small>
			<table class="table table-bordered" style="margin-bottom: 0px;">
				<thead>
					<tr>
						<th class="col-xs-1">Tgl Trans</th>
						<th class="col-xs-2">Member</th>
						<th class="col-xs-1">Kode Pesanan</th>
						<th class="col-xs-1">Kode Faktur</th>
						<th class="col-xs-1">Kode Faktur Utama</th>
						<th class="col-xs-1">Waitress</th>
						<th class="col-xs-1">Kasir</th>
						<th class="col-xs-1">Grand Total</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan="8">Data tidak ditemukan.</td>
					</tr>
				</tbody>
			</table>
		</small>
	</div>
</div>