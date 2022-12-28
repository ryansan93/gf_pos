<div class="modal-header no-padding header" style="">
	<span class="modal-title"><label class="label-control">LIST BILL</label></span>	
	<!-- <button type="button" class="close" data-dismiss="modal" style="color: #000000;">&times;</button> -->
</div>
<div class="modal-body body no-padding">
	<div class="col-xs-12 no-padding" style="padding-top: 10px;">
		<?php $idx = 0; ?>
		<?php foreach ($data as $key => $value): ?>
			<?php
				if ( $idx == 0 ) {
					$class = 'cb_left';
					$idx++;
				} else if ( $idx == 1 && $idx == 2 ) {
					$class = 'cb_left cb_right';
					$idx++;
				} else if ( $idx == 3 ) {
					$class = 'cb_right';
					$idx = 0;
				}

				$click = 'onclick="bayar.pembayaranForm(this);"';
				$class_btn = 'btn-primary';
				$disabled = '';
				if ( $value['lunas'] == 1 ) {
					// $disabled = 'disabled';
					$class_btn = 'btn-success';
				} else {
					if ( $value['hutang'] == 1 ) {
						$class_btn = 'btn-danger';
					}
				}

				if ( $bayar == 1 ) {
					$click = 'onclick="bayar.pembayaranFormEdit(this);"';
				}
			?>
			<div class="col-xs-3 no-padding <?php echo $class; ?>">
				<button class="col-xs-12 btn <?php echo $class_btn; ?> text-left" data-kode="<?php echo exEncrypt($value['kode_faktur']); ?>" <?php echo $click; ?> <?php echo $disabled; ?> >
					BILL : <?php echo $value['member']; ?>
					<hr style="margin-top: 5px; margin-bottom: 5px;">
					<?php echo 'Rp. '.angkaDecimal($value['grand_total']); ?>
				</button>
			</div>
		<?php endforeach ?>
	</div>
	<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
	<div class="col-xs-12 no-padding">
		<?php if ( $bayar == 0 ): ?>
			<div class="col-xs-4 no-padding cb_left">
				<button type="button" class="col-xs-12 btn btn-danger" onclick="bayar.modalListBayar()"><i class="fa fa-times"></i> BATAL</button>
			</div>
			<div class="col-xs-4 no-padding cb_left cb_right">
				<button type="button" class="col-xs-12 btn btn-primary" data-kode="<?php echo $pesanan_kode; ?>" onclick="bayar.modalSplitBill(this)">Split Bill</button>
			</div>
			<div class="col-xs-4 no-padding cb_right">
				<button type="button" class="col-xs-12 btn btn-primary" data-kode="<?php echo $kode_faktur; ?>" onclick="bayar.modalGabungBill(this)">Gabung Bill</button>
			</div>
		<?php else: ?>
			<div class="col-xs-12 no-padding cb_left">
				<button type="button" class="col-xs-12 btn btn-danger" onclick="bayar.modalListBayar()"><i class="fa fa-times"></i> BATAL</button>
			</div>
		<?php endif ?>
	</div>
</div>