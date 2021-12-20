<?php
/*
 * @package marspress/wp-options
 */

namespace MarsPress\Options\ThemeMods;

if( ! class_exists( 'Customization_Control_Multi_Select' ) )
{

    class Customization_Control_Multi_Select extends \WP_Customize_Control
    {

        public $type = 'multi-select';

        public function render_content() {

            if ( empty( $this->choices ) ){ return; }

            ?>
            <label>
                <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
                <select <?php $this->link(); ?> multiple="multiple" style="height: 100%;">
                    <?php
                    foreach ( $this->choices as $value => $label ) {

                        $selected = ( in_array( $value, $this->value() ) ) ? selected( 1, 1, false ) : '';
                        echo '<option value="' . esc_attr( $value ) . '" ' . $selected . '>' . $label . '</option>';

                    }
                    ?>
                </select>
            </label>
            <?php
        }

    }

}