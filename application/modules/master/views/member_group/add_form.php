<div class="modal-body body no-padding">
	<div class="row">
		<div class="col-lg-12 no-padding" style="padding-top: 10px; padding-bottom: 10px;">
			<div class="col-lg-12">
				<span style="font-weight: bold;">TAMBAH MEMBER GROUP</span>
			</div>
		</div>
		<div class="col-lg-12 no-padding">
			<div class="col-md-12 no-padding">
				<div class="col-lg-12 text-left"><label class="control-label">Nama</label></div>
		        <div class="col-lg-12">
		            <input type="text" class="form-control nama" placeholder="Nama (MAX : 50)" data-required="1" maxlength="50">
		        </div>
			</div>
		</div>
		<div class="col-lg-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
		<div class="col-lg-12 no-padding">
			<div class="col-md-12">
				<div class="col-md-6 no-padding" style="padding-right: 5px;">
					<button class="btn btn-danger col-md-12" onclick="mg.modalMemberGroup()"><i class="fa fa-times"> Batal</i></button>
				</div>
				<div class="col-md-6 no-padding" style="padding-left: 5px;">
					<button class="btn btn-primary col-md-12" onclick="mg.save(this)"><i class="fa fa-save"> Simpan</i></button>
				</div>
			</div>
		</div>
	</div>
</div>