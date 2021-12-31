# MarsPress Options

### Installation

Require the composer package in your composer.json with `marspress/wp-options` with minimum `dev-main` OR run `composer require marspress/wp-options`

## WP Options

Options are generally under the Settings menu in wp-admin.

### Resources

* https://developer.wordpress.org/reference/functions/register_setting/

### Usage

`new \MarsPress\Options\Settings\Option_Group()` takes 1 required parameter.
* Key (required)(string)
    * Unique key for the Option Group.
    * If the option key is already in use, the Option Group will not be registered, and an admin notice will be displayed in wp-admin.

#### Available Methods

* `add_options` takes any number of parameters as long as they are of the type `\MarsPress\Options\Settings\Option`
  * Returns self: `$this`.
* `get_option_value()` takes 2 parameters, 1 required and 1 optional.
  * Option Name (required)(string)
    * The option name for which value to return.
    * If the option value does not exist in the database, the method will return `null`.
    * If the Option object has a Return Callback, the method will return the Return Callback's return value.
  * Return Raw Value (optional)(bool)
    * Whether to skip the Return Callback of the option.
    * Defaults to `false`.
    
`new \MarsPress\Options\Settings\Option` takes 8 parameter, 3 required and 5 optional.
* Name (required)(string)
  * Unique name for the Option in the Option Group.
  * If the option name is already in use inside the Option Group, it will not be added, and an admin notice will be displayed in wp-admin.
* Label (required)(string)
  * The field's label.
* Type (required)(string)
  * The field's type.
  * Valid values are:
    * `text`
    * `password`
    * `email`
    * `radio`
    * `checkbox`
    * `select`
    * `select-multiple`
    * `media`
* Description (optional)(string)
  * A brief description of what the option does.
  * This displays underneath the HTML input element.
* Options (optional)(array)
  * An array of `value` => `label` pairs.
  * Defaults to an empty array: `[]`.
  * This parameter should be used for `radio`, `checkbox`, `select`, and `select-multiple`. Pass an empty array otherwise.
* Default Value (optional)(mixed)
  * The default value for the option.
  * Defaults to `null`.
* Sanitization Callback (optional)(callable)
  * A callable function to sanitize the input value BEFORE it is entered into the database.
  * This can be a Closure function, or `[ $this, '<public_method_name>' ]` for non-static classes or `[ __CLASS__, '<public_method_name>' ]` for static classes.
  * Your function should take 1 parameter, which is the value of the user input for the field. Thus, it is a `mixed` field.
  * Defaults to `null`.
* Return Callback (optional)(callable)
  * A callable function to format the option value in its return method.
  * This can be a Closure function, or `[ $this, '<public_method_name>' ]` for non-static classes or `[ __CLASS__, '<public_method_name>' ]` for static classes.
  * Your function should take 1 parameter, which is the value of the option returned from the database.
  * Defaults to `null`.

`new \MarsPress\Options\Settings\Page` takes 7 parameter, 3 required and 4 optional.
* Option Group (required)(\MarsPress\Options\Settings\Option_Group)
  * An Option Group instance.
* Page Title (required)(string)
  * The Page Title, this will be outputted onto the page as an `h1` element.
* Page Slug (required)(string)
  * The wp-admin slug for the page.
* Parent Slug (optional)(string)
  * The parent slug for the page.
  * Defaults to `null`.
  * If not provided, your page will be added to the main wp-admin menu.
  * If you want your page under the Settings menu, pass `options-general.php`.
* Menu Title (optional)(string)
  * The Menu Title that displays in the wp-admin menu.
  * Defaults to `Page Title`.
* Icon (optional)(string)
  * The dashicon string or absolute URL for the menu icon.
  * This is only used if your page does not have a Parent Slug.
  * Defaults to `dashicons-admin-generic`.
* Menu Position (optional)(int)
  * The menu item position in the wp-admin menu.
  * This is only used if your page does not have a Parent Slug.
  * Defaults to `80`.

#### Examples

First, you must create and Option Group:

```PHP
$exampleOptionGroup = new \MarsPress\Options\Settings\Option_Group('example_settings');
```

Then you can add Options to your Option Group (including examples of each field type):

```PHP
$exampleOptionGroup->add_options(
    new \MarsPress\Options\Settings\Option(
      'example_text',
      'Text Field Example',
      'text',
      'Example description.',
        [],
        null,
        function( $_input ){
          $_input = str_replace( '-example-sanitization', '', $_input );
          return $_input . '-example-sanitization';
        }
    ),
    new \MarsPress\Options\Settings\Option(
        'example_radio',
        'Radio Field Example',
        'radio',
        'Example description.',
        [
            '1' => 'Value One',
            '2' => 'Value Two',
            '3' => 'Value Three',
        ]
    ),
    new \MarsPress\Options\Settings\Option(
        'example_checkbox',
        'Checkbox Field Example',
        'checkbox',
        'Example description.',
        [
            '1' => 'Value One',
            '2' => 'Value Two',
            '3' => 'Value Three',
        ]
    ),
    new \MarsPress\Options\Settings\Option(
        'example_select',
        'Select Field Example',
        'select',
        'Example description.',
        [
            '1' => 'Value One',
            '2' => 'Value Two',
            '3' => 'Value Three',
        ]
    ),
    new \MarsPress\Options\Settings\Option(
        'example_select_w_groups',
        'Select Field with Groups Example',
        'select',
        'Example description.',
        [
            'Option Group Integers' => [
                '1' => 'Value One',
                '2' => 'Value Two',
                '3' => 'Value Three',
            ],
            'Option Group Floats'   => [
                '1.00' => 'Value One.00',
                '2.00' => 'Value Two.00',
                '3.00' => 'Value Three.00',
            ],
        ]
    ),
    new \MarsPress\Options\Settings\Option(
        'example_select_multiple',
        'Select Multiple Field Example',
        'select-multiple',
        'Example description.',
        [
            '1' => 'Value One',
            '2' => 'Value Two',
            '3' => 'Value Three',
        ]
    ),
    new \MarsPress\Options\Settings\Option(
        'example_select_multiple_w_groups',
        'Select Multiple Field with Groups Example',
        'select-multiple',
        'Example description.',
        [
            'Option Group Integers' => [
                '1' => 'Value One',
                '2' => 'Value Two',
                '3' => 'Value Three',
            ],
            'Option Group Floats'   => [
                '1.00' => 'Value One.00',
                '2.00' => 'Value Two.00',
                '3.00' => 'Value Three.00',
            ],
        ],
        null,
        null,
        function( $_value ){
            //Example Return Callback
            return implode( ', ', $_value );
        }
    ),
    new \MarsPress\Options\Settings\Option(
        'example_media',
        'Media Field Example',
        'media',
        'Example description.',
        [],
        null,
        null,
        function( $_value ){
            //Example Return Callback
            return "<img style='max-width: 300px; height: auto;' src='$_value'>";
        }
    ),
```

Then you can create a Page:

```PHP
new \MarsPress\Options\Settings\Page(
  $exampleOptionGroup,
  'Example Settings',
  'example-settings',
  'options-general.php'
);
```

You can them access your option values:

```PHP
echo $optionGroup->get_option_value('example_media');
```

#### Method / Class Chaining

Because the `add_options` method of the Option Group class returns itself, you are able to use class and method chaining as such:
```PHP
$exampleOptionGroup = (new \MarsPress\Options\Settings\Option_Group('example_settings'))->add_options(
    new \MarsPress\Options\Settings\Option(
        'example_text',
        'Text Field Example',
        'text',
        'This is the description.',
        [],
        null,
        function ( $_input ){
            $_input = str_replace( '-working', '', $_input );
            return $_input . '-working';
        }
    ),
);
```

Although you could chain inside the `\MarsPress\Options\Settings\Page` constructor as well, it is recommended that you store your instance of your Option Group so you can access the option values easily.

## WP Theme Mods

Theme mods are generally in the Appearance Customizer.

### Resources

* https://developer.wordpress.org/themes/customize-api/customizer-objects/

### Usage

`new \MarsPress\Options\ThemeMods\Customization_Section()` takes 4 parameters, 2 required and 2 optional.
* Key (required)(string)
  * Unique section key.
  * If the section key already exists inside Customize Manager, an admin notice will be outputted in the Appearance Customizer.
* Label (required)(string)
  * Label to be displayed in the Customizer Panel.
* Description (optional)(string)
  * Description which displays in the Customizer Panel underneath the Label.
  * Defaults to `null`
* Position (optional)(string)
  * The priority/position in the Customizer Panel.
  * Defaults to `160` (near the bottom of the Panel)

#### Available Methods

* `add_customizations` takes any number of parameters as long as they are of the type `\MarsPress\Options\ThemeMods\Customization`
  * Returns self: `$this`.
* `get_customization_value()` takes 2 parameters, 1 required and 1 optional.
  * Customization Name (required)(string)
    * The theme mod name for which value to return.
    * If the theme mod value does not exist in the database, the method will return `null`.
    * If the Customization object has a Return Callback, the method will return the Return Callback's return value.
  * Return Raw Value (optional)(bool)
    * Whether to skip the Return Callback of the theme mod.
    * Defaults to `false`.

`new \MarsPress\Options\ThemeMods\Customization()` takes 10 parameters, 3 required and 7 optional.
* Name (required)(string)
  * The ID of the theme mod.
  * This should be unique for the Section.
  * If the Name already exists inside the Section, it will not be added and an admin notification will be displayed in wp-admin.
* Label (required)(string)
  * The label for the HTML input.
* Type (required)(string)
  * The type of input.
  * Valid values are (some depending on browser support):
    * `text`
    * `hidden`
    * `number`
    * `range`
    * `url`
    * `tel`
    * `email`
    * `search`
    * `time`
    * `date`
    * `datetime`
    * `week`
    * `checkbox`
    * `checkbox-multiple`
    * `select`
    * `select-multiple`
    * `radio`
    * `textarea`
    * `media`
* Description (optional)(string)
  * Description to display underneath the HTML input. 
  * Defaults to `null`
* Placeholder (optional)(string)
  * The placeholder for the HTML input.
  * This is subject to HTML supported placeholder elements.
  * Defaults to `null`
* Options (optional)(array)
  * Array of `value => label` options.
  * This is only used for `checkbox`, `checkbox-multiple`, `select`, `select-multiple`, and `radio` types.
  * You should pass an empty array (`[]`) otherwise.
  * Defaults to an empty array: `[]`
* Position (optional)(int)
  * The priority/position inside the Section.
  * Defaults to `10`
* Default Value (optional)(mixed)
  * The default value for the input.
  * This is a mixed type as it depends on the type of your theme mod. E.g. a `text` type could have a default value of `"This is the default value"` but types like `select-multiple` should have an array.
  * Defaults to `null`
* Sanitization Callback (optional)(callable)
  * The sanitization callback to be executed before the value is saved into the database.
  * This can be a Closure function, or `[ $this, '<public_method_name>' ]` for non-static classes or `[ __CLASS__, '<public_method_name>' ]` for static classes. 
  * Your function should take 1 parameter, which is the value of the user input for the field. Thus, it is a mixed field.
  * Defaults to `null`
* Return Callback (optional)(callable)
  * The return callback to be executed before the value is returned to you in PHP.
  * This can be a Closure function, or `[ $this, '<public_method_name>' ]` for non-static classes or `[ __CLASS__, '<public_method_name>' ]` for static classes.
  * Your function should take 1 parameter, which is the value of the user input for the field. Thus, it is a mixed field.
  * Defaults to `null`

### Examples

First you need to make a Section:

```PHP
$exampleCustomizationSection = new \MarsPress\Options\ThemeMods\Customization_Section(
    'example_section',
    'Example Section',
    'Example Section Description.',
    1
);
```

Then you can add theme mods to the Section (including examples of the most common field types):

```PHP
$exampleCustomizationSection->add_customizations(
    new \MarsPress\Options\ThemeMods\Customization(
      'example_text',
      'Example Text Field',
      'text',
      'Example Field Description.',
      'Example placeholder'
    ),
    new \MarsPress\Options\ThemeMods\Customization(
        'example_select',
        'Example Select Field',
        'select',
        'Example Field Description.',
        null,
        [
            ''  => '-- Select --',
            '1' => 'Value One',
            '2' => 'Value Two',
            '3' => 'Value Three',
        ]
    ),
    new \MarsPress\Options\ThemeMods\Customization(
        'example_multi_select',
        'Example Select Multiple Field',
        'select-multiple',
        'Example Field Description.',
        null,
        [
            ''  => '-- Select --',
            '1' => 'Value One',
            '2' => 'Value Two',
            '3' => 'Value Three',
        ]
    ),
    new \MarsPress\Options\ThemeMods\Customization(
        'example_checkbox',
        'Example Checkbox Field',
        'checkbox',
        'Example Field Description.',
        null,
    ),
    new \MarsPress\Options\ThemeMods\Customization(
        'example_multi_checkbox',
        'Example Checkbox Multiple Field',
        'checkbox-multiple',
        'Example Field Description.',
        null,
        [
            '1' => 'Value One',
            '2' => 'Value Two',
            '3' => 'Value Three',
        ],
        10,
        null,
        null,
        function ( $_value ){
            if( is_array( $_value ) ){
                return implode( ',', $_value );
            }
            return $_value;
        }
    ),
    new \MarsPress\Options\ThemeMods\Customization(
        'example_radio',
        'Example Radio Field',
        'radio',
        'Example Field Description.',
        null,
        [
            ''  => '-- None --',
            '1' => 'Value One',
            '2' => 'Value Two',
            '3' => 'Value Three',
        ]
    ),
    new \MarsPress\Options\ThemeMods\Customization(
        'example_text_area',
        'Example Text Area Field',
        'textarea',
        'Example Field Description.',
        'Example placeholder'
    ),
    new \MarsPress\Options\ThemeMods\Customization(
        'example_media',
        'Example Media Field',
        'media',
        'Example Field Description.',
        null
    ),
);
```

#### Method / Class Chaining

Because the `add_customizations` method of the Customization Section class returns itself, you are able to use class and method chaining as such:

```PHP
$exampleCustomizationSection = (new \MarsPress\Options\ThemeMods\Customization_Section(
    'example_section',
    'Example Section',
    'Example Section Description.',
    1
)->add_customizations(
    new \MarsPress\Options\ThemeMods\Customization(
      'example_text',
      'Example Text Field',
      'text',
      'Example Field Description.',
      'Example placeholder'
    ),
));
```