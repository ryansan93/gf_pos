<div class="modal-header no-padding header" style="">
	<span class="modal-title"><label class="label-control">GABUNG BILL</label></span>	
	<!-- <button type="button" class="close" data-dismiss="modal" style="color: #000000;">&times;</button> -->
</div>
<div class="modal-body body no-padding modal-gabung-bill">
	<div class="col-xs-12 no-padding" style="height: 90%; padding-top: 10px;">
		<div class="col-xs-6 no-padding" style="height: 100%; max-width: 46.5%;">
			<div class="col-xs-12 no-padding" style="height: 5%;"><label class="control-label">List Bill Belum Bayar</label></div>
			<div class="col-xs-12 no-padding belum_bayar" style="height: 90%;">
				<div class="col-md-12 search left-inner-addon no-padding" style="padding-bottom: 10px;">
					<i class="fa fa-search"></i><input class="form-control" type="search" data-table="tbl_belum_bayar" placeholder="Search" onkeyup="filter_all(this)">
				</div>
				<small>
					<table class="table table-bordered tbl_belum_bayar" style="margin-bottom: 0px;">
						<tbody>
							<?php foreach ($data_belum_bayar as $key => $value): ?>
								<tr class="search cursor-p" onclick="bayar.activeRow(this)" data-aktif="0" data-kodepesanan="<?php echo $value['kode_pesanan']; ?>">
									<td class="col-xs-3"><?php echo $value['lantai'].' - '.$value['meja']; ?></td>
									<!-- <td class="col-xs-2"><?php echo $value['kode_pesanan']; ?></td> -->
									<td class="col-xs-3"><?php echo !empty($value['member_group']) ? $value['member_group'].' - '.$value['pelanggan'] : $value['pelanggan']; ?></td>
									<td class="col-xs-2 text-right total"><?php echo angkaDecimal($value['total']); ?></td>
								</tr>
							<?php endforeach ?>
						</tbody>
					</table>
				</small>
			</div>
		</div>
		<div class="col-xs-1 no-padding" style="height: 100%; max-width: 7%; padding-left: 10px; padding-right: 10px; display: flex; justify-content: center; flex-direction: column; align-items: center;">
			<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
				<button type="button" class="col-xs-12 btn btn-default" onclick="bayar.changeRightAll()"><i class="fa fa-angle-double-right"></i></button>
			</div>
			<br>
			<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
				<button type="button" class="col-xs-12 btn btn-default" onclick="bayar.changeRight()"><i class="fa fa-angle-right"></i></button>
			</div>
			<br>
			<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
				<button type="button" class="col-xs-12 btn btn-default" onclick="bayar.changeLeft()"><i class="fa fa-angle-left"></i></button>
			</div>
			<br>
			<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
				<button type="button" class="col-xs-12 btn btn-default" onclick="bayar.changeLeftAll()"><i class="fa fa-angle-double-left"></i></button>
			</div>
			<br>
		</div>
		<div class="col-xs-6 no-padding" style="height: 100%; max-width: 46.5%;">
			<div class="col-xs-12 no-padding" style="height: 5%;"><label class="control-label">List Bill Gabung</label></div>
			<div class="col-xs-12 no-padding bill_gabung" style="height: 90%;">
				<small>
					<table class="table table-bordered" style="margin-bottom: 0px;">
						<tbody>
							<tr class="cursor-p" onclick="bayar.activeRow(this)" data-aktif="0" data-utama="1" data-kodepesanan="<?php echo $data_utama['kode_pesanan']; ?>">
								<td class="col-xs-3"><?php echo $data_utama['lantai'].' - '.$data_utama['meja']; ?></td>
								<!-- <td class="col-xs-2"><?php echo $data_utama['kode_pesanan']; ?></td> -->
								<td class="col-xs-3"><?php echo !empty($data_utama['member_group']) ? $data_utama['member_group'].' - '.$data_utama['pelanggan'] : $data_utama['pelanggan']; ?></td>
								<td class="col-xs-2 text-right total"><?php echo angkaDecimal($data_utama['total']); ?></td>
							</tr>
						</tbody>
					</table>
				</small>
			</div>
		</div>
	</div>
	<div class="col-xs-12 no-padding" style="height: 5%;">
		<div class="col-xs-12 no-padding">
			<div class="col-xs-6 no-padding" style="padding-right: 5px;">
				<button type="button" class="col-xs-12 btn btn-danger" onclick="bayar.modalListBill(this)" data-kode="<?php echo $data_utama['kode_pesanan']; ?>"><i class="fa fa-times"></i> Batal</button>
			</div>
			<div class="col-xs-6 no-padding" style="padding-left: 5px;">
				<button type="button" class="col-xs-12 btn btn-success" onclick="bayar.saveBillGabung()"><i class="fa fa-usd"></i> Bayar</button>
			</div>
		</div>
	</div>
</div>