<?php 

namespace Eav;

use Eav\Traits\Attribute as AttributeTraits;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Eav\Database\Eloquent\Builder as EavEloquentBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Eav\Database\Query\Builder as EavQueryBuilder;
use Illuminate\Database\Eloquent\ModelNotFoundException;

abstract class Model extends Eloquent
{
    use AttributeTraits;

    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY  = '';

    /**
     * Indicates if all mass assignment is enabled.
     *
     * @var bool
     */
    protected static $unguarded = true;
    
    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        if (static::ENTITY === '') {
            throw new \Exception("Entity Type need to be specified for :: ".static::class);
        }

        $this->addModelEvent();

        parent::__construct($attributes);
    }

    /**
     * Get the current attribute set using one-to-one relationship for attribute.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function currentAttributeSet()
    {
        return $this->hasOne(AttributeSet::class, 'attribute_set_id');
    }
    
    /**
     * Get the Entity related to the model.
     *
     * @return \Eav\Entity
     * @throws \Exception.
     */
    public function baseEntity()
    {
        try {
            $eavEntity = Entity::findByCode(static::ENTITY);
            $eavEntity->setEntityKey($this->getKeyName());
            $eavEntity->setEntityCustomTable($this->table);
        } catch (ModelNotFoundException $e) {
            throw new \Exception("Unable to load Entity : ".static::ENTITY);
        }

        return $eavEntity;
    }

    /**
     * Get the Entity ID related to the model.
     *
     * @return int.
     */
    public function baseEntityId()
    {
        if ($value = $this->getAttributeValue('entity_id')) {
            return $value;
        }

        return $this->baseEntity()->entity_id;
    }

    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable()
    {
        $table = parent::getTable();

        if ($this->canUseFlat()) {
            return $table.'_flat';
        }

        return $table;
    }
    
    /**
     * Enable or Disable Flat table.
     *
     * @param bool $flag
     */
    public function setUseFlat($flag)
    {
        $this->baseEntity()->is_flat_enabled = $flag;
    }
    
    /**
     * Check if the Entity can use flat table.
     *
     * @return bool
     */
    public function canUseFlat()
    {
        return $this->baseEntity()->canUseFlat();
    }

    /**
     * Validate the attributes.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validate()
    {
        $attributes = $this->attributes;
        
        $loadedAttributes = $this->loadAttributes(
            array_keys($attributes),
            true,
            true
        )->validate($attributes);
    }

    /**
    * Create a new Eloquent query builder for the model.
    *
    * @param  \Illuminate\Database\Query\Builder  $query
    * @return \Illuminate\Database\Eloquent\Builder|static
    */
    public function newEloquentBuilder($query)
    {
        return new EavEloquentBuilder($query);
    }

    /**
     * Get a new query builder instance for the connection.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function newBaseQueryBuilder()
    {
        $connection = $this->getConnection();

        return new EavQueryBuilder(
            $connection,
            $connection->getQueryGrammar(),
            $connection->getPostProcessor(),
            $this->baseEntity()
        );
    }

    /**
     * Perform a model insert operation.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  array  $options
     * @return bool
     */
    protected function performInsert(Builder $query, array $options = [])
    {
        if ($this->canUseFlat()) {
            return parent::performInsert($query, $options);
        }

        // First we'll need to create a fresh query instance and touch the creation and
        // update timestamps on this model, which are maintained by us for developer
        // convenience. After, we will just continue saving these model instances.
        if ($this->usesTimestamps()) {
            $this->updateTimestamps();
        }
        
        return $this->getConnection()->transaction(function () use ($query, $options) {
            if ($this->fireModelEvent('creating') === false) {
                return false;
            }

            // If the model has an incrementing key, we can use the "insertGetId" method on
            // the query builder, which will give us back the final inserted ID for this
            // table from the database. Not all tables have to be incrementing though.
            $attributes = $this->getAttributes();
            
            $loadedAttributes = $this->loadAttributes(array_keys($attributes), true, true);
            
            $loadedAttributes->validate($attributes);
            
            if (!$this->insertMainTable($query, $options, $attributes, $loadedAttributes)) {
                return false;
            }
            
            if (!$this->insertAttributes($query, $options, $attributes, $loadedAttributes)) {
                return false;
            }
            
            $this->fireModelEvent('created', false);
             
            return true;
        });
    }


    /**
     * Perform a model insert operation on the main entity table.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  array  $options
     * @return bool
     */
    public function insertMainTable(Builder $query, array $options, $attributes, $loadedAttributes)
    {
        if ($this->fireModelEvent('creating.main') === false) {
            return false;
        }
         
        $mainTableAttribute = $this->getMainTableAttribute($loadedAttributes);
        
        $mainData = array_intersect_key($attributes, array_flip($mainTableAttribute));

        if ($this->getIncrementing()) {
            $this->insertAndSetId($query, $mainData);
        }

        // If the table isn't incrementing we'll simply insert these attributes as they
        // are. These attribute arrays must contain an "id" column previously placed
        // there by the developer as the manually determined key for these models.
        else {
            if (empty($mainData)) {
                return true;
            }

            $query->insert($mainData);
        }
        
        // We will go ahead and set the exists property to true, so that it is set when
        // the created event is fired, just in case the developer tries to update it
        // during the event. This will allow them to do so and run an update here.
        $this->exists = true;

        $this->wasRecentlyCreated = true;

        $this->fireModelEvent('created.main', false);
        
        return true;
    }

    /**
     * Perform a model insert operation on the attributes related to entity.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  array  $options
     * @return bool
     */
    public function insertAttributes(Builder $query, array $options, $modelData, $loadedAttributes)
    {
        $loadedAttributes->each(function ($attribute, $key) use ($modelData) {
            if (!$attribute->isStatic()) {
                $attribute->setEntity($this->baseEntity());
                $attribute->insertAttribute($modelData[$attribute->getAttributeCode()], $this->getKey());
            }
        });
        
        return true;
    }

    /**
     * Perform a model update operation.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  array  $options
     * @return bool
     */
    protected function performUpdate(Builder $query, array $options = [])
    {
        if ($this->canUseFlat()) {
            return parent::performUpdate($query, $options);
        }

        // First we need to create a fresh query instance and touch the creation and
        // update timestamp on the model which are maintained by us for developer
        // convenience. Then we will just continue saving the model instances.
        if ($this->usesTimestamps()) {
            $this->updateTimestamps();
        }
             
        $dirty = $this->getDirty();

        if (count($dirty) > 0) {
            $loadedAttributes = $this->loadAttributes(array_keys($dirty));
        
            $loadedAttributes->validate($dirty);

            return $this->getConnection()->transaction(function () use ($query, $options, $dirty, $loadedAttributes) {
            
                // If the updating event returns false, we will cancel the update operation so
                // developers can hook Validation systems into their models and cancel this
                // operation if the model does not pass validation. Otherwise, we update.
                if ($this->fireModelEvent('updating') === false) {
                    return false;
                }
                
                if (!$this->updateMainTable($query, $options, $dirty, $loadedAttributes)) {
                    return false;
                }
                
                if (!$this->updateAttributes($query, $options, $dirty, $loadedAttributes)) {
                    return false;
                }
                
                $this->fireModelEvent('updated', false);
                 
                return true;
            });
        }

        return true;
    }

    /**
     * Perform a model update operation on the main entity table.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  array  $options
     * @return bool
     */
    public function updateMainTable(Builder $query, array $options, $attributes, $loadedAttributes)
    {
        if ($this->fireModelEvent('updating.main') === false) {
            return false;
        }
         
        $mainTableAttribute = $this->getMainTableAttribute($loadedAttributes);
        
        $mainData = array_intersect_key($attributes, array_flip($mainTableAttribute));
        
        $numRows = $this->setKeysForSaveQuery($query)->update($mainData);

        // @see Illuminate\Database\Eloquent\Concerns\HasAttributes::syncChanges()
        $this->changes = $mainData;

        $this->fireModelEvent('updated.main', false);
        
        return true;
    }

    /**
     * Perform a model update operation on the attributes related to entity.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  array  $options
     * @return bool
     */
    public function updateAttributes(Builder $query, array $options, $modelData, $loadedAttributes)
    {
        $loadedAttributes->each(function ($attribute, $key) use ($modelData) {
            if (!$attribute->isStatic()) {
                $attribute->setEntity($this->baseEntity());
                $attribute->updateAttribute($modelData[$attribute->getAttributeCode()], $this->getKey());
            }
        });
        
        return true;
    }

    /**
     * Add a model event on saving, to save the entity meta data.
     * @return void
     */
    protected function addModelEvent()
    {
        $model = $this;

        static::saving(function () use ($model) {
            if (!$model->exists) {
                if (!$model->attribute_set_id) {
                    $model->setAttribute('attribute_set_id', $model->baseEntity()->default_attribute_set_id);
                }
            }
            $model->setAttribute('entity_id', $this->baseEntityId());
        }, 9999);
    }
}
