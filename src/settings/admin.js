jQuery(document).ready(function ($) {
    //init WP Media Library
    let gk_media_init = function(_selector, _buttonSelector){
        let clicked_button = false;
        $(_selector).each(function (_i, _input) {
            let controls = $(_input).attr('data-control-media');
            let button = $(_buttonSelector + '[data-control-media="'+controls+'"]');
            $(button).off('click.selectMedia');
            $(button).on('click.selectMedia',function (_event) {
                _event.preventDefault();
                let selectedImg;
                clicked_button = $(this);
                if(wp.media.frames.gk_frame) {
                    wp.media.frames.gk_frame.open();
                    return;
                }
                wp.media.frames.gk_frame = wp.media({
                    title: 'Select a Media Attachment',
                    multiple: false,
                    library: {
                        type: 'image'
                    },
                    button: {
                        text: 'Use selected media'
                    }
                });
                let gk_media_set_image = function() {
                    let selection = wp.media.frames.gk_frame.state().get('selection');
                    if (!selection) {
                        return;
                    }
                    selection.each(function(_attachment) {
                        let url = _attachment.attributes.url;
                        url = url.replace( MP_SETTINGS.siteURL, '' );
                        $(_selector + '[data-control-media="'+controls+'"]').val(url);
                        let previewElement = $('.mp-settings-media-preview[data-control-media="'+controls+'"]');
                        $(previewElement).attr('src',url);
                        if( url.length > 0 ){
                            $(previewElement).css({
                                'margin-bottom': '10px',
                            })
                        }else{
                            $(previewElement).css({
                                'margin-bottom': '0',
                            })
                        }
                    });
                };
                wp.media.frames.gk_frame.on('close', gk_media_set_image);
                wp.media.frames.gk_frame.on('select', gk_media_set_image);
                wp.media.frames.gk_frame.open();
            });
            $(_input).off('keyup.updateImagePreview');
            $(_input).on('keyup.updateImagePreview',function (_event){
                let controls = $(_input).attr('data-control-media');
                let previewElement = $('.mp-settings-media-preview[data-control-media="'+controls+'"]');
                let url = $(_input).val()
                $(previewElement).attr('src',url);
                if( url.length > 0 ){
                    $(previewElement).css({
                        'margin-bottom': '10px',
                    })
                }else{
                    $(previewElement).css({
                        'margin-bottom': '0',
                    })
                }
            });
        });
    };
    gk_media_init('.mp-settings-media-input', '.mp-settings-media-button');
//end
});