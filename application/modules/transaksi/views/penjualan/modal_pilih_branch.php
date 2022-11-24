<div class="modal-header no-padding header" style="">
	<span class="modal-title"><label class="label-control">PILIH BRANCH</label></span>
	<!-- <button type="button" class="close" data-dismiss="modal" style="color: #000000;">&times;</button> -->
</div>
<div class="modal-body body no-padding">
	<div class="row">
		<div class="col-lg-12" style="padding-top: 10px;">
			<?php $idx = 0; ?>
			<?php foreach ($branch as $key => $value): ?>
				<?php
					$class = 'cb_left';
					if ( $idx == 1 ) {
						$class = 'cb_right';
						$idx = 0;
					} else {
						$idx++;
					}
				?>

				<div class="col-xs-6 no-padding <?php echo $class; ?>">
					<button type="button" class="col-xs-12 btn btn-primary" data-kode="<?php echo $value['kode_branch']; ?>" onclick="jual.setBranch(this)"><?php echo strtoupper($value['nama']); ?></button>
				</div>
			<?php endforeach ?>
		</div>
	</div>
</div>