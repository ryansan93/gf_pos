<div class="col-md-12 no-padding" style="border-top: 1px solid #dedede;"><b>PESANAN</b></div>
<div class="col-md-12 no-padding list_pesanan">
	<?php foreach ($data['pesanan_item'] as $k_ji => $v_ji): ?>
		<div class="col-md-12 cursor-p no-padding jenis_pesanan" style="margin-bottom: 10px;" data-kodejp="<?php echo $v_ji['kode']; ?>">
	    <div class="col-md-12 cursor-p no-padding">
	    <span style="font-weight: bold;"><?php echo strtoupper($v_ji['nama']) ?></span>
	    </div>
	    <div class="col-md-12 cursor-p no-padding pesanan">
	    	<?php foreach ($v_ji['detail'] as $k_det => $v_det): ?>
	    		<?php $data_detail = 'kosong'; ?>
	    		<?php
	    			if ( !empty($v_det['pesanan_item_detail']) ) {
		    			foreach ($v_det['pesanan_item_detail'] as $k_jid => $v_jid) {
		    				$data_detail .= $v_jid['menu_kode'].$v_jid['jumlah'].$v_det['request'];
		    			}
		    		}
	    		?>
		        <div class="col-md-12 cursor-p no-padding menu" style="margin-bottom: 10px;" data-kode="<?php echo $v_det['menu_kode']; ?>" data-detail="<?php echo $data_detail; ?>">
			        <div class="col-md-11 no-padding menu_utama" onclick="jual.modalPaketMenu(this)" data-kode="<?php echo $v_det['menu_kode']; ?>">
				        <div class="col-md-6 no-padding">
				        <span class="nama_menu"><?php echo strtoupper($v_det['menu_nama']); ?></span>
				        <span> @ <span class="hrg"><?php echo angkaRibuan($v_det['harga']); ?></span></span>
				        </div>
				        <div class="col-md-2 text-right no-padding"><span class="jumlah"><?php echo angkaRibuan($v_det['jumlah']); ?></span></div>
				        <div class="col-md-3 text-right no-padding"><span class="total"><?php echo angkaRibuan($v_det['total']); ?></span></div>
			        </div>
			        <div class="col-md-1 text-center no-padding">
				        <span class="col-md-12" style="background-color: #a94442; border-radius: 3px; color: #ffffff; padding-left: 0px; padding-right: 0px;" onclick="jual.hapusMenu(this)">
				        	<i class="fa fa-times"></i>
				        </span>
			        </div>
			        <?php if ( !empty($v_det['pesanan_item_detail']) ): ?>
			        	<?php foreach ($v_det['pesanan_item_detail'] as $k_jid => $v_jid): ?>
					        <div class="col-md-11 detail no-padding" style="font-size:10px;" data-kode="<?php echo $v_jid['menu_kode']; ?>">
						        <div class="col-md-12 no-padding" style="padding-left: 15px;">
						        	<span class="nama_menu"><?php echo strtoupper($v_jid['menu_nama']); ?></span>
						        </div>
					        </div>
			        	<?php endforeach ?>
			        <?php endif ?>
			        <?php if ( !empty($v_det['request']) ): ?>
				        <div class="col-md-11 request no-padding" style="font-size:10px;">
					        <div class="col-md-12 no-padding" style="padding-left: 15px;">
					        	<span class="request"><?php echo strtoupper($v_det['request']); ?></span>
					        </div>
				        </div>
			        <?php endif ?>
		        </div>
	    	<?php endforeach ?>
	    </div>
	<?php endforeach ?>
</div>
<div class="col-md-12 no-padding" style="border-top: 1px solid #dedede;"><b>DISKON</b></div>
<div class="col-md-12 no-padding list_diskon">
	<?php if ( !empty($data['jual_diskon']) ): ?>
		<?php foreach ($data['jual_diskon'] as $k_jd => $v_jd): ?>
			<div class="col-md-12 cursor-p no-padding diskon" style="margin-bottom: 10px;" data-kode="<?php echo $v_jd['diskon']['kode']; ?>" data-persen="<?php echo $v_jd['diskon']['detail'][0]['persen']; ?>" data-nilai="<?php echo $v_jd['diskon']['detail'][0]['nilai']; ?>" data-nonmember="<?php echo $v_jd['diskon']['detail'][0]['non_member']; ?>" data-member="<?php echo $v_jd['diskon']['detail'][0]['member']; ?>" data-minbeli="<?php echo $v_jd['diskon']['detail'][0]['min_beli']; ?>" data-level="<?php echo $v_jd['diskon']['detail'][0]['level']; ?>">
				<div class="col-md-11 no-padding">
					<div class="col-md-12 no-padding">
						<span class="nama_diskon"><?php echo $v_jd['diskon_nama']; ?></span>
					</div>
				</div>
				<div class="col-md-1 text-center no-padding">
					<span class="col-md-12" style="background-color: #a94442; border-radius: 3px; color: #ffffff; padding-left: 0px; padding-right: 0px;" onclick="jual.hapusDiskon(this)">
						<i class="fa fa-times"></i>
					</span>
				</div>
			</div>
		<?php endforeach ?>
	<?php endif ?>
</div>