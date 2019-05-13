
$(function () {

	$('#gen-pdf').click(function (event) {
		event.preventDefault();
		$('#generating').fadeIn(400);

		if ($(this).hasClass('confirm')) {
			var message = $(this).attr('rel');
			if (!confirm(message)) return;
		}

		var periodId = $('#pdf-period').val();
		// console.log(periodId);

		$.ajax({
			url: "generate-pdf.php",
			dataType: "html",
			data: { pid: periodId },
			success: function (msg) {
				$('#generating').fadeOut(200);
				$('#pdf-complete').fadeIn(400);
				if (msg != '') alert(msg);
				// location.reload();
			}
		});

	});

	$('a.scrollLink').slideto({
		speed: 'slow'
	});

	$('.confirm_cleanup').click(function (event) {
		var message = $(this).attr('rel');
		if (!confirm(message)) {
			event.preventDefault();
			return;
		}
	});

	// Let's handle some AJAX requests, shall we?	
	$('.delete').click(function (event) {
		event.preventDefault();
		var params = $(this).attr('href');
		//alert(params);
		if ($(this).hasClass('confirm')) {
			var message = $(this).attr('rel');
			if (!confirm(message)) return;
		}

		$.ajax({
			url: "ajax-delete-file.php",
			data: params,
			dataType: "html",
			success: function (msg) {
				if (msg != '') alert(msg);
				location.reload();
			}
		});

	});

	$('#search_filter').focus();

	$('#search_filter').fastLiveFilter('#user-list');

	$('.toggle_xml_errors').click(function (event) {
		event.preventDefault();
		$('.xml-errors').toggle(300);
	});
});

