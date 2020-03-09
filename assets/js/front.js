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
    		el.find('[type="submit"]').attr('disabled', 'true').after('<img class="loading" src="'+mswa.spinner_url+'"/>');
    	} else {
    		el.find('[type="submit"]').removeAttr('disabled').next('.loading').remove();
    		if ( msg != '' ) {
    			var cls = 'successtext';
    			if ( fail ) {
    				cls = 'failtext';
    			}
    			el.find('[type="submit"]').after('<div class="mswa-msg '+cls+'">'+msg+'</span>');
    			setTimeout(function(){
    				el.find('.mswa-msg.' + cls).fadeOut().remove();
    			},2000);
    		}
    	}
    }

    $('form[name="ms-wa-campaign-form"]').on('submit', function(e) {
        e.preventDefault();

        var $form = $(this);
        var data = $form.serializeArray();
        data.push({ name: "request", value: "submit_campaign" });
        data.push({ name: "campaign_id", value: $form.attr('data-campaign') });

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

})(jQuery);