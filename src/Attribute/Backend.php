<?php

namespace Eav\Attribute;

use Eav\Attribute;
use Eav\Contracts\Attribute\Backend as BackendContract;

abstract class Backend implements BackendContract
{
    /**
     * Table name for this attribute
     *
     * @var string
     */
    protected $table;

    /**
     * Reference to the attribute instance
     *
     * @var Eav\Attribute
     */
    protected $attribute;
    
    /**
     * Default value for the attribute
     *
     * @var mixed
     */
    protected $defaultValue = null;
    
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
     * Get backend type of the attribute
     *
     * @return string
     */
    public function type()
    {
        return $this->attribute()->backendType();
    }
    
    /**
    * Get table name for the values of the attribute
    *
    * @return string
    */
    public function tableName()
    {
        if (empty($this->table)) {
            $attribute = $this->attribute();
            if ($attribute->isStatic()) {
                $this->table = $attribute->entityType()->entityTableName();
            } else {
                $this->table = $this->type();
            }
        }

        return $this->table;
    }
    
    /**
     * Retrieve default value
     *
     * @return mixed
     */
    public function defaultValue()
    {
        if ($this->defaultValue === null) {
            $this->defaultValue = $this->attribute()->defaultValue()?:"";
        }

        return $this->defaultValue;
    }

    /**
     * Validate object
     *
     * @param mixed $attribute
     * @return boolean
     */
    abstract public function validate($attribute, $validator);
}
