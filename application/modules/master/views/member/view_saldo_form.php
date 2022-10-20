<div class="modal-body body no-padding">
	<div class="row">
		<div class="col-lg-12 no-padding" style="padding-top: 10px; padding-bottom: 10px;">
			<div class="col-lg-8">
				<span style="font-weight: bold;">DETAIL SALDO MEMBER</span>
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
		            <select class="form-control member" disabled>
		            	<option value="">-- Pilih Member --</option>
		            	<?php if ( !empty($member) ): ?>
		            		<?php foreach ($member as $k_mbr => $v_mbr): ?>
		            			<?php
		            				$selected = null;
		            				if ( $data['member_kode'] == $v_mbr['kode_member'] ) {
		            					$selected = 'selected';
		            				}
		            			?>
		            			<option value="<?php echo $v_mbr['kode_member']; ?>" <?php echo $selected; ?> ><?php echo $v_mbr['nama']; ?></option>
		            		<?php endforeach ?>
		            	<?php endif ?>
		            </select>
		        </div>
			</div>
			<div class="col-md-12 no-padding" style="margin-top: 10px;">
				<div class="col-lg-12 text-left"><label class="control-label">Saldo</label></div>
		        <div class="col-lg-12">
		            <input type="text" class="form-control text-right saldo" placeholder="Saldo" data-required="1" maxlength="15" data-tipe="decimal" value="<?php echo angkaDecimal($data['saldo']); ?>" disabled>
		        </div>
			</div>
		</div>
		<div class="col-lg-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
		<div class="col-lg-12 no-padding btn_view">
			<div class="col-md-12">
				<div class="col-md-6 no-padding" style="padding-right: 5px;">
					<button class="btn btn-danger col-md-12" onclick="mbr.deleteSm(this)" data-kode="<?php echo $data['id']; ?>"><i class="fa fa-trash"> Hapus</i></button>
				</div>
				<div class="col-md-6 no-padding" style="padding-left: 5px;">
					<button class="btn btn-primary col-md-12" onclick="mbr.editSaldoForm(this)" data-kode="<?php echo $data['id']; ?>"><i class="fa fa-edit"> Edit</i></button>
				</div>
			</div>
		</div>
		<div class="col-lg-12 no-padding btn_edit hide">
			<div class="col-md-12">
				<div class="col-md-6 no-padding" style="padding-right: 5px;">
					<button class="btn btn-danger col-md-12" onclick="mbr.batalEditSm(this)" data-kode="<?php echo $data['id']; ?>"><i class="fa fa-times"> Batal</i></button>
				</div>
				<div class="col-md-6 no-padding" style="padding-left: 5px;">
					<button class="btn btn-primary col-md-12" onclick="mbr.editSm(this)" data-kode="<?php echo $data['id']; ?>"><i class="fa fa-edit"> Simpan Perubahan</i></button>
				</div>
			</div>
		</div>
	</div>
</div>