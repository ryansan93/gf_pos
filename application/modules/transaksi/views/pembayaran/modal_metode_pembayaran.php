<div class="modal-header no-padding header" style="">
	<span class="modal-title"><label class="label-control"><?php echo strtoupper($data['nama']); ?></label></span>
	<!-- <button type="button" class="close" data-dismiss="modal" style="color: #000000;">&times;</button> -->
</div>
<div class="modal-body body no-padding">
	<div class="col-xs-12 no-padding" style="padding-top: 10px;">
		<div class="col-xs-6 no-padding cb_left">
			<div class="col-xs-12 no-padding"><label class="label-control">SISA TAGIHAN</label></div>
			<div class="col-xs-12 no-padding">
				<input type="text" class="form-control text-right total" value="<?php echo angkaRibuan($data['sisa_tagihan']); ?>" readonly>
			</div>
		</div>
		<div class="col-xs-6 no-padding cb_right">
			<div class="col-xs-12 no-padding"><label class="label-control">JUMLAH</label></div>
			<div class="col-xs-12 no-padding">
				<input type="text" class="form-control text-right jml_bayar" value="<?php echo angkaRibuan($data['sisa_tagihan']); ?>" data-tipe="integer" data-val="<?php echo $data['sisa_tagihan']; ?>" onkeyup="bayar.cekNominalBayarHutang(this)">
			</div>
		</div>
	</div>
	<?php if ( !empty($data['kode_jenis_kartu']) ): ?>
		<div class="col-xs-12 no-padding" style="padding-top: 10px;">
			<div class="col-xs-6 no-padding cb_left">
				<div class="col-xs-12 no-padding"><label class="label-control">NO. KARTU</label></div>
				<div class="col-xs-12 no-padding">
					<input type="text" class="form-control no_kartu" placeholder="NO. KARTU">
				</div>
			</div>
			<div class="col-xs-6 no-padding cb_right">
				<div class="col-xs-12 no-padding"><label class="label-control">NAMA KARTU</label></div>
				<div class="col-xs-12 no-padding">
					<input type="text" class="form-control nama_kartu" placeholder="NAMA KARTU">
				</div>
			</div>
		</div>
	<?php endif ?>
	<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
	<div class="col-xs-12 no-padding">
		<div class="col-xs-6 no-padding cb_left">
			<button type="button" class="col-xs-12 btn btn-danger" class="close" data-dismiss="modal"><i class="fa fa-times"></i> BATAL</button>
		</div>
		<div class="col-xs-6 no-padding cb_right">
			<button type="button" class="col-xs-12 btn btn-primary" data-nama="<?php echo $data['nama']; ?>" data-kode="<?php echo $data['kode_jenis_kartu']; ?>" onclick="bayar.saveMetodePembayaran(this)"><i class="fa fa-save"></i> SIMPAN</button>
		</div>
	</div>
</div>