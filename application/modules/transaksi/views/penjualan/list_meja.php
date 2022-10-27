<?php foreach ($data as $k_lantai => $v_lantai): ?>
	<?php foreach ($v_lantai['list_meja'] as $k_meja => $v_meja): ?>
		<div class="col-xs-2" style="padding-left: 0px;">
			<?php
				$class = 'btn-primary';
				$disabled = '';
				if ( $v_meja['aktif'] == 1 ) {
					$class = 'btn-danger';
					$disabled = 'disabled';
				}
			?>
			<button type="button" class="col-xs-12 btn <?php echo $class; ?>" <?php echo $disabled; ?> onclick="jual.pilihMeja(this)" data-id="<?php echo $v_meja['id']; ?>" data-namalantai="<?php echo $v_lantai['nama']; ?>"><?php echo strtoupper($v_meja['nama']); ?></button>
		</div>
	<?php endforeach ?>
<?php endforeach ?>