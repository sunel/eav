<?php

namespace Eav\Attribute;

use Eav\Attribute;
use Eav\Contracts\Attribute\Frontend as FrontendContract;

class Frontend implements FrontendContract
{
    /**
     * Reference to the attribute instance
     *
     * @var Eav\Attribute
     */
    protected $attribute;
    
    /**
     * Set attribute instance
     *
     * @param Eav\Attribute
     * @return Eav\Attribute\Backend
     */
    public function setAttribute(Attribute $attribute)
    {
        $this->attribute = $attribute;
        return $this;
    }

    /**
     * Get attribute instance
     *
     * @return Eav\Attribute
     */
    public function attribute()
    {
        return $this->attribute;
    }
    
    /**
     * Get attribute type for user interface form
     *
     * @return string
     */
    public function inputType()
    {
        return $this->attribute()->frontendInput();
    }
    
    /**
     * Retreive lable
     *
     * @return string
     */
    public function label()
    {
        $label = $this->attribute()->frontendLabel();
        if (($label === null) || $label == '') {
            $label = $this->attribute()->code();
        }

        return $label;
    }
    
    /**
     * Get select options in case it's select and options source is defined
     *
     * @return array
     */
    public function selectOptions()
    {
        return $this->attribute()->options();
    }

    /**
     * Retreive option by option id
     *
     * @param int $optionId
     * @param mixed $default
     * @return mixed|boolean
     */
    public function option($optionId, $default = false)
    {
        $options = $this->attribute()->options();
        foreach ($options as $option) {
            if ($option['value'] == $optionId) {
                return $option['label'];
            }
        }
        return $default;
    }
}
