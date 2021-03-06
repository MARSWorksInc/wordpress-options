<?php
/*
 * @package marspress/wp-options
 */

namespace MarsPress\Options\ThemeMods;

if( ! class_exists( 'Customization_Section' ) )
{

    final class Customization_Section
    {

        private string $key;

        private string $label;

        private ?string $description;

        private int $position;

        private bool $displayError = false;

        /**
         * @var Customization[] $customizations
         */
        private array $customizations;

        private $value;

        public function __construct(
            string $_key,
            string $_label,
            ?string $_description = null,
            int $_position = 160
        )
        {

            $this->key = $_key;
            $this->label = $_label;
            $this->description = $_description;
            $this->position = $_position;
            $this->value = \get_theme_mod( $this->key );

            add_action( 'customize_register', [ $this, 'register_customization_group' ], 10 );
            add_action( 'customize_controls_print_scripts', [ $this, 'load_editor_script' ], 10 );

        }

        /**
         * @action customize_controls_print_scripts
         * @class \MarsPress\Options\ThemeMods\Option_Group
         * @function load_editor_script
         * @priority 10
         * @return void
         */
        public function load_editor_script()
        {

            if( $this->displayError ){

                $message = "The customization section with the key <strong><em>{$this->key}</em></strong> already exists in the customization manager. Please update your customization section key to a unique value.";
                echo <<<JS
                <script>
                    ( function( $ ) {
                        'use strict';
                        wp.customize.bind( 'ready', function (){
                            wp.customize.notifications.add(
                                'marspress-theme-mods-section-exists-{$this->key}',
                                new wp.customize.Notification(
                                    'marspress-theme-mods-section-exists-{$this->key}', {
                                        dismissible: true,
                                        message: "{$message}",
                                        type: 'error'
                                    }
                                )
                            );
                        });
                    } )( jQuery );
                </script>
                JS;

            }

        }

        /**
         * @action customize_register
         * @class \MarsPress\Options\ThemeMods\Option_Group
         * @function register_customization_group
         * @param \WP_Customize_Manager|false $_customizeManager
         * @priority 10
         * @return void
         */
        public function register_customization_group( $_customizeManager ){

            if( ! is_object( $_customizeManager ) ){ return; }

            if( ! isset( $this->customizations ) ){ return; }

            if( array_key_exists( $this->key, $_customizeManager->sections() ) ){

                $this->displayError = true;

                return;

            }

            $_customizeManager->add_section(
                $this->key,
                [
                    'title'             => $this->label,
                    'description'       => $this->description,
                    'panel'             => '',
                    'priority'          => $this->position,
                    'capability'        => 'edit_theme_options',
                    'theme_supports'    => '',
                ]
            );

            foreach ( $this->customizations as $_customizationName => $_customization ){

                $key = "$this->key[$_customizationName]";

                $sanitizationCallback = $_customization->get_sanitization_callback();

                if( $_customization->get_type() === 'checkbox-multiple' ){

                    $sanitizationCallback = function( $_value ) use ($_customization){

                        $values = $_value;

                        if( ! is_array( $values ) ){

                            $values = explode( ',', $values );

                        }

                        if( ! is_null( $callback = $_customization->get_sanitization_callback() ) ){

                            $values = call_user_func_array( $callback, [$values] );

                        }

                        return $values;

                    };

                }

                $_customizeManager->add_setting(
                    "$key",
                    [
                        'type'                      => 'theme_mod',
                        'capability'                => 'edit_theme_options',
                        'theme_supports'            => '',
                        'default'                   => $_customization->get_default_value(),
                        'transport'                 => 'refresh',
                        'sanitize_callback'         => $sanitizationCallback,
                        'sanitize_js_callback'      => '',
                    ]
                );

                if( $_customization->get_type() === 'select-multiple' ){

                    $_customizeManager->add_control(
                        new \MarsPress\Options\ThemeMods\Customization_Control_Multi_Select(
                            $_customizeManager,
                            "$key",
                            [
                                'type'                      => $_customization->get_type(),
                                'priority'                  => $_customization->get_position(),
                                'section'                   => $this->key,
                                'label'                     => $_customization->get_label(),
                                'description'               => $_customization->get_description(),
                                'input_attrs'               => [
                                    'class'             => "$key marspress-field marspress-multi-select",
                                    'style'             => 'height: 100%;',
                                    'placeholder'       => $_customization->get_placeholder(),
                                ],
                                'active_callback'           => 'is_front_page',
                                'choices'                   => $_customization->get_options(),
                            ]
                        )
                    );

                }elseif( $_customization->get_type() === 'checkbox-multiple' ) {

                    $_customizeManager->add_control(
                        new \MarsPress\Options\ThemeMods\Customization_Control_Multi_Checkbox(
                            $_customizeManager,
                            "$key",
                            [
                                'type'                      => $_customization->get_type(),
                                'priority'                  => $_customization->get_position(),
                                'section'                   => $this->key,
                                'label'                     => $_customization->get_label(),
                                'description'               => $_customization->get_description(),
                                'input_attrs'               => [
                                    'class'             => "$key marspress-field marspress-multi-checkbox",
                                    'style'             => '',
                                    'placeholder'       => $_customization->get_placeholder(),
                                ],
                                'active_callback'           => 'is_front_page',
                                'choices'                   => $_customization->get_options(),
                            ]
                        )
                    );

                }else if( $_customization->get_type() === 'media' ) {

                    $_customizeManager->add_control(
                        new \WP_Customize_Media_Control(
                            $_customizeManager,
                            "$key",
                            [
                                'type'                      => $_customization->get_type(),
                                'priority'                  => $_customization->get_position(),
                                'section'                   => $this->key,
                                'label'                     => $_customization->get_label(),
                                'description'               => $_customization->get_description(),
                                'input_attrs'               => [
                                    'class'             => "$key marspress-field marspress-media",
                                    'style'             => '',
                                    'placeholder'       => $_customization->get_placeholder(),
                                ],
                                'active_callback'           => 'is_front_page',
                                'choices'                   => $_customization->get_options(),
                            ]
                        )
                    );

                }else{

                    $_customizeManager->add_control(
                        "$key",
                        [
                            'type'                      => $_customization->get_type(),
                            'priority'                  => $_customization->get_position(),
                            'section'                   => $this->key,
                            'label'                     => $_customization->get_label(),
                            'description'               => $_customization->get_description(),
                            'input_attrs'               => [
                                'class'             => "$key marspress-field",
                                'style'             => '',
                                'placeholder'       => $_customization->get_placeholder(),
                            ],
                            'active_callback'           => 'is_front_page',
                            'choices'                   => $_customization->get_options(),
                        ]
                    );

                }

            }

        }

        private function get_customization_object( $_customizationName ): ?Customization
        {

            if( ! isset( $this->customizations ) ){ return null; }

            if( array_key_exists( $_customizationName, $this->customizations ) ){

                return $this->customizations[$_customizationName];

            }

            return null;

        }

        public function get_customization_value( $_customizationName, $_returnRawValue = false )
        {

            if( ! isset( $this->customizations ) ){ return null; }

            if(
                array_key_exists( $_customizationName, $this->customizations ) &&
                array_key_exists( $_customizationName, $this->value )
            ){

                if(
                    ! $_returnRawValue &&
                    ! is_null( $returnCallback = $this->customizations[$_customizationName]->get_return_callback() ) &&
                    is_callable( $returnCallback )
                ){

                    return call_user_func_array( $returnCallback, [$this->value[$_customizationName]] );

                }

                return $this->value[$_customizationName];

            }

            return null;

        }

        /**
         * @param Customization[] $_customizations
         * @return Customization_Section
         */
        public function add_customizations( \MarsPress\Options\ThemeMods\Customization ...$_customizations ): Customization_Section
        {

            if( ! isset( $this->customizations ) ){

                $this->customizations = [];

            }

            if( count( $_customizations ) > 0 ){

                foreach ( $_customizations as $_customization ){

                    if( ! array_key_exists( $_customization->get_name(), $this->customizations ) ){

                        $this->customizations[$_customization->get_name()] = $_customization;

                    }else{

                        $message = "The customization <strong><em>{$_customization->get_name()}</em></strong> in the customization section <strong><em>{$this->key}</em></strong> already exists. Please update your customization name to a unique value for the customization section.";
                        add_action( 'admin_notices', function () use ($message){
                            $output = $this->output_admin_notice($message);
                            echo $output;
                        }, 10, 0 );

                    }


                }

            }

            return $this;

        }

        private function output_admin_notice( string $_message ): string
        {

            if( strlen( $_message ) > 0 && \current_user_can( 'administrator' ) ){

                return "<div style='background: white; padding: 12px 20px; border-radius: 3px; border-left: 5px solid #dc3545;' class='notice notice-error is-dismissible'><p style='font-size: 16px;'>$_message</p><small><em>This message is only visible to site admins</em></small></div>";

            }

            return '';

        }

    }

}