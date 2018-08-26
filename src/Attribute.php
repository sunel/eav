<?php

namespace Eav;

use ReflectionException;
use Eav\Attribute\Concerns;
use Eav\Attribute\Collection;
use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    use Concerns\QueryBuilder;

    const TYPE_STATIC = 'static';
    
    /**
     * @{inheriteDoc}
     */
    protected $primaryKey = 'attribute_id';

    /**
     * @{inheriteDoc}
     */
    public $timestamps = false;
    

    /**
     * @{inheriteDoc}
     */
    protected $fillable = [
        'attribute_code', 'backend_class', 'backend_type',
        'backend_table', 'frontend_class', 'frontend_type',
        'frontend_label', 'source_class',  'default_value',
        'is_required', 'required_validate_class', 'entity_id'
    ];

    /**
     * Entity instance
     *
     * @var Eav\Entity
     */
    protected $entity;

    /**
     * Backend instance
     *
     * @var Eav\Attribute\Backend
     */
    protected $backend;

    /**
     * Frontend instance
     *
     * @var Eav\Attribute\Frontend
     */
    protected $frontend;

    /**
     * Source instance
     *
     * @var Eav\Attribute\Source
     */
    protected $source;

    /**
     * Attribute id cache
     *
     * @var array
     */
    protected $attributeIdCache  = [];

    /**
     * Attribute data table name
     *
     * @var string
     */
    protected $dataTable  = null;
    
    /**
     * Set attribute code
     *
     * @param   string $code
     * @return $this
     */
    public function setAttributeCode(string $code)
    {
        return $this->setAttribute('attribute_code', $code);
    }
    
    /**
     * Get attribute name
     *
     * @return string
     */
    public function getAttributeCode()
    {
        return $this->getAttribute('attribute_code');
    }

    /**
     * Get attribute identifuer
     *
     * @return int | null
     */
    public function getAttributeId()
    {
        return $this->getKey();
    }
    
    /**
     * Set attribute entity instance
     *
     * @param Eav\Entity $entity
     * @return $this
     */
    public function setEntity(Entity $entity)
    {
        $this->entity = $entity;
        return $this;
    }

    /**
     * Retrieve entity instance
     *
     * @return Eav\Entity
     */
    public function getEntity()
    {
        if (!$this->entity) {
            $this->entity = $this->getEntityType();
        }
        return $this->entity;
    }
    
    /**
     * Get Entity Type Id
     *
     * @return int|string $code
     */
    public function getEntityTypeId()
    {
        return $this->getAttribute('entity_id');
    }
    
    /**
     * Retreive entity type
     *
     * @return string
     */
    public function getEntityType()
    {
        return Entity::findById($this->getEntityTypeId());
    }
    
    /**
     * Retreive backend type
     *
     * @return string
     */
    public function getBackendType()
    {
        return $this->getAttribute('backend_type');
    }
    
    /**
     * Retreive frontend type
     *
     * @return string
     */
    public function getFrontendInput()
    {
        return $this->getAttribute('frontend_type');
    }
    
    /**
     * Retreive frontend label
     *
     * @return string
     */
    public function getFrontendLabel()
    {
        return $this->getAttribute('frontend_label');
    }
    
    /**
     * Retreive default value
     *
     * @return string
     */
    public function getDefaultValue()
    {
        return $this->getAttribute('default_value');
    }

    /**
     * Create a new attribute.
     *
     * @param array $data
     * @return \Eav\Attribute
     */
    public static function add(array $data)
    {
        $instance = new static;
                
        try {
            $eavEntity = Entity::where('entity_code', '=', $data['entity_code'])->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new \Exception("Unable to load Entity : ".$data['entity_code']);
        }
        
        unset($data['entity_code']);
        
        $data['entity_id'] = $eavEntity->entity_id;
        
        $options = [];
        
        if ($data['frontend_type'] == 'select' && empty($data['source_class'])) {
            if (isset($data['options'])) {
                $options = $data['options'];
                unset($data['options']);
            }
        }
        
        
        $instance->fill($data)->save();
        
        if ($instance->getKey()) {
            AttributeOption::add($instance, $options);
        }

        return $instance;
    }
    
    /**
     * Delete a attribute from the database.
     *
     * @param array $data
     * @return mixed
     */
    public static function remove(array $data)
    {
        $instance = new static;
                
        try {
            $eavEntity = Entity::where('entity_code', '=', $data['entity_code'])->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new \Exception("Unable to load Entity : ".$data['entity_code']);
        }
        
        unset($data['entity_code']);
        
        $data['entity_id'] = $eavEntity->entity_id;
        
        $instance->where($data)->delete();
    }

    /**
     * Get All the option for the attribute.
     *
     * @return array
     */
    public function options()
    {
        if ($this->usesSource()) {
            return $this->getSource()->toArray();
        }
        return $this->optionValues->toOptions();
    }

    /**
     * Relates to the option table.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function optionValues()
    {
        return $this->hasMany(AttributeOption::class, 'attribute_id');
    }

    /**
     * Check if attribute is static
     *
     * @return bool
     */
    public function isStatic()
    {
        return $this->getAttribute('backend_type') == self::TYPE_STATIC || $this->getAttribute('backend_type') == '';
    }
    
    /**
     * Retrieve backend instance
     *
     * @return Eav\Attribute\Backend
     */
    public function getBackend()
    {
        if (empty($this->backend)) {
            try {
                if (!$this->getAttribute('backend_class')) {
                    throw new ReflectionException('No class specified');
                }
                $backend = app($this->getAttribute('backend_class'));
            } catch (ReflectionException $e) {
                throw new \Exception('Invalid backend class specified: ' . $this->getAttribute('backend_class'));
            }

            $this->backend = $backend->setAttribute($this);
        }

        return $this->backend;
    }

    /**
     * Retrieve frontend instance
     *
     * @return Eav\Attribute\Frontend
     */
    public function getFrontend()
    {
        if (empty($this->frontend)) {
            try {
                if (!$this->getAttribute('frontend_class')) {
                    throw new ReflectionException('No class specified');
                }
                $frontend = app($this->getAttribute('frontend_class'));
            } catch (ReflectionException $e) {
                throw new \Exception('Invalid frontend class specified: ' . $this->getAttribute('frontend_class'));
            }
            
            $this->frontend = $frontend->setAttribute($this);
        }

        return $this->frontend;
    }

    /**
     * Retrieve source instance
     *
     * @return Eav\Attribute\Source
     */
    public function getSource()
    {
        if (empty($this->source)) {
            try {
                if (!$this->getAttribute('source_class')) {
                    throw new ReflectionException('No class specified');
                }
                $source = app($this->getAttribute('source_class'));
            } catch (ReflectionException $e) {
                throw new \Exception('Invalid source class specified: ' . $this->getAttribute('source_class'));
            }
            
            $this->source = $source->setAttribute($this);
        }
        return $this->source;
    }

    /**
     * Check if the atribute uses any source for options.
     *
     * @return bool
     */
    public function usesSource()
    {
        return ($this->getAttribute('frontend_type') === 'select' || $this->getAttribute('frontend_type') === 'multiselect')
            && !empty($this->getAttribute('source_class'));
    }
    
    /**
     * Get attribute backend table name
     *
     * @return string
     */
    public function getBackendTable()
    {
        if ($this->dataTable === null) {
            $backendTable = trim($this->getAttribute('backend_table'));
            if (empty($backendTable)) {
                $backendTable  = $this->getEntity()->getEntityTableName().'_'.$this->getAttribute('backend_type');
            }
            $this->dataTable = $backendTable;
        }
        return $this->dataTable;
    }
    
    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array  $models
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function newCollection(array $models = [])
    {
        return new Collection($models);
    }
    
    /**
     * Find the attribute by code.
     *
     * @param  string $code
     * @param  string $entityCode
     * @return \Eav\Attribute
     */
    public static function findByCode(string $code, string $entityCode)
    {
        $entity = Entity::findByCode($entityCode);
        
        $instance = new static;
        
        return $instance->newQuery()->where([
            'attribute_code' => $code,
            'entity_id' => $entity->getkey()
        ])->firstOrFail();
    }
    
    
    /**
     * Return attribute id
     *
     * @param string $code
     * @param string $entityType
     * @return int | null
     */
    public function getIdByCode(string $code, int $entityType)
    {
        $k = "{$entityType}|{$code}";
        if (!isset($this->attributeIdCache[$k])) {
            $attribute = \DB::table($this->getTable())
                ->select('attribute_id')
                ->where('attribute_code', $code)
                ->where('entity_id', $entityType)
                ->first();
            if ($attribute) {
                $this->attributeIdCache[$k] = $attribute->attribute_id;
            } else {
                return null;
            }
        }
        return $this->attributeIdCache[$k];
    }
    
    /**
     * Insert the data for the attribute.
     *
     * @param  mixed $value
     * @param  int $entityId
     * @return bool
     */
    public function insertAttribute($value, $entityId)
    {
        $insertData = [
            'entity_type_id' => $this->getEntity()->getKey(),
            'attribute_id' => $this->getKey(),
            'entity_id' => $entityId,
            'value' => $value
        ];
        
        return $this->newBaseQueryBuilder()
            ->from($this->getBackendTable())
            ->insert($insertData);
    }

    /**
     * Update the data for the attribute.
     *
     * @param  mixed $value
     * @param  int $entityId
     * @return bool
     */
    public function updateAttribute($value, $entityId)
    {
        $attributes = [
            'entity_type_id' => $this->getEntity()->getKey(),
            'attribute_id' => $this->getKey(),
            'entity_id' => $entityId,
        ];

        return $this->newBaseQueryBuilder()
            ->from($this->getBackendTable())
            ->updateOrInsert($attributes, ['value' => $value]);
    }

    /**
     * Mass Update the data for the attribute.
     *
     * @param  mixed $value
     * @param  array  $entityIds
     * @return mixed
     */
    public function massUpdate($value, array $entityIds)
    {
        return collect($entityIds)->map(function ($entityId) use ($value) {
            return $this->updateAttribute($value, $entityId);
        });
    }
}
