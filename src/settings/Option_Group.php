<?php
/*
 * @package marspress/wp-options
 */

namespace MarsPress\Options\Settings;

if( ! class_exists( 'Option_Group' ) )
{

    final class Option_Group
    {

        private string $key;

        /**
         * @var Option[] $options
         */
        private array $options;

        public function __construct(
            string $_key
        )
        {

            $this->key = $_key;

            add_action( 'admin_init', [ $this, 'register_option_group' ], 10, 0 );

        }

        public function get_key(): string
        {

            return $this->key;

        }

        /**
         * @action admin_init
         * @class \MarsPress\Options\Settings\Option_Group
         * @function register_option_group
         * @priority 10
         * @return void
         */
        public function register_option_group()
        {

            \register_setting(
                $this->key,
                $this->key,
                [
                    'type'                  => 'string',
                    'description'           => '',
                    'sanitize_callback'     => [ $this, 'sanitize_options' ],
                    'show_in_rest'          => true,
                    'default'               => [],
                ]
            );

        }

        public function sanitize_options( $_input )
        {

            if(
                isset( $this->options ) &&
                is_array( $_input )
            ){

                foreach ( $this->options as $_optionName => $_optionObject ){

                    if(
                        ! is_null( $sanitizationCallback = $_optionObject->get_sanitization_callback() ) &&
                        array_key_exists( $_optionName, $_input )
                    ){

                        $_input[$_optionName] = call_user_func_array( $sanitizationCallback, $_input );

                    }

                }

            }

            return $_input;

        }

        private function get_option_object( $_optionName ): ?Option
        {

            if( ! isset( $this->options ) ){ return null; }

            if( array_key_exists( $_optionName, $this->options ) ){

                return $this->options[$_optionName];

            }

            return null;

        }

        public function get_options(): ?array
        {

            if( ! isset( $this->options ) ){ return null; }

            if( count( $this->options ) > 0 ){

                return $this->options;

            }

            return null;

        }

        /**
         * @param Option[] $_options
         * @return Option_Group
         */
        public function add_options( \MarsPress\Options\Settings\Option ...$_options ): Option_Group
        {

            if( ! isset( $this->options ) ){

                $this->options = [];

            }

            if( count( $_options ) > 0 ){

                foreach ( $_options as $_option ){

                    if( ! array_key_exists( $_option->get_name(), $this->options ) ){

                        $this->options[$_option->get_name()] = $_option;

                    }else{

                        $message = "The option <strong><em>{$_option->get_name()}</em></strong> in the option group <strong><em>{$this->key}</em></strong> already exists. Please update your option name to a unique value for the option group.";
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