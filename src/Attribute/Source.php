<?php

namespace Eav\Attribute;

use Eav\Contracts\Attribute\Source as SourceContract;

abstract class Source implements SourceContract
{
    /**
     * Reference to the attribute instance
     *
     * @var Eav\Attribute
     */
    protected $attribute;
    
    /**
     * Options array
     *
     * @var array
     */
    protected $options  = null;
    
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
     * Retrieve option array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->getOptionArray();
    }
}
