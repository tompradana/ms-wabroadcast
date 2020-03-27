(function($) {
    // var fbTemplate = document.getElementById('build-wrap');
    // var options = {
    // 	dataType: 'json',
    // 	formData: JSON.parse(fonnletter.formdata),
    //     onSave: function(evt, formData) {
    //         toggleEdit(false);
    //         $('.render-wrap').formRender({ formData });
    //         $('input[name="fonnletter-formdata"]').val(JSON.stringify(formData));
    //         console.log($('input[name="fonnletter-formdata"]').val());
    //     }
    // };
    // var fb = $(fbTemplate).formBuilder(options);

    $(document).ready(function() {
        $('select[name="_fonnletter_template"]').on('change', function() {
            if ( $(this).val() == 'custom' ) {
            	$('.fonnletter #custom-template').show();
            } else {
            	$('.fonnletter #custom-template').hide();
            }
        }).trigger('change');
    });
})(jQuery)

// function toggleEdit(editing) {
//     document.body.classList.toggle('form-rendered', !editing);
// }

// document.getElementById('edit-form').onclick = function() {
//     toggleEdit(true);
// };