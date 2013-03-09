var My = {}     
My.List = {
        Filter : function (inputSelector, listSelector) {

                // Sanity check  
                var inp, rgx = new RegExp(), titles = $(listSelector), keys;
                if (titles.length === 0) {
                        return false;
                }

                // The list with keys to skip (esc, arrows, return, etc)
                // 8 is backspace, you might want to remove that for better usability
                keys = [ 13, 27, 32, 37, 38, 39, 40];

                // binding keyup to the unordered list
                $(inputSelector).bind('keyup', function (e) {
                        if (jQuery.inArray(e.keyCode, keys) >= 0) {
                                return false;
                        }

                        // Building the regex from our user input, 'inp' should be escaped
                        inp = $(this).attr('value');
                        rgx.compile(inp, 'im');
                        titles.each(function () {
                                if (rgx.source !== '' && !rgx.test($(this).html())) {
                                        $(this).parent('li').hide();
                                } else {
                                        $(this).parent('li').show();    
                                }
                        });
                });
        }
};

$(function() {

	$('#gen-pdf').click(function(event){
		event.preventDefault();
		$('#generating').fadeIn(400);
		
		if($(this).hasClass('confirm')){
			var message = $(this).attr('rel');
			if(!confirm(message)) return;
		}
		
		$.ajax({
			url: "generate-pdf.php",
			dataType: "html",
			success: function(msg){
				$('#generating').fadeOut(200);
				$('#pdf-complete').fadeIn(400);
				// if(msg != '') alert(msg);
				// location.reload();
			}
		});
		
	});

/*	$('.scroll-link').click(function(event){
		event.preventDefault();

		var target = '#' + $(this).attr('href');
		console.log(target);
		$(target).slideto({
		  highlight: false,
		  slide_duration: 600
		});
	});*/

	$('.scroll-link').slideto({
		speed  : 'slow'
	});

	$('.confirm_cleanup').click(function(event){
		var message = $(this).attr('rel');
		if(!confirm(message)){
			event.preventDefault();
			return;	
		} 
	});

		// Let's handle some AJAX requests, shall we?	
	$('.delete').click(function(event){
		event.preventDefault();
		var params = $(this).attr('href');
		//alert(params);
		if($(this).hasClass('confirm')){
			var message = $(this).attr('rel');
			if(!confirm(message)) return;
		}
		
		$.ajax({
			url: "ajax-delete-file.php",
			data: params,
			dataType: "html",
			success: function(msg){
				if(msg != '') alert(msg);
				location.reload();
			}
		});
		
	});

	$('#search_filter').focus();

	My.List.Filter('input#search_filter', 'ul#user-list>li>a');

});

