/**
 * Add actions delete to the actions.
 */
jQuery(function ($) {
    jQuery(document).ready(function ($) {

        var subsubsub = $('.subsubsub').find('li');
        
        // Add Delete Icon to the actions.
        subsubsub.each(function(){
            if ( 'pending' !== $(this).attr('class') || 'pending' !== $(this).attr('class') ) {
                var list_class = $(this).attr('class');
                $(this).append('<button type="submit" class="cas-delete" name="' + list_class + '"><span class="dashicons dashicons-trash"></span></div>');
            }
        });

        // Delete the actions passed.
        $('.cas-delete').on('click', function(e){
            e.preventDefault();
            let confirm = window.confirm( "Are you sure you want to delete all " + $(this).attr('name') + " actions" );
            if ( confirm === true ) {
                $('#wpcontent').addClass('cas-delete-loader');
                var data = {
                    action: 'cas_delete_all',
                    action_status: $(this).attr('name'),
                    cas_delete_nonce: cas_params.cas_delete_nonce
                };
                $.ajax({
                    url: cas_params.ajax_url,
                    type: "POST",
                    data: data,
                    dataType: "json",
                    success: function (response) {
                        location.reload();
                        $('#wpcontent').removeClass('cas-delete-loader');
                    }
                });
            }
        })

    });
});
