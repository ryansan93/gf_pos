<div class="modal-header no-padding header" style="">
    <span class="modal-title"><label class="label-control">Saldo Akhir Kasir</label></span>
    <?php if ( !empty($nominal) ): ?>
        <button type="button" class="close" data-dismiss="modal" style="color: #000000;">&times;</button>
    <?php endif ?>
</div>
<div class="modal-body body no-padding">
    <div class="row">
        <div class="col-lg-12" style="padding-top: 10px;">
            <div class="col-md-12 no-padding">
                <div class="col-md-2 no-padding"><label class="label-control" style="padding-top: 0px;">Nama</label></div>
                <div class="col-md-10 no-padding">
                    <label class="label-control" style="padding-top: 0px;">: <?php echo strtoupper($this->session->userdata()['detail_user']['nama_detuser']); ?></label>
                </div>
            </div>
            <div class="col-md-12 no-padding">
                <div class="col-md-2 no-padding"><label class="label-control" style="padding-top: 0px;">Waktu</label></div>
                <div class="col-md-10 no-padding">
                    <label class="label-control" style="padding-top: 0px;">: <?php echo strtoupper(tglIndonesia(date('Y-m-d h:s'), '-', ' ')).' '.substr(date('Y-m-d h:s'), 11, 5); ?></label>
                </div>
            </div>
            <div class="col-md-12 no-padding" style="padding-top: 2%;">
                <div class="input-group">
                    <span class="input-group-addon">
                      <b>Rp</b>
                    </span>
                    <input id="jumlah_uang" type="text" data-tipe="decimal" class="form-control text-right" placeholder="SALDO AKHIR KASIR" maxlength="13" value="<?php echo angkaDecimal($nominal); ?>">
                </div>
            </div>
            <div class="col-md-12 no-padding" style="padding-top: 2%;">
                <button type="button" class="col-md-12 btn btn-primary" onclick="co.saveSaldoAkhirKasir()"><i class="fa fa-check"></i> Simpan</button>
            </div>
        </div>
    </div>
</div>