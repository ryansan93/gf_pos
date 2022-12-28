<div class="modal-body body no-padding modal-member" style="height: 100%;">
	<div class="row">
		<div class="col-lg-12" style="height: 100%;">
			<div class="col-md-12 no-padding" style="padding: 5px;">
				<div class="col-md-12 text-left no-padding">
					<div class="col-md-8 text-left no-padding">
						<span style="font-weight: bold;">PELANGGAN</span>
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
							<i class="glyphicon glyphicon-search"></i><input class="form-control" type="search" data-target="list_member" placeholder="Search" onkeyup="bayar.filter_all(this)">
						</div>
					</div>
				</div>
			</div>
			<small>
				<div class="col-md-12 no-padding" style="padding: 5px; height: 86.5%;">
					<div class="col-md-12 text-center no-padding head_member">
						<div class="col-md-12 no-padding head">
							<div class="col-md-3"><label class="label-control">Kode</label></div>
							<div class="col-md-4"><label class="label-control">Grup</label></div>
							<div class="col-md-4"><label class="label-control">Nama</label></div>
							<div class="col-md-1"><label class="label-control">Action</label></div>
						</div>
					</div>
					<div class="col-md-12 text-center no-padding list_member">
						<?php if ( !empty($data) ): ?>
							<?php foreach ($data as $key => $value): ?>
								<div class="col-md-12 no-padding detail">
									<div class="col-md-3 search kode" data-sensitive="false"><label class="label-control"><?php echo !empty($value['kode_member']) ? $value['kode_member'] : '-'; ?></label></div>
									<div class="col-md-4 search grup" data-sensitive="false"><label class="label-control"><?php echo !empty($value['group_id']) ? $value['nama_grup'] : 'NON GRUP'; ?></label></div>
									<div class="col-md-4 search nama" data-sensitive="false"><label class="label-control"><?php echo $value['member']; ?></label></div>
									<div class="col-md-1">
										<div class="col-md-12 no-padding">
											<button type="button" class="btn btn-success col-md-12 btn_pilih" style="padding: 0px; margin-left: 5px;"><i class="fa fa-arrow-right"></i></button>
										</div>
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