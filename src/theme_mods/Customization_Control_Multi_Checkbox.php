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

        public function render_content() {

            if ( empty( $this->choices ) ){ return; }

            $key = '_customize-input-' . $this->settings['default']->id;

            $link = $this->settings['default']->id;

            ?>
            <label>
                <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
                    <?php
                    $i = 0;

                    foreach ( $this->choices as $value => $label ) {

                        $subKey = '_customize-input-' . $this->settings['default']->id . '[' . $i . ']';

                        $subLink = $this->settings['default']->id . '[' . $i . ']';

                        $fieldName = '_customize-input-' . $this->settings['default']->id;
                        $id = $subKey;

                        $checked = ( is_array( $this->value() ) && in_array( $value, $this->value() ) ) ? 'checked' : '';
                        echo '<label for="' . $id . '"><input type="checkbox" name="' . $fieldName . '" id="' . $id . '" data-customize-setting-link="' . $link . '" value="' . $value . '" ' . $checked . ' /><span>' . $label . '</span></label>';

                        if( $i < count($this->choices) ){

                            echo '<br/>';

                        }

                        $i++;

                    }
                    ?>
            </label>
            <?php
        }

    }

}