<div class="modal-body body no-padding">
	<div class="row">
		<div class="col-lg-12">
			<div class="col-md-12 no-padding" style="padding: 5px;">
				<div class="col-md-12 text-left no-padding">
					<span style="font-weight: bold;">Nama Pelanggan Non Member</span>
				</div>
			</div>
			<div class="col-md-12 no-padding" style="height: 40px; padding: 5px;">
				<div class="col-md-12 text-center no-padding" style="height: 100%;">
					<input type="text" class="form-control uppercase">
				</div>
			</div>
			<div class="col-md-12 no-padding" style="margin-top: 5px; padding: 5px;">
				<div class="col-lg-12 no-padding text-left"><label class="control-label">Grup Member</label></div>
		        <div class="col-lg-12 no-padding">
		            <select class="form-control member_group">
		            	<option value="">NON GRUP</option>
		            	<?php if ( !empty($member_group) ): ?>
		            		<?php foreach ($member_group as $k_mg => $v_mg): ?>
		            			<option value="<?php echo $v_mg['nama']; ?>"><?php echo $v_mg['nama']; ?></option>
		            		<?php endforeach ?>
		            	<?php endif ?>
		            </select>
		        </div>
			</div>
			<div class="col-md-6 no-padding" style="height: 40px; padding: 5px;">
				<div class="col-md-12 text-center cursor-p btn-cancel button" style="height: 100%; display: flex; justify-content: center; align-items: center;">
					<span><b><i class="fa fa-long-arrow-left"></i></b></span>
				</div>
			</div>
			<div class="col-md-6 no-padding" style="height: 40px; padding: 5px;">
				<div class="col-md-12 text-center cursor-p btn-ok button" style="height: 100%; display: flex; justify-content: center; align-items: center;">
					<span><b><i class="fa fa-long-arrow-right"></i></b></span>
				</div>
			</div>
		</div>
	</div>
</div>