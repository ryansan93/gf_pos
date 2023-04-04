<div class="modal-body body no-padding" style="height: 100%;">
	<div class="col-xs-12 no-padding" style="height: 100%;">
		<div class="col-xs-12 no-padding" style="height: 100%;">
			<div class="col-xs-12 no-padding" style="padding: 5px;">
				<div class="col-xs-12 text-left no-padding">
					<span style="font-weight: bold;">DISKON / PROMO</span>
				</div>
			</div>
			<div class="col-xs-12 no-padding" style="height: 89%; padding-bottom: 10px;">
				<small>
					<div class="col-xs-12 text-center no-padding" style="height: 100%; border-bottom: 1px solid #dddddd;">
						<table class="table table-bordered tbl_diskon" style="margin-bottom: 0px;">
							<thead>
								<tr>
									<th class="col-xs-2">Kode</th>
									<th class="col-xs-4">Nama</th>
									<th class="col-xs-6">Deskripsi</th>
								</tr>
							</thead>
							<tbody>
								<?php if ( !empty($data) ): ?>
									<?php foreach ($data as $key => $value): ?>
										<?php
											$aktif = 0;
											// if ( !empty($data_diskon_aktif) ) {
											// 	if ( in_array($value['kode'], $data_diskon_aktif) ) {
											// 		$aktif = 1;
											// 	}
											// }
										?>

										<tr class="cursor-p data" onclick="bayar.pilihDiskon(this)" data-aktif="<?php echo $aktif; ?>" data-kode="<?php echo $value['kode']; ?>" data-hargahpp="<?php echo $value['harga_hpp']; ?>">
											<td class="col-xs-2 text-left kode"><?php echo $value['kode']; ?></td>
											<td class="col-xs-4 text-left nama"><?php echo $value['nama']; ?></td>
											<td class="col-xs-6 text-left"><?php echo $value['deskripsi']; ?></td>
										</tr>						
									<?php endforeach ?>
								<?php else: ?>
									<tr>
										<td colspan="3">Data tidak ditemukan.</td>
									</tr>
								<?php endif ?>
							</tbody>
						</table>
					</div>
				</small>
			</div>
			<div class="col-xs-6 no-padding" style="padding-right: 5px;">
				<button type="button" class="col-xs-12 btn btn-danger" onclick="$('.modal').modal('hide');"><i class="fa fa-times"></i> BATAL</button>
			</div>
			<div class="col-xs-6 no-padding" style="padding-left: 5px;">
				<button type="button" class="col-xs-12 btn btn-primary" onclick="bayar.applyDiskon(this)" data-kode="<?php echo $kode_faktur; ?>"><i class="fa fa-check"></i> APPLY</button>
			</div>
		</div>
	</div>
</div>