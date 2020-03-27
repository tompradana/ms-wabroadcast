(function($) {
    'use strict';
    console.log('init');

    function MS_WA_Request(url, type, formdata, cbsuccess, cbfail) {
    	var action = [{ name: "action", value: "fonnletter_ajax" }];
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

    function fonnletter_loading(el,a,msg,fail) {
    	if (a!==false) {
    		el.find('[type="submit"]').attr('disabled', 'true').after('<img class="loading" src="'+fonnletter.spinner_url+'"/>');
    	} else {
    		el.find('[type="submit"]').removeAttr('disabled').next('.loading').remove();
    		if ( msg != '' ) {
    			var cls = 'successtext';
    			if ( fail ) {
    				cls = 'failtext';
    			}
    			el.find('[type="submit"]').after('<div class="fonnletter-msg '+cls+'">'+msg+'</span>');
    			setTimeout(function(){
    				el.find('.fonnletter-msg.' + cls).fadeOut().remove();
    			},2000);
    		}
    	}
    }

    $('form[name="fonnletter-campaign-form"]').on('submit', function(e) {
        e.preventDefault();

        var $form = $(this);
        var data = $form.serializeArray();
        data.push({ name: "request", value: "submit_campaign" });
        data.push({ name: "campaign_id", value: $form.attr('data-campaign') });

        fonnletter_loading($form);

        MS_WA_Request(
            fonnletter.ajax_url,
            'post',
            data,
            function(response) {
                console.log(response);
                fonnletter_loading($form,false,response.message);
            },
            function(fail) {
                console.log(fail.message);
                fonnletter_loading($form,false,fail.message,true);
            }
        );
    });

})(jQuery);