<?php

namespace Eav\Attribute;

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
    public function setAttribute($attribute)
    {
        $this->attribute = $attribute;
        return $this;
    }

    /**
     * Get attribute instance
     *
     * @return Eav\Attribute\Backend
     */
    public function getAttribute()
    {
        return $this->attribute;
    }
    
    /**
     * Get attribute type for user interface form
     *
     * @return string
     */
    public function getInputType()
    {
        return $this->getAttribute()->getFrontendInput();
    }
    
    /**
     * Retreive lable
     *
     * @return string
     */
    public function getLabel()
    {
        $label = $this->getAttribute()->getFrontendLabel();
        if (($label === null) || $label == '') {
            $label = $this->getAttribute()->getAttributeCode();
        }

        return $label;
    }
    
    /**
     * Get select options in case it's select box and options source is defined
     *
     * @return array
     */
    public function getSelectOptions()
    {
        return $this->getAttribute()->getSource()->getAllOptions();
    }

    /**
     * Retreive option by option id
     *
     * @param int $optionId
     * @return mixed|boolean
     */
    public function getOption($optionId)
    {
        $source = $this->getAttribute()->getSource();
        if ($source) {
            return $source->getOptionText($optionId);
        }
        return false;
    }
}
