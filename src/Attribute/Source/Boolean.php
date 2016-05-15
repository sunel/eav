<?php

namespace Eav\Attribute\Source;

use Eav\Attribute\Source;

class Boolean extends Source
{
        
    /**
     * Option values
     */
    const VALUE_YES = 1;
    const VALUE_NO = 0;


    /**
     * Retrieve all options array
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (is_null($this->options)) {
            $this->options = array(
                array(
                    'label' =>'Yes',
                    'value' => self::VALUE_YES
                ),
                array(
                    'label' => 'No',
                    'value' => self::VALUE_NO
                ),
            );
        }
        return $this->options;
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $_options = array();
        foreach ($this->getAllOptions() as $option) {
            $_options[$option['value']] = $option['label'];
        }
        return $_options;
    }

    /**
     * Get a text for option value
     *
     * @param string|integer $value
     * @return string
     */
    public function getOptionText($value)
    {
        $options = $this->getAllOptions();
        foreach ($options as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }
        return false;
    }
}
