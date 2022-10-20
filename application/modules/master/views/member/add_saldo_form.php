<div class="modal-body body no-padding">
	<div class="row">
		<div class="col-lg-12 no-padding" style="padding-top: 10px; padding-bottom: 10px;">
			<div class="col-lg-12">
				<span style="font-weight: bold;">TAMBAH SALDO MEMBER</span>
			</div>
		</div>
		<div class="col-lg-12 no-padding">
			<div class="col-md-12 no-padding">
				<div class="col-lg-12 text-left"><label class="control-label">Nama</label></div>
		        <div class="col-lg-12">
		            <select class="form-control member">
		            	<option value="">-- Pilih Member --</option>
		            	<?php if ( !empty($member) ): ?>
		            		<?php for ($i=0; $i < 20; $i++) { ?>
			            		<?php foreach ($member as $k_mbr => $v_mbr): ?>
			            			<option value="<?php echo $v_mbr['kode_member']; ?>"><?php echo $v_mbr['nama']; ?></option>
			            		<?php endforeach ?>
		            		<?php } ?>
		            	<?php endif ?>
		            </select>
		        </div>
			</div>
			<div class="col-md-12 no-padding" style="margin-top: 10px;">
				<div class="col-lg-12 text-left"><label class="control-label">Saldo</label></div>
		        <div class="col-lg-12">
		            <input type="text" class="form-control text-right saldo" placeholder="Saldo" data-required="1" maxlength="15" data-tipe="decimal">
		        </div>
			</div>
		</div>
		<div class="col-lg-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
		<div class="col-lg-12 no-padding">
			<div class="col-md-12">
				<div class="col-md-6 no-padding" style="padding-right: 5px;">
					<button class="btn btn-danger col-md-12" onclick="mbr.modalSaldoMember()"><i class="fa fa-times"> Batal</i></button>
				</div>
				<div class="col-md-6 no-padding" style="padding-left: 5px;">
					<button class="btn btn-primary col-md-12" onclick="mbr.saveSm(this)"><i class="fa fa-save"> Simpan</i></button>
				</div>
			</div>
		</div>
	</div>
</div>