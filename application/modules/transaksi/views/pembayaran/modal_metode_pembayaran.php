<div class="modal-header no-padding header" style="">
	<span class="modal-title"><label class="label-control"><?php echo strtoupper($data['nama']); ?></label></span>
	<!-- <button type="button" class="close" data-dismiss="modal" style="color: #000000;">&times;</button> -->
</div>
<div class="modal-body body no-padding">
	<?php if ( !empty($data['kode_jenis_kartu']) ): ?>
		<?php if ( stristr($data['nama'], 'saldo member') !== false ): ?>
			<div class="col-xs-12 no-padding saldo_member" style="padding-top: 10px;">
				<div class="col-xs-6 no-padding cb_left">
					<div class="col-xs-12 no-padding"><label class="label-control">SALDO MEMBER</label></div>
					<div class="col-xs-12 no-padding">
						<input type="text" class="form-control text-right saldo" placeholder="SALDO" value="<?php echo angkaDecimal($saldo_member); ?>" disabled>
					</div>
				</div>
				<div class="col-xs-6 no-padding cb_right">
					<div class="col-xs-12 no-padding"><label class="label-control">SISA SALDO</label></div>
					<div class="col-xs-12 no-padding">
						<input type="text" class="form-control text-right sisa_saldo" placeholder="SISA SALDO" value="<?php echo angkaDecimal($saldo_member); ?>" disabled>
					</div>
				</div>
			</div>
		<?php endif ?>
	<?php endif ?>

	<div class="col-xs-12 no-padding" style="padding-top: 10px;">
		<div class="col-xs-6 no-padding cb_left">
			<div class="col-xs-12 no-padding"><label class="label-control">SISA TAGIHAN</label></div>
			<div class="col-xs-12 no-padding">
				<input type="text" class="form-control text-right total" value="<?php echo angkaDecimal($data['sisa_tagihan']); ?>" readonly>
			</div>
		</div>
		<div class="col-xs-6 no-padding cb_right">
			<div class="col-xs-12 no-padding"><label class="label-control">JUMLAH</label></div>
			<div class="col-xs-12 no-padding">
				<?php 
					$jumlah = $data['sisa_tagihan']; 
					if ( !empty($data['kode_jenis_kartu']) ) {
						if ( $data['kode_jenis_kartu'] == 'saldo_member' or $data['cl'] == 1 ) {
							$jumlah = 0; 
						}
					}
				?>
				<input type="text" class="form-control text-right jml_bayar" value="<?php echo angkaDecimal($jumlah); ?>" data-tipe="integer" data-val="<?php echo $jumlah; ?>" data-jk="<?php echo $data['kode_jenis_kartu']; ?>" onkeyup="bayar.cekNominalBayarHutang(this)" <?php echo ($data['cl'] == 1) ? 'readonly' : null; ?> >
			</div>
		</div>
	</div>
	<?php if ( !empty($data['kode_jenis_kartu']) ): ?>
		<?php if ( $data['cl'] == 0 && stristr($data['nama'], 'tunai') === false && stristr($data['nama'], 'saldo member') === false ): ?>
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
	<?php endif ?>
	<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
	<div class="col-xs-12 no-padding">
		<div class="col-xs-6 no-padding cb_left">
			<button type="button" class="col-xs-12 btn btn-danger" class="close" data-dismiss="modal"><i class="fa fa-times"></i> BATAL</button>
		</div>
		<div class="col-xs-6 no-padding cb_right">
			<button type="button" class="col-xs-12 btn btn-primary" data-nama="<?php echo $data['nama']; ?>" data-kode="<?php echo $data['kode_jenis_kartu']; ?>" data-kategori="<?php echo $data['kategori_jenis_kartu_id']; ?>" data-kodefaktur="<?php echo $kode_faktur; ?>" data-cl="<?php echo $data['cl']; ?>" onclick="bayar.saveMetodePembayaran(this)"><i class="fa fa-save"></i> APPLY</button>
		</div>
	</div>
</div>