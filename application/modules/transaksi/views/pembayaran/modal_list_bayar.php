<div class="modal-header no-padding header" style="">
	<span class="modal-title"><label class="label-control">PESANAN | <?php echo strtoupper(tglIndonesia(date('Y-m-d'), '-', ' ', TRUE)); ?></label></span>
	<button type="button" class="close" data-dismiss="modal" style="color: #000000;">&times;</button>
</div>
<div class="modal-body body no-padding">
	<div class="row">
		<div class="col-lg-12" style="padding-top: 10px;">
			<div class="panel-heading no-padding">
				<ul class="nav nav-tabs nav-justified">
					<li class="nav-item">
						<a class="nav-link active" data-toggle="tab" href="#belum_bayar" data-tab="belum_bayar">BELUM BAYAR</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-toggle="tab" href="#bayar" data-tab="bayar">BAYAR</a>
					</li>
				</ul>
			</div>
			<div class="panel-body no-padding">
				<div class="tab-content">
					<div id="belum_bayar" class="tab-pane fade show active" role="tabpanel" style="padding-top: 10px;">
						<?php $jml_transaksi = 0; $grand_total = 0; ?>
						<div class="col-lg-12 no-padding">
							<div class="col-md-12 search left-inner-addon no-padding" style="margin-bottom: 10px;">
								<i class="fa fa-search"></i><input class="form-control" type="search" data-table="tbl_belum_bayar" placeholder="Search" onkeyup="filter_all(this)">
							</div>
							<small>
								<?php
									$colspan_action = 3;
									if ( $akses_kasir['a_submit'] == 0 ) {
										$colspan_action -= 1;
									}

									if ( $akses_waitress['a_edit'] == 0 ) {
										$colspan_action -= 1;
									}

									if ( $akses_waitress['a_delete'] == 0 ) {
										$colspan_action -= 1;
									}
								?>
								<table class="table table-bordered tbl_belum_bayar" style="margin-bottom: 0px;">
									<thead>
										<tr>
											<th class="col-lg-2">Kasir</th>
											<th class="col-lg-2">No. Pesanan</th>
											<th class="col-lg-3">Pelanggan</th>
											<th class="col-lg-2">Total</th>
											<th colspan="<?php echo $colspan_action; ?>">Action</th>
										</tr>
									</thead>
									<tbody>
										<?php if ( !empty($data['data_belum_bayar']) ): ?>
											<?php foreach ($data['data_belum_bayar'] as $key => $value): ?>
												<tr class="search belum_bayar">
													<td><?php echo $value['kasir']; ?></td>
													<td><?php echo $value['kode_pesanan']; ?></td>
													<td><?php echo $value['pelanggan']; ?></td>
													<td class="text-right total"><?php echo angkaDecimal($value['total']); ?></td>
													<?php if ( $akses_kasir['a_submit'] == 1 ): ?>
														<td class="col-lg-1 text-center">
															<button type="button" class="btn btn-success" style="padding: 1px 0px; width: 100%;" onclick="bayar.modalListBill(this)" data-kode="<?php echo $value['kode_pesanan']; ?>"><i class="fa fa-usd"></i></button>
														</td>
													<?php endif ?>
													<?php if ( $akses_waitress['a_edit'] == 1 ): ?>
														<td class="col-lg-1 text-center">
															<button type="button" class="btn btn-primary" style="padding: 1px 0px; width: 100%;" onclick="jual.edit(this)" data-kode="<?php echo $value['kode_pesanan']; ?>"><i class="fa fa-edit"></i></button>
														</td>
													<?php endif ?>
													<?php if ( $akses_waitress['a_delete'] == 1 ): ?>
														<td class="col-lg-1 text-center">
															<button type="button" class="btn btn-danger" style="padding: 1px 0px; width: 100%;" onclick="jual.delete(this)"><i class="fa fa-trash"></i></button>
														</td>
													<?php endif ?>
												</tr>
												<?php $jml_transaksi++; $grand_total += $value['total']; ?>
											<?php endforeach ?>
										<?php else: ?>
											<tr>
												<td colspan="7">Data tidak ditemukan.</td>
											</tr>
										<?php endif ?>
									</tbody>
								</table>
							</small>
						</div>
						<div class="col-lg-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
						<div class="col-lg-12 no-padding">
							<div class="col-lg-12 no-padding">
								<label class="col-lg-2 no-padding">Jumlah Transaksi</label>
								<label class="col-lg-1 no-padding" style="max-width: 2%;">:</label>
								<label class="col-lg-8 no-padding"><?php echo angkaRibuan($jml_transaksi); ?></label>
							</div>
							<div class="col-lg-12 no-padding">
								<label class="col-lg-2 no-padding">Total Transaksi</label>
								<label class="col-lg-1 no-padding" style="max-width: 2%;">:</label>
								<label class="col-lg-8 no-padding"><?php echo angkaDecimal($grand_total); ?></label>
							</div>
						</div>
					</div>
					<div id="bayar" class="tab-pane fade" role="tabpanel" style="padding-top: 10px;">
						<?php $jml_transaksi = 0; $grand_total = 0; ?>
						<div class="col-lg-12 no-padding">
							<div class="col-md-12 search left-inner-addon no-padding" style="margin-bottom: 10px;">
								<i class="fa fa-search"></i><input class="form-control" type="search" data-table="tbl_bayar" placeholder="Search" onkeyup="filter_all(this)">
							</div>
							<small>
								<table class="table table-bordered tbl_bayar" style="margin-bottom: 0px;">
									<thead>
										<tr>
											<th class="col-lg-2">Kasir</th>
											<th class="col-lg-2">No. Pesanan</th>
											<th class="col-lg-3">Pelanggan</th>
											<th class="col-lg-2">Total</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody>
										<?php if ( !empty($data['data_bayar']) ): ?>
											<?php foreach ($data['data_bayar'] as $key => $value): ?>
												<tr class="search bayar">
													<td><?php echo $value['kasir']; ?></td>
													<td><?php echo $value['kode_pesanan']; ?></td>
													<td><?php echo $value['pelanggan']; ?></td>
													<td class="text-right total"><?php echo angkaDecimal($value['total']); ?></td>
													<td class="col-lg-1 text-center">
														<?php if ( $akses_kasir['a_edit'] == 1 ): ?>
															<button type="button" class="btn btn-success" style="padding: 1px 0px; width: 100%;" onclick="bayar.modalListBill(this)" data-kode="<?php echo $value['kode_pesanan']; ?>"><i class="fa fa-usd"></i></button>
														<?php endif ?>
														</td>
													</td>
												</tr>
												<?php $jml_transaksi++; $grand_total += $value['total']; ?>
											<?php endforeach ?>
										<?php else: ?>
											<tr>
												<td colspan="7">Data tidak ditemukan.</td>
											</tr>
										<?php endif ?>
									</tbody>
								</table>
							</small>
						</div>
						<div class="col-lg-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
						<div class="col-lg-12 no-padding">
							<div class="col-lg-12 no-padding">
								<label class="col-lg-2 no-padding">Jumlah Transaksi</label>
								<label class="col-lg-1 no-padding" style="max-width: 2%;">:</label>
								<label class="col-lg-8 no-padding"><?php echo angkaRibuan($jml_transaksi); ?></label>
							</div>
							<div class="col-lg-12 no-padding">
								<label class="col-lg-2 no-padding">Total Transaksi</label>
								<label class="col-lg-1 no-padding" style="max-width: 2%;">:</label>
								<label class="col-lg-8 no-padding"><?php echo angkaDecimal($grand_total); ?></label>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- <div class="col-lg-12">
			<hr style="margin-top: 10px; margin-bottom: 10px;">
		</div>
		<div class="col-lg-12" style="padding-top: 10px;">
			<button type="button" class="btn btn-success col-lg-12 btn_print_closing_shift"><i class="fa fa-print"></i> Print Closing Kasir</button>
		</div> -->
	</div>
</div>