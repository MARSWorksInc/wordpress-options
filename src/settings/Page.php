<?php
/*
 * @package marspress/wp-options
 */

namespace MarsPress\Options\Settings;

if( ! class_exists( 'Page' ) )
{

    final class Page
    {

        private string $pageTitle;

        private string $pageSlug;

        private ?string $parentSlug;

        private string $menuTitle;

        private string $iconURL;

        private int $menuPosition;

        private Option_Group $optionGroup;

        public function __construct(
            Option_Group $_optionGroup,
            string $_pageTitle,
            string $_pageSlug,
            string $_parentSlug = null,
            string $_menuTitle = null,
            string $_iconURL = 'dashicons-admin-generic',
            int $_menuPosition = 80
        )
        {

            if( is_null( $_menuTitle ) ){

                $_menuTitle = $_pageTitle;

            }

            $this->optionGroup = $_optionGroup;
            $this->pageTitle = $_pageTitle;
            $this->pageSlug = $_pageSlug;
            $this->parentSlug = $_parentSlug;
            $this->menuTitle = $_menuTitle;
            $this->iconURL = $_iconURL;
            $this->menuPosition = $_menuPosition;

            add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ], 10, 0 );
            add_action( 'admin_menu', [ $this, 'register_page' ], 10, 1 );

        }

        /**
         * @action admin_enqueue_scripts
         * @class \MarsPress\Options\Settings\Page
         * @function enqueue_admin_scripts
         * @priority 10
         * @return void
         */
        public function enqueue_admin_scripts()
        {

            if( strpos( get_current_screen()->id, $this->pageSlug ) !== false ){

                wp_enqueue_style( 'wp-color-picker' );

                $file = __DIR__ . '/admin.js';
                $file = str_replace( $_SERVER['DOCUMENT_ROOT'], get_site_url() . '/', $file );
                wp_register_script( 'mp-settings-admin-script', $file, ['jquery','wp-color-picker'], '1.0', true );
                wp_localize_script( 'mp-settings-admin-script', 'MP_SETTINGS', [
                    'siteURL' => get_site_url(),
                ]);
                wp_enqueue_script( 'mp-settings-admin-script' );

            }

        }

        /**
         * @action admin_menu
         * @class \MarsPress\Options\Settings\Page
         * @function register_page
         * @priority 10
         * @param string $_context
         * @return void
         */
        public function register_page( string $_context )
        {

            if( is_null( $this->parentSlug ) ){

                \add_menu_page(
                  $this->pageTitle,
                  $this->menuTitle,
                  'manage_options',
                  $this->pageSlug,
                  [ $this, 'render_page' ],
                  $this->iconURL,
                  $this->menuPosition
                );

            }else{

                \add_submenu_page(
                  $this->parentSlug,
                  $this->pageTitle,
                  $this->menuTitle,
                  'manage_options',
                  $this->pageSlug,
                  [ $this, 'render_page' ],
                  $this->menuPosition
                );

            }

        }

        private function get_field_markup( $_fieldName, $_fieldState, $_fieldLabel, $_fieldDescription, $_type, $_options ): string
        {

            if(
                $_type === 'text' ||
                $_type === 'password' ||
                $_type === 'email'
            ){

                $input = "<input style='min-width: 350px; pointer-events: all;' type='{$_type}' name='{$_fieldName}' id='{$_fieldName}' value='$_fieldState'>";

            }else if(
                $_type === 'radio' ||
                $_type === 'checkbox'
            ){

                $input = '';
                $counter = 0;

                foreach ( $_options as $_value => $_label ){

                    $checked = '';

                    if(
                        $_type === 'radio' &&
                        $_value == $_fieldState
                    ){

                        $checked = 'checked';

                    }

                    if(
                        $_type === 'checkbox' &&
                        is_array( $_fieldState ) &&
                        in_array( $_value, $_fieldState )
                    ){

                        foreach ( $_fieldState as $_checkedValue ){

                            if( $_checkedValue === strval($_value) ){

                                $checked = 'checked';
                                break;

                            }

                        }

                    }

                    $id = $_fieldName;
                    $fieldName = $_fieldName;

                    if( $_type === 'radio' ){

                        $id = $_fieldName . '_' . $_value;
                        $fieldName = $_fieldName;

                    }else if( $_type === 'checkbox' ){

                        $id = $_fieldName . '[' . $counter . ']';
                        $fieldName = $id;

                    }

                    $topMargin = '0';
                    if( $counter !== 0 ){

                        $topMargin = '10px';

                    }

                    $input .= "<label style='pointer-events: all; margin-top: {$topMargin}; display: inline-block;' for='{$id}'><input style='pointer-events: all;' type='{$_type}' name='{$fieldName}' id='{$id}' value='{$_value}' {$checked} /><span style='pointer-events: all;'>{$_label}</span></label>";

                    $counter++;

                    if( $counter < count($_options) ){

                        $input .= "<br/>";

                    }

                }

            }else if(
                $_type === 'select' ||
                $_type === 'select-multiple'
            ){

                $fieldName = $_fieldName;

                $multiple = '';
                if( $_type === 'select-multiple' ){

                    $multiple = 'multiple';
                    $fieldName = $_fieldName . '[]';

                }

                $input = "<select style='pointer-events: all;' name='{$fieldName}' id='{$fieldName}' {$multiple}><option>-- Select an option --</option>";

                foreach ( $_options as $_value => $_label ){

                    if( is_array( $_label ) ){

                        $input .= "<optgroup label='{$_value}'>";

                        foreach ( $_label as $_val => $_lab ){

                            $selected = '';

                            if(
                                $_type === 'select' &&
                                $_val == $_fieldState
                            ){

                                $selected = 'selected';

                            }

                            if(
                                $_type === 'select-multiple' &&
                                is_array( $_fieldState )
                            ){

                                foreach ( $_fieldState as $_selectedValue ){

                                    if( $_selectedValue === strval($_val) ){

                                        $selected = 'selected';
                                        break;

                                    }

                                }

                            }

                            $input .= "<option value='{$_val}' {$selected}>{$_lab}</option>";

                        }

                        $input .= "</optgroup>";

                    }else{

                        $selected = '';

                        if(
                            $_type === 'select' &&
                            $_value == $_fieldState
                        ){

                            $selected = 'selected';

                        }

                        if(
                            $_type === 'select-multiple' &&
                            is_array( $_fieldState )
                        ){

                            foreach ( $_fieldState as $_selectedValue ){

                                if( $_selectedValue === strval($_value) ){

                                    $selected = 'selected';
                                    break;

                                }

                            }

                        }

                        $input .= "<option value='{$_value}' {$selected}>{$_label}</option>";

                    }

                }

                $input .= "</select>";

            }else if(
                $_type === 'media'
            ){

                $marginBottom = '0';

                if( strlen( $_fieldState ) > 0 ){

                    $marginBottom = '10px';

                }

                $input = "<img data-control-media='{$_fieldName}' style='pointer-events: none; width: 175px; height: auto; display: block; margin-bottom: {$marginBottom};' class='mp-settings-media-preview' src='{$_fieldState}'/>";
                $input .= "<input data-control-media='{$_fieldName}' style='min-width: 350px; pointer-events: all;' class='mp-settings-media-input' type='text' id='{$_fieldName}' name='{$_fieldName}' value='{$_fieldState}' />";
                $input .= "<span data-control-media='{$_fieldName}' style='pointer-events: all;' class='mp-settings-media-button mp-settings-add-media-button'>Select Media</span>";

            }

            return <<<HTML

            <div style="margin-left: -20px; padding: 10px 20px;">
                <label class="mp-settings-label" style=" pointer-events: none;" for="{$_fieldName}">
                    <span class="mp-settings-label-inner" style="min-width: 200px; max-width: 200px; margin-bottom: 10px; padding-right: 10px; padding-top: 5px; vertical-align: top; display: inline-block; color: #1d2327; font-weight: 600; cursor: pointer; line-height: 1.5; font-size: 14px; pointer-events: all;">
                        {$_fieldLabel}
                    </span>
                    <span style="display: inline-block;">
                        {$input}
                        <br/>
                        <span style="padding-top: 5px; display: inline-block; font-size: 14px; color: #646970; line-height: 1.5; pointer-events: none;">
                            {$_fieldDescription}
                        </span>
                    </span>
                </label>
            </div>

            HTML;

        }

        public function render_page()
        {

            ?>

            <h1 id="mp-settings-header" style="padding: 0.67em 0 0.67em 20px;margin: 0 0 18px -20px;background: #1d2327;color: #f0f0f1;position: relative;"><?php echo $this->pageTitle; ?></h1>

            <!-- Admin Notice Forced Container Position -->
            <div class="wrap"><h2></h2></div>
            <!-- END Admin Notice Forced Container Position END -->

            <form method="post" action="options.php">

                <?php
                \settings_fields( $this->optionGroup->get_key() );
                \do_settings_sections( $this->optionGroup->get_key() );
                \wp_enqueue_media();

                if( $this->parentSlug !== 'options-general.php' ){

                    \settings_errors();

                }

                if( ! is_null( $this->optionGroup->get_options() ) ){

                    $state = \get_option( $this->optionGroup->get_key() );

                    foreach ( $this->optionGroup->get_options() as $_option ){

                        $fieldName = $this->optionGroup->get_key() . '[' . $_option->get_name() . ']';

                        if( array_key_exists( $_option->get_name(), $state ) ){

                            $fieldState = $state[$_option->get_name()];

                        }else{

                            $fieldState = $_option->get_default_value();

                        }

                        echo $this->get_field_markup( $fieldName, $fieldState, $_option->get_label(), $_option->get_description(), $_option->get_type(), $_option->get_options() );

                    }

                }

                ?>

                <style>
                    h1#mp-settings-header::after{
                        content: '';
                        background: #1d2327;
                        position: absolute;
                        top: -1000px;
                        left: 0;
                        height: 1000px;
                        width: 100%;
                        display: block;
                    }
                    .mp-settings-label:hover span.mp-settings-label-inner,
                    .mp-settings-label:active span.mp-settings-label-inner,
                    .mp-settings-label:focus span.mp-settings-label-inner{
                        text-decoration: underline;
                        text-decoration-style: dotted;
                    }
                    .mp-settings-add-media-button,
                    .mp-settings-submit-wrapper input[type="submit"]{
                        padding: 0.35rem 1.65rem;
                        border-radius: 3px;
                        transition-duration: 0.35s;
                        background-color: #32373E !important;
                        border: 1px solid #32373E !important;
                        color: #FFFFFF !important;
                        cursor: pointer;
                        pointer-events: all;
                    }
                    .mp-settings-add-media-button:hover,
                    .mp-settings-add-media-button:active,
                    .mp-settings-add-media-button:focus,
                    .mp-settings-submit-wrapper input[type="submit"]:hover,
                    .mp-settings-submit-wrapper input[type="submit"]:active,
                    .mp-settings-submit-wrapper input[type="submit"]:focus{
                        background-color: #FFFFFF !important;
                        border-color: #32373E !important;
                        color: #32373E !important;
                    }
                </style>
                <div class="mp-settings-submit-wrapper">
                <?php
                \submit_button();
                ?>
                </div>

            </form>

            <?php

        }

    }

}