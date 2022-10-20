<div class="modal-body body no-padding modal-member" style="height: 100%;">
	<div class="row">
		<div class="col-lg-12" style="height: 100%;">
			<div class="col-md-12 no-padding" style="padding: 5px;">
				<div class="col-md-12 text-left no-padding">
					<div class="col-md-8 text-left no-padding">
						<span style="font-weight: bold;">MEMBER</span>
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
						<button type="button" class="btn btn-primary pull-right" onclick="mbr.addForm(this)" style="padding: 0px 5px;"><i class="fa fa-plus"></i> Tambah</button>
					</div>
				</div>
			</div>
			<small>
				<div class="col-md-12 no-padding" style="padding: 5px; height: 86.5%;">
					<div class="col-md-12 text-center no-padding head_member">
						<div class="col-md-12 no-padding head">
							<div class="col-md-2"><label class="label-control">Kode</label></div>
							<div class="col-md-5"><label class="label-control">Nama</label></div>
							<div class="col-md-2"><label class="label-control">No. Telp</label></div>
							<div class="col-md-1"><label class="label-control">Privilege</label></div>
							<div class="col-md-2"><label class="label-control">Action</label></div>
						</div>
						<!-- <table class="table table-bordered" style="margin-bottom: 0px;">
							<thead>
								<tr>
									<th class="col-md-3">Kode</th>
									<th class="col-md-6">Nama</th>
									<th class="col-md-3">No. Telp</th>
								</tr>
							</thead>
							<tbody>
								<?php if ( !empty($data) ): ?>
									<?php for ($i=0; $i < 20; $i++) { ?>
										<?php foreach ($data as $key => $value): ?>
											<tr class="cursor-p" onclick="jual.pilihMember(this)">
												<td class="col-md-3 text-left kode"><?php echo $value['kode_member']; ?></td>
												<td class="col-md-6 text-left nama"><?php echo $value['nama']; ?></td>
												<td class="col-md-3 text-left no_telp"><?php echo $value['no_telp']; ?></td>
											</tr>								
										<?php endforeach ?>
									<?php } ?>
								<?php else: ?>
									<tr>
										<td colspan="3">Data tidak ditemukan.</td>
									</tr>
								<?php endif ?>
							</tbody>
						</table> -->
					</div>
					<div class="col-md-12 text-center no-padding list_member">
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
									<div class="col-md-2 kode"><label class="label-control"><?php echo $value['kode_member']; ?></label></div>
									<div class="col-md-5 nama"><label class="label-control"><?php echo $value['nama']; ?></label></div>
									<div class="col-md-2"><label class="label-control"><?php echo $value['no_telp']; ?></label></div>
									<div class="col-md-1">
										<?php if ( $value['privilege'] == 1 ): ?>
											<i class="fa fa-check"></i>
										<?php else: ?>
											<label class="label-control">-</label>
										<?php endif ?>
									</div>
									<div class="col-md-2">
										<div class="col-md-6 no-padding"><button type="button" class="btn btn-primary col-md-12" style="padding: 0px; margin-right: 5px;" onclick="mbr.viewForm(this)" data-kode="<?php echo $value['kode_member']; ?>"><i class="fa fa-file"></i></button></div>
										<div class="col-md-6 no-padding"><button type="button" class="btn btn-success col-md-12 btn_pilih" style="padding: 0px; margin-left: 5px;"><i class="fa fa-arrow-right"></i></button></div>
									</div>
								</div>
							<?php endforeach ?>
						<?php else: ?>
							<div class="col-md-12 no-padding detail">
								<div class="col-md-12" style="border-bottom: none;"><label class="label-control">Data tidak ditemukan.</label></div>
							</div>
						<?php endif ?>
					</div>
					<!-- <div class="col-md-12 text-center no-padding list_member" style="height: 92.5%; border-bottom: 1px solid #dddddd;">
						<table class="table table-bordered" style="margin-bottom: 0px;">
							<tbody>
								<?php if ( !empty($data) ): ?>
									<?php foreach ($data as $key => $value): ?>
										<tr class="cursor-p" onclick="jual.pilihMember(this)">
											<td class="col-md-3 text-left kode"><?php echo $value['kode_member']; ?></td>
											<td class="col-md-6 text-left nama"><?php echo $value['nama']; ?></td>
											<td class="col-md-3 text-left no_telp"><?php echo $value['no_telp']; ?></td>
										</tr>								
									<?php endforeach ?>
								<?php else: ?>
									<tr>
										<td colspan="3">Data tidak ditemukan.</td>
									</tr>
								<?php endif ?>
							</tbody>
						</table>
					</div> -->
				</div>
			</small>
			<!-- <div class="col-md-6 no-padding" style="height: 40px; padding: 5px;">
				<div class="col-md-12 text-center cursor-p btn-cancel button" style="height: 100%; display: flex; justify-content: center; align-items: center;">
					<span><b><i class="fa fa-long-arrow-left"></i></b></span>
				</div>
			</div>
			<div class="col-md-6 no-padding" style="height: 40px; padding: 5px;">
				<div class="col-md-12 text-center cursor-p btn-ok button" style="height: 100%; display: flex; justify-content: center; align-items: center;">
					<span><b><i class="fa fa-long-arrow-right"></i></b></span>
				</div>
			</div> -->
		</div>
	</div>
</div>