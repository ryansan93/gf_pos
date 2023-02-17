<?php if ( !empty($data) && count($data) > 0 ): ?>
	<?php foreach ($data as $key => $value): ?>
		<tr>
			<td><?php echo $value['tgl_trans']; ?></td>
			<td><?php echo $value['member']; ?></td>
			<td><?php echo $value['kode_pesanan']; ?></td>
			<td><?php echo $value['kode_faktur']; ?></td>
			<td><?php echo $value['kode_faktur_utama']; ?></td>
			<td><?php echo $value['nama_waitress']; ?></td>
			<td><?php echo $value['nama_kasir']; ?></td>
			<td class="text-right"><?php echo angkaDecimal($value['grand_total']); ?></td>
		</tr>
	<?php endforeach ?>
<?php else: ?>
	<tr>
		<td colspan="8">Data tidak ditemukan.</td>
	</tr>
<?php endif ?>