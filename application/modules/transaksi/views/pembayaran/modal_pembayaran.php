<div class="modal-header no-padding header" style="">
	<span class="modal-title"><label class="label-control">KONFIRMASI PEMBAYARAN</label></span>	
	<!-- <button type="button" class="close" data-dismiss="modal" style="color: #000000;">&times;</button> -->
</div>
<div class="modal-body body no-padding">
	<div class="col-xs-12 no-padding" style="padding-top: 10px;">
		<small>
			<table class="table table-bordered" style="margin-bottom: 0px;">
				<thead>
					<tr>
						<th class="col-xs-3">Metode</th>
						<th class="col-xs-6">Keterangan</th>
						<th class="col-xs-2">Jumlah</th>
						<th class="col-xs-1">Jumlah</th>
					</tr>
				</thead>
				<tbody>
					<?php if ( isset($data['dataMetodeBayar']) ): ?>
						<?php foreach ($data['dataMetodeBayar'] as $key => $value): ?>
							<?php if ( !empty($value) ): ?>
								<tr>
									<td><?php echo $value['nama']; ?></td>
									<td>
										<?php if ( !empty($value['kode_jenis_kartu']) ): ?>
											<div class="col-xs-12 no-padding"><?php echo $value['no_kartu']; ?></div>
											<div class="col-xs-12 no-padding"><?php echo $value['nama_kartu']; ?></div>
										<?php endif ?>
									</td>
									<td class="text-right"><?php echo angkaRibuan($value['jumlah']); ?></td>
									<td class="text-right">
										<button type="button" class="col-xs-12 btn btn-danger" onclick="bayar.hapusMetodePembayaran(this)" data-id="<?php echo $key; ?>"><i class="fa fa-times"></i></button>
									</td>
								</tr>
							<?php endif ?>
						<?php endforeach ?>
					<?php else: ?>
						<tr>
							<td colspan="3">Data tidak ditemukan.</td>
						</tr>
					<?php endif ?>
				</tbody>
			</table>
		</small>
	</div>
	<div class="col-xs-12 no-padding" style="padding-top: 10px;">
		<div class="col-md-12 no-padding" style="margin-bottom: 10px;">
			<div class="col-md-6 no-padding cb_left">
				<div class="col-md-12 no-padding"><label class="control-label">DISKON</label></div>
				<div class="col-md-12 no-padding">
					<input type="text" class="form-control text-right diskon" placeholder="DISKON" data-tipe="integer" value="0" readonly>
				</div>
			</div>
			<div class="col-md-6 no-padding cb_right">
				<div class="col-md-12 no-padding"><label class="control-label">TOTAL TAGIHAN</label></div>
				<div class="col-md-12 no-padding">
					<input type="text" class="form-control text-right total_tagihan" placeholder="TOTAL" data-tipe="integer" value="<?php echo angkaRibuan($data['jml_tagihan']); ?>" readonly>
				</div>
			</div>
		</div>
		<div class="col-md-12 no-padding">
			<div class="col-md-6 no-padding cb_left">
				<div class="col-md-12 no-padding"><label class="control-label">TOTAL BAYAR</label></div>
				<div class="col-md-12 no-padding">
					<input type="text" class="form-control text-right total_bayar" placeholder="TOTAL BAYAR" data-tipe="integer" value="<?php echo angkaRibuan($data['jml_bayar']); ?>" readonly>
				</div>
			</div>
			<div class="col-md-6 no-padding cb_right">
				<div class="col-md-12 no-padding"><label class="control-label">KEMBALIAN</label></div>
				<div class="col-md-12 no-padding">
					<input type="text" class="form-control text-right kembalian" placeholder="KEMBALIAN" data-tipe="integer" value="<?php echo angkaRibuan($data['kembalian']); ?>" readonly>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
	<div class="col-xs-12 no-padding">
		<div class="col-xs-6 no-padding cb_left">
			<button type="button" class="col-xs-12 btn btn-danger" class="close" data-dismiss="modal"><i class="fa fa-times"></i> BATAL</button>
		</div>
		<div class="col-xs-6 no-padding cb_right">
			<button type="button" class="col-xs-12 btn btn-primary" data-kode="<?php echo $data['faktur_kode']; ?>" onclick="bayar.savePembayaran(this)"><i class="fa fa-save"></i> PROSES</button>
		</div>
	</div>
</div>