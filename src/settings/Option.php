<?php
/*
 * @package marspress/wp-options
 */

namespace MarsPress\Options\Settings;

if( ! class_exists( 'Option' ) )
{

    final class Option
    {

        private string $name;

        private string $label;

        private string $type;

        private ?string $description;

        private array $options;

        /**
         * @var mixed $defaultValue
         */
        private $defaultValue;

        /**
         * @var callable $sanitizationCallback
         */
        private $sanitizationCallback;

        public function __construct(
            string $_name,
            string $_label,
            string $_type,
            ?string $_description = null,
            array $_options = [],
            $_defaultValue = null,
            $_sanitizationCallback = null
        )
        {

            $this->name = $_name;
            $this->label = $_label;
            $this->type = $_type;
            $this->description = $_description;
            $this->options = $_options;
            $this->defaultValue = $_defaultValue;
            $this->sanitizationCallback = $_sanitizationCallback;

        }

        public function get_name(): string
        {

            return $this->name;

        }

        public function get_label(): string
        {

            return $this->label;

        }

        public function get_type(): string
        {

            return $this->type;

        }

        public function get_description(): string
        {

            return $this->description;

        }

        public function get_options(): array
        {

            return $this->options;

        }

        public function get_default_value()
        {

            return $this->defaultValue;

        }

        public function get_sanitization_callback(): ?callable
        {

            return $this->sanitizationCallback;

        }

    }

}