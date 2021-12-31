<?php
/*
 * @package marspress/wp-options
 */

namespace MarsPress\Options\ThemeMods;

if( ! class_exists( 'Customization_Control_Multi_Checkbox' ) )
{

    class Customization_Control_Multi_Checkbox extends \WP_Customize_Control
    {

        public $type = 'multi-checkbox';

        public function enqueue() {

            $file = __DIR__ . '/admin.js';
            $file = str_replace( $_SERVER['DOCUMENT_ROOT'], get_site_url() . '/', $file );
            wp_register_script( 'mp-theme-mods-admin-script', $file, ['jquery'], '1.0', true );
            wp_enqueue_script( 'mp-theme-mods-admin-script' );

        }

        public function render_content() {

            if ( empty( $this->choices ) ){ return; }

            ?>
            <label class="marspress-multi-checkbox-wrapper">
                <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
                    <?php

                    $i = 0;

                    $values = $this->value();

                    if( ! is_array( $values ) ){

                        $values = explode( ',', $values );

                    }

                    foreach ( $this->choices as $value => $label ) {

                        $subKey = '_marspress-input-' . $this->settings['default']->id . '[' . $i . ']';
                        $fieldName = $subKey;
                        $id = $subKey;

                        $checked = ( is_array( $values ) && in_array( $value, $values ) ) ? 'checked' : '';
                        echo '<label for="' . $id . '"><input type="checkbox" name="' . $fieldName . '" id="' . $id . '" value="' . $value . '" ' . $checked . ' /><span>' . $label . '</span></label>';

                        if( $i < count($this->choices) ){

                            echo '<br/>';

                        }

                        $i++;

                    }

                    $fieldName = '_customize-input-' . $this->settings['default']->id;
                    $id = $fieldName;

                    ?>

                    <input <?php $this->input_attrs(); ?> type="hidden" name="<?php echo $fieldName; ?>" id="<?php echo $id; ?>" <?php $this->link(); ?> value="<?php echo esc_attr( implode( ',', $values ) ); ?>" />

            </label>
            <?php
        }

    }

}