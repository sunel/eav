<?php

namespace Eav\Attribute;

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
     * Get backend type of the attribute
     *
     * @return string
     */
    public function getType()
    {
        return $this->getAttribute()->getBackendType();
    }
    
    /**
    * Get table name for the values of the attribute
    *
    * @return string
    */
    public function getTable()
    {
        if (empty($this->table)) {
            if ($this->getAttribute()->isStatic()) {
                $this->table = $this->getAttribute()->getEntityType()->getEntityTablePrefix();
            } elseif ($this->getAttribute()->getBackendTable()) {
                $this->table = $this->getAttribute()->getBackendTable();
            } else {
                $entity = $this->getAttribute()->getEntity();
                $tableName = sprintf('%s_%s', $entity->getEntityTablePrefix(), $this->getType());
                $this->table = $tableName;
            }
        }

        return $this->table;
    }
    
    /**
     * Retrieve default value
     *
     * @return mixed
     */
    public function getDefaultValue()
    {
        if ($this->defaultValue === null) {
            if ($this->getAttribute()->getDefaultValue()) {
                $this->defaultValue = $this->getAttribute()->getDefaultValue();
            } else {
                $this->defaultValue = "";
            }
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
