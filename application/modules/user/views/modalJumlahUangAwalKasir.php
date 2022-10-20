<div class="modal-header no-padding header" style="">
	<span class="modal-title"><label class="label-control">Saldo Awal Kasir</label></span>
	<button type="button" class="close" data-dismiss="modal" style="color: #000000;">&times;</button>
</div>
<div class="modal-body body no-padding">
	<div class="row">
		<div class="col-lg-12" style="padding-top: 10px;">
			<div class="col-md-12 no-padding" style="padding-top: 2%;">
                <div class="col-md-12 no-padding"><label class="label-control"><?php echo strtoupper($this->session->userdata()['detail_user']['nama_detuser']); ?></label></div>
            </div>
            <div class="col-md-12 no-padding" style="padding-top: 2%;">
                <div class="col-md-12 no-padding"><label class="label-control"><?php echo strtoupper(tglIndonesia(date('Y-m-d h:s'), '-', ' ')); ?></label></div>
            </div>
			<div class="col-md-12 no-padding" style="padding-top: 2%;">
				<div class="input-group">
	                <span class="input-group-addon">
	                  <b>Rp</b>
	                </span>
	                <input id="jumlah_uang" type="text" data-tipe="decimal" class="form-control text-right" placeholder="SALDO AWAL KASIR" maxlength="13">
	            </div>
			</div>
			<div class="col-md-12 no-padding" style="padding-top: 2%;">
				<button type="button" class="col-md-12 btn btn-primary" onclick="login.saveJmlUangKasir()"><i class="fa fa-check"></i> Simpan</button>
			</div>
		</div>
	</div>
</div>