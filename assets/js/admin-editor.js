(function($) {
    // var fbTemplate = document.getElementById('build-wrap');
    // var options = {
    // 	dataType: 'json',
    // 	formData: JSON.parse(mswa.formdata),
    //     onSave: function(evt, formData) {
    //         toggleEdit(false);
    //         $('.render-wrap').formRender({ formData });
    //         $('input[name="mswa-formdata"]').val(JSON.stringify(formData));
    //         console.log($('input[name="mswa-formdata"]').val());
    //     }
    // };
    // var fb = $(fbTemplate).formBuilder(options);

    $(document).ready(function() {
        $('select[name="_mswa_template"]').on('change', function() {
            if ( $(this).val() == 'custom' ) {
            	$('.ms-wa #custom-template').show();
            } else {
            	$('.ms-wa #custom-template').hide();
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