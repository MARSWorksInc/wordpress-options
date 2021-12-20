<?php
/*
 * @package marspress/wp-options
 */

namespace MarsPress\Options\ThemeMods;

if( ! class_exists( 'Customization' ) )
{

    final class Customization
    {

        private string $name;

        private string $label;

        private string $type;

        private ?string $description;

        private ?string $placeholder;

        private array $options;

        private int $position;

        /**
         * @var mixed $defaultValue
         */
        private $defaultValue;

        /**
         * @var callable $sanitizationCallback
         */
        private $sanitizationCallback;

        /**
         * @var callable $returnCallback
         */
        private $returnCallback;

        public function __construct(
            string $_name,
            string $_label,
            string $_type,
            ?string $_description = null,
            ?string $_placeholder = null,
            array $_options = [],
            int $_position = 10,
            $_defaultValue = null,
            $_sanitizationCallback = null,
            $_returnCallback = null
        )
        {

            $this->name = $_name;
            $this->label = $_label;
            $this->type = $_type;
            $this->description = $_description;
            $this->placeholder = $_placeholder;
            $this->options = $_options;
            $this->position = $_position;
            $this->defaultValue = $_defaultValue;
            $this->sanitizationCallback = $_sanitizationCallback;
            $this->returnCallback = $_returnCallback;

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

        public function get_description(): ?string
        {

            return $this->description;

        }

        public function get_placeholder(): ?string
        {

            return $this->placeholder;

        }

        public function get_options(): array
        {

            return $this->options;

        }

        public function get_position(): int
        {

            return $this->position;

        }

        public function get_default_value()
        {

            return $this->defaultValue;

        }

        public function get_sanitization_callback(): ?callable
        {

            return $this->sanitizationCallback;

        }

        public function get_return_callback(): ?callable
        {

            return $this->returnCallback;

        }

    }

}