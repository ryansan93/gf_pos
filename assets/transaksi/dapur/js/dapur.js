const ws = new WebSocket("ws://localhost:8033");

var dapur = {
	startUp: function () {
        ws.addEventListener("open", () => {
            console.log("We are connected!");

            dapur.listPesanan();
        });

        ws.addEventListener("message", (msg) => {
            if ( msg.data == 'pesan' ) {
            	dapur.listPesanan();
            }

            // dapur.listPesanan();
        });
	}, // end - startUp

	listPesanan: function() {
		$.ajax({
            url: 'transaksi/Dapur/listPesanan',
            data: {},
            type: 'GET',
            dataType: 'HTML',
            beforeSend: function() {},
            success: function(html) {
                $('.listPesanan').html( html );
            }
        });
	}, // end - listPesanan

    ubahStatusPesanan: function(elm) {  
        var kode_pesanan_item = $(elm).attr('data-kode');
        var status_tujuan = $(elm).attr('data-statustujuan');

        var params = {
            'kode_pesanan_item': kode_pesanan_item,
            'status_tujuan': status_tujuan
        };

        $.ajax({
            url: 'transaksi/Dapur/ubahStatusPesanan',
            data: {
                'params': params
            },
            type: 'POST',
            dataType: 'JSON',
            beforeSend: function() {},
            success: function(data) {
                if ( data.status == 1 ) {
                    var div = $(elm).closest('div');

                    $(div).find('button, label').addClass('hide');
                    $(div).find('[data-status='+data.content.status+']').removeClass('hide');

                    dapur.cekPesananStatus( $(div) );
                } else {
                    bootbox.alert( data.message );
                }
            }
        });
    }, // end - siapkanPesanan

    cekPesananStatus: function(elm) {
        var div_detail_contain = $(elm).closest('div.detail_contain');

        var jml_menu_not_done = $(div_detail_contain).find('label[data-status=2].hide').length;

        if ( jml_menu_not_done == 0 ) {
            dapur.listPesanan();
        }
    }, // end - cekPesananStatus

    ubahContain: function(elm) {
        var tujuan = $(elm).attr('data-tujuan');

        $('div.contain').addClass('hide');

        $('div.'+tujuan).removeClass('hide');
    }, // end - ubahContain
};

dapur.startUp();