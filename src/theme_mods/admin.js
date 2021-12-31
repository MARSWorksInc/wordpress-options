jQuery(document).ready(function ($) {

    let checkboxWrappers = $('.marspress-multi-checkbox-wrapper');

    if( checkboxWrappers.length > 0 ){

        $(checkboxWrappers).each(function (_index,_wrapper){

            let inputs = $(_wrapper).find('input[type="checkbox"]');

            if( inputs.length > 0 ){

                $(inputs).each(function (_i,_input){

                   $(_input).off('change.updateHiddenField');
                   $(_input).on('change.updateHiddenField',function (_event){

                       let values = '';

                       let checkedBoxes = $(_wrapper).find('input[type="checkbox"]:checked');

                       if( checkedBoxes.length > 0 ){

                           let counter = 1;

                           $(checkedBoxes).each(function (_c,_checkedBox){

                               values += $(_checkedBox).val();

                               if( counter < checkedBoxes.length ){

                                   values += ',';

                               }

                               counter++;

                           });

                       }

                       $(_wrapper).find('input[type="hidden"]').val(values).change().trigger('change');

                       //$('.customize-save-button-wrapper').find('input[type="submit"]').removeAttr('disabled');

                   });

                });

            }

        });

    }

});