<div class="col-xs-12" style="padding-top: 15px;">
	<div class="col-xs-12 no-padding">
		<button type="button" class="btn btn-primary pull-left"><i class="fa fa-print"></i> Print Closing Shift</button>
		<button type="button" class="btn btn-primary pull-right" onclick="clo.saveClosingOrder()" <?php echo ($closing_order == 1) ? 'disabled' : ''; ?> ><i class="fa fa-clock-o"></i> Closing Order</button>
		<button type="button" class="btn btn-primary pull-right" style="margin-right: 10px;" onclick="clo.saveEndShift()" <?php echo ($closing_order == 1) ? 'disabled' : ''; ?>><i class="fa fa-clock-o"></i> End Shift</button>
	</div>
</div>
<div class="col-xs-12"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
<div class="col-xs-12">
	<div class="panel panel-default">
		<div class="panel-heading">Shift Detail</div>
		<div class="panel-body">
		</div>
	</div>
</div>
<div class="col-xs-12">
	<div class="panel panel-default">
		<div class="panel-heading">Sales Recapitulation</div>
		<div class="panel-body">
			<table class="table" style="margin-bottom: 0px; border-bottom: 1px solid #dedede;">
				<tbody>
					<tr>
						<td class="col-xs-8">Pending Sales</td>
						<td class="col-xs-4 text-right">0</td>
					</tr>
					<tr>
						<td class="col-xs-8">Sales Total</td>
						<td class="col-xs-4 text-right">0</td>
					</tr>
					<tr>
						<td class="col-xs-8">Discount Total</td>
						<td class="col-xs-4 text-right">0</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>