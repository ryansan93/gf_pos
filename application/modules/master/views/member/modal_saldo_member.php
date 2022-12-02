<div class="modal-body body no-padding modal-saldo-member" style="height: 100%;">
	<div class="row">
		<div class="col-lg-12" style="height: 100%;">
			<div class="col-md-12 no-padding" style="padding: 5px;">
				<div class="col-md-12 text-left no-padding">
					<div class="col-md-8 text-left no-padding">
						<span style="font-weight: bold;">SALDO MEMBER</span>
					</div>
					<div class="col-md-4 text-right no-padding">
						<button type="button" class="close pull-right" data-dismiss="modal" style="color: #000000;">&times;</button>
					</div>
				</div>
				<div class="col-md-12 text-left no-padding">
					<hr style="margin-top: 5px; margin-bottom: 10px;">
				</div>
				<div class="col-md-12 text-left no-padding">
					<div class="col-md-8 no-padding">
						<div class="col-lg-12 search right-inner-addon no-padding">
							<i class="glyphicon glyphicon-search"></i><input class="form-control" type="search" data-table="tbl_gaji" placeholder="Search" onkeyup="filter_all(this)">
						</div>
					</div>
					<div class="col-md-4 no-padding">
						<button type="button" class="btn btn-primary pull-right" onclick="mbr.addSaldoForm(this)" style="padding: 0px 5px;"><i class="fa fa-plus"></i> Tambah</button>
					</div>
				</div>
			</div>
			<small>
				<div class="col-md-12 no-padding" style="padding: 5px; height: 86.5%;">
					<div class="col-md-12 text-center no-padding head_sm">
						<div class="col-md-12 no-padding head">
							<div class="col-md-4"><label class="label-control">Nama</label></div>
							<div class="col-md-3"><label class="label-control">Saldo (Rp.)</label></div>
							<div class="col-md-3"><label class="label-control">Sisa Saldo (Rp.)</label></div>
							<div class="col-md-2"><label class="label-control">Action</label></div>
						</div>
					</div>
					<div class="col-md-12 text-center no-padding list_sm">
						<?php if ( !empty($data) ): ?>
							<?php $idx = 1; ?>
							<?php foreach ($data as $key => $value): ?>
								<?php 
									$bgcolor = 'putih';
									if ( $idx % 2 == 0 ) {
										$bgcolor = 'abu';
									}

									$idx++;
								?>
								<div class="col-md-12 no-padding detail <?php echo $bgcolor; ?>">
									<div class="col-md-4"><label class="label-control"><?php echo $value['member']['nama']; ?></label></div>
									<div class="col-md-3 text-right"><label class="label-control"><?php echo angkaDecimal($value['saldo']); ?></label></div>
									<div class="col-md-3 text-right"><label class="label-control"><?php echo angkaDecimal($value['sisa_saldo']); ?></label></div>
									<div class="col-md-2">
										<div class="col-md-12 no-padding"><button type="button" class="btn btn-primary col-md-12" style="padding: 0px; margin-right: 5px;" onclick="mbr.viewSaldoForm(this)" data-kode="<?php echo $value['id']; ?>"><i class="fa fa-file"></i></button></div>
									</div>
								</div>
							<?php endforeach ?>
						<?php else: ?>
							<div class="col-md-12 no-padding detail">
								<div class="col-md-12" style="border-bottom: none;"><label class="label-control">Data tidak ditemukan.</label></div>
							</div>
						<?php endif ?>
					</div>
				</div>
			</small>
		</div>
	</div>
</div>