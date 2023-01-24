<div class="modal-body body no-padding" style="height: 100%; font-size: 12px;">
	<div class="row">
		<div class="col-lg-12" style="height: 100%;">
			<div class="col-md-12 no-padding" style="padding: 5px;">
				<div class="col-md-12 text-left no-padding">
					<span style="font-weight: bold;">REQUEST MENU</span>
				</div>
			</div>
			<div class="col-md-12 no-padding">
				<div class="col-md-8 no-padding">
					<?php if ( !empty($data) && count($data) > 0 ): ?>
						<div class="col-md-12 no-padding" style="padding: 5px; height: 400px;">
							<div class="col-md-12 text-center no-padding" style="height: 100%;">
								<table class="table table-bordered" style="margin-bottom: 0px; border: 1px solid #dddddd; height: 100%;">
									<tbody>
										<tr>
											<td class="col-md-2">
												<?php foreach ($data as $k_data => $v_data): ?>
													<div class="col-md-12 no-padding" style="height: 50px; padding-bottom: 5%;">
														<div class="col-md-12 text-center cursor-p button" style="height: 100%; display: flex; justify-content: center; align-items: center;" data-kode="<?php echo $v_data['kode_paket_menu']; ?>">
															<span><b><?php echo $v_data['nama'].' ('.$v_data['max_pilih'].')'; ?></b></span>
														</div>
													</div>
												<?php endforeach ?>
											</td>
											<td class="col-md-10">
												<?php $index = 0; ?>
												<?php foreach ($data as $k_data => $v_data): ?>
													<?php $hide = ($index == 0) ? '' : 'hide'; ?>
													<div class="col-md-12 no-padding detail <?php echo $hide; ?>" data-kode="<?php echo $v_data['kode_paket_menu']; ?>" data-maxpilih="<?php echo $v_data['max_pilih']; ?>">
														<?php $idx = 0; ?>
														<?php foreach ($v_data['isi_paket_menu'] as $k => $val): ?>
															<?php if ( $idx == 0 ): ?>
																<div class="col-md-12 no-padding" style="margin-bottom: 10px;">
															<?php endif ?>
															<?php if ( isset($val['menu']['nama']) ): ?>
																<div class="col-md-4 text-left cursor-p no-padding menu_det" style="height: 100%; padding: 0% 1% 0% 1%;">
																	<table class="table table-bordered" style="margin-bottom: 0px;">
																		<tbody>
																			<tr>
																				<td class="pilih" data-pilih="0" data-kode="<?php echo $val['menu_kode']; ?>">
																					<span><b><?php echo $val['menu']['nama']; ?></b></span>
																					<i class="fa fa-check-circle hide"></i>
																				</td>
																			</tr>
																			<tr>
																				<td>
																					<div class="col-md-6 no-padding jumlah" style="height: 25px; display: flex; justify-content: center; align-items: center; background-color: #ffffff; border: 1px solid #dedede;" data-min="<?php echo $val['jumlah_min']; ?>" data-max="<?php echo $val['jumlah_max']; ?>">
																						<span style="font-weight: bold;"><?php echo angkaRibuan($val['jumlah_min']); ?></span>
																					</div>
																					<div class="col-md-3 no-padding cursor-p btn-remove disable" style="height: 25px; display: flex; justify-content: center; align-items: center;">
																						<i class="fa fa-minus"></i>
																					</div>
																					<div class="col-md-3 no-padding cursor-p btn-add disable" style="height: 25px; display: flex; justify-content: center; align-items: center;">
																						<i class="fa fa-plus"></i>
																					</div>
																				</td>
																			</tr>
																		</tbody>
																	</table> 
																</div>
															<?php endif ?>
															<?php $idx++; ?>

															<?php if ( $idx > 2 ): ?>
																<?php $idx = 0; ?>
																</div>
															<?php endif ?>

															<?php if ( $idx > 2 ): ?>
															<?php endif ?>
														<?php endforeach ?>
													</div>
													<?php $index++; ?>
												<?php endforeach ?>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					<?php endif ?>
					<div class="col-md-12 no-padding" style="padding: 5px;">
						<textarea class="form-control request uppercase" placeholder="Request Khusus"></textarea>
					</div>
				</div>
				<div class="col-md-4 no-padding">
					<div class="col-lg-12 no-padding">
						<div class="col-md-8 no-padding" style="height: 40px; padding: 5px;">
							<div class="col-md-12 text-center jumlah_pesanan button" style="height: 100%; display: flex; justify-content: center; align-items: center; background-color: #ffffff; border-color: #dedede;">
								<span></span>
							</div>
						</div>
						<div class="col-md-4 no-padding" style="height: 40px; padding: 5px;">
							<div class="col-md-12 text-center cursor-p btn-erase button" style="height: 100%; display: flex; justify-content: center; align-items: center;">
								<span><b><i class="fa fa-long-arrow-left"></i></b></span>
							</div>
						</div>
					</div>
					<div class="col-lg-12"><hr style="margin-top: 5px; margin-bottom: 5px;"></div>
					<div class="col-lg-12 no-padding">
						<div class="col-md-4 no-padding" style="height: 75px; padding: 5px;">
							<div class="col-md-12 text-center cursor-p btn-angka button" style="height: 100%; display: flex; justify-content: center; align-items: center;">
								<span><b>1</b></span>
							</div>
						</div>
						<div class="col-md-4 no-padding" style="height: 75px; padding: 5px;">
							<div class="col-md-12 text-center cursor-p btn-angka button" style="height: 100%; display: flex; justify-content: center; align-items: center;">
								<span><b>2</b></span>
							</div>
						</div>
						<div class="col-md-4 no-padding" style="height: 75px; padding: 5px;">
							<div class="col-md-12 text-center cursor-p btn-angka button" style="height: 100%; display: flex; justify-content: center; align-items: center;">
								<span><b>3</b></span>
							</div>
						</div>
					</div>
					<div class="col-lg-12 no-padding">
						<div class="col-md-4 no-padding" style="height: 75px; padding: 5px;">
							<div class="col-md-12 text-center cursor-p btn-angka button" style="height: 100%; display: flex; justify-content: center; align-items: center;">
								<span><b>4</b></span>
							</div>
						</div>
						<div class="col-md-4 no-padding" style="height: 75px; padding: 5px;">
							<div class="col-md-12 text-center cursor-p btn-angka button" style="height: 100%; display: flex; justify-content: center; align-items: center;">
								<span><b>5</b></span>
							</div>
						</div>
						<div class="col-md-4 no-padding" style="height: 75px; padding: 5px;">
							<div class="col-md-12 text-center cursor-p btn-angka button" style="height: 100%; display: flex; justify-content: center; align-items: center;">
								<span><b>6</b></span>
							</div>
						</div>
					</div>
					<div class="col-lg-12 no-padding">
						<div class="col-md-4 no-padding" style="height: 75px; padding: 5px;">
							<div class="col-md-12 text-center cursor-p btn-angka button" style="height: 100%; display: flex; justify-content: center; align-items: center;">
								<span><b>7</b></span>
							</div>
						</div>
						<div class="col-md-4 no-padding" style="height: 75px; padding: 5px;">
							<div class="col-md-12 text-center cursor-p btn-angka button" style="height: 100%; display: flex; justify-content: center; align-items: center;">
								<span><b>8</b></span>
							</div>
						</div>
						<div class="col-md-4 no-padding" style="height: 75px; padding: 5px;">
							<div class="col-md-12 text-center cursor-p btn-angka button" style="height: 100%; display: flex; justify-content: center; align-items: center;">
								<span><b>9</b></span>
							</div>
						</div>
					</div>
					<div class="col-lg-12 no-padding">
						<div class="col-md-4 no-padding" style="height: 75px; padding: 5px;">
							<!-- <div class="col-md-12 text-center cursor-p btn-cancel button" style="height: 100%; display: flex; justify-content: center; align-items: center;">
								<span><b><i class="fa fa-times"></i></b></span>
							</div> -->
						</div>
						<div class="col-md-4 no-padding" style="height: 75px; padding: 5px;">
							<div class="col-md-12 text-center cursor-p btn-angka button" style="height: 100%; display: flex; justify-content: center; align-items: center;">
								<span><b>0</b></span>
							</div>
						</div>
						<div class="col-md-4 no-padding" style="height: 75px; padding: 5px;">
							<!-- <div class="col-md-12 text-center cursor-p btn-ok button" style="height: 100%; display: flex; justify-content: center; align-items: center;">
								<span><b><i class="fa fa-check"></i></b></span>
							</div> -->
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-6 no-padding" style="height: 40px; padding: 5px;">
				<div class="col-md-12 text-center cursor-p btn-cancel button" style="height: 100%; display: flex; justify-content: center; align-items: center;">
					<span><b><i class="fa fa-long-arrow-left"></i></b></span>
				</div>
			</div>
			<div class="col-md-6 no-padding" style="height: 40px; padding: 5px;">
				<div class="col-md-12 text-center cursor-p btn-ok button" style="height: 100%; display: flex; justify-content: center; align-items: center;">
					<span><b><i class="fa fa-long-arrow-right"></i></b></span>
				</div>
			</div>
		</div>
	</div>
</div>