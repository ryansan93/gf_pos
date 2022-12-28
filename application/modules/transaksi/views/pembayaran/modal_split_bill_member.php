<div class="modal-header no-padding header" style="">
	<span class="modal-title"><label class="label-control">MEMBER</label></span>	
	<!-- <button type="button" class="close" data-dismiss="modal" style="color: #000000;">&times;</button> -->
</div>
<div class="modal-body body no-padding">
	<div class="col-xs-12 no-padding" style="padding-top: 10px;">
		<div class="col-xs-11 no-padding cb_left">
			<input type="text" class="form-control member" placeholder="MEMBER">
		</div>
		<div class="col-xs-1 no-padding cb_right">
			<button type="button" class="col-xs-12 btn btn-primary" onclick="bayar.modalMemberSplitBill()"><i class="fa fa-address-book-o"></i></button>
		</div>
	</div>
	<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
	<div class="col-xs-12 no-padding">
		<div class="col-xs-6 no-padding cb_left">
			<button type="button" class="col-xs-12 btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> BATAL</button>
		</div>
		<div class="col-xs-6 no-padding cb_right">
			<button type="button" class="col-xs-12 btn btn-primary" onclick="bayar.tambahBill(this)">APPLY</button>
		</div>
	</div>
</div>