(function($) {
    'use strict';
    console.log('init');

    function MS_WA_Request(url, type, formdata, cbsuccess, cbfail) {
    	var action = [{ name: "action", value: "ms_wa_ajax" }];
    	var data = $.merge(action,formdata);
        $.ajax({
            url: url,
            type: type,
            data: data,
        }).done(function(response) {
            if (response.code === 200) {
                cbsuccess(response);
            } else {
                cbfail(response);
            }
        });
    }

    function mswa_loading(el,a,msg,fail) {
    	if (a!==false) {
    		el.find('input#submit').attr('disabled', 'true').after('<img class="loading" src="'+mswa.spinner_url+'"/>');
    	} else {
    		console.log('ini');
    		el.find('input#submit').removeAttr('disabled').next('.loading').remove();
    		if ( msg != '' ) {
    			var cls = 'successtext';
    			if ( fail ) {
    				cls = 'failtext';
    			}
    			el.find('input#submit').after('<span class="'+cls+'">'+msg+'</span>');
    			setTimeout(function(){
    				$('.ms-wa .successtext,.ms-wa .failtext').fadeOut(function(){
    					$(this).remove();
    				});
    			},3000);
    		}
    	}
    }

    $(document).ready(function() {
        $("#message,#message2,#message3").emojioneArea({
            filtersPosition: 'bottom',
            pickerPosition: 'bottom'
        });
    });

    $('#broadcast-form').on('submit', function(e) {
        e.preventDefault();

        var $form = $(this);
        var data = $form.serializeArray();
        data.push({ name: "request", value: "send_message" });

        mswa_loading($form);

        MS_WA_Request(
            mswa.ajax_url,
            'post',
            data,
            function(response) {
                console.log(response);
                mswa_loading($form,false,response.message);
            },
            function(fail) {
                console.log(fail.message);
                mswa_loading($form,false,fail.message,true);
            }
        );
    });

    let reset = false;
    $('input[name="reset"]').on('click',function(){
		reset = true;
    });

    $('#settings-form').on('submit', function(e) {
        e.preventDefault();

        var $form = $(this);
        var data = $form.serializeArray();

        if ( reset ) {
        	data.push({ name: "request", value: "reset_settings" });
        } else {
        	data.push({ name: "request", value: "save_settings" });
        }

        mswa_loading($form);

        MS_WA_Request(
            mswa.ajax_url,
            'post',
            data,
            function(response) {
                console.log(response);
                mswa_loading($form,false,response.message);
                if ( reset ) {
                	location.reload();
                }
            },
            function(fail) {
                console.log(fail.message);
                mswa_loading($form,false,fail.message,true);
            }
        );
    });

})(jQuery);