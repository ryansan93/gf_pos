var dataMetodeBayar = [];
var dataHutangBayar = [];
var nama_member = null;
var kode_member = null;
var tr_split = null;
var dataDiskon = [];
var dataDiskonSave = null;
var fakturPrint = [];
var idxFaktur = 0;

var bayar = {
	startUp: function () {
        if (window.location.href.indexOf("pembayaranFormEdit") > -1) {
            var kodeFaktur = window.location.href.substring(window.location.href.lastIndexOf('/') + 1);
            bayar.loadDetailPembayaran( kodeFaktur, null );
        }

        if (window.location.href.indexOf("pembayaranFormHutangEdit") > -1) {
            var id = window.location.href.substring(window.location.href.lastIndexOf('/') + 1);
            bayar.loadDetailPembayaran( null, id );
        }
	}, // end - startUp

    filter_all: function (elm) {
        var _target = $(elm).data('target');

        var _div_target = $('.'+_target);
        var _div = $(_div_target).find('div.detail');
        var _content, _target;

        _div.show();
        _content = $(elm).val().toUpperCase().trim();

        if (!empty(_content) && _content != '') {
            $.map( $(_div), function(div){

                // CEK DI TR ADA ATAU TIDAK
                var ada = 0;
                $.map( $(div).find('.search'), function(div_val){
                    var _div_val = $(div_val).find('label').html().trim();
                    var _sensitive = $(div_val).attr('data-sensitive');

                    if ( _sensitive == 'false' ) {
                        if (_div_val.toUpperCase().indexOf(_content) > -1) {
                            ada = 1;
                        }
                    } else {
                        if (_div_val.toUpperCase() == _content) {
                            ada = 1;
                        }
                    }
                });

                if ( ada == 0 ) {
                    $(div).hide();
                } else {
                    $(div).show();
                };
            });
        }
    }, // end - filter_all

	modalListBayar: function () {
        $('.modal').modal('hide');

        $.ajax({
            url: 'transaksi/Pembayaran/modalListBayar',
            data: {},
            type: 'POST',
            dataType: 'JSON',
            beforeSend: function() { showLoading(); },
            success: function(data) {
                hideLoading();
                if ( data.status == 1 ) {
                    var _options = {
                        className : 'large',
                        message : data.html,
                        addClass : 'form',
                        onEscape: true,
                    };
                    bootbox.dialog(_options).bind('shown.bs.modal', function(){
                        $(this).css({'overflow-y': 'auto'});

                        $(this).find('.modal-header').css({'padding-top': '0px'});
                        $(this).find('.modal-dialog').css({'width': '90%', 'max-width': '100%'});

                        $('input').keyup(function(){
                            $(this).val($(this).val().toUpperCase());
                        });

                        $('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal]').each(function(){
                            $(this).priceFormat(Config[$(this).data('tipe')]);
                        });

                        var modal_body = $(this).find('.modal-body');
                        $.map( $(modal_body).find('li.nav-item'), function(li) {
                            $(li).click(function() {
                                var id = $(li).find('a').attr('href');

                                $(modal_body).find('.tab-pane').removeClass('show');
                                $(modal_body).find('.tab-pane').removeClass('active');

                                $(modal_body).find(id).addClass('show');
                                $(modal_body).find(id).addClass('active');
                            });
                        });
                    });
                } else {
                    bootbox.alert(data.message);
                }
            }
        });
    }, // end - modalListBayar

    modalListBill: function(elm) {
        $('.modal').modal('hide');

        var data = {
            'pesanan_kode': $(elm).data('kode'),
        };

        $.get('transaksi/Pembayaran/modalListBill',{
            'params': data
        },function(data){
            var _options = {
                className : 'large',
                message : data,
                addClass : 'form',
                onEscape: true,
            };
            bootbox.dialog(_options).bind('shown.bs.modal', function(){
                $(this).find('.modal-header').css({'padding-top': '0px'});
                $(this).find('.modal-dialog').css({'width': '50%', 'max-width': '100%'});
                $(this).find('.modal-content').css({'width': '100%', 'max-width': '100%'});

                $('input').keyup(function(){
                    $(this).val($(this).val().toUpperCase());
                });

                $('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal]').each(function(){
                    $(this).priceFormat(Config[$(this).data('tipe')]);
                });

                var modal_body = $(this).find('.modal-body');
            });
        },'html');
    }, // end - modalListBill

    modalSplitBill: function(elm) {
        $('.modal').modal('hide');

        var data = {
            'pesanan_kode': $(elm).data('kode'),
        };

        $.get('transaksi/Pembayaran/modalSplitBill',{
            'params': data
        },function(data){
            var _options = {
                className : 'large',
                message : data,
                addClass : 'form',
                onEscape: true,
            };
            bootbox.dialog(_options).bind('shown.bs.modal', function(){
                // $(this).find('.modal-header').css({'padding-top': '0px'});
                // $(this).find('.modal-dialog').css({'width': '70%', 'max-width': '100%'});
                // $(this).find('.modal-content').css({'width': '100%', 'max-width': '100%'});

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

                var modal_body = $(this).find('.modal-body');

                $(modal_body).find('.nav-tabs .nav-link:first').click();
                $(modal_body).find('.btn_remove').click(function() {
                    bayar.removeItem( $(this) );
                });

                $(modal_body).find('.btn_apply').click(function() {
                    bayar.modalJumlahSplit( $(this) );
                });
            });
        },'html');
    }, // end - modalSplitBill

    modalSplitBillMember: function() {
        kode_member = null;
        member = null;

        $.get('transaksi/Pembayaran/modalSplitBillMember',{
        },function(data){
            var _options = {
                className : 'large',
                message : data,
                addClass : 'form',
                onEscape: true,
            };
            bootbox.dialog(_options).bind('shown.bs.modal', function(){
                $(this).find('.modal-header').css({'padding-top': '0px'});
                $(this).find('.modal-dialog').css({'width': '60%', 'max-width': '100%'});
                $(this).find('.modal-content').css({'width': '100%', 'max-width': '100%'});

                $('input').keyup(function(){
                    $(this).val($(this).val().toUpperCase());
                });

                $('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal]').each(function(){
                    $(this).priceFormat(Config[$(this).data('tipe')]);
                });

                var modal_body = $(this).find('.modal-body');
            });
        },'html');
    }, // end - modalSplitBillMember

    modalMember: function () {
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

                $(this).find('.btn_pilih').click(function() { bayar.pilihMember( $(this) ); });
            });
        },'html');
    }, // end - modalMember

    pilihMember: function(elm) {
        var modal = $(elm).closest('.modal');

        var div = $(elm).closest('div.detail');

        kode_member = $(div).find('.kode label').text().toUpperCase();
        member = $(div).find('.nama label').text().toUpperCase();

        $('.member').val(member);

        $(modal).modal('hide');
    }, // end - pilihMember

    tambahBill: function(elm) {
        var modal = $(elm).closest('.modal');
        if ( !empty($(modal).find('.member').val()) ) {
            $(modal).modal('hide');

            $(modal).find('.member').parent().removeClass('has-error');
            if ( $(modal).find('.member').val() != member ) {
                kode_member = null;
            }

            member = $(modal).find('.member').val();

            var div = $('div.split');

            var  no_urut = 2;
            if ( $(div).find('li.nav-item').length > 0 ) {
                no_urut = $(div).find('li.nav-item').length + 2;
            }

            var header = '<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#bill'+no_urut+'" data-tab="bill'+no_urut+'" style="padding: 0px 5px; text-align: left;"><small>Bill '+no_urut+' - '+member+'</small></a></li>';
            var contain = '<div id="bill'+no_urut+'" class="tab-pane fade" role="tabpanel" data-kodemember="'+kode_member+'" data-member="'+member+'">';
            contain += '<table class="table table-nobordered">';
            contain += '<tbody>';
            contain += '</tbody>';
            contain += '</table>';
            contain += '</div>';

            $(div).find('.nav-tabs').append( $(header) );
            $(div).find('.tab-content').append( $(contain) );

            $(div).find('.nav-tabs .nav-link:last').click();
        } else {
            $(modal).find('.member').parent().addClass('has-error');
            bootbox.alert('Harap isi nama pelanggan terlebih dahulu.');
        }
    }, // end - tambahBill

    hapusBill: function() {
        var div = $('div.split');

        $.map( $(div).find('.tab-content .active button'), function(btn) {
            $(btn).click();
        });

        $(div).find('.active').closest('.nav-item').remove();
        $(div).find('.active').remove();
    }, // end - hapusBill

    modalJumlahSplit: function(elm) {
        var div = $('div.split');

        if ( $(div).find('.nav-tabs .nav-link').length > 0 ) {
            var kode = $(elm).closest('tr').attr('data-kode');
            var jumlah = $(elm).closest('tr').attr('data-kode');

            var params = {
                'pesanan_item_kode': kode,
                'jumlah': jumlah
            };

            $.get('transaksi/Pembayaran/modalJumlahSplit',{
                'params': params
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
                    $(this).find('.modal-dialog').css({'width': '40%', 'max-width': '100%'});
                    // $(this).find('.modal-dialog').css({'height': '100%'});
                    // $(this).find('.modal-content').css({'width': '100%', 'max-width': '100%'});
                    // $(this).find('.modal-content').css({'height': '90%'});
                    // $(this).find('.modal-body').css({'height': '100%'});
                    // $(this).find('.bootbox-body').css({'height': '100%'});
                    // $(this).find('.bootbox-body .modal-body').css({'height': '100%'});
                    // $(this).find('.bootbox-body .modal-body .row').css({'height': '100%'});

                    $('input').keyup(function(){
                        $(this).val($(this).val().toUpperCase());
                    });

                    $('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal]').each(function(){
                        $(this).priceFormat(Config[$(this).data('tipe')]);
                    });

                    tr_split = $(elm).closest('tr');
                });
            },'html');
        } else {
            bootbox.alert('Harap tambah bill terlebih dahulu.');
        }
    }, // end - modalJumlahSplit

    applyItem: function(elm) {
        var modal = $(elm).closest('.modal');

        var jumlah = numeral.unformat($(tr_split).find('td.jumlah').text());
        var pesanan_item_kode = $(elm).data('kode');

        var jumlah_pindah = numeral.unformat($(modal).find('input.jumlah').val());
        if ( !empty($(modal).find('input.jumlah').val()) && jumlah_pindah > 0 ) {
            $(modal).find('input').parent().removeClass('has-error');

            if ( jumlah_pindah > jumlah ) {
                bootbox.alert('Jumlah yang anda masukkan sudah melebihi, harap cek kembali.');
            } else {
                bayar.closeModal( $(elm) );

                var harga = numeral.unformat($(tr_split).find('.harga').text());

                var ppn = ($(tr_split).find('.total').attr('data-ppn') > 0) ? $(tr_split).find('.total').attr('data-ppn') / jumlah : 0;
                var sc = ($(tr_split).find('.total').attr('data-sc') > 0) ? $(tr_split).find('.total').attr('data-sc') / jumlah : 0;

                var sisa = jumlah - jumlah_pindah;

                $(tr_split).find('td.jumlah').text( numeral.formatInt(sisa) );
                var total = harga * sisa;
                var total_ppn = ppn * sisa;
                var total_sc = sc * sisa;
                $(tr_split).find('.total').text( numeral.formatInt(total) );
                $(tr_split).find('.total').attr( 'data-ppn', total_ppn);
                $(tr_split).find('.total').attr( 'data-sc', total_sc);
                if ( sisa == 0 ) {
                    $(tr_split).addClass('hide');
                }

                var tr_split_clone = $(tr_split).clone();
                $(tr_split_clone).removeClass('hide');
                $(tr_split_clone).find('td.jumlah').text( numeral.formatInt(jumlah_pindah) );
                $(tr_split_clone).find('button').removeClass('btn-primary');
                $(tr_split_clone).find('button').addClass('btn-danger');
                $(tr_split_clone).find('button').removeClass('btn_apply');
                $(tr_split_clone).find('button').addClass('btn_remove');
                $(tr_split_clone).find('button').click(function() {
                    bayar.removeItem( $(this) );
                });
                $(tr_split_clone).find('button i').removeClass('fa-plus');
                $(tr_split_clone).find('button i').addClass('fa-minus');

                var total_split = harga * jumlah_pindah;
                var total_split_ppn = ppn * jumlah_pindah;
                var total_split_sc = sc * jumlah_pindah;
                $(tr_split_clone).find('.total').text( numeral.formatInt(total_split) );
                $(tr_split_clone).find('.total').attr( 'data-ppn', total_split_ppn);
                $(tr_split_clone).find('.total').attr( 'data-sc', total_split_sc);

                $('div.active').find('table tbody').append( $(tr_split_clone) );
            }
        } else {
            $(modal).find('input').parent().addClass('has-error');
            bootbox.alert('Harap isi jumlah terlebih dahulu.');
        }
    }, // end - applyItem

    removeItem: function(elm) {
        var tr = $(elm).closest('tr');

        var div = $('div.main');

        var jumlah_remove = numeral.unformat($(tr).find('td.jumlah').text());
        var pesanan_item_kode = $(tr).data('kode');

        var tr_main = $(div).find('tr[data-kode="'+pesanan_item_kode+'"]');
        var jumlah = numeral.unformat($(tr_main).find('.jumlah').text());

        var ppn = ($(tr_split).find('.total').attr('data-ppn') > 0) ? $(tr_split).find('.total').attr('data-ppn') / jumlah : 0;
        var sc = ($(tr_split).find('.total').attr('data-sc') > 0) ? $(tr_split).find('.total').attr('data-sc') / jumlah : 0;

        var total_jumlah = jumlah + jumlah_remove;

        if ( total_jumlah > 0 ) {
            $(tr_main).removeClass('hide');

            var harga = numeral.unformat($(tr_main).find('.harga').text());
            var total = harga * total_jumlah;
            var total_ppn = ppn * total_jumlah;
            var total_sc = sc * total_jumlah;

            $(tr_main).find('.jumlah').text(numeral.formatInt(total_jumlah));
            $(tr_main).find('.total').text(numeral.formatInt(total));
            $(tr_main).find('.total').attr( 'data-ppn', total_ppn);
            $(tr_main).find('.total').attr( 'data-sc', total_sc);

            $(tr).remove();
        }
    }, // end - removeItem

    saveSplitBill: function(elm) {
        var modal = $(elm).closest('.modal');

        var kode_pesanan = $(elm).data('kode');

        var div_split = $(modal).find('div.split');
        var div_main = $(modal).find('div.main');

        var data_split = $.map( $(div_split).find('.tab-pane'), function(div_tab_pane) {
            if ( $(div_tab_pane).find('tr').length > 0 ) {
                var grand_total = 0;
                var grand_total_ppn = 0;
                var grand_total_sc = 0;
                var jual_item = $.map( $(div_tab_pane).find('tr'), function(tr) {
                    var jual_item_detail = null;
                    if ( $(tr).find('div.detail').length > 0 ) {
                        jual_item_detail = $.map( $(tr).find('div.detail'), function(div_detail) {
                            var _jual_item_detail = {
                                'menu_nama': $(div_detail).find('span.nama_menu').text().toUpperCase(),
                                'menu_kode': $(div_detail).data('kode'),
                            };
                            return  _jual_item_detail;
                        });
                    }

                    var _jual_item = {
                        'pesanan_item_kode': $(tr).data('kode'),
                        'kode_jenis_pesanan': $(tr).data('kodejp'),
                        'menu_nama': $(tr).find('span.menu_nama').text().toUpperCase(),
                        'menu_kode': $(tr).find('span.menu_nama').data('kode'),
                        'jumlah': numeral.unformat($(tr).find('.jumlah').text()),
                        'harga': numeral.unformat($(tr).find('.harga').text()),
                        'total': numeral.unformat($(tr).find('.total').text()),
                        'ppn': $(tr).find('.total').attr('data-ppn'),
                        'sc': $(tr).find('.total').attr('data-sc'),
                        'request': $(tr).find('span.request').text(),
                        'jual_item_detail': jual_item_detail
                    };

                    grand_total += numeral.unformat($(tr).find('.total').text());
                    grand_total_ppn += parseFloat($(tr).find('.total').attr('data-ppn'));
                    grand_total_sc += parseFloat($(tr).find('.total').attr('data-sc'));

                    return _jual_item;
                });

                var _data_split = {
                    'member': $(div_tab_pane).data('member'),
                    'kode_member': $(div_tab_pane).data('kodemember'),
                    'jual_item': jual_item,
                    'grand_total': grand_total,
                    'grand_total_ppn': grand_total_ppn,
                    'grand_total_sc': grand_total_sc
                };

                return _data_split;
            }
        });

        var data_main = null;
        if ( $(div_main).find('tr:not(.hide)').length > 0 ) {
            var grand_total = 0;
            var grand_total_ppn = 0;
            var grand_total_sc = 0;
            var jual_item = $.map( $(div_main).find('tr'), function(tr) {
                var jual_item_detail = null;
                if ( $(tr).find('div.detail').length > 0 ) {
                    jual_item_detail = $.map( $(tr).find('div.detail'), function(div_detail) {
                        var _jual_item_detail = {
                            'menu_nama': $(div_detail).find('span.nama_menu').text().toUpperCase(),
                            'menu_kode': $(div_detail).data('kode'),
                        };
                        return  _jual_item_detail;
                    });
                }

                var _jual_item = {
                    'pesanan_item_kode': $(tr).data('kode'),
                    'kode_jenis_pesanan': $(tr).data('kodejp'),
                    'menu_nama': $(tr).find('span.menu_nama').text().toUpperCase(),
                    'menu_kode': $(tr).find('span.menu_nama').data('kode'),
                    'jumlah': numeral.unformat($(tr).find('.jumlah').text()),
                    'harga': numeral.unformat($(tr).find('.harga').text()),
                    'total': numeral.unformat($(tr).find('.total').text()),
                    'ppn': $(tr).find('.total').attr('data-ppn'),
                    'sc': $(tr).find('.total').attr('data-sc'),
                    'request': $(tr).find('span.request').text(),
                    'jual_item_detail': jual_item_detail
                };

                grand_total += numeral.unformat($(tr).find('.total').text());
                grand_total_ppn += parseFloat($(tr).find('.total').attr('data-ppn'));
                grand_total_sc += parseFloat($(tr).find('.total').attr('data-sc'));

                return _jual_item;
            });

            var data_main = {
                'faktur_kode': $(div_main).data('kode'),
                'jual_item': jual_item,
                'grand_total': grand_total,
                'grand_total_ppn': grand_total_ppn,
                'grand_total_sc': grand_total_sc
            };
        }

        var data = {
            'pesanan_kode': kode_pesanan,
            'data_main': data_main,
            'data_split': data_split
        };

        $.ajax({
            url: 'transaksi/Pembayaran/saveSplitBill',
            data: {
                'params': data
            },
            type: 'POST',
            dataType: 'JSON',
            beforeSend: function() { showLoading(); },
            success: function(data) {
                hideLoading();
                if ( data.status == 1 ) {
                    var button = '<button type="button" class="col-xs-12 btn btn-danger" onclick="bayar.modalListBill(this)" data-kode="'+kode_pesanan+'"></button>';
                    $(button).click();
                } else {
                    bootbox.alert(data.message);
                }
            }
        });
    }, // end - saveSplitBill

    closeModal: function(elm) {
        var modal = $(elm).closest('.modal');

        $(modal).modal('hide');
    }, // end - closeModal

    pembayaranForm: function (elm) {
        var kodeFaktur = $(elm).data('kode');

        var baseurl = $('head base').attr('href');
        var pagePembayaran = baseurl + 'transaksi/Pembayaran/pembayaranForm/'+kodeFaktur;

        window.location.href = pagePembayaran;
    }, // end - pembayaranForm

    formFakturHutang: function(elm) {
        var tr = $(elm).closest('tr');
        var kode_faktur = $(tr).find('td.faktur').attr('data-val');

        if ( $(elm).is(':checked') ) {
            $.ajax({
                url: 'transaksi/Pembayaran/formFakturHutang',
                data: {
                    'params': kode_faktur
                },
                type: 'GET',
                dataType: 'HTML',
                beforeSend: function() { showLoading(); },
                success: function(html) {
                    hideLoading();

                    $('div.detail_faktur').append( html );

                    bayar.hitungTotalTagihan();
                }
            });
        } else {
            $('div.data[data-faktur="'+kode_faktur+'"]').remove();
        }
    }, // end - formFakturHutang

    hitungTotalTagihan: function() {
        dataHutangBayar = [];

        var total_tagihan = 0;
        $.map( $('input.pilih_hutang'), function(ipt_check) {
            var tr = $(ipt_check).closest('tr');

            var faktur_kode = $(tr).find('td.faktur').attr('data-val');
            var hutang = $(tr).find('td.hutang').attr('data-val');
            var sudah_bayar = $(tr).find('td.bayar').attr('data-val');

            var sisa_hutang = hutang - sudah_bayar;

            if ( $(ipt_check).is(':checked') ) {
                total_tagihan += sisa_hutang;
                if ( empty($(tr).find('input').val()) ) {
                    $(tr).find('input').val( numeral.formatInt(sisa_hutang) );
                }
                $(tr).find('input').removeAttr('readonly');
                $(tr).find('input').attr( 'data-val', sisa_hutang );

                var _dataHutangBayar = {
                    'faktur_kode': faktur_kode,
                    'hutang': hutang,
                    'sudah_bayar': sudah_bayar,
                    'bayar': numeral.unformat($(tr).find('input.nominal_bayar_hutang').val())
                };

                $('.faktur_hutang[data-faktur="'+faktur_kode+'"]').find('.bayar').text( numeral.formatInt(numeral.unformat($(tr).find('input.nominal_bayar_hutang').val())) );

                dataHutangBayar.push( _dataHutangBayar );
            } else {
                $(tr).find('input').attr('readonly', true);
                $(tr).find('input').val( 0 );
                $(tr).find('input').attr( 'data-val', 0 );
            }
        });

        var tagihan = numeral.unformat($('input.tagihan').val());

        total_tagihan += tagihan;

        $('input.total_tagihan').val( numeral.formatInt(total_tagihan) );

        bayar.hitungTotalBayar();
    }, // end - hitungTotalTagihan

    hitungTotalBayar: function() {
        var hutang = 0;

        $.map( $('.tbl_hutang').find('input[type=checkbox]'), function (ipt) {
            if ( $(ipt).is(':checked') ) {
                var tr = $(ipt).closest('tr');

                var nominal = numeral.unformat( $(tr).find('.nominal_bayar_hutang').val() );

                hutang += nominal;
            }
        });

        var kembalian = 0;
        var total_belanja = numeral.unformat( $('.tot_belanja').find('label').text() );
        var total_ppn = numeral.unformat( $('.ppn').find('label').text() );
        var total_service_charge = numeral.unformat( $('.service_charge').find('label').text() );
        var total_diskon = numeral.unformat( $('.diskon').val() );
        var tagihan = (total_belanja + total_ppn + total_service_charge );
        $('.tagihan').val( numeral.formatInt(tagihan) );
        var total_tagihan = tagihan - total_diskon;

        total_tagihan = (total_tagihan > 0 ) ? total_tagihan : 0;

        total_tagihan += hutang;

        $('.data .hutang').attr('data-val', hutang);
        $('.data .hutang').text( numeral.formatDec(hutang) );
        $('.total_tagihan').val( numeral.formatInt(total_tagihan) );

        $('.nota_diskon').attr('data-val', total_diskon);
        $('.nota_diskon').text( '('+numeral.formatDec(total_diskon)+')' );
        $('.nota_total_bayar').attr('data-val', total_tagihan);
        $('.nota_total_bayar').text( numeral.formatDec(total_tagihan) );

        var total_bayar = 0;
        if ( dataMetodeBayar.length > 0 ) {
            for (var i = 0; i < dataMetodeBayar.length; i++) {
                if ( !empty(dataMetodeBayar[i]) ) {
                    total_bayar += parseInt(dataMetodeBayar[i].jumlah);
                }
            }
        }


        var _total_bayar = (total_bayar < 0) ? 0 : total_bayar;
        kembalian = (total_bayar < 0) ? 0 : ((total_bayar - total_tagihan < 0) ? 0 : total_bayar - total_tagihan);

        $('.total_bayar').val( numeral.formatInt(_total_bayar) );
        $('div.data').find('.jml_bayar').text( numeral.formatDec(_total_bayar) );
        $('div.data').find('.jml_bayar').attr('data-val', _total_bayar);

        if ( kembalian > 0 ) {
            // $('.kembalian').val( numeral.formatInt(kembalian) );
            $('input.kembalian').val( numeral.formatInt(kembalian) );
            $('.data .kembalian').text( numeral.formatDec(kembalian) );
            $('.data .kembalian').attr('data-val', kembalian);
        } else {
            // $('.kembalian').val( numeral.formatInt(0) );
            $('input.kembalian').val( numeral.formatInt(0) );
            $('.data .kembalian').text( numeral.formatDec(0) );
            $('.data .kembalian').attr('data-val', 0);
        }

        bayar.hitungSisaTagihan();
    }, // end - hitungTotalBayar

    hitungSisaTagihan: function() {
        var sisa_tagihan = 0;
        
        var total_tagihan = numeral.unformat($('input.total_tagihan').val());
        var total_bayar = numeral.unformat($('input.total_bayar').val());

        sisa_tagihan = (total_tagihan <= total_bayar) ? 0 : total_tagihan - total_bayar;

        $('input.sisa_tagihan').val( numeral.formatInt(sisa_tagihan) );

        bayar.hitKategoriPembayaran();
    }, // end - hitungSisaTagihan

    cekNominalBayarHutang: function(elm) {
        var nominal_bayar_hutang = numeral.unformat($(elm).val());

        $(elm).attr( 'value', numeral.formatInt( nominal_bayar_hutang ) );

        var hutang = $(elm).attr('data-val');
        var jenis_kartu = $(elm).attr('data-jk');

        var div_saldo_member = $('div.saldo_member');
        if ( $(div_saldo_member).length > 0 ) {
            var jumlah = numeral.unformat( $(elm).val() );
            var saldo = numeral.unformat( $(div_saldo_member).find('input.saldo').val() );

            if ( jumlah > saldo ) {
                bootbox.alert('Nominal yang anda masukkan melebihi saldo, harap cek kembali nominal yang anda inputkan.', function() {
                    $(elm).val( numeral.formatInt(0) );
                });
            } else {
                var sisa_saldo = saldo - jumlah;

                $(div_saldo_member).find('input.sisa_saldo').val( numeral.formatInt(sisa_saldo) );
            }
        } else {
            // if ( !empty(jenis_kartu) ) {
                if ( nominal_bayar_hutang > hutang ) {
                    bootbox.alert('Nominal yang anda masukkan melebihi hutang sejumlah <b>Rp. '+numeral.formatInt(hutang)+',00</b>', function() {
                        $(elm).val( numeral.formatInt(hutang) );
                    });
                } else {
                    bayar.hitungTotalTagihan();
                }
            // }
        }
    }, // end - cekNominalBayarHutang

    modalMetodePembayaran: function (elm) {
        $('.modal').modal('hide');

        var hutang = 0;

        $.map( $('.tbl_hutang').find('input[type=checkbox]'), function (ipt) {
            if ( $(ipt).is(':checked') ) {
                var tr = $(ipt).closest('tr');

                var nominal = numeral.unformat( $(tr).find('.nominal_bayar_hutang').val() );

                hutang += nominal;
            }
        });

        var params = {
            'nama': $(elm).text(),
            'kode_jenis_kartu': $(elm).data('kode'),
            'kategori_jenis_kartu_id': $(elm).data('kategori'),
            'cl': $(elm).attr('data-cl'),
            'total_bayar': numeral.unformat($('input.total_bayar').val()),
            'sisa_tagihan': numeral.unformat($('input.sisa_tagihan').val()),
            'member_kode': $('.kode_member').attr('data-val'),
            'faktur_kode': $('.kode_faktur').attr('data-val'),
            'hutang': hutang,
            'data_diskon': dataDiskon
        };

        $.get('transaksi/Pembayaran/modalMetodePembayaran',{
            'kode_faktur': $(elm).attr('data-kodefaktur'),
            'params': params
        },function(data){
            var _options = {
                className : 'large',
                message : data,
                addClass : 'form',
                onEscape: true,
            };
            bootbox.dialog(_options).bind('shown.bs.modal', function(){
                $(this).find('.modal-header').css({'padding-top': '0px'});
                $(this).find('.modal-dialog').css({'width': '60%', 'max-width': '100%'});
                $(this).find('.modal-content').css({'width': '100%', 'max-width': '100%'});

                $('input').keyup(function(){
                    $(this).val($(this).val().toUpperCase());
                });

                $('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal]').each(function(){
                    $(this).priceFormat(Config[$(this).data('tipe')]);
                });

                $("input.jml_bayar").focus();

                var modal_body = $(this).find('.modal-body');
            });
        },'html');
    }, // end - modalMetodePembayaran

    saveMetodePembayaran: function(elm) {
        var modal = $(elm).closest('.modal-body');

        var cl = $(elm).attr('data-cl');

        $('div.jenis_pembayaran').find('button').removeAttr('disabled');
        if ( cl == 1 ) {
            $('div.jenis_pembayaran').find('button:not([data-cl="1"])').attr('disabled', 'disabled');
        } else {
            $('div.jenis_pembayaran').find('button[data-cl="1"]').attr('disabled', 'disabled');
        }

        var _dataMetodeBayar = {
            'nama': $(elm).data('nama'),
            'kode_jenis_kartu': $(elm).data('kode'),
            'kategori_jenis_kartu': $(elm).data('kategori'),
            'no_kartu': $(modal).find('.no_kartu').val(),
            'nama_kartu': $(modal).find('.nama_kartu').val(),
            'jumlah': numeral.unformat($(modal).find('.jml_bayar').val()),
            'cl': cl
        };

        dataMetodeBayar.push( _dataMetodeBayar );
        
        // bayar.hitKategoriPembayaran();
        bayar.getDataDiskon( $(elm).attr('data-kodefaktur') );

        $('.modal').modal('hide');
    }, // end - saveMetodePembayaran

    hitKategoriPembayaran: function() {
        $('.kategori_jenis_kartu').attr('data-val', 0);
        $('.kategori_jenis_kartu').text( numeral.formatDec(0) );

        var jenis_bayar = '';

        if ( !empty(dataMetodeBayar) ) {
            for (var i = 0; i < dataMetodeBayar.length; i++) {
                if ( !empty(dataMetodeBayar[i]) ) {
                    var id = dataMetodeBayar[i].kategori_jenis_kartu;
                    var val = $('.kategori_jenis_kartu'+id).attr('data-val');

                    console.log( $('.nota_total_bayar').attr('data-val') );

                    var total = (dataMetodeBayar[i].cl == 0) ? dataMetodeBayar[i].jumlah : $('.nota_total_bayar').attr('data-val');

                    $('.kategori_jenis_kartu'+id).attr('data-val', total);
                    $('.kategori_jenis_kartu'+id).text( numeral.formatDec(total) );

                    jenis_bayar += '<span>'+dataMetodeBayar[i].nama+'</span>';
                    if ( typeof dataMetodeBayar[i+1] !== 'undefined' ) {
                        jenis_bayar += '<br>';
                    }
                }
            }
        }

        $('.faktur_hutang').find('.pembayaran label').html( jenis_bayar );
    }, // end - hitKategoriPembayaran

    modalPembayaran: function(elm) {
        $('.modal').modal('hide');

        var data = {
            'faktur_kode': $(elm).data('kode'),
            'id': $(elm).data('id'),
            'diskon': numeral.unformat($('.diskon').val()),
            'jml_tagihan': numeral.unformat($('.total_tagihan').val()),
            'jml_bayar': numeral.unformat($('.total_bayar').val()),
            'kembalian': numeral.unformat($('.kembalian').val()),
            'dataMetodeBayar': dataMetodeBayar
        };

        $.get('transaksi/Pembayaran/modalPembayaran',{
            'params': data
        },function(data){
            var _options = {
                className : 'large',
                message : data,
                addClass : 'form',
                onEscape: true,
            };
            bootbox.dialog(_options).bind('shown.bs.modal', function(){
                $(this).find('.modal-header').css({'padding-top': '0px'});
                $(this).find('.modal-dialog').css({'width': '60%', 'max-width': '100%'});
                $(this).find('.modal-content').css({'width': '100%', 'max-width': '100%'});

                $('input').keyup(function(){
                    $(this).val($(this).val().toUpperCase());
                });

                $('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal]').each(function(){
                    $(this).priceFormat(Config[$(this).data('tipe')]);
                });

                var modal_body = $(this).find('.modal-body');
            });
        },'html');
    }, // end - modalPembayaran

    hapusMetodePembayaran: function(elm) {
        var id = $(elm).data('id');

        var tr = $(elm).closest('tr');
        $(tr).remove();

        dataMetodeBayar.splice(id, (id+1));

        if ( dataMetodeBayar.length == 0 ) {
            $('div.jenis_pembayaran').find('button').removeAttr('disabled');
        }

        // bayar.hitKategoriPembayaran();
        bayar.getDataDiskon( $(elm).attr('data-kode') );
    }, // end - hapusMetodePembayaran

    savePembayaran: function(elm) {
        var modal = $(elm).closest('.modal');

        var jml_tagihan = numeral.unformat($(modal).find('.total_tagihan').val());
        var jml_bayar = numeral.unformat($(modal).find('.total_bayar').val());

        var save = 0;
        if ( jml_tagihan > jml_bayar ) {
            bootbox.confirm('Pembayaran kurang dari tagihan apakah anda tetap ingin melanjutkan pembayaran ?', function(result) {
                if ( result ) {
                    bayar.execSavePembayaran(elm);
                }
            });
        } else {
            bayar.execSavePembayaran(elm);
        }
    }, // end - savePembayaran

    execSavePembayaran: function(elm) {
        var modal = $(elm).closest('.modal');
        
        var data = {
            'faktur_kode': $(elm).data('kode'),
            'id': $(elm).data('id'),
            'tot_belanja': numeral.unformat($('.tot_belanja').find('label').text()),
            'ppn': numeral.unformat($('.ppn').find('label').text()),
            'service_charge': numeral.unformat($('.service_charge').find('label').text()),
            'diskon': numeral.unformat($(modal).find('.diskon').val()),
            'jml_tagihan': numeral.unformat($(modal).find('.total_tagihan').val()),
            'jml_bayar': numeral.unformat($(modal).find('.total_bayar').val()),
            'kembalian': numeral.unformat($(modal).find('.kembalian').val()),
            'member_kode': $('.kode_member').attr('data-val'),
            'member': $('.member').attr('data-val'),
            'dataMetodeBayar': dataMetodeBayar,
            'dataHutangBayar': dataHutangBayar,
            'dataDiskon': dataDiskonSave
        };

        var idx = 0;
        if ( $('.data').length > 0 ) {
            $.map( $('.data'), function (div) {
                fakturPrint[idx] = {
                    'kode_faktur': $(div).attr('data-faktur'),
                    'jenis': ($(div).hasClass('faktur_hutang')) ? 'hutang' : ''
                };

                idx++;
            });
        }

        $.ajax({
            url: 'transaksi/Pembayaran/savePembayaran',
            data: {
                'params': data
            },
            type: 'POST',
            dataType: 'JSON',
            beforeSend: function() { showLoading(); },
            success: function(data) {
                hideLoading();
                if ( data.status == 1 ) {
                    bayar.printNota(data.content.id_bayar);
                    // if ( data.print_nota == 1 ) {
                    // } else {
                    //     bootbox.alert(data.message, function() {
                    //         bayar.penjualanForm();
                    //     });
                    // }
                } else {
                    bootbox.alert(data.message);
                }
            }
        });
    }, // end - execSavePembayaran

    printNota: function (id_bayar) {
        var data = {
            'id_bayar': id_bayar,
            'faktur_kode': fakturPrint[idxFaktur]['kode_faktur'],
            'jenis': fakturPrint[idxFaktur]['jenis'],
        };

        $.ajax({
            url: 'transaksi/Pembayaran/printNota',
            data: {
                'params': data
            },
            type: 'POST',
            dataType: 'JSON',
            beforeSend: function() { showLoading('Print Nota ...'); },
            success: function(data) {
                hideLoading();
                if ( data.status == 1 ) {
                    idxFaktur++;
                    if ( typeof fakturPrint[idxFaktur] !== 'undefined' ) {
                        bayar.printNota(id_bayar);
                    } else {
                        bayar.penjualanForm();
                    }
                } else {
                    bootbox.alert(data.message);
                }
            }
        });
    }, // end  - printNota

    rePrintNota: function (elm) {
        var idx = 0;
        fakturPrint[idx] = {
            'kode_faktur': $(elm).attr('data-faktur'),
            'jenis': ''
        };

        bayar.printNota( $(elm).attr('data-id') );
    }, // end - rePrintNota

    printDraft: function (elm) {
        var data = {
            'faktur_kode': $(elm).attr('data-kode'),
            'tot_belanja': numeral.unformat($('.tot_belanja').find('label').text()),
            'diskon': $('.nota_diskon').attr('data-val'),
            'ppn': numeral.unformat($('.ppn').find('label').text()),
            'service_charge': numeral.unformat($('.service_charge').find('label').text()),
            'jml_tagihan': $('.nota_total_bayar').attr('data-val')
        };

        $.ajax({
            url: 'transaksi/Pembayaran/printDraft',
            data: {
                'params': data
            },
            type: 'POST',
            dataType: 'JSON',
            beforeSend: function() { showLoading('Print Draft ...'); },
            success: function(data) {
                hideLoading();
                if ( data.status == 1 ) {
                    bootbox.alert(data.message);
                } else {
                    bootbox.alert(data.message);
                }
            }
        });
    }, // end  - printDraft

    penjualanForm: function () {
        var baseurl = $('head base').attr('href');
        var pagePenjualan = baseurl + 'transaksi/Penjualan';

        window.location.href = pagePenjualan;
    }, // end - penjualanForm

    batal: function() {
        bayar.penjualanForm();
    }, // end - batal

    saveHutang: function(elm) {
        bootbox.prompt({
            title: 'Alasan Hutang',
            inputType: 'textarea',
            callback: function (alasan) {
                if ( alasan != null ) {
                    var params = {
                        'faktur_kode': $(elm).data('kode'),
                        'alasan': alasan
                    }

                    $.ajax({
                        url: 'transaksi/Pembayaran/saveHutang',
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
                                    bayar.batal();
                                });
                            } else {
                                bootbox.alert(data.message);
                            }
                        }
                    });
                }
            }
        });
    }, // end - hutang

    pembayaranFormEdit: function (elm) {
        var kodeFaktur = $(elm).data('kode');

        var baseurl = $('head base').attr('href');
        var pagePembayaran = baseurl + 'transaksi/Pembayaran/pembayaranFormEdit/'+kodeFaktur;

        window.location.href = pagePembayaran;
    }, // end - pembayaranFormEdit

    loadDetailPembayaran: function (kodeFaktur, id) {
        var params = {
            'faktur_kode': kodeFaktur,
            'id': id
        }

        $.ajax({
            url: 'transaksi/Pembayaran/loadDetailPembayaran',
            data: {
                'params': params
            },
            type: 'POST',
            dataType: 'JSON',
            beforeSend: function() { showLoading(); },
            success: function(data) {
                hideLoading();

                if ( data.status == 1 ) {
                    if ( !empty(data.content.dataMetodeBayar) ) {
                        for (var i = 0; i < data.content.dataMetodeBayar.length; i++) {
                            var _dataMetodeBayar = {
                                'nama': data.content.dataMetodeBayar[i].nama,
                                'kode_jenis_kartu': data.content.dataMetodeBayar[i].kode_jenis_kartu,
                                'no_kartu': data.content.dataMetodeBayar[i].no_kartu,
                                'nama_kartu': data.content.dataMetodeBayar[i].nama_kartu,
                                'jumlah': data.content.dataMetodeBayar[i].jumlah
                            };

                            dataMetodeBayar.push( _dataMetodeBayar );
                        }
                    }

                    if ( !empty(data.content.dataHutangBayar) ) {
                        for (var i = 0; i < data.content.dataHutangBayar.length; i++) {
                            var _dataHutangBayar = {
                                'faktur_kode': data.content.dataHutangBayar[i].faktur_kode,
                                'hutang': data.content.dataHutangBayar[i].hutang,
                                'sudah_bayar': data.content.dataHutangBayar[i].sudah_bayar,
                                'bayar': data.content.dataHutangBayar[i].bayar,
                            };

                            dataHutangBayar.push( _dataHutangBayar );
                        }
                    }

                    if ( !empty(data.content.dataDiskon) ) {
                        for (var i = 0; i < data.content.dataDiskon.length; i++) {
                            dataDiskon[ i ] = data.content.dataDiskon[i].diskon_kode;
                        }

                        dataDiskonSave = data.content.dataDiskon;
                    }
                } else {
                    bootbox.alert(data.message);
                }
            }
        });
    }, // end - loadDataPembayaran

    deletePembayaran: function(elm) {
        bootbox.confirm('Apakah anda yakin ingin menghapus data pembayaran ini ?', function (result) {
            if ( result ) {                
                var params = {
                    'faktur_kode': $(elm).data('kode')
                }

                $.ajax({
                    url: 'transaksi/Pembayaran/deletePembayaran',
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
                                bayar.penjualanForm();
                            });
                        } else {
                            bootbox.alert(data.message);
                        }
                    }
                });
            }
        });
    }, // end - deletePembayaran

    modalGabungBill: function(elm) {
        $('.modal').modal('hide');

        var data = {
            'faktur_kode': $(elm).data('kode'),
        };

        $.get('transaksi/Pembayaran/modalGabungBill',{
            'params': data
        },function(data){
            var _options = {
                className : 'large',
                message : data,
                addClass : 'form',
                onEscape: true,
            };
            bootbox.dialog(_options).bind('shown.bs.modal', function(){
                // $(this).find('.modal-header').css({'padding-top': '0px'});
                // $(this).find('.modal-dialog').css({'width': '70%', 'max-width': '100%'});
                // $(this).find('.modal-content').css({'width': '100%', 'max-width': '100%'});

                $(this).css({'height': '100%'});
                $(this).find('.modal-header').css({'padding-top': '0px'});
                $(this).find('.modal-dialog').css({'width': '80%', 'max-width': '100%'});
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

                var modal_body = $(this).find('.modal-body');

                // $(modal_body).find('.nav-tabs .nav-link:first').click();
                // $(modal_body).find('.btn_remove').click(function() {
                //     bayar.removeItem( $(this) );
                // });

                // $(modal_body).find('.btn_apply').click(function() {
                //     bayar.modalJumlahSplit( $(this) );
                // });
            });
        },'html');
    }, // end - modalGabungBill

    activeRow: function (elm) {
        var aktif = $(elm).attr('data-aktif');

        if ( aktif == 0 ) {
            $(elm).attr('data-aktif', 1);
        } else {
            $(elm).attr('data-aktif', 0);
        }
    }, // end - activeRow

    changeRightAll: function () {
        var table_left = $('table.tbl_belum_bayar');
        var table_right = $('div.bill_gabung table');

        var tr_aktif = $(table_left).find('tr:visible').remove().clone();
        $(tr_aktif).attr('data-aktif', 0);

        $(table_right).find('tbody').append( $(tr_aktif) );
    }, // end - changeRightAll

    changeRight: function () {
        var table_left = $('table.tbl_belum_bayar');
        var table_right = $('div.bill_gabung table');

        var tr_aktif = $(table_left).find('tr[data-aktif=1]').remove().clone();
        $(tr_aktif).attr('data-aktif', 0);

        $(table_right).find('tbody').append( $(tr_aktif) );
    }, // end - changeRight

    changeLeft: function () {
        var table_left = $('table.tbl_belum_bayar');
        var table_right = $('div.bill_gabung table');

        var tr_aktif = $(table_right).find('tr[data-aktif=1]:not([data-utama=1])').remove().clone();
        $(tr_aktif).attr('data-aktif', 0);

        $(table_left).find('tbody').append( $(tr_aktif) );
    }, // end - changeLeft

    changeLeftAll: function () {
        var table_left = $('table.tbl_belum_bayar');
        var table_right = $('div.bill_gabung table');

        var tr_aktif = $(table_right).find('tr:visible:not([data-utama=1])').remove().clone();
        $(tr_aktif).attr('data-aktif', 0);

        $(table_left).find('tbody').append( $(tr_aktif) );
    }, // end - changeLeftAll

    saveBillGabung: function (elm) {
        var table = $('div.bill_gabung table');

        var _bayar = $(elm).attr('data-bayar');

        var data_utama = {
            'utama': $(table).find('tbody tr[data-utama=1]').attr('data-utama'),
            'kode_faktur': $(table).find('tbody tr[data-utama=1]').attr('data-kodefaktur'),
            'total': numeral.unformat($(table).find('tbody tr[data-utama=1] .total').text())
        };

        var data = $.map( $(table).find('tbody tr:not([data-utama=1])'), function (tr) {
            var _data = {
                'utama': $(tr).attr('data-utama'),
                'kode_faktur': $(tr).attr('data-kodefaktur'),
                'total': numeral.unformat($(tr).find('.total').text())
            };

            return _data;
        });

        var params = {
            'data_utama': data_utama,
            'data': data
        };

        $.ajax({
            url: 'transaksi/Pembayaran/saveBillGabung',
            data: {
                'params': params
            },
            type: 'POST',
            dataType: 'JSON',
            beforeSend: function() { showLoading(); },
            success: function(data) {
                if ( data.status == 1 ) {
                    if ( _bayar == 1 ) {
                        var btn = '<button type="button" data-kode="'+data.content.kode+'"></button>';

                        bayar.pembayaranForm( $(btn) );
                    } else {
                        bayar.penjualanForm();
                    }
                } else {
                    bootbox.alert(data.message);
                }
            }
        });
    }, // end - saveBillGabung

    modalMemberSplitBill: function () {
        $.get('transaksi/Pembayaran/modalMemberSplitBill',{
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
                $(this).find('.modal-dialog').css({'width': '90%', 'max-width': '100%'});
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

                $(this).find('.btn_pilih').click(function() { bayar.pilihMember( $(this) ); });
            });
        },'html');
    }, // end - modalMemberSplitBill

    modalDiskon: function (elm) {
        var kode_faktur = $(elm).attr('data-kode');

        $.get('transaksi/Pembayaran/modalDiskon',{
            'kode_faktur': kode_faktur
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

                $('.tbl_diskon').find('tr.data').attr('data-aktif', 0);
            });
        },'html');
    }, // end - modalDiskon

    pilihDiskon: function (elm) {
        var aktif = $(elm).attr('data-aktif');

        if ( aktif == 1 ) {
            $(elm).attr('data-aktif', 0);
        } else {
            $(elm).attr('data-aktif', 1);
        }
    }, // end - pilihDiskon

    applyDiskon: function (elm) {
        var kodeFaktur = $(elm).attr('data-kode');

        dataDiskon = [];
        $('.diskon').val( 0 );
        dataDiskonSave = null;

        var idx = 0;
        $.map( $('.tbl_diskon').find('tr.data[data-aktif=1]'), function (tr) {
            dataDiskon[ idx ] = $(tr).attr('data-kode');

            idx++;
        });

        if ( dataDiskon.length > 0 ) {
            bayar.getDataDiskon( kodeFaktur );
        } else {
            bayar.hitungTotalBayar();
        }

        $('.modal').modal('hide');
    }, // end - applyDiskon

    getDataDiskon: function (kodeFaktur) {
        // var metodeBayar = [];
        // for (var i = 0; i < dataMetodeBayar.length; i++) {
        //     metodeBayar[i] = {

        //     }dataMetodeBayar[i].kode_jenis_kartu;
        //     // if ( !empty(dataMetodeBayar[i].kode_jenis_kartu) ) {
        //     // }
        // }

        var params = {
            'kode_faktur': kodeFaktur,
            'data_diskon': dataDiskon,
            'data_metode_bayar': dataMetodeBayar
        };

        $.ajax({
            url: 'transaksi/Pembayaran/getDataDiskon',
            data: {
                'params': params
            },
            type: 'POST',
            dataType: 'JSON',
            beforeSend: function() { showLoading(); },
            success: function(data) {
                hideLoading();

                if ( data.status == 1 ) {
                    $('.diskon').val( numeral.formatInt( data.content.total_diskon ) );

                    var ppn = $('.ppn').attr('data-real');
                    var service_charge = $('.service_charge').attr('data-real');

                    var ppn_new = parseFloat(data.content.total_ppn);
                    var service_charge_new = parseFloat(data.content.total_service_charge);

                    $('.ppn').find('label').text( numeral.formatDec(ppn_new) );
                    $('.service_charge').find('label').text( numeral.formatDec(service_charge_new) );
                    if ( data.content.jenis_harga_exclude == 1 ) {
                        $('.include').addClass('hide');
                        $('.ppn').find('label').text( numeral.formatDec(ppn_new) );
                        $('.service_charge').find('label').text( numeral.formatDec(service_charge_new) );

                    } else if ( data.content.jenis_harga_include == 1 ) {
                        $('.include').removeClass('hide');
                        $('.ppn_include').find('label').text( numeral.formatDec(ppn_new) );
                        $('.service_charge_include').find('label').text( numeral.formatDec(service_charge_new) );
                    }

                    dataDiskonSave = data.content.data_diskon;

                    bayar.hitungTotalBayar();
                } else {
                    bootbox.alert(data.message);
                }
            }
        });
    }, // end - getDataDiskon

    pembayaranFormHutang: function (elm) {
        var kodeMember = $(elm).data('kode');

        var baseurl = $('head base').attr('href');
        var pagePembayaran = baseurl + 'transaksi/Pembayaran/pembayaranFormHutang/'+kodeMember;

        window.location.href = pagePembayaran;
    }, // end - pembayaranFormByMember

    pembayaranFormHutangEdit: function (elm) {
        var id = $(elm).data('id');

        var baseurl = $('head base').attr('href');
        var pagePembayaran = baseurl + 'transaksi/Pembayaran/pembayaranFormHutangEdit/'+id;

        window.location.href = pagePembayaran;
    }, // end - pembayaranFormByMember
};

bayar.startUp();