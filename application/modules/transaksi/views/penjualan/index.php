<div class="col-md-2 no-padding" style="height: 100%;">
	<div class="col-md-12" style="padding-top: 10px;">
		<button type="button" class="col-md-12 btn btn-primary btn-kode-branch" data-kode="<?php echo strtoupper($kode_branch); ?>" onclick="jual.modalPilihBranch()"><b><?php echo strtoupper($kode_branch); ?></b></button>
	</div>
	<div class="col-md-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
	<div class="col-md-12 no-padding jenis">
		<ul class="jenis">
			<?php if ( !empty($jenis) ): ?>
				<?php foreach ($jenis as $k_jenis => $v_jenis): ?>
					<li class="cursor-p" onclick="jual.getMenu(this)" data-id="<?php echo $v_jenis['id']; ?>" data-aktif="0"><?php echo strtoupper($v_jenis['nama']); ?></li>
				<?php endforeach ?>
			<?php endif ?>
		</ul>
	</div>
</div>
<div class="col-md-6 list_menu" style="height: 100%;">
	<div class="col-md-12 no-padding" style="padding: 0px 5px 10px 5px; height: 7%;">
		<div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-search"></i>
            </span>
            <input type="text" class="form-control filter_menu" placeholder="Nama Menu" onkeyup="jual.filterMenu()">
        </div>
	</div>
	<div class="col-md-12 no-padding detail_menu" style="height: 81%;"></div>
	<div class="col-md-12 no-padding" style="height: 4%; padding-left: 5px; padding-top: 5px; border-top: 1px solid #dedede;">
		<span style="font-weight: bold;"><div class="col-xs-3" style="padding-left: 0px;">CUSTOMER</div><div class="col-xs-9"> : <span class="member" data-kode="">-</span></div></span>
	</div>
	<div class="col-md-12 no-padding" style="height: 4%; padding-left: 5px; padding-top: 5px;">
		<span style="font-weight: bold;"><div class="col-xs-3" style="padding-left: 0px;">JENIS PESANAN</div><div class="col-xs-9"> : <span class="jenis_pesanan" data-kode="">-</span></div></span>
	</div>
	<div class="col-md-12 no-padding" style="height: 4%; padding-left: 5px; padding-top: 3px;">
		<span style="font-weight: bold;"><div class="col-xs-3" style="padding-left: 0px;">MEJA</div><div class="col-xs-9"> : <span class="meja" data-id="">-</span></div></span>
	</div>
</div>
<div class="col-md-4 nota" style="padding: 0.8% 0.8%; height: 100%;">
	<div class="col-md-12 no-padding" style="height: 100%;">
		<div class="col-md-12 no-padding" style="height: 10%;">
			<div class="col-md-10 no-padding" style="height: 100%;">
				<div class="col-md-4 no-padding" style="height: 100%; padding-right: 5px;">
					<div class="col-md-12 no-padding text-center cursor-p button" style="border: 1px solid #dedede; height: 100%; display: flex; justify-content: center; align-items: center;" onclick="jual.modalPilihMember()" title="MEMBER">
						<span style=""><b><i class="fa fa-address-book-o" style="font-size: 28px;"></i></b></span>
					</div>
				</div>
				<div class="col-md-4 no-padding" style="height: 100%; padding-right: 5px;">
					<div class="col-md-12 no-padding text-center cursor-p button" style="border: 1px solid #dedede; height: 100%; display: flex; justify-content: center; align-items: center;" onclick="jual.modalJenisPesanan()" title="JENIS PESANAN">
						<span style=""><b><i class="fa fa-cutlery" style="font-size: 28px;"></i></b></span>
					</div>
				</div>
				<div class="col-md-4 no-padding" style="height: 100%; padding-right: 5px;">
					<div class="col-md-12 no-padding text-center cursor-p button" style="border: 1px solid #dedede; height: 100%; display: flex; justify-content: center; align-items: center;" onclick="jual.modalMeja()" title="MEJA">
						<span style=""><b><i class="fa fa-table" style="font-size: 28px;"></i></b></span>
					</div>
				</div>
			</div>
			<div class="col-md-2 no-padding" style="height: 100%;">
				<div class="col-md-12 no-padding text-center cursor-p btn-danger" style="border: 1px solid #dedede; height: 100%; display: flex; justify-content: center; align-items: center;" onclick="location.reload()" title="REFRESH">
					<span style=""><b><i class="fa fa-refresh" style="font-size: 28px;"></i></b></span>
				</div>
			</div>
		</div>

		<div class="col-md-12 no-padding" style="padding-top: 2.5%; height: 59%; font-size: 14px;">
			<div class="col-md-12 no-padding" style="border: 1px solid #dedede; height: 100%; padding: 5px;">
				<div class="col-md-12 no-padding lpesanan" style="height: 100%;">
					<div class="col-md-12 no-padding" style="border-top: 1px solid #dedede;"><b>PESANAN</b></div>
					<div class="col-md-12 no-padding pesanan_contain list_pesanan"></div>
					<div class="col-md-12 no-padding" style="border-top: 1px solid #dedede;"><b>DISKON</b></div>
					<div class="col-md-12 no-padding list_diskon"></div>
				</div>
			</div>
		</div>

		<div class="col-md-12 no-padding" style="padding-top: 2.5%; height: 30%; font-size: 12px;">
			<div class="col-md-12 no-padding" style="height: 100%;">
				<label class="control-label col-md-4 no-padding" style="height: 10%; margin-bottom: 0.5%;">Subtotal</label>
				<label class="control-label col-md-8 no-padding text-right" style="height: 10%; margin-bottom: 0.5%;">Rp. <span class="subtotal">0,00</span></label>
				<label class="control-label col-md-4 no-padding" style="height: 10%; margin-bottom: 0.5%;">Diskon</label>
				<label class="control-label col-md-8 no-padding text-right" style="height: 10%; margin-bottom: 0.5%;">Rp. <span class="diskon">0,00</span></label>
				<label class="control-label col-md-5 no-padding persen_service_charge" data-val="<?php echo $service_charge; ?>" style="height: 10%; margin-bottom: 0.5%;">Service Charge</label>
				<label class="control-label col-md-7 no-padding text-right" style="height: 10%; margin-bottom: 0.5%;">Rp. <span class="service_charge">0,00</span></label>
				<label class="control-label col-md-4 no-padding persen_ppn" data-val="<?php echo $persen_ppn; ?>" style="height: 10%; margin-bottom: 0.5%;">PB1</label>
				<label class="control-label col-md-8 no-padding text-right" style="height: 10%; margin-bottom: 0.5%;">Rp. <span class="ppn">0,00</span></label>
				<label class="control-label col-md-12 no-padding text-right" style="height: 2.5%; margin-bottom: 0.5%;"><hr style="margin: 0% 0%;"></label>
				<label class="control-label col-md-4 no-padding" style="height: 12.5%; margin-bottom: 0.5%;">TOTAL</label>
				<label class="control-label col-md-8 no-padding text-right" style="height: 12.5%; margin-bottom: 0.5%;">Rp. <span class="grandtotal">0,00</span></label>
				<label class="control-label col-md-12 no-padding text-right" style="height: 2.5%; margin-bottom: 0.5%;"><hr style="margin: 0% 0%;"></label>
				<!-- <div class="col-md-12 no-padding" style="padding-bottom: 2%; height: 20%;">
					<div class="col-md-12 text-center cursor-p button" style="height: 100%; display: flex; justify-content: center; align-items: center;" onclick="jual.modalDiskon()">
						<span><b>DISKON</b></span>
					</div>
				</div> -->
				<div class="col-md-12 no-padding" style="padding-bottom: 1%; height: 40%;">
					<div class="col-md-12 no-padding simpan_pesanan" style="height: 100%;">
						<?php 
							$hide_div_submit = 'hide';
							$col_md = 'col-md-12';
							$padding = 0;
							if ( $akses['a_submit'] == 1 ) {
								$hide_div_submit = '';
								$col_md = 'col-md-6';
								$padding = 1;
							}
						?>
						<div class="no-padding <?php echo $hide_div_submit.' '.$col_md; ?>" style="height: 100%; padding-right: <?php echo $padding.'%'; ?>;">
								<div class="col-md-12 text-center cursor-p button save" style="height: 100%; display: flex; justify-content: center; align-items: center;" onclick="/* jual.savePesanan('simpan'); */ jual.modalPilihPrivilege(this);">
									<span><b>SIMPAN</b></span>
								</div>
						</div>
						<div class="no-padding <?php echo $col_md; ?>" style="height: 100%; padding-left: <?php echo $padding.'%'; ?>;">
							<div class="col-md-12 text-center cursor-p button btn-list-pesanan" style="height: 100%; display: flex; justify-content: center; align-items: center;" onclick="bayar.modalListBayar()">
								<span><b>LIST PESANAN</b></span>
							</div>
						</div>
					</div>
					<div class="col-md-12 no-padding edit_pesanan hide" style="height: 100%;">
						<?php
							$padding1 = 'padding-right: 1%;'; 
							$padding2 = 'padding-left: 1%;'; 
							$padding3 = 'padding-left: 1%;'; 
							$col_size = 'col-md-6'; 

							$kasir = 0;
							if ( hasAkses('transaksi/Pembayaran') ) {
								$col_size = 'col-md-4';
								$padding2 = 'padding-left: 1%; padding-right: 1%;'; 
								$kasir = 1;
							}
						?>

						<div class="<?php echo $col_size; ?> no-padding" style="height: 100%; <?php echo $padding1; ?>">
							<div class="col-md-12 text-center cursor-p button edit" style="height: 100%; display: flex; justify-content: center; align-items: center;" onclick="/* jual.editPesanan(this); */ jual.modalPilihPrivilege(this);" data-kode="" data-kasir="<?php echo $kasir; ?>">
								<span><b>SIMPAN UBAH</b></span>
							</div>
						</div>
						<div class="<?php echo $col_size; ?> no-padding" style="height: 100%; <?php echo $padding2; ?>">
							<div class="col-md-12 text-center cursor-p button" style="height: 100%; display: flex; justify-content: center; align-items: center;" onclick="jual.batalEdit()">
								<span><b>BATAL UBAH</b></span>
							</div>
						</div>
						<?php if ( hasAkses('transaksi/Pembayaran') ): ?>
							<div class="col-md-4 no-padding" style="height: 100%; <?php echo $padding3; ?>">
								<div class="col-md-12 text-center cursor-p button bayar" style="height: 100%; display: flex; justify-content: center; align-items: center;" onclick="jual.bayarDenganUpdate(this)" data-kode="">
									<span><b>BAYAR</b></span>
								</div>
							</div>
						<?php endif ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>