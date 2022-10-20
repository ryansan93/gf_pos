var mbr = {
	// startUp: function () {
	// }, // end - startUp

	modalMember: function () {
		$('.modal').modal('hide');

        $.get('master/Member/modalMember',{
        },function(data){
            var _options = {
                className : 'large',
                message : data,
                addClass : 'form',
                onEscape: true,
            };
            bootbox.dialog(_options).bind('shown.bs.modal', function(){
                $(this).css({'height': '100%'});
                $(this).find('.modal-header').css({'padding-top': '0px'});
                $(this).find('.modal-dialog').css({'width': '70%', 'max-width': '100%'});
                $(this).find('.modal-dialog').css({'height': '100%'});
                $(this).find('.modal-content').css({'width': '100%', 'max-width': '100%'});
                $(this).find('.modal-content').css({'height': '90%'});
                $(this).find('.modal-body').css({'height': '100%'});
                $(this).find('.bootbox-body').css({'height': '100%'});
                $(this).find('.bootbox-body .modal-body').css({'height': '100%'});
                $(this).find('.bootbox-body .modal-body .row').css({'height': '100%'});

                $('input').keyup(function(){
                    $(this).val($(this).val().toUpperCase());
                });

                $('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal]').each(function(){
                    $(this).priceFormat(Config[$(this).data('tipe')]);
                });

                var modal_dialog = $(this).find('.modal-dialog');
                var div = $(modal_dialog).find('.list_member');

                $(this).find('.btn_pilih').click(function() {mbr.pilihMember( $(this) ); });

                // $(this).find('.btn-cancel').click(function() { jual.modalPilihMember(); });
                // $(this).find('.btn-ok').click(function() { 
                //     kode_member = $(modal_dialog).find('tr[data-aktif=1] td.kode').text().toUpperCase();
                //     member = $(modal_dialog).find('tr[data-aktif=1] td.nama').text().toUpperCase();

                //     $('.member').attr('data-kode', kode_member);
                //     $('.member').text(member+' (MEMBER)');
                //     $('.list_menu').find('.jenis_pesanan').attr('data-kode', jenis_pesanan);
                //     $('.list_menu').find('.jenis_pesanan').text(nama_jenis_pesanan);

                //     $.map( $('div.kategori').find('ul.kategori li'), function(li) {
                //         var kategori = $(li).text();

                //         if ( kategori == 'PAKET' ) {
                //             $(li).click();
                //         }
                //     });

                //     $('.list_diskon').find('div.diskon[data-member=0]').remove();
                //     jual.hitDiskon();

                //     $('.modal').modal('hide');
                // });
            });
        },'html');
	}, // end - modalMember

    pilihMember: function(elm) {
        var div = $(elm).closest('div.detail');

        kode_member = $(div).find('.kode label').text().toUpperCase();
        member = $(div).find('.nama label').text().toUpperCase();

        $('.member').attr('data-kode', kode_member);
        $('.member').text(member+' (MEMBER)');
        $('.list_menu').find('.jenis_pesanan').attr('data-kode', jenis_pesanan);
        $('.list_menu').find('.jenis_pesanan').text(nama_jenis_pesanan);

        $.map( $('div.kategori').find('ul.kategori li'), function(li) {
            var kategori = $(li).text();

            if ( kategori == 'PAKET' ) {
                $(li).click();
            }
        });

        $('.list_diskon').find('div.diskon[data-member=0]').remove();
        jual.hitDiskon();

        $('.modal').modal('hide');
    }, // end - pilihMember

    addForm: function () {
        $('.modal').modal('hide');

        $.get('master/Member/addForm',{
        },function(data){
            var _options = {
                className : 'large',
                message : data,
                addClass : 'form',
                onEscape: true,
            };
            bootbox.dialog(_options).bind('shown.bs.modal', function(){
                $(this).find('.modal-header').css({'padding-top': '0px'});
                $(this).find('.modal-dialog').css({'width': '40%', 'max-width': '100%'});

                $('input').keyup(function(){
                    $(this).val($(this).val().toUpperCase());
                });

                $('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal]').each(function(){
                    $(this).priceFormat(Config[$(this).data('tipe')]);
                });

                $(this).find('.close').on('click', function() {
                    mbr.modalMember();
                });
            });
        },'html');
    }, // end - addForm

    viewForm: function (elm) {
        $('.modal').modal('hide');

        $.get('master/Member/viewForm',{
            'kode': $(elm).data('kode')
        },function(data){
            var _options = {
                className : 'large',
                message : data,
                addClass : 'form',
                onEscape: true,
            };
            bootbox.dialog(_options).bind('shown.bs.modal', function(){
                $(this).find('.modal-header').css({'padding-top': '0px'});
                $(this).find('.modal-dialog').css({'width': '40%', 'max-width': '100%'});

                $('input').keyup(function(){
                    $(this).val($(this).val().toUpperCase());
                });

                $('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal]').each(function(){
                    $(this).priceFormat(Config[$(this).data('tipe')]);
                });

                $(this).find('.close').on('click', function() {
                    mbr.modalMember();
                });
            });
        },'html');
    }, // end - viewForm

    editForm: function (elm) {
        var modal = $(elm).closest('.modal');

        $(modal).find('input, textarea').removeAttr('disabled');
        $(modal).find('.btn_view').addClass('hide');
        $(modal).find('.btn_edit').removeClass('hide');
    }, // end - editForm

    batalEdit: function(elm) {
        mbr.viewForm($(elm));
    }, // end - batalEdit

    save: function(elm) {
        var modal = $(elm).closest('.modal');

        var err = 0;

        $.map( $(modal).find('[data-required=1]'), function(ipt) {
            if ( empty( $(ipt).val() ) ) {
                $(ipt).parent().addClass('has-error');
                err++;
            } else {
                $(ipt).parent().removeClass('has-error');
            }
        });

        if ( err == 0 ) {
            bootbox.confirm('Apakah anda yakin ingin menyimpan data member ?', function( result ) {
                if ( result ) {
                    var params = {
                        'nama': $(modal).find('.nama').val(),
                        'no_telp': $(modal).find('.no_telp').val(),
                        'alamat': $(modal).find('.alamat').val(),
                        'privilege': $(modal).find('[name=optradio]:checked').val(),
                    };

                    $.ajax({
                        url: 'master/Member/save',
                        data: {
                            'params': params
                        },
                        type: 'POST',
                        dataType: 'JSON',
                        beforeSend: function() { showLoading(); },
                        success: function(data) {
                            hideLoading();

                            if ( data.status == 1 ) {
                                bootbox.alert(data.message, function() {
                                    mbr.modalMember();
                                });
                            } else {
                                bootbox.alert(data.message);
                            }
                        }
                    });
                }
            });
        }
    }, // end - save

    edit: function(elm) {
        var modal = $(elm).closest('.modal');

        var err = 0;

        $.map( $(modal).find('[data-required=1]'), function(ipt) {
            if ( empty( $(ipt).val() ) ) {
                $(ipt).parent().addClass('has-error');
                err++;
            } else {
                $(ipt).parent().removeClass('has-error');
            }
        });

        if ( err == 0 ) {
            bootbox.confirm('Apakah anda yakin ingin meng-ubah data member ?', function( result ) {
                if ( result ) {
                    var params = {
                        'kode': $(elm).data('kode'),
                        'nama': $(modal).find('.nama').val(),
                        'no_telp': $(modal).find('.no_telp').val(),
                        'alamat': $(modal).find('.alamat').val(),
                        'privilege': $(modal).find('[name=optradio]:checked').val(),
                    };

                    $.ajax({
                        url: 'master/Member/edit',
                        data: {
                            'params': params
                        },
                        type: 'POST',
                        dataType: 'JSON',
                        beforeSend: function() { showLoading(); },
                        success: function(data) {
                            hideLoading();

                            if ( data.status == 1 ) {
                                bootbox.alert(data.message, function() {
                                    mbr.modalMember();
                                });
                            } else {
                                bootbox.alert(data.message);
                            }
                        }
                    });
                }
            });
        }
    }, // end - edit

    delete: function(elm) {
        bootbox.confirm('Apakah anda yakin ingin meng-hapus data member ?', function( result ) {
            if ( result ) {
                var params = {
                    'kode': $(elm).data('kode')
                };

                $.ajax({
                    url: 'master/Member/delete',
                    data: {
                        'params': params
                    },
                    type: 'POST',
                    dataType: 'JSON',
                    beforeSend: function() { showLoading(); },
                    success: function(data) {
                        hideLoading();

                        if ( data.status == 1 ) {
                            bootbox.alert(data.message, function() {
                                mbr.modalMember();
                            });
                        } else {
                            bootbox.alert(data.message);
                        }
                    }
                });
            }
        });
    }, // end - delete

    modalSaldoMember: function () {
        $('.modal').modal('hide');

        $.get('master/Member/modalSaldoMember',{
        },function(data){
            var _options = {
                className : 'large',
                message : data,
                addClass : 'form',
                onEscape: true,
            };
            bootbox.dialog(_options).bind('shown.bs.modal', function(){
                $(this).css({'height': '100%'});
                $(this).find('.modal-header').css({'padding-top': '0px'});
                $(this).find('.modal-dialog').css({'width': '70%', 'max-width': '100%'});
                $(this).find('.modal-dialog').css({'height': '100%'});
                $(this).find('.modal-content').css({'width': '100%', 'max-width': '100%'});
                $(this).find('.modal-content').css({'height': '90%'});
                $(this).find('.modal-body').css({'height': '100%'});
                $(this).find('.bootbox-body').css({'height': '100%'});
                $(this).find('.bootbox-body .modal-body').css({'height': '100%'});
                $(this).find('.bootbox-body .modal-body .row').css({'height': '100%'});

                $('input').keyup(function(){
                    $(this).val($(this).val().toUpperCase());
                });

                $('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal]').each(function(){
                    $(this).priceFormat(Config[$(this).data('tipe')]);
                });

                var modal_dialog = $(this).find('.modal-dialog');
                var div = $(modal_dialog).find('.list_member');
            });
        },'html');
    }, // end - modalSaldoMember

    addSaldoForm: function () {
        $('.modal').modal('hide');

        $.get('master/Member/addSaldoForm',{
        },function(data){
            var _options = {
                className : 'large',
                message : data,
                addClass : 'form',
                onEscape: true,
            };
            bootbox.dialog(_options).bind('shown.bs.modal', function(){
                $(this).find('.modal-header').css({'padding-top': '0px'});

                $(this).find('.modal-dialog').css({'width': '40%', 'max-width': '100%'});

                $('input').keyup(function(){
                    $(this).val($(this).val().toUpperCase());
                });

                $('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal]').each(function(){
                    $(this).priceFormat(Config[$(this).data('tipe')]);
                });

                $(this).find('.member').select2();
                $(this).removeAttr('tabindex');

                $(this).find('.close').on('click', function() {
                    mbr.modalSaldoMember();
                });
            });
        },'html');
    }, // end - addSaldoForm

    viewSaldoForm: function (elm) {
        $('.modal').modal('hide');

        $.get('master/Member/viewSaldoForm',{
            'kode': $(elm).data('kode')
        },function(data){
            var _options = {
                className : 'large',
                message : data,
                addClass : 'form',
                onEscape: true,
            };
            bootbox.dialog(_options).bind('shown.bs.modal', function(){
                $(this).find('.modal-header').css({'padding-top': '0px'});
                $(this).find('.modal-dialog').css({'width': '40%', 'max-width': '100%'});

                $('input').keyup(function(){
                    $(this).val($(this).val().toUpperCase());
                });

                $('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal]').each(function(){
                    $(this).priceFormat(Config[$(this).data('tipe')]);
                });

                $(this).find('.close').on('click', function() {
                    mbr.modalSaldoMember();
                });
            });
        },'html');
    }, // end - viewSaldoForm

    editSaldoForm: function (elm) {
        var modal = $(elm).closest('.modal');

        $(modal).find('input, select').removeAttr('disabled');
        $(modal).find('.btn_view').addClass('hide');
        $(modal).find('.btn_edit').removeClass('hide');

        $(modal).find('.member').select2();
        $(modal).removeAttr('tabindex');
    }, // end - editSaldoForm

    batalEditSm: function(elm) {
        mbr.viewSaldoForm($(elm));
    }, // end - batalEditSm

    saveSm: function (elm) {
        var modal = $(elm).closest('.modal');

        var err = 0;

        $.map( $(modal).find('[data-required=1]'), function(ipt) {
            if ( empty( $(ipt).val() ) ) {
                $(ipt).parent().addClass('has-error');
                err++;
            } else {
                $(ipt).parent().removeClass('has-error');
            }
        });

        if ( err == 0 ) {
            bootbox.confirm('Apakah anda yakin ingin menyimpan data saldo member ?', function( result ) {
                if ( result ) {
                    var params = {
                        'kode_member': $(modal).find('.member').select2().val(),
                        'saldo': numeral.unformat($(modal).find('.saldo').val())
                    };

                    $.ajax({
                        url: 'master/Member/saveSm',
                        data: {
                            'params': params
                        },
                        type: 'POST',
                        dataType: 'JSON',
                        beforeSend: function() { showLoading(); },
                        success: function(data) {
                            hideLoading();

                            if ( data.status == 1 ) {
                                bootbox.alert(data.message, function() {
                                    mbr.modalSaldoMember();
                                });
                            } else {
                                bootbox.alert(data.message);
                            }
                        }
                    });
                }
            });
        }
    }, // end - saveSm

    editSm: function(elm) {
        var modal = $(elm).closest('.modal');

        var err = 0;

        $.map( $(modal).find('[data-required=1]'), function(ipt) {
            if ( empty( $(ipt).val() ) ) {
                $(ipt).parent().addClass('has-error');
                err++;
            } else {
                $(ipt).parent().removeClass('has-error');
            }
        });

        if ( err == 0 ) {
            bootbox.confirm('Apakah anda yakin ingin meng-ubah data saldo member ?', function( result ) {
                if ( result ) {
                    var params = {
                        'kode': $(elm).data('kode'),
                        'kode_member': $(modal).find('.member').select2().val(),
                        'saldo': numeral.unformat($(modal).find('.saldo').val())
                    };

                    $.ajax({
                        url: 'master/Member/editSm',
                        data: {
                            'params': params
                        },
                        type: 'POST',
                        dataType: 'JSON',
                        beforeSend: function() { showLoading(); },
                        success: function(data) {
                            hideLoading();

                            if ( data.status == 1 ) {
                                bootbox.alert(data.message, function() {
                                    mbr.modalSaldoMember();
                                });
                            } else {
                                bootbox.alert(data.message);
                            }
                        }
                    });
                }
            });
        }
    }, // end - editSm

    deleteSm: function(elm) {
        bootbox.confirm('Apakah anda yakin ingin meng-hapus data saldo member ?', function( result ) {
            if ( result ) {
                var params = {
                    'kode': $(elm).data('kode')
                };

                $.ajax({
                    url: 'master/Member/deleteSm',
                    data: {
                        'params': params
                    },
                    type: 'POST',
                    dataType: 'JSON',
                    beforeSend: function() { showLoading(); },
                    success: function(data) {
                        hideLoading();

                        if ( data.status == 1 ) {
                            bootbox.alert(data.message, function() {
                                mbr.modalSaldoMember();
                            });
                        } else {
                            bootbox.alert(data.message);
                        }
                    }
                });
            }
        });
    }, // end - deleteSm
};

// mbr.startUp();