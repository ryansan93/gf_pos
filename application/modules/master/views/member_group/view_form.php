<div class="modal-body body no-padding">
	<div class="row">
		<div class="col-lg-12 no-padding" style="padding-top: 10px; padding-bottom: 10px;">
			<div class="col-lg-8">
				<span style="font-weight: bold;">DETAIL MEMBER GROUP</span>
			</div>
			<div class="col-md-4 text-right">
				<button type="button" class="close pull-right" data-dismiss="modal" style="color: #000000;">&times;</button>
			</div>
			<div class="col-md-12 text-left">
				<hr style="margin-top: 5px; margin-bottom: 10px;">
			</div>
		</div>
		<div class="col-lg-12 no-padding">
			<div class="col-md-12 no-padding">
				<div class="col-lg-12 text-left"><label class="control-label">Nama</label></div>
		        <div class="col-lg-12">
		            <input type="text" class="form-control nama" placeholder="Nama (MAX : 50)" data-required="1" maxlength="50" value="<?php echo $data['nama']; ?>" disabled>
		        </div>
			</div>
		</div>
		<div class="col-lg-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
		<div class="col-lg-12 no-padding btn_view">
			<div class="col-md-12" style="padding-bottom: 10px;">
				<div class="col-md-6 no-padding" style="padding-right: 5px;">
					<?php if ( $akses['a_delete'] == 1 ): ?>
						<button class="btn btn-danger col-md-12" onclick="mg.delete(this)" data-kode="<?php echo $data['id']; ?>"><i class="fa fa-trash"> Hapus</i></button>
					<?php endif ?>
				</div>
				<div class="col-md-6 no-padding" style="padding-left: 5px;">
					<?php if ( $akses['a_edit'] == 1 ): ?>
						<button class="btn btn-primary col-md-12" onclick="mg.editForm(this)" data-kode="<?php echo $data['id']; ?>"><i class="fa fa-edit"> Edit</i></button>
					<?php endif ?>
				</div>
			</div>
		</div>
		<div class="col-lg-12 no-padding btn_edit hide">
			<div class="col-md-12">
				<div class="col-md-6 no-padding" style="padding-right: 5px;">
					<button class="btn btn-danger col-md-12" onclick="mg.batalEdit(this)" data-kode="<?php echo $data['id']; ?>"><i class="fa fa-times"> Batal</i></button>
				</div>
				<div class="col-md-6 no-padding" style="padding-left: 5px;">
					<button class="btn btn-primary col-md-12" onclick="mg.edit(this)" data-kode="<?php echo $data['id']; ?>"><i class="fa fa-edit"> Simpan Perubahan</i></button>
				</div>
			</div>
		</div>
	</div>
</div>