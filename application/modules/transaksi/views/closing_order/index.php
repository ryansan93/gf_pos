<div class="col-xs-12" style="padding-top: 15px;">
	<div class="col-xs-12 no-padding">
		<button type="button" class="btn btn-primary pull-left" onclick="clo.printClosingOrder()"><i class="fa fa-print"></i> Print Closing Shift</button>
		<button type="button" class="btn btn-danger pull-right" onclick="clo.saveClosingOrder()" <?php echo ($closing_order == 1) ? 'disabled' : ''; ?> ><i class="fa fa-clock-o"></i> Closing Order Shift 2</button>
		<button type="button" class="btn btn-primary pull-right" style="margin-right: 10px;" onclick="clo.saveEndShift()" <?php echo ($closing_order == 1) ? 'disabled' : ''; ?>><i class="fa fa-clock-o"></i> End Shift</button>
	</div>
</div>
<div class="col-xs-12"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
<div class="col-xs-12 no-padding" style="height: 80%; overflow-y: scroll;">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">Sales Recapitulation</div>
			<div class="panel-body">
				<table class="table" style="margin-bottom: 0px; border-bottom: 1px solid #dedede;">
					<tbody>
						<tr>
							<td class="col-xs-8">Sales Total</td>
							<td class="col-xs-4 text-right"><?php echo angkaRibuan($sales_recapitulation['sales_total']); ?></td>
						</tr>
						<tr>
							<td class="col-xs-8">Pending Sales</td>
							<td class="col-xs-4 text-right"><?php echo angkaRibuan($sales_recapitulation['pending']); ?></td>
						</tr>
						<tr>
							<td class="col-xs-8">CL</td>
							<td class="col-xs-4 text-right"><?php echo angkaRibuan($sales_recapitulation['cl']); ?></td>
						</tr>
						<tr>
							<td class="col-xs-8">Discount Total</td>
							<td class="col-xs-4 text-right"><?php echo angkaRibuan($sales_recapitulation['discount']); ?></td>
						</tr>
						<tr>
							<td class="col-xs-8">Net Sales Total</td>
							<td class="col-xs-4 text-right"><?php echo angkaRibuan($sales_recapitulation['net_sales_total']); ?></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">Shift Detail</div>
			<div class="panel-body">
				<div class="col-xs-12 no-padding">
					<div class="col-xs-12 no-padding"><label class="control-label">Sales</label></div>
					<div class="col-xs-12 no-padding">
						<small>
							<table class="table" style="margin-bottom: 0px; border-bottom: 1px solid #dedede;">
								<thead>
									<tr>
										<th class="col-xs-5" style="background-color: transparent;">Menu</th>
										<th class="col-xs-4 text-right" style="background-color: transparent;">Qty</th>
										<th class="col-xs-3 text-right" style="background-color: transparent;">Value</th>
									</tr>
								</thead>
								<tbody>
									<?php $jumlah = 0; $total_value = 0; ?>
									<?php foreach ($shift_detail['data_sales'] as $k_km => $v_km): ?>
										<tr>
											<th colspan="3" style="background-color: transparent;"><?php echo $v_km['nama']; ?></th>
										</tr>
										<?php $kategori_jumlah = 0; $kategori_total_value = 0; ?>
										<?php foreach ($v_km['detail'] as $k_det => $v_det): ?>
											<tr>
												<td><?php echo $v_det['menu_nama']; ?></td>
												<td class="text-right"><?php echo angkaRibuan($v_det['jumlah']); ?></td>
												<td class="text-right"><?php echo angkaRibuan($v_det['total']); ?></td>
											</tr>
											<?php $kategori_jumlah += $v_det['jumlah']; $kategori_total_value += $v_det['total']; ?>
											<?php $jumlah += $v_det['jumlah']; $total_value += $v_det['total']; ?>
										<?php endforeach ?>
										<tr>
											<th class="text-right" style="background-color: transparent;">Total <?php echo $v_km['nama']; ?></th>
											<th class="text-right" style="background-color: transparent;"><?php echo angkaRibuan($kategori_jumlah); ?></th>
											<th class="text-right" style="background-color: transparent;"><?php echo angkaRibuan($kategori_total_value); ?></th>
										</tr>
									<?php endforeach ?>
									<tr>
										<th class="text-right" style="background-color: transparent;">Total</th>
										<th class="text-right" style="background-color: transparent;"><?php echo angkaRibuan($jumlah); ?></th>
										<th class="text-right" style="background-color: transparent;"><?php echo angkaRibuan($total_value); ?></th>
									</tr>
								</tbody>
							</table>
						</small>
					</div>
				</div>
				<div class="col-xs-12 no-padding"><br></div>
				<div class="col-xs-12 no-padding">
					<div class="col-xs-12 no-padding"><label class="control-label">Cashier</label></div>
					<div class="col-xs-12 no-padding">
						<small>
							<table class="table" style="margin-bottom: 0px; border-bottom: 1px solid #dedede;">
								<thead>
									<tr>
										<th class="col-xs-6" style="background-color: transparent;">No. Bill</th>
										<th class="col-xs-4 text-right" style="background-color: transparent;">Value</th>
									</tr>
								</thead>
								<tbody>
									<?php $total_value = 0; ?>
									<?php foreach ($shift_detail['data_cashier'] as $k_jk => $v_jk): ?>
										<tr>
											<th colspan="2" style="background-color: transparent;"><?php echo $v_jk['nama']; ?></th>
										</tr>
										<?php foreach ($v_jk['detail'] as $k_det => $v_det): ?>
											<tr>
												<td><?php echo $v_det['kode_faktur']; ?></td>
												<td class="text-right"><?php echo angkaRibuan($v_det['total']); ?></td>
											</tr>
											<?php $total_value += $v_det['total']; ?>
										<?php endforeach ?>
									<?php endforeach ?>
									<tr>
										<th class="text-right" style="background-color: transparent;">Total</th>
										<th class="text-right" style="background-color: transparent;"><?php echo angkaRibuan($total_value); ?></th>
									</tr>
								</tbody>
							</table>
						</small>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>