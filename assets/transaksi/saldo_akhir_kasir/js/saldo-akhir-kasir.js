var co = {
	modalSaldoAkhirKasir: function () {
        $('.modal').modal('hide');

        $.get('transaksi/SaldoAkhirKasir/modalSaldoAkhirKasir',{
        },function(data){
            var _options = {
                className : 'large',
                message : data,
                addClass : 'form',
                onEscape: true,
            };
            bootbox.dialog(_options).bind('shown.bs.modal', function(){
                $(this).find('.modal-header').css({'padding-top': '0px'});
                $(this).find('.modal-dialog').css({'width': '30%', 'max-width': '100%'});

                var modal_body = $(this).find('.modal-body');

                $('input').keyup(function(){
                    $(this).val($(this).val().toUpperCase());
                });

                $('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal]').each(function(){
                    $(this).priceFormat(Config[$(this).data('tipe')]);
                });

                $('.modal-backdrop').css({
                    'display': 'inline-block',
                    'height': '100%',
                    'vertical-align': 'middle',
                });

                $(this).css({
                    'justify-content': 'center',
                    'align-items': 'center'
                });

                $(this).find('.modal-dialog').css({
                    'justify-content': 'center',
                    'align-items': 'center'
                });

                $(modal_body).find('input').selectionStart = $(modal_body).find('input').selectionEnd = $(modal_body).find('input').val().length;
                $(modal_body).find('input').focus();
            });
        },'html');
    }, // end - modalJenisPesanan

    saveSaldoAkhirKasir: function() {
        var modal = $('.modal');

        var jmlUang = numeral.unformat($(modal).find('#jumlah_uang').val());

        if ( jmlUang >= 0 ) {
            $.ajax({
                url: 'transaksi/SaldoAkhirKasir/saveSaldoAkhirKasir',
                data: {
                    'jmlUang': jmlUang
                },
                type: 'POST',
                dataType: 'json',
                beforeSend: function() {
                    showLoading();
                },
                success: function(data) {
                	hideLoading();
                    if ( data.status == 1 ) {
                    	$('modal').modal('hide');
                    	jual.start_up();
                    } else {
                        bootbox.alert( data.message );
                    }
                }
            });
        } else {
            bootbox.alert('Harap isi jumlah uang terlebih dahulu.');
        }
    }, // end - saveSaldoAkhirKasir
};