<div class="col-xs-12" style="padding-top: 10px; height: 100%;">
	<div class="col-xs-9 no-padding" style="height: 100%;">
		<div class="col-xs-12 no-padding jenis_pembayaran">
			<div class="col-xs-6 no-padding contain_border cb_left">
				<div class="col-xs-12 border">
					<div class="col-xs-12 no-padding">
						<div class="col-xs-5 no-padding"><label class="control-label">NO. BILL</label></div>
						<div class="col-xs-7 no-padding"><label class="control-label">: <?php echo $data['kode_faktur']; ?></label></div>
					</div>
					<div class="col-xs-12 no-padding">
						<div class="col-xs-5 no-padding"><label class="control-label">MEJA</label></div>
						<div class="col-xs-7 no-padding"><label class="control-label">: -</label></div>
					</div>
					<div class="col-xs-12 no-padding">
						<div class="col-xs-5 no-padding"><label class="control-label">MEMBER / PELANGGAN</label></div>
						<div class="col-xs-7 no-padding"><label class="control-label">: <?php echo $data['member']; ?></label></div>
					</div>
					<div class="col-xs-12 no-padding kode_member" data-val="<?php echo $data['kode_member']; ?>">
						<div class="col-xs-5 no-padding"><label class="control-label">KODE MEMBER</label></div>
						<div class="col-xs-7 no-padding"><label class="control-label">: <?php echo !empty($data['kode_member']) ? $data['kode_member'] : '-'; ?></label></div>
					</div>
					<div class="col-xs-12 no-padding"><hr style="margin-top: 5px; margin-bottom: 10px;"></div>
					<!-- <div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
						<input type="text" class="form-control metode_pembayaran" placeholder="Metode Pembayaran" readonly>
					</div> -->
					<div class="col-xs-12 no-padding">
						<?php $idx = 1; ?>
						<?php foreach ($jenis_kartu as $key => $value): ?>
							<?php
								$tampil = 1;
								if ( $value['kode_jenis_kartu'] == 'saldo_member' ) {
									if ( empty($data['kode_member']) ) {
										$tampil = 0;
									}
								}

								if ( $tampil == 1 ) {
									if ( $idx > 3 ) {
										$idx = 1;
									}

									$class = 'cb_left';
									if ( $idx % 2 == 0 ) {
										$class = 'cb_left cb_right';
									}

									if ( $idx % 3 == 0 ) {
										$class = 'cb_right';
									}

									$idx++;
								}
							?>
							<?php if ( $tampil == 1 ) { ?>
								<div class="col-xs-4 no-padding <?php echo $class; ?>" style="padding-bottom: 10px;">
									<button type="button" class="col-xs-12 btn btn-primary" data-kode="<?php echo $value['kode_jenis_kartu']; ?>" onclick="bayar.modalMetodePembayaran(this)"><?php echo strtoupper($value['nama']); ?></button>
								</div>
							<?php } ?>
						<?php endforeach ?>
					</div>
					<!-- <div class="col-xs-6 no-padding" style="padding-right: 1%;">
						<button type="button" class="col-xs-12 btn btn-primary btn-tunai button" data-aktif="1"><b>TUNAI</b></button>
					</div>
					<div class="col-xs-6 no-padding" style="padding-left: 1%;">
						<button type="button" class="col-xs-12 btn btn-primary btn-kartu button" data-aktif="0"><b>NON TUNAI</b></button>
					</div>
					<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div> -->
				</div>
			</div>
			<div class="col-xs-6 no-padding contain_border cb_right">
				<div class="col-xs-12 border" style="overflow-y: auto;">
					<div class="col-xs-12 no-padding">
						<div class="col-xs-12 no-padding"><label class="control-label">LIST HUTANG MEMBER</label></div>
					</div>
					<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
					<div class="col-xs-12 no-padding">
						<?php if ( !empty($data_hutang) ): ?>
							<small>
								<table class="table table-bordered tbl_hutang" style="margin-bottom: 0px; overflow-y: auto;">
									<thead>
										<tr>
											<th class="col-xs-1"></th>
											<th class="col-xs-3">Kode</th>
											<th class="col-xs-2">Hutang</th>
											<th class="col-xs-2">Bayar</th>
											<th class="col-xs-3">Nominal</th>
										</tr>
									</thead>
									<tbody>
										<?php $total_hutang = 0; $total_bayar = 0; ?>
										<?php foreach ($data_hutang as $key => $value): ?>
											<tr>
												<td>
													<input type="checkbox" class="form-control pilih_hutang" style="margin-top: 0px;" onchange="bayar.hitungTotalTagihan()">
												</td>
												<td class="faktur" data-val="<?php echo $value['faktur_kode']; ?>">
													<div class="col-xs-12 no-padding"><?php echo strtoupper(tglIndonesia($value['tgl_pesan'], '-', ' ')); ?></div>
													<div class="col-xs-12 no-padding"><?php echo $value['faktur_kode']; ?></div>
												</td>
												<td class="text-right hutang" data-val="<?php echo $value['hutang']; ?>"><?php echo angkaRibuan($value['hutang']) ?></td>
												<td class="text-right bayar" data-val="<?php echo $value['bayar']; ?>"><?php echo angkaRibuan($value['bayar']) ?></td>
												<td>
													<!-- <div class="col-xs-12 no-padding"> -->
														<input type="text" class="text-right form-control nominal_bayar_hutang" placeholder="Nominal" data-tipe="integer" style="padding: 6px;" maxlength="11" onkeyup="bayar.cekNominalBayarHutang(this)" data-val="0" readonly>
													<!-- </div> -->
												</td>
											</tr>
											<?php $total_hutang += $value['hutang']; $total_bayar += $value['bayar']; ?>
										<?php endforeach ?>
										<tr>
											<td class="text-right"><b>Total</b></td>
											<td class="text-right"><b><?php echo angkaRibuan($total_hutang) ?></b></td>
											<td class="text-right"><b><?php echo angkaRibuan($total_bayar) ?></b></td>
											<td></td>
										</tr>
									</tbody>
								</table>
							</small>
						<?php else: ?>
							<span>Data tidak ditemukan.</span>
						<?php endif ?>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12 no-padding summary">
			<div class="col-xs-12 no-padding border">
				<div class="col-xs-12 no-padding" style="margin-bottom: 10px;">
					<div class="col-xs-4 no-padding cb_left">
						<div class="col-xs-12 no-padding"><label class="control-label">TAGIHAN BILL</label></div>
						<div class="col-xs-12 no-padding">
							<input type="text" class="form-control text-right tagihan" placeholder="TAGIHAN" data-tipe="integer" value="<?php echo angkaRibuan($data['grand_total']); ?>" readonly>
						</div>
					</div>
					<div class="col-xs-4 no-padding cb_left cb_right">
						<div class="col-xs-12 no-padding"><label class="control-label">DISKON</label></div>
						<div class="col-xs-12 no-padding">
							<input type="text" class="form-control text-right diskon" placeholder="DISKON" data-tipe="integer" value="<?php echo angkaRibuan($data['diskon']); ?>" readonly>
						</div>
					</div>
					<div class="col-xs-4 no-padding cb_right">
						<div class="col-xs-12 no-padding"><label class="control-label">TOTAL TAGIHAN</label></div>
						<div class="col-xs-12 no-padding">
							<input type="text" class="form-control text-right total_tagihan" placeholder="TOTAL TAGIHAN" data-tipe="integer" value="<?php echo angkaRibuan($data['grand_total']); ?>" readonly>
						</div>
					</div>
				</div>
				<div class="col-xs-12 no-padding">
					<div class="col-xs-4 no-padding cb_left">
						<div class="col-xs-12 no-padding"><label class="control-label">TOTAL BAYAR</label></div>
						<div class="col-xs-12 no-padding">
							<input type="text" class="form-control text-right total_bayar" placeholder="TOTAL BAYAR" data-tipe="integer" value="0" readonly>
						</div>
					</div>
					<div class="col-xs-4 no-padding cb_left cb_right">
						<div class="col-xs-12 no-padding"><label class="control-label">KEMBALIAN</label></div>
						<div class="col-xs-12 no-padding">
							<input type="text" class="form-control text-right kembalian" placeholder="KEMBALIAN" data-tipe="integer" value="0" readonly>
						</div>
					</div>
					<div class="col-xs-4 no-padding cb_right">
						<div class="col-xs-12 no-padding"><label class="control-label">SISA TAGIHAN</label></div>
						<div class="col-xs-12 no-padding">
							<input type="text" class="form-control text-right sisa_tagihan" placeholder="SISA TAGIHAN" data-tipe="integer" value="<?php echo angkaRibuan($data['grand_total']); ?>" readonly>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xs-3 no-padding faktur">
		<div class="col-xs-12 no-padding border">
			<div class="col-xs-12 no-padding detail_faktur">
				<div class="col-xs-12 no-padding data">
					<div class="col-xs-12 text-center no-padding"><label class="control-label"><?php echo $data_branch['nama']; ?></label></div>
					<div class="col-xs-12 text-center no-padding font10"><label class="control-label"><?php echo $data_branch['alamat']; ?></label></div>
					<div class="col-xs-12 text-center no-padding font10"><label class="control-label"><?php echo 'Telp. '.$data_branch['telp']; ?></label></div>
					<div class="col-xs-12 text-center no-padding font10"><br></div>
					<div class="col-xs-12 no-padding font10">
						<div class="col-xs-3" style="padding-right: 0px;"><label class="control-label">No. Bill</label></div>
						<div class="col-xs-9"><label class="control-label">: <?php echo $data['kode_faktur']; ?></label></div>
					</div>
					<div class="col-xs-12 no-padding font10">
						<div class="col-xs-3"><label class="control-label">Kasir</label></div>
						<div class="col-xs-9"><label class="control-label">: <?php echo $data_branch['nama_kasir']; ?></label></div>
					</div>
					<div class="col-xs-12 text-center no-padding font10"><hr class="double-dashed"></div>
					<?php foreach ($data['detail'] as $k_det => $v_det): ?>						
						<div class="col-xs-12 no-padding font10">
							<div class="col-xs-3"><label class="control-label">Member</label></div>
							<div class="col-xs-9"><label class="control-label">: <?php echo $v_det['member']; ?></label></div>
						</div>
						<div class="col-xs-12 text-center no-padding font10"><hr class="dashed"></div>
						<div class="col-xs-12 font10">
							<?php foreach ($v_det['jenis_pesanan'] as $k_jp => $v_jp) { ?>
								<div class="col-xs-12 no-padding"><label class="control-label"><?php echo $v_jp['nama']; ?></label></div>
	                			<?php foreach ($v_jp['jual_item'] as $k_ji => $v_ji) { ?>
	                				<div class="col-xs-12 no-padding">
	                					<div class="col-xs-1 no-padding"><label class="control-label"><?php echo $v_ji['jumlah'].'X'; ?></label></div>
	                					<div class="col-xs-7 no-padding"><label class="control-label"><?php echo $v_ji['nama']; ?></label></div>
	                					<div class="col-xs-4 no-padding text-right"><label class="control-label"><?php echo angkaDecimal($v_ji['total']); ?></label></div>
	                				</div>
	                		<?php } ?>
	            		<?php } ?>
						</div>
						<div class="col-xs-12 text-center no-padding font10"><br></div>
					<?php endforeach ?>
					<?php // for ($i=0; $i < 10; $i++) { ?>
						<div class="col-xs-12 text-center no-padding font10"><hr class="double-dashed"></div>
						<div class="col-xs-12 font10">
	        				<div class="col-xs-12 no-padding">
	        					<div class="col-xs-9 text-right no-padding"><label class="control-label">Total Belanja. =</label></div>
	        					<div class="col-xs-3 no-padding text-right tot_belanja"><label class="control-label"><?php echo angkaDecimal($data['total']); ?></label></div>
	        				</div>
	        				<?php if ( $data['ppn'] > 0 ): ?>
	        					<?php
	        						$persen_ppn = ($data['ppn'] / $data['total']) * 100;
	        					?>
		        				<div class="col-xs-12 no-padding">
		        					<div class="col-xs-9 text-right no-padding"><label class="control-label">PB1 (<?php echo angkaDecimal($persen_ppn); ?> %). =</label></div>
		        					<div class="col-xs-3 no-padding text-right ppn"><label class="control-label"><?php echo angkaDecimal($data['ppn']); ?></label></div>
		        				</div>
	        				<?php endif ?>
	        				<?php if ( $data['service_charge'] > 0 ): ?>
	        					<?php
	        						$persen_service_charge = ($data['service_charge'] / $data['total']) * 100;
	        					?>
		        				<div class="col-xs-12 no-padding">
		        					<div class="col-xs-9 text-right no-padding"><label class="control-label">Service Charge (<?php echo angkaDecimal($persen_service_charge); ?> %). =</label></div>
		        					<div class="col-xs-3 no-padding text-right service_charge"><label class="control-label"><?php echo angkaDecimal($data['service_charge']); ?></label></div>
		        				</div>
	        				<?php endif ?>
	        				<div class="col-xs-12 no-padding">
	        					<div class="col-xs-9 text-right no-padding"><label class="control-label">Disc. =</label></div>
	        					<div class="col-xs-3 no-padding text-right"><label class="control-label"><?php echo '('.angkaDecimal($data['diskon']).')'; ?></label></div>
	        				</div>
	        				<div class="col-xs-12 no-padding">
	        					<div class="col-xs-9 text-right no-padding"><label class="control-label">Total Bayar. =</label></div>
	        					<div class="col-xs-3 no-padding text-right"><label class="control-label"><?php echo angkaDecimal($data['grand_total']); ?></label></div>
	        				</div>
	        				<div class="col-xs-12 no-padding">
	        					<div class="col-xs-9 text-right no-padding"><label class="control-label">Jumlah Bayar. =</label></div>
	        					<div class="col-xs-3 no-padding text-right"><label class="control-label jml_bayar" data-val="0">0</label></div>
	        				</div>
	        				<div class="col-xs-12 no-padding">
	        					<div class="col-xs-9 text-right no-padding"><label class="control-label">Kembalian. =</label></div>
	        					<div class="col-xs-3 no-padding text-right"><label class="control-label kembalian" data-val="0">0</label></div>
	        				</div>
						</div>
					<?php // } ?>
					<div class="col-xs-12 text-center no-padding font10"><hr class="double-dashed"></div>
					<div class="col-xs-12 text-center no-padding font10"><label class="control-label">*** TERIMA KASIH ***</label></div>
					<div class="col-xs-12 text-center no-padding font10"><label class="control-label"><?php echo $data_branch['waktu']; ?></label></div>
				</div>
			</div>
			<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
			<div class="col-xs-12 no-padding" style="margin-bottom: 10px;">
				<div class="col-xs-6 no-padding cb_left">
					<button type="button" class="col-xs-12 btn btn-primary" data-kode="<?php echo $data['kode_faktur']; ?>" onclick="bayar.saveHutang(this)"><i class="fa fa-save"></i> Hutang</button>
				</div>
				<div class="col-xs-6 no-padding cb_right">
					<button type="button" class="col-xs-12 btn btn-primary">Diskon Bayar</button>
				</div>
			</div>
			<div class="col-xs-12 no-padding">
				<div class="col-xs-6 no-padding cb_left">
					<button type="button" class="col-xs-12 btn btn-danger" onclick="bayar.batal();"><i class="fa fa-times"></i> Batal</button>
				</div>
				<div class="col-xs-6 no-padding cb_right">
					<button type="button" class="col-xs-12 btn btn-success" data-kode="<?php echo $data['kode_faktur']; ?>" onclick="bayar.modalPembayaran(this)"><i class="fa fa-check"></i> Bayar</button>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- <div class="col-lg-12" style="padding-top: 10px;">
	<div class="col-xs-6 no-padding" style="height: 15%; padding-right: 1%;">
		<div class="col-xs-12 text-center cursor-p btn-tunai button" data-jenis="tunai" style="height: 100%; display: flex; justify-content: center; align-items: center;" data-aktif="1">
			<span><b>TUNAI</b></span>
		</div>
	</div>
	<div class="col-xs-6 no-padding" style="height: 15%; padding-left: 1%;">
		<div class="col-xs-12 text-center cursor-p btn-kartu button" data-jenis="kartu" style="height: 100%; display: flex; justify-content: center; align-items: center;" data-aktif="0">
			<span><b>KARTU</b></span>
		</div>
	</div>
	<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
	<div class="col-xs-12 no-padding form_pembayaran tunai" style="height: 85%;">
		<div class="col-xs-12" style="height: 20%; display: flex; align-items: center; border: 1px solid #dedede; margin-bottom: 10px;">
			<div class="col-xs-4 no-padding text-left">
				<span style="font-size: 28px;"><b>TOTAL</b></span>
			</div>
			<div class="col-xs-8 no-padding text-right">
				<span style="font-size: 28px;"><b>Rp. <span class="gTotal">0,00</span></b></span>
			</div>
		</div>
		<div class="col-xs-12" style="height: 20%; display: flex; align-items: center; border: 1px solid #dedede; margin-bottom: 10px;">
			<div class="col-xs-4 no-padding text-left">
				<span style="font-size: 28px;"><b>SISA TAGIHAN</b></span>
			</div>
			<div class="col-xs-8 no-padding text-right">
				<span style="font-size: 28px;"><b>Rp. <span class="gKurangBayar">0,00</span></b></span>
			</div>
		</div>
		<div class="col-xs-12 cursor-p bayar" style="height: 15%; display: flex; align-items: center; border: 1px solid #dedede; margin-bottom: 10px; background-color: #c7dfff;">
			<div class="col-xs-4 no-padding text-left">
				<span style="font-size: 20px;"><b>BAYAR</b></span>
			</div>
			<div class="col-xs-8 no-padding text-right">
				<span style="font-size: 20px;"><b>Rp. <span class="gBayar">0,00</span></b></span>
			</div>
		</div>
		<div class="col-xs-12" style="height: 15%; display: flex; align-items: center; border: 1px solid #dedede; margin-bottom: 10px;">
			<div class="col-xs-4 no-padding text-left">
				<span style="font-size: 20px;"><b>KEMBALI</b></span>
			</div>
			<div class="col-xs-8 no-padding text-right">
				<span style="font-size: 20px;"><b>Rp. <span class="gKembali">0,00</span></b></span>
			</div>
		</div>
		<div class="col-xs-6 no-padding" style="height: 40px; padding-right: 5px; position: absolute; bottom: 10%; left: 0%;">
			<div class="col-xs-12 text-center cursor-p btn-cancel button" style="height: 100%; display: flex; justify-content: center; align-items: center;">
				<span><b><i class="fa fa-long-arrow-left"></i></b></span>
			</div>
		</div>
		<div class="col-xs-6 no-padding" style="height: 40px; padding-left: 5px; position: absolute; bottom: 10%; right: 0%;">
			<div class="col-xs-12 text-center cursor-p btn-ok-tunai button" style="height: 100%; display: flex; justify-content: center; align-items: center;">
				<span style="font-size: 16px;"><b><i class="fa fa-print"></i></b></span>
			</div>
		</div>
	</div>
	<div class="col-xs-12 no-padding form_pembayaran kartu hide" style="height: 85%;">
		<div class="col-xs-12" style="height: 20%; display: flex; align-items: center; border: 1px solid #dedede; margin-bottom: 10px;">
			<div class="col-xs-4 no-padding text-left">
				<span style="font-size: 28px;"><b>TOTAL</b></span>
			</div>
			<div class="col-xs-8 no-padding text-right">
				<span style="font-size: 28px;"><b>Rp. <span class="gTotal">0,00</span></b></span>
			</div>
		</div>
		<div class="col-xs-12" style="height: 20%; display: flex; align-items: center; border: 1px solid #dedede; margin-bottom: 10px;">
			<div class="col-xs-4 no-padding text-left">
				<span style="font-size: 28px;"><b>SISA TAGIHAN</b></span>
			</div>
			<div class="col-xs-8 no-padding text-right">
				<span style="font-size: 28px;"><b>Rp. <span class="gKurangBayar">0,00</span></b></span>
			</div>
		</div>
		<div class="col-xs-12 cursor-p jenis_kartu" style="height: 15%; display: flex; align-items: center; border: 1px solid #dedede; margin-bottom: 10px; background-color: #c7dfff;">
			<div class="col-xs-4 no-padding text-left">
				<span style="font-size: 20px;"><b>KARTU</b></span>
			</div>
			<div class="col-xs-8 no-padding text-right">
				<span style="font-size: 20px;"><b><span class="gKartu">-</span></b></span>
			</div>
		</div>
		<div class="col-xs-12 cursor-p no_bukti" style="height: 15%; display: flex; align-items: center; border: 1px solid #dedede; margin-bottom: 10px;">
			<div class="col-xs-4 no-padding text-left">
				<span style="font-size: 20px;"><b>NO. BUKTI</b></span>
			</div>
			<div class="col-xs-8 no-padding text-right">
				<span style="font-size: 20px;"><b><span class="gNoBukti">-</span></b></span>
			</div>
		</div>
		<div class="col-xs-6 no-padding" style="height: 40px; padding-right: 5px; position: absolute; bottom: 10%; left: 0%;">
			<div class="col-xs-12 text-center cursor-p btn-cancel button" style="height: 100%; display: flex; justify-content: center; align-items: center;">
				<span><b><i class="fa fa-long-arrow-left"></i></b></span>
			</div>
		</div>
		<div class="col-xs-6 no-padding" style="height: 40px; padding-left: 5px; position: absolute; bottom: 10%; right: 0%;">
			<div class="col-xs-12 text-center cursor-p btn-ok-kartu button" style="height: 100%; display: flex; justify-content: center; align-items: center;">
				<span style="font-size: 16px;"><b><i class="fa fa-print"></i></b></span>
			</div>
		</div>
	</div>
</div> -->