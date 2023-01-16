var clo = {
	startUp: function () {
	}, // end - startUp

	saveClosingOrder: function () {
		bootbox.confirm('Apakah anda yakin ingin mengakhiri transaksi hari ini ?', function (result) {
			if ( result ) {
				$.ajax({
		            url: 'transaksi/ClosingOrder/saveClosingOrder',
		            data: {},
		            type: 'POST',
		            dataType: 'json',
		            beforeSend: function() {
		                showLoading();
		            },
		            success: function(data) {
		            	hideLoading();
		                if ( data.status == 1 ) {
		                	clo.hitungStok( data.content.kode );
		                } else {
		                    bootbox.alert( data.message );
		                }
		            }
		        });
			}
		});
	}, // end - saveClosingOrder

	hitungStok: function (kode) {
        var params = {'kode': kode};

        $.ajax({
            url: 'transaksi/ClosingOrder/hitungStok',
            data: {
                'params': params
            },
            type: 'POST',
            dataType: 'JSON',
            beforeSend: function() { showLoading(); },
            success: function(data) {
                hideLoading();
                if ( data.status == 1 ) {
                    bootbox.alert( data.message, function () {
                        location.reload();
                    });
                } else {
                    bootbox.alert( data.message );
                }
            }
        });
    }, // end - hitungStok
};

clo.startUp();