<div class="modal-header no-padding header" style="">
	<span class="modal-title"><label class="label-control">SPLIT BILL</label></span>	
	<!-- <button type="button" class="close" data-dismiss="modal" style="color: #000000;">&times;</button> -->
</div>
<div class="modal-body body no-padding">
	<div class="col-xs-12 no-padding" style="padding-top: 10px; height: 86%;">
		<div class="col-xs-5 no-padding cb_left main" style="height: 100%;" data-kode="<?php echo $data_utama['kode_faktur']; ?>">
			<div class="col-xs-12 no-padding" style="border: 1px solid #dedede; height: 100%;">
				<small>
					<table class="table table-nobordered" style="margin-bottom: 0px;">
						<tbody>
							<?php foreach ($data_utama['jual_item'] as $k_ji => $v_ji): ?>
								<tr data-kode="<?php echo $v_ji['pesanan_item_kode']; ?>" data-kodejp="<?php echo $v_ji['kode_jenis_pesanan']; ?>">
									<td class="col-xs-1 jumlah"><?php echo angkaRibuan($v_ji['jumlah']); ?></td>
									<td class="col-xs-10">
										<span class="menu_nama" data-kode="<?php echo $v_ji['menu_kode']; ?>"><?php echo $v_ji['menu_nama']; ?></span>
										<br>
										<b>@ <span class="harga"><?php echo angkaRibuan($v_ji['harga']); ?></span> | TOTAL : <span class="total" data-sc="<?php echo $v_ji['service_charge']; ?>" data-ppn="<?php echo $v_ji['ppn']; ?>"><?php echo angkaRibuan($v_ji['total']); ?></span></b>
										<br>
										<?php if ( !empty($v_ji['jual_item_detail']) ): ?>
											<?php foreach ($v_ji['jual_item_detail'] as $k_jid => $v_jid): ?>
												<div class="col-md-11 detail no-padding" style="font-size:10px;" data-kode="<?php echo $v_jid['menu_kode'] ?>">
								                    <div class="col-md-12 no-padding" style="padding-left: 15px;">
								                    	<span class="nama_menu"><?php echo $v_jid['menu_nama'] ?></span>
								                    </div>
							                    </div>
											<?php endforeach ?>											
										<?php endif ?>
										<?php if ( !empty($v_ji['request']) ): ?>
											<div class="col-md-11 request no-padding" style="font-size:10px;">
								                <div class="col-md-12 no-padding" style="padding-left: 15px;">
								                	<span class="request"><?php echo strtoupper($v_ji['request']); ?></span>
								                </div>
							                </div>
							            <?php endif ?>
									</td>
									<td class="col-xs-1">
										<button class="col-xs-12 btn btn-primary btn_apply" style="padding: 0px 12px;"><i class="fa fa-plus"></i></button>
									</td>
								</tr>
							<?php endforeach ?>
						</tbody>
					</table>
				</small>
			</div>
		</div>
		<div class="col-xs-7 no-padding cb_right split" style="height: 100%;">
			<div class="col-xs-12 no-padding" style="border: 1px solid #dedede; height: 90%; margin-bottom: 10px;">
				<div class="col-xs-12" style="padding: 8px;">
					<div class="panel-heading no-padding">
						<ul class="nav nav-tabs nav-justified">
							<?php if ( !empty($data_split) ): ?>
								<?php $idx = 1; ?>
								<?php foreach ($data_split as $key => $value): ?>
									<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#bill<?php echo $idx; ?>" data-tab="bill<?php echo $idx; ?>" style="padding: 0px 5px; text-align: left;"><small>Bill <?php echo $idx; ?> - <?php echo $value['member']; ?></small></a></li>
									<?php $idx++; ?>
								<?php endforeach ?>
							<?php endif ?>
						</ul>
					</div>
					<div class="panel-body no-padding">
						<small>
							<div class="tab-content">
								<?php if ( !empty($data_split) ): ?>
									<?php $idx = 1; ?>
									<?php foreach ($data_split as $key => $value): ?>
										<div id="bill<?php echo $idx; ?>" class="tab-pane fade" role="tabpanel" data-kodemember="<?php echo $value['kode_member']; ?>" data-member="<?php echo $value['member']; ?>">
								            <table class="table table-nobordered">
									            <tbody>
									            	<?php foreach ($value['jual_item'] as $k_ji => $v_ji): ?>
														<tr data-kode="<?php echo $v_ji['pesanan_item_kode']; ?>" data-kodejp="<?php echo $v_ji['kode_jenis_pesanan']; ?>">
															<td class="col-xs-1 jumlah"><?php echo angkaRibuan($v_ji['jumlah']); ?></td>
															<td class="col-xs-10">
																<span class="menu_nama" data-kode="<?php echo $v_ji['menu_kode']; ?>"><?php echo $v_ji['menu_nama']; ?></span>
																<br>
																<b>@ <span class="harga"><?php echo angkaRibuan($v_ji['harga']); ?></span> | TOTAL : <span class="total" data-sc="<?php echo $v_ji['service_charge']; ?>" data-ppn="<?php echo $v_ji['ppn']; ?>"><?php echo angkaRibuan($v_ji['total']); ?></span></b>
																<br>
																<?php if ( !empty($v_ji['jual_item_detail']) ): ?>
																	<?php foreach ($v_ji['jual_item_detail'] as $k_jid => $v_jid): ?>
																		<div class="col-md-11 detail no-padding" style="font-size:10px;" data-kode="<?php echo $v_jid['menu_kode'] ?>">
														                    <div class="col-md-12 no-padding" style="padding-left: 15px;">
														                    	<span class="nama_menu"><?php echo $v_jid['menu_nama'] ?></span>
														                    </div>
													                    </div>
																	<?php endforeach ?>											
																<?php endif ?>
																<?php if ( !empty($v_ji['request']) ): ?>
																	<div class="col-md-11 request no-padding" style="font-size:10px;">
														                <div class="col-md-12 no-padding" style="padding-left: 15px;">
														                	<span class="request"><?php echo strtoupper($v_ji['request']); ?></span>
														                </div>
													                </div>
													            <?php endif ?>
															</td>
															<td class="col-xs-1">
																<button class="col-xs-12 btn btn-danger btn_remove" style="padding: 0px 12px;"><i class="fa fa-minus"></i></button>
															</td>
														</tr>
													<?php endforeach ?>
									            </tbody>
								            </table>
							            </div>
										<?php $idx++; ?>
									<?php endforeach ?>
								<?php endif ?>
							</div>
						</small>
					</div>
				</div>
			</div>
			<div class="col-xs-12 no-padding" style="height: 10%;">
				<button class="btn btn-primary pull-right" onclick="bayar.modalSplitBillMember(this)"><i class="fa fa-plus"></i> Tambah</button>
				<button class="btn btn-danger pull-right" onclick="bayar.hapusBill()" style="margin-right: 10px;"><i class="fa fa-trash"></i> Hapus</button>
			</div>
		</div>
	</div>
	<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
	<div class="col-xs-12 no-padding">
		<div class="col-xs-6 no-padding cb_left">
			<button type="button" class="col-xs-12 btn btn-danger" onclick="bayar.modalListBill(this)" data-kode="<?php echo $pesanan_kode; ?>"><i class="fa fa-times"></i> BATAL</button>
		</div>
		<div class="col-xs-6 no-padding cb_right">
			<button type="button" class="col-xs-12 btn btn-primary" data-kode="<?php echo $pesanan_kode; ?>" onclick="bayar.saveSplitBill(this)">Simpan Bill</button>
		</div>
	</div>
</div>