<div class="modal-header no-padding header" style="">
	<span class="modal-title"><label class="label-control">JUMLAH BARANG</label></span>	
	<!-- <button type="button" class="close" data-dismiss="modal" style="color: #000000;">&times;</button> -->
</div>
<div class="modal-body body no-padding">
	<div class="col-xs-12 no-padding" style="padding-top: 10px;">
		<input type="text" class="form-control text-center jumlah" data-tipe="integer" data-required="1" placeholder="JUMLAH">
	</div>
	<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
	<div class="col-xs-12 no-padding">
		<div class="col-xs-6 no-padding cb_left">
			<button type="button" class="col-xs-12 btn btn-danger" onclick="bayar.closeModal(this)"><i class="fa fa-times"></i> BATAL</button>
		</div>
		<div class="col-xs-6 no-padding cb_right">
			<button type="button" class="col-xs-12 btn btn-primary" data-jumlah="<?php echo $jumlah; ?>" data-kode="<?php echo $data['kode_faktur_item']; ?>" onclick="bayar.applyItem(this)">PASANG</button>
		</div>
	</div>
</div>