var sr = {
	startUp: function() {
		sr.settingUp();
	}, // end - startUp

	settingUp: function() {
		$("#StartDate").datetimepicker({
            locale: 'id',
            format: 'DD MMM Y'
        });

        $("#EndDate").datetimepicker({
            locale: 'id',
            format: 'DD MMM Y'
        });

        $("#StartDate").on("dp.change", function (e) {
            var minDate = dateSQL($("#StartDate").data("DateTimePicker").date())+' 00:00:00';
            $("#EndDate").data("DateTimePicker").minDate(moment(new Date(minDate)));
        });
        var start_date = $("#StartDate").find('input').data('tgl');
        if ( !empty(start_date) && empty($("#StartDate").find('input').val()) ) {
        	$("#StartDate").data('DateTimePicker').date(moment(new Date(start_date)));
        }
	}, // end - settingUp

	getLists: function() {
		var err = 0;
		$.map( $('[data-required=1]'), function(ipt) {
			if ( empty( $(ipt).val() ) ) {
				$(ipt).parent().addClass('has-error');
				err++;
			} else {
				$(ipt).parent().removeClass('has-error');
			}
		});

		if ( err > 0 ) {
			bootbox.alert('Harap lengkapi data terlebih dahulu.');
		} else {
			var params = {
				'start_date': dateSQL($("#StartDate").data('DateTimePicker').date()),
				'end_date': dateSQL($("#EndDate").data('DateTimePicker').date())
			};

			$.ajax({
                url: 'transaksi/SalesRecapitulation/getLists',
                data: {
                    'params': params
                },
                type: 'GET',
                dataType: 'HTML',
                beforeSend: function() { showLoading(); },
                success: function(html) {
                    hideLoading();

                    $('table tbody').html( html );
                }
            });
		}
	}, // end - getLists
};

sr.startUp();