<div class="modal-header no-padding header" style="">
	<span class="modal-title"><label class="label-control">Pilih Meja</label></span>
</div>
<div class="modal-body body no-padding">
	<div class="col-xs-12 no-padding" style="height: 100%;">
		<div class="col-xs-12 no-padding" style="padding-top: 10px;">
			<?php foreach ($lantai as $k_lantai => $v_lantai): ?>
				<div class="col-xs-2" style="padding-left: 0px;">
					<button type="button" class="col-xs-12 btn btn-primary btn_lantai" data-id="<?php echo $v_lantai['id']; ?>" onclick="jual.listMeja(this)"><?php echo strtoupper($v_lantai['nama']); ?></button>
				</div>
			<?php endforeach ?>
		</div>
		<div class="col-xs-12 no-padding" style="height: 80%; padding-top: 10px; padding-bottom: 10px;">
			<div class="col-xs-12 meja" style="border: 1px solid #dedede; border-radius: 3px; height: 100%; padding: 10px;">
			</div>
		</div>
		<div class="col-xs-12 no-padding">
			<button type="button" class="btn btn-danger pull-right" data-dismiss="modal">Keluar</button>
		</div>
	</div>
</div>