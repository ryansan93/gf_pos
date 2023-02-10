<div class="col-xs-12" style="padding-top: 10px; height: 100%;">
	<div class="col-xs-9 no-padding" style="height: 100%;">
		<div class="col-xs-12 no-padding jenis_pembayaran">
			<div class="col-xs-6 no-padding contain_border cb_left">
				<div class="col-xs-12 border">
					<div class="col-xs-12 no-padding kode_faktur" data-val="<?php echo $data['kode_faktur']; ?>">
						<div class="col-xs-5 no-padding"><label class="control-label">NO. BILL</label></div>
						<div class="col-xs-7 no-padding"><label class="control-label">: <?php echo $data['kode_faktur']; ?></label></div>
					</div>
					<div class="col-xs-12 no-padding">
						<div class="col-xs-5 no-padding"><label class="control-label">MEJA</label></div>
						<div class="col-xs-7 no-padding"><label class="control-label">: <?php echo $data['nama_meja']; ?></label></div>
					</div>
					<div class="col-xs-12 no-padding member" data-val="<?php echo $data['member']; ?>">
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
							?>
							<div class="col-xs-4 no-padding <?php echo $class; ?>" style="padding-bottom: 10px;">
								<button type="button" class="col-xs-12 btn btn-primary" data-kode="<?php echo $value['kode_jenis_kartu']; ?>" data-kategori="<?php echo $value['kategori_jenis_kartu_id']; ?>" data-kodefaktur="<?php echo $data['kode_faktur']; ?>" data-cl="<?php echo $value['cl']; ?>" onclick="bayar.modalMetodePembayaran(this)"><?php echo strtoupper($value['nama']); ?></button>
							</div>
						<?php endforeach ?>
						<!-- <div class="col-xs-4 no-padding cb_left">
							<button type="button" class="col-xs-12 btn btn-primary" data-kode="<?php echo $data['kode_faktur']; ?>" onclick="bayar.saveHutang(this)">CL</button>
						</div> -->
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
				<div class="col-xs-12 border">
					<div class="col-xs-12 no-padding">
						<div class="col-xs-12 no-padding"><label class="control-label">LIST HUTANG MEMBER</label></div>
					</div>
					<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
					<div class="col-xs-12 no-padding">
						<?php if ( !empty($data_hutang) ): ?>
							<small>
								<table class="table table-bordered tbl_hutang" style="margin-bottom: 0px;">
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
													<input type="checkbox" class="form-control pilih_hutang" style="margin-top: 0px;" onchange="bayar.formFakturHutang(this)" checked>
												</td>
												<td class="faktur" data-val="<?php echo $value['faktur_kode']; ?>">
													<div class="col-xs-12 no-padding"><?php echo strtoupper(tglIndonesia($value['tgl_pesan'], '-', ' ')); ?></div>
													<div class="col-xs-12 no-padding"><?php echo $value['faktur_kode']; ?></div>
												</td>
												<td class="text-right hutang" data-val="<?php echo $value['hutang']; ?>"><?php echo angkaRibuan($value['hutang']) ?></td>
												<td class="text-right bayar" data-val="<?php echo $value['sudah_bayar']; ?>"><?php echo angkaRibuan($value['sudah_bayar']) ?></td>
												<td>
													<!-- <div class="col-xs-12 no-padding"> -->
														<input type="text" class="text-right form-control nominal_bayar_hutang" placeholder="Nominal" data-tipe="integer" style="padding: 6px;" maxlength="11" onkeyup="bayar.cekNominalBayarHutang(this)" data-val="<?php echo ($value['hutang'] - $value['sudah_bayar']); ?>" value="<?php echo angkaRibuan($value['bayar']) ?>">
													<!-- </div> -->
												</td>
											</tr>
											<?php $total_hutang += $value['hutang']; $total_bayar += $value['sudah_bayar']; ?>
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
				<?php
					$bayar = !empty($data_bayar) ? $data_bayar['jml_bayar'] : 0;

					$jml_tagihan = $data_bayar->jml_tagihan;
					$sisa_tagihan = ($data_bayar->jml_tagihan > $bayar) ? $data_bayar->jml_tagihan - $bayar : 0;
					$kembalian = ($bayar > 0 && $data_bayar->jml_tagihan > 0 && $bayar > $data_bayar->jml_tagihan) ? $bayar - $data_bayar->jml_tagihan : 0;
				?>

				<div class="col-xs-12 no-padding" style="margin-bottom: 10px;">
					<div class="col-xs-4 no-padding cb_left">
						<div class="col-xs-12 no-padding"><label class="control-label">TAGIHAN BILL</label></div>
						<div class="col-xs-12 no-padding">
							<input type="text" class="form-control text-right tagihan" placeholder="TAGIHAN" data-tipe="integer" value="<?php echo angkaRibuan($data_bayar->total); ?>" readonly>
						</div>
					</div>
					<div class="col-xs-4 no-padding cb_left cb_right">
						<div class="col-xs-12 no-padding"><label class="control-label">DISKON</label></div>
						<div class="col-xs-12 no-padding">
							<input type="text" class="form-control text-right diskon" placeholder="DISKON" data-tipe="integer" value="<?php echo angkaRibuan($data_bayar->diskon); ?>" readonly>
						</div>
					</div>
					<div class="col-xs-4 no-padding cb_right">
						<div class="col-xs-12 no-padding"><label class="control-label">TOTAL TAGIHAN</label></div>
						<div class="col-xs-12 no-padding">
							<input type="text" class="form-control text-right total_tagihan" placeholder="TOTAL TAGIHAN" data-tipe="integer" value="<?php echo angkaRibuan($data_bayar->jml_tagihan); ?>" readonly>
						</div>
					</div>
				</div>
				<div class="col-xs-12 no-padding">
					<div class="col-xs-4 no-padding cb_left">
						<div class="col-xs-12 no-padding"><label class="control-label">TOTAL BAYAR</label></div>
						<div class="col-xs-12 no-padding">
							<input type="text" class="form-control text-right total_bayar" placeholder="TOTAL BAYAR" data-tipe="integer" value="<?php echo angkaRibuan($bayar); ?>" readonly>
						</div>
					</div>
					<div class="col-xs-4 no-padding cb_left cb_right">
						<div class="col-xs-12 no-padding"><label class="control-label">KEMBALIAN</label></div>
						<div class="col-xs-12 no-padding">
							<input type="text" class="form-control text-right kembalian" placeholder="KEMBALIAN" data-tipe="integer" value="<?php echo angkaRibuan($kembalian); ?>" readonly>
						</div>
					</div>
					<div class="col-xs-4 no-padding cb_right">
						<div class="col-xs-12 no-padding"><label class="control-label">SISA TAGIHAN</label></div>
						<div class="col-xs-12 no-padding">
							<input type="text" class="form-control text-right sisa_tagihan" placeholder="SISA TAGIHAN" data-tipe="integer" value="<?php echo angkaRibuan($sisa_tagihan); ?>" readonly>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xs-3 no-padding faktur">
		<div class="col-xs-12 no-padding border">
			<div class="col-xs-12 no-padding detail_faktur">
				<div class="col-xs-12 no-padding data" data-faktur="<?php echo $data['kode_faktur']; ?>" style="width: 100%;">
					<div class="col-xs-12 text-center no-padding"><label class="control-label"><?php echo $data_branch['nama']; ?></label></div>
					<div class="col-xs-12 text-center no-padding font10"><label class="control-label"><?php echo $data_branch['alamat']; ?></label></div>
					<div class="col-xs-12 text-center no-padding font10"><label class="control-label"><?php echo 'Telp. '.$data_branch['telp']; ?></label></div>
					<div class="col-xs-12 text-center no-padding font10"><br></div>
					<div class="col-xs-12 font10">
						<table class="table table-nobordered" style="margin-bottom: 0px;">
							<tbody>
								<tr>
									<td class="col-xs-3"><label class="control-label">No. Bill</label></td>
									<td class="col-xs-9"><label class="control-label">: <?php echo $data['kode_faktur']; ?></label></td>
								</tr>
								<tr>
									<td class="col-xs-3"><label class="control-label">Kasir</label></td>
									<td class="col-xs-9"><label class="control-label">: <?php echo $data_branch['nama_kasir']; ?></label></td>
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
												<td class="col-xs-4 text-right"><label class="control-label"><?php echo angkaDecimal($v_ji['total_show']); ?></label></td>
											</tr>
										<?php endforeach ?>
									</tbody>
								</table>
							<?php endforeach ?>
						<?php endforeach ?>
					</div>
					<div class="col-xs-12 text-center font10"><hr class="double-dashed"></div>
					<?php
    					$hide_include = 'hide';
    					$hide_exclude = 'hide';
    					if ( $data['jenis_bayar_include'] ) {
    						$hide_include = null;
    						$hide_exclude = 'hide';
    					} else if ( $data['jenis_bayar_exclude'] ) {
    						$hide_include = 'hide';
    						$hide_exclude = null;
    					}
    				?>
					<div class="col-xs-12 font10">
						<table class="table table-nobordered" style="margin-bottom: 0px;">
							<tbody>
								<tr>
		        					<td class="col-xs-8 text-right"><label class="control-label">Total Belanja. =</label></td>
		        					<td class="col-xs-4 text-right tot_belanja"><label class="control-label"><?php echo angkaDecimal($data['total']); ?></label></td>
		        				</tr>
		        				<tr>
		        					<td class="col-xs-8 text-right"><label class="control-label">Disc. =</label></td>
		        					<td class="col-xs-4 text-right"><label class="control-label nota_diskon" data-val="<?php echo $data['diskon']; ?>"><?php echo ($data['diskon'] > 0) ? '('.angkaDecimal($data['diskon']).')' : angkaDecimal($data['diskon']); ?></label></td>
		        				</tr>
		        				<tr class="<?php echo $hide_exclude; ?>">
		        					<td class="col-xs-8 text-right"><label class="control-label">Service Charge. =</label></td>
		        					<td class="col-xs-4 text-right service_charge" data-real="<?php echo $data['service_charge']; ?>"><label class="control-label"><?php echo angkaDecimal($data['service_charge']); ?></label></td>
		        				</tr>
		        				<tr class="<?php echo $hide_exclude; ?>">
		        					<td class="col-xs-8 text-right"><label class="control-label">PB1. =</label></td>
		        					<td class="col-xs-4 text-right ppn" data-real="<?php echo $data['ppn']; ?>"><label class="control-label"><?php echo angkaDecimal($data['ppn']); ?></label></td>
		        				</tr>
		        				<tr>
		        					<td class="col-xs-8 text-right"><label class="control-label">CL. =</label></td>
		        					<td class="col-xs-4 text-right"><label class="control-label hutang" data-val="<?php echo $data['hutang']; ?>"><?php echo angkaDecimal($data['hutang']); ?></label></td>
		        				</tr>
		        				<tr>
		        					<td class="col-xs-8 text-right"><label class="control-label">Total Bayar. =</label></td>
		        					<td class="col-xs-4 text-right"><label class="control-label nota_total_bayar" data-val="<?php echo $data['grand_total']; ?>"><?php echo angkaDecimal($data['grand_total']); ?></label></td>
		        				</tr>
		        				<tr>
		        					<td class="col-xs-8 text-right"><label class="control-label">Jumlah Bayar. =</label></td>
		        					<td class="col-xs-4 text-right"><label class="control-label jml_bayar" data-val="<?php echo $data_bayar['jml_bayar']; ?>"><?php echo angkaDecimal($data_bayar['jml_bayar']); ?></label></td>
		        				</tr>
		        				<tr>
		        					<td class="col-xs-8 text-right"><label class="control-label">Kembalian. =</label></td>
		        					<?php 
		        						$kembalian = (($data_bayar['jml_bayar'] - $data['grand_total']) > 0) ? $data_bayar['jml_bayar'] - $data['grand_total'] : 0; 
		        					?>
		        					<td class="col-xs-4 text-right"><label class="control-label kembalian" data-val="<?php echo $kembalian; ?>"><?php echo angkaDecimal($kembalian); ?></label></td>
		        				</tr>
		        				<tr>
		        					<td class="col-xs-4"></td>
		        					<td class="col-xs-8 font10"><hr class="dashed" style="margin-left: 0px; margin-right: 0px;"></td>
		        				</tr>
		        				<?php foreach ($kategori_pembayaran as $k_kp => $v_kp): ?>
		        					<tr>
			        					<td class="col-xs-8 text-right"><label class="control-label"><?php echo ucfirst($v_kp['nama']); ?>. =</label></td>
			        					<td class="col-xs-4 text-right"><label class="control-label kategori_jenis_kartu kategori_jenis_kartu<?php echo $v_kp['id']; ?>" data-val="<?php echo $v_kp['nominal']; ?>"><?php echo angkaDecimal($v_kp['nominal']); ?></label></td>
			        				</tr>
		        				<?php endforeach ?>
							</tbody>
						</table>
					</div>
					<div class="col-xs-12 include font10 <?php echo $hide_include; ?>">
        				<div class="col-xs-12 no-padding">
        					<br>
        					<br>
        				</div>
						<table class="table table-nobordered" style="margin-bottom: 0px;">
							<tr>
								<td class="col-xs-8 text-right"><label class="control-label">Price Include of Service Charge. =</label></td>
	    						<td class="col-xs-4 text-right"><label class="control-label include_service_charge"><?php echo angkaDecimal($data['service_charge_include']); ?></label></td>
							</tr>
							<tr>
								<td class="col-xs-8 text-right"><label class="control-label">Price Include of PB1. =</label></td>
	    						<td class="col-xs-4 text-right"><label class="control-label include_ppn"><?php echo angkaDecimal($data['ppn_include']); ?></label></td>
							</tr>
						</table>
	    			</div>
	    			<div class="col-xs-12 text-left font10"><hr class="dashed"></div>
					<div class="col-xs-12 text-center font10"><label class="control-label">*** TERIMA KASIH ***</label></div>
				</div>
				<?php if ( !empty($data_hutang) ): ?>
					<?php foreach ($data_hutang as $k_dh => $v_dh): ?>
						<?php $data_dh = $v_dh['data']; ?>
						<div class="col-xs-12 no-padding data faktur_hutang" data-faktur="<?php echo $data_dh['kode_faktur']; ?>" style="margin-top: 10px;">
							<div class="col-xs-12 text-center no-padding"><label class="control-label"><?php echo $data_dh['nama_branch']; ?></label></div>
							<div class="col-xs-12 text-center no-padding font10"><label class="control-label"><?php echo $data_dh['alamat_branch']; ?></label></div>
							<div class="col-xs-12 text-center no-padding font10"><label class="control-label"><?php echo 'Telp. '.$data_dh['telp_branch']; ?></label></div>
							<div class="col-xs-12 text-center no-padding font10"><br></div>
							<div class="col-xs-12 font10">
								<table class="table table-nobordered" style="margin-bottom: 0px;">
									<tbody>
										<tr>
											<td class="col-xs-3"><label class="control-label">No. Bill</label></td>
											<td class="col-xs-9"><label class="control-label">: <?php echo $data_dh['kode_faktur'].' (CL)'; ?></label></td>
										</tr>
										<tr>
											<td class="col-xs-3"><label class="control-label">Kasir</label></td>
											<td class="col-xs-9"><label class="control-label">: <?php echo $data_dh['nama_kasir']; ?></label></td>
										</tr>
										<tr>
											<td class="col-xs-3"><label class="control-label">Tanggal</label></td>
											<td class="col-xs-9"><label class="control-label">: <?php echo substr($data_dh['tgl_trans'], 0, 19); ?></label></td>
										</tr>
									</tbody>
								</table>
							</div>
							<div class="col-xs-12 text-center font10"><hr class="double-dashed"></div>
							<div class="col-xs-12 font10">
								<?php foreach ($data_dh['detail'] as $k_det => $v_det): ?>
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
														<td class="col-xs-4 text-right"><label class="control-label"><?php echo angkaDecimal($v_ji['total_show']); ?></label></td>
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
											<td class="col-xs-4 text-right"><label class="control-label"><?php echo angkaDecimal($data_dh['total']); ?></label></td>
										</tr>
										<tr>
											<td class="col-xs-8 text-right"><label class="control-label">Bayar. =</label></td>
											<td class="col-xs-4 text-right"><label class="control-label bayar"><?php echo angkaDecimal($v_dh['bayar']); ?></label></td>
										</tr>
									</tbody>
								</table>
							</div>
							<div class="col-xs-12 text-center font10">
								<div class="col-xs-3 no-padding text-left"><label class="control-label">Pembayaran</label></div>
								<div class="col-xs-9 no-padding"><hr class="dashed"></div>
							</div>
							<div class="col-xs-12 text-left font10 pembayaran">
								<label class="control-label">
									<?php if ( !empty($v_dh['jenis_bayar']) ): ?>
										<?php foreach ($v_dh['jenis_bayar'] as $k_jb => $v_jb): ?>
											<span><?php echo $v_jb['jenis_bayar']; ?></span>
											<?php if ( isset($v_dh['jenis_bayar'][ $k_jb+1 ]) ): ?>
												<br>
											<?php endif ?>
										<?php endforeach ?>
									<?php endif ?>
								</label>
							</div>
							<div class="col-xs-12 text-left font10"><hr class="dashed"></div>
							<div class="col-xs-12 text-center font10"><label class="control-label">*** TERIMA KASIH ***</label></div>
						</div>
					<?php endforeach ?>
				<?php endif ?>
			</div>
			<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
			<div class="col-xs-12 no-padding">
				<div class="col-xs-12 no-padding">
					<button type="button" class="col-xs-12 btn btn-primary" onclick="bayar.printDraft(this)" data-kode="<?php echo $data['kode_faktur']; ?>"><i class="fa fa-print"></i> Draft</button>
				</div>
			</div>
			<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
			<div class="col-xs-12 no-padding" style="margin-bottom: 10px;">
				<!-- <div class="col-xs-6 no-padding cb_left">
					<button type="button" class="col-xs-12 btn btn-primary" data-kode="<?php echo $data['kode_faktur']; ?>" onclick="bayar.saveHutang(this)"><i class="fa fa-save"></i> Hutang</button>
				</div> -->
				<div class="col-xs-12 no-padding">
					<button type="button" class="col-xs-12 btn btn-primary" onclick="bayar.modalDiskon(this)" data-kode="<?php echo $data['kode_faktur']; ?>">Diskon Bayar</button>
				</div>
			</div>
			<div class="col-xs-12 no-padding">
				<div class="col-xs-4 no-padding cb_left">
					<button type="button" class="col-xs-12 btn btn-danger" onclick="bayar.batal();"><i class="fa fa-times"></i> Batal</button>
				</div>
				<div class="col-xs-4 no-padding cb_left cb_right">
					<?php if ( $akses['a_delete'] == 1 ): ?>
						<button type="button" class="col-xs-12 btn btn-danger" onclick="bayar.deletePembayaran(this);" data-kode="<?php echo $data['kode_faktur']; ?>" data-id="<?php echo $data_bayar['id']; ?>"><i class="fa fa-trash"></i> Hapus</button>
					<?php endif ?>
				</div>
				<div class="col-xs-4 no-padding cb_right">
					<button type="button" class="col-xs-12 btn btn-success" data-kode="<?php echo $data['kode_faktur']; ?>" data-id="<?php echo $data_bayar['id']; ?>" onclick="bayar.modalPembayaran(this)"><i class="fa fa-check"></i> Bayar</button>
				</div>
			</div>
		</div>
	</div>
</div>