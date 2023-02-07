<div class="col-xs-12 no-padding data faktur_hutang" data-faktur="<?php echo $data['kode_faktur']; ?>" style="margin-top: 10px;">
	<div class="col-xs-12 text-center no-padding"><label class="control-label"><?php echo $data_branch['nama']; ?></label></div>
	<div class="col-xs-12 text-center no-padding font10"><label class="control-label"><?php echo $data_branch['alamat']; ?></label></div>
	<div class="col-xs-12 text-center no-padding font10"><label class="control-label"><?php echo 'Telp. '.$data_branch['telp']; ?></label></div>
	<div class="col-xs-12 text-center no-padding font10"><br></div>
	<div class="col-xs-12 font10">
		<table class="table table-nobordered" style="margin-bottom: 0px;">
			<tbody>
				<tr>
					<td class="col-xs-3"><label class="control-label">No. Bill</label></td>
					<td class="col-xs-9"><label class="control-label">: <?php echo $data['kode_faktur'].' (CL)'; ?></label></td>
				</tr>
				<tr>
					<td class="col-xs-3"><label class="control-label">Kasir</label></td>
					<td class="col-xs-9"><label class="control-label">: <?php echo $data['nama_kasir']; ?></label></td>
				</tr>
				<tr>
					<td class="col-xs-3"><label class="control-label">Tanggal</label></td>
					<td class="col-xs-9"><label class="control-label">: <?php echo substr($data_branch['waktu'], 0, 19); ?></label></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-xs-12 text-center font10"><hr class="double-dashed"></div>
	<div class="col-xs-12 font10">
		<?php foreach ($data['detail'] as $k_det => $v_det): ?>
			<table class="table table-nobordered" style="margin-bottom: 0px;">
				<tbody>
					<tr>
						<td class="col-xs-3"><label class="control-label">Order ID</label></td>
						<td class="col-xs-9"><label class="control-label">: <?php echo $v_det['member']; ?></label></td>
					</tr>
				</tbody>
			</table>
			<div class="col-xs-12 text-center no-padding font10"><hr class="dashed"></div>
			<?php foreach ($v_det['jenis_pesanan'] as $k_jp => $v_jp): ?>
				<table class="table table-nobordered" style="margin-bottom: 0px;">
					<tbody>
						<tr>
							<td class="col-xs-12"><label class="control-label"><?php echo $v_jp['nama']; ?></label></td>
						</tr>
					</tbody>
				</table>
				<table class="table table-nobordered" style="margin-bottom: 0px;">
					<tbody>
						<?php foreach ($v_jp['jual_item'] as $k_ji => $v_ji): ?>
							<tr>
								<td class="col-xs-1"><label class="control-label"><?php echo $v_ji['jumlah'].'X'; ?></label></td>
								<td class="col-xs-7"><label class="control-label"><?php echo $v_ji['nama']; ?></label></td>
								<td class="col-xs-4 text-right"><label class="control-label"><?php echo angkaRibuan($v_ji['total_show']); ?></label></td>
							</tr>
						<?php endforeach ?>
					</tbody>
				</table>
			<?php endforeach ?>
		<?php endforeach ?>
	</div>
	<div class="col-xs-12 text-center font10"><hr class="double-dashed"></div>
	<div class="col-xs-12 font10">
		<table class="table table-nobordered" style="margin-bottom: 0px;">
			<tbody>
				<tr>
					<td class="col-xs-8 text-right"><label class="control-label">Total. =</label></td>
					<td class="col-xs-4 text-right"><label class="control-label"><?php echo angkaRibuan($data['grand_total']); ?></label></td>
				</tr>
				<tr>
					<td class="col-xs-8 text-right"><label class="control-label">Bayar. =</label></td>
					<td class="col-xs-4 text-right"><label class="control-label bayar"><?php echo angkaRibuan(0); ?></label></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-xs-12 text-center font10">
		<div class="col-xs-3 no-padding text-left"><label class="control-label">Pembayaran</label></div>
		<div class="col-xs-9 no-padding"><hr class="dashed"></div>
	</div>
	<div class="col-xs-12 text-left font10 pembayaran">
		<label class="control-label"></label>
	</div>
	<div class="col-xs-12 text-left font10"><hr class="dashed"></div>
	<div class="col-xs-12 text-center font10"><label class="control-label">*** TERIMA KASIH ***</label></div>
</div>