var clo = {
	startUp: function () {
	}, // end - startUp

	saveEndShift: function () {
		bootbox.confirm('Apakah anda yakin ingin mengakhiri shift hari ini ?', function (result) {
			if ( result ) {
				$.ajax({
		            url: 'transaksi/ClosingOrder/saveEndShift',
		            data: {},
		            type: 'POST',
		            dataType: 'json',
		            beforeSend: function() {
		                showLoading();
		            },
		            success: function(data) {
		            	// hideLoading();
		                if ( data.status == 1 ) {
		                	clo.printEndShift();
		                } else {
		                    bootbox.alert( data.message );
		                }
		            }
		        });
			}
		});
	}, // end - saveEndShift

	printEndShift: function () {
        $.ajax({
            url: 'transaksi/ClosingOrder/printEndShift',
            data: {},
            type: 'POST',
            dataType: 'JSON',
            beforeSend: function() { 
                // showLoading(); 
            },
            success: function(data) {
                hideLoading();
                if ( data.status == 1 ) {
                    bootbox.alert( data.message );
                } else {
                    bootbox.alert(data.message);
                }
            }
        });
    }, // end  - printEndShift

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

    printClosingOrder: function () {
        $.ajax({
            url: 'transaksi/ClosingOrder/printClosingOrder',
            data: {},
            type: 'POST',
            dataType: 'JSON',
            beforeSend: function() { 
                showLoading('Print Closing Order ...'); 
            },
            success: function(data) {
                hideLoading();
                if ( data.status == 1 ) {
                    bootbox.alert(data.message);
                } else {
                    bootbox.alert(data.message);
                }
            }
        });
    }, // end  - printClosingOrder
};

clo.startUp();