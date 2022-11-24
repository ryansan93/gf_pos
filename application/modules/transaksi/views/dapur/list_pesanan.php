<div class="col-xs-12 no-padding contain outstanding">
	<?php if ( !empty($data_outstanding) ): ?>
		<?php $idx = 0; ?>
		<?php foreach ($data_outstanding as $key => $value): ?>
			<?php
				$privilege = '';
				if ( $value['privilege'] == 1 ) {
					$privilege = 'privilege';
				}
			?>
			<?php if ( $idx == 0 ): ?>
				<div class="col-xs-12 no-padding">
			<?php endif ?>
				<div class="col-xs-3">
					<div class="col-xs-12 no-padding cursor-p contain_pesanan <?php echo $privilege; ?>">
						<div class="col-xs-12 no-padding header_contain">
							<label class="control-label"><?php echo strtoupper($value['meja']['lantai']['nama_lantai'].' - '.$value['meja']['nama_meja']); ?></label>
						</div>
						<!-- <div class="col-xs-12 no-padding">
							<hr style="margin-top: 10px; margin-bottom: 10px;">
						</div> -->
						<div class="col-xs-12 no-padding detail_contain">
							<?php foreach ($value['pesanan_item'] as $k_pi => $v_pi): ?>
					            <div class="col-xs-12 cursor-p no-padding menu" style="margin-bottom: 5px;">
						            <div class="col-xs-12 no-padding menu_utama">
							            <div class="col-xs-2 text-left no-padding"><span class="jumlah"><?php echo $v_pi['jumlah']; ?> x</span></div>
							            <div class="col-xs-8 no-padding">
								            <span class="nama_menu"><?php echo $v_pi['menu_nama']; ?></span>
							            </div>
							            <div class="col-xs-2 no-padding text-center">
							            	<?php 
							            		$hide_btn1 = 'hide';
							            		$hide_btn2 = 'hide';
							            		$hide_label = 'hide';
								            	if ( empty($v_pi['proses']) ) {
							            			$hide_btn1 = '';
								            	} else if ( $v_pi['proses'] == 1 ) { 
							            			$hide_btn2 = '';
								            	} else {
							            			$hide_label = '';
								            	} 
							            	?>

							            	<button class="col-xs-12 btn btn-primary <?php echo $hide_btn1; ?>" data-kode="<?php echo $v_pi['kode_pesanan_item']; ?>" onclick="dapur.ubahStatusPesanan(this)" data-status="0" data-statustujuan="1"><i class="fa fa-cutlery"></i></button>
						            		<button class="col-xs-12 btn btn-primary <?php echo $hide_btn2; ?>" data-kode="<?php echo $v_pi['kode_pesanan_item']; ?>" onclick="dapur.ubahStatusPesanan(this)" data-status="1" data-statustujuan="2"><i class="fa fa-check"></i></button>
						            		<label class="control-label <?php echo $hide_label; ?>" data-status="2">DONE</label>
							            </div>
						            </div>
						            <?php if ( !empty($v_pi['pesanan_item_detail']) ): ?>
							            <?php foreach ($v_pi['pesanan_item_detail'] as $k_pid => $v_pid): ?>
							                <div class="col-xs-12 detail no-padding" style="font-size:10px;">
							                	<div class="col-xs-2 text-left no-padding"></div>
								                <div class="col-xs-10 no-padding">
								                	<span class="nama_menu"><?php echo $v_pid['menu_nama']; ?></span>
								                </div>
								            </div>
							            <?php endforeach ?>
						            <?php endif ?>
						            <?php if ( !empty($v_pi['request']) ): ?>
						                <div class="col-xs-12 request no-padding" style="font-size:10px;">
							                <div class="col-xs-2 text-left no-padding"></div>
								                <div class="col-xs-10 no-padding">
								                	<span class="nama_menu"><?php echo $v_pi['request']; ?></span>
								                </div>
						                </div>
						            <?php endif ?>
					            </div>
							<?php endforeach ?>
						</div>
					</div>
				</div>
			<?php if ( $idx == 3 ): ?>
				<?php $idx = 0; ?>
				</div>
			<?php else: ?>
				<?php $idx++; ?>
			<?php endif ?>
		<?php endforeach ?>
	<?php endif ?>
</div>
<div class="col-xs-12 no-padding contain done hide">
	<?php if ( !empty($data_done) ): ?>
		<?php $idx = 0; ?>
		<?php foreach ($data_done as $key => $value): ?>
			<?php
				$privilege = '';
				if ( $value['privilege'] == 1 ) {
					$privilege = 'privilege';
				}
			?>
			<?php if ( $idx == 0 ): ?>
				<div class="col-xs-12 no-padding">
			<?php endif ?>
				<div class="col-xs-3">
					<div class="col-xs-12 no-padding cursor-p contain_pesanan <?php echo $privilege; ?>">
						<div class="col-xs-12 no-padding header_contain">
							<label class="control-label"><?php echo strtoupper($value['meja']['lantai']['nama_lantai'].' - '.$value['meja']['nama_meja']); ?></label>
						</div>
						<!-- <div class="col-xs-12 no-padding">
							<hr style="margin-top: 10px; margin-bottom: 10px;">
						</div> -->
						<div class="col-xs-12 no-padding detail_contain">
							<?php foreach ($value['pesanan_item'] as $k_pi => $v_pi): ?>
					            <div class="col-xs-12 cursor-p no-padding menu" style="margin-bottom: 5px;">
						            <div class="col-xs-12 no-padding menu_utama">
							            <div class="col-xs-2 text-left no-padding"><span class="jumlah"><?php echo $v_pi['jumlah']; ?> x</span></div>
							            <div class="col-xs-8 no-padding">
								            <span class="nama_menu"><?php echo $v_pi['menu_nama']; ?></span>
							            </div>
							            <div class="col-xs-2 no-padding text-center">
							            	<?php 
							            		$hide_btn1 = 'hide';
							            		$hide_btn2 = 'hide';
							            		$hide_label = 'hide';
								            	if ( empty($v_pi['proses']) ) {
							            			$hide_btn1 = '';
								            	} else if ( $v_pi['proses'] == 1 ) { 
							            			$hide_btn2 = '';
								            	} else {
							            			$hide_label = '';
								            	} 
							            	?>

							            	<button class="col-xs-12 btn btn-primary <?php echo $hide_btn1; ?>" data-kode="<?php echo $v_pi['kode_pesanan_item']; ?>" onclick="dapur.ubahStatusPesanan(this)" data-status="0" data-statustujuan="1"><i class="fa fa-cutlery"></i></button>
						            		<button class="col-xs-12 btn btn-primary <?php echo $hide_btn2; ?>" data-kode="<?php echo $v_pi['kode_pesanan_item']; ?>" onclick="dapur.ubahStatusPesanan(this)" data-status="1" data-statustujuan="2"><i class="fa fa-check"></i></button>
						            		<label class="control-label <?php echo $hide_label; ?>" data-status="2">DONE</label>
							            </div>
						            </div>
						            <?php if ( !empty($v_pi['pesanan_item_detail']) ): ?>
							            <?php foreach ($v_pi['pesanan_item_detail'] as $k_pid => $v_pid): ?>
							                <div class="col-xs-12 detail no-padding" style="font-size:10px;">
							                	<div class="col-xs-2 text-left no-padding"></div>
								                <div class="col-xs-10 no-padding">
								                	<span class="nama_menu"><?php echo $v_pid['menu_nama']; ?></span>
								                </div>
								            </div>
							            <?php endforeach ?>
						            <?php endif ?>
						            <?php if ( !empty($v_pi['request']) ): ?>
						                <div class="col-xs-12 request no-padding" style="font-size:10px;">
							                <div class="col-xs-2 text-left no-padding"></div>
								                <div class="col-xs-10 no-padding">
								                	<span class="nama_menu"><?php echo $v_pi['request']; ?></span>
								                </div>
						                </div>
						            <?php endif ?>
					            </div>
							<?php endforeach ?>
						</div>
					</div>
				</div>
			<?php if ( $idx == 3 ): ?>
				<?php $idx = 0; ?>
				</div>
			<?php else: ?>
				<?php $idx++; ?>
			<?php endif ?>
		<?php endforeach ?>
	<?php endif ?>
</div>