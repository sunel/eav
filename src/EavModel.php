<?php 

namespace Eav;

use Eav\Traits\Attribute;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;
use Eav\Database\Query\Builder as EavQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;

abstract class EavModel extends Model
{
    use Attribute;
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY  = '';

    protected static $unguarded = true;

    protected static $baseEntity = [];
    
    
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

        $this->loadEntityProperty();

        $this->addModelEvent();
            
        $this->bootIfNotBooted();

        $this->syncOriginal();

        $this->fill($attributes);
    }

    protected function loadEntityProperty()
    {
        if (!isset(static::$baseEntity[static::ENTITY])) {
            try {
                $eavEntity = Entity::findByCode(static::ENTITY);
            } catch (ModelNotFoundException $e) {
                throw new \Exception("Unable to load Entity : ".static::ENTITY);
            }
            static::$baseEntity[static::ENTITY] = $eavEntity;
        }
    }
    
    public function baseEntity()
    {
        return static::$baseEntity[static::ENTITY];
    }

    protected function addModelEvent()
    {
        $model = $this;

        static::saving(function () use ($model) {
            if (!$model->exists) {
                if (!$model->attribute_set_id) {
                    $model->setAttribute('attribute_set_id', $model->baseEntity()->default_attribute_set_id);
                }
            }
            $model->setAttribute('entity_id', $model->baseEntity()->entity_id);
        }, 9999);

        return true;
    }
    
    public function validate()
    {
        if ($this->exists) {
        }
    }

    /**
     * Save the model to the database.
     *
     * @param  array  $options
     * @return bool
     */
    public function save(array $options = [])
    {
        // First we'll need to create a fresh query instance and touch the creation and
        // update timestamps on this model, which are maintained by us for developer
        // convenience. After, we will just continue saving these model instances.
        if ($this->timestamps && Arr::get($options, 'timestamps', true)) {
            $this->updateTimestamps();
        }

        return parent::save($options);
    }

    /**
     * Get a new query builder instance for the connection.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function newBaseQueryBuilder()
    {
        $conn = $this->getConnection();

        $grammar = $conn->getQueryGrammar();

        return new EavQueryBuilder($conn, $grammar, $conn->getPostProcessor(), $this->baseEntity());
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
        $dirty = $this->getDirty();

        if (count($dirty) > 0) {
            //$loadedAttributes = $this->loadAttributes(array_keys($dirty), true, true);
            $loadedAttributes = $this->loadAttributes([], true, true);
        
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

            // Once we have run the update operation, we will fire the "updated" event for
            // this model instance. This will allow developers to hook into these after
            // models are updated, giving them a chance to do any special processing.
            $dirty = $this->getDirty();

            if (count($dirty) > 0) {
                $this->fireModelEvent('updated', false);
            }
        }

        return true;
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
        // If the model has an incrementing key, we can use the "insertGetId" method on
        // the query builder, which will give us back the final inserted ID for this
        // table from the database. Not all tables have to be incrementing though.
        $attributes = $this->attributes;
        
        //$loadedAttributes = $this->loadAttributes(array_keys($attributes), true, true);
        $loadedAttributes = $this->loadAttributes([], true, true);
        
        $loadedAttributes->validate($attributes);
        
        return $this->getConnection()->transaction(function () use ($query, $options, $attributes, $loadedAttributes) {
            
            if ($this->fireModelEvent('creating') === false) {
                return false;
            }
            
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

    public function updateAttributes($query, $options, $modelData, $loadedAttributes)
    {
        $loadedAttributes->each(function ($attribute, $key) use ($modelData) {
            if (!$attribute->isStatic()) {
                $attribute->setEntity($this->baseEntity());
                
                $insertData = [
                    'value' => $modelData[$attribute->getAttributeCode()]
                ];
                
                $attribute->updateAttribute($insertData, $this->getKey(), 0);
            }
        });
        
        return true;
    }

    public function updateMainTable($query, $options, $attributes, $loadedAttributes)
    {
        if ($this->fireModelEvent('updating.main') === false) {
            return false;
        }
         
        $mainTableAttribute = $this->getMainTableAttribute($loadedAttributes);
        
        $mainData = array_intersect_key($attributes, array_flip($mainTableAttribute));
        
        $numRows = $this->setKeysForSaveQuery($query)->update($mainData);

        $this->fireModelEvent('updated.main', false);
        
        return true;
    }
    
    
    public function insertAttributes($query, $options, $modelData, $loadedAttributes)
    {
        $loadedAttributes->each(function ($attribute, $key) use ($modelData) {
            if (!$attribute->isStatic()) {
                $attribute->setEntity($this->baseEntity());
                
                $insertData = [
                    'entity_id' => $this->getKey(),
                    'value' => $modelData[$attribute->getAttributeCode()],
                    'store_id'=>0,
                ];
                
                $attribute->insertAttribute($insertData);
            }
        });
        
        return true;
        
        //return $this->newBaseQueryBuilder()->getConnection()->statement($bulkInsert);     
    }


    public function insertMainTable($query, $options, $attributes, $loadedAttributes)
    {
        if ($this->fireModelEvent('creating.main') === false) {
            return false;
        }
         
        $mainTableAttribute = $this->getMainTableAttribute($loadedAttributes);
        
        $mainData = array_intersect_key($attributes, array_flip($mainTableAttribute));
        
        $this->insertAndSetId($query, $mainData);
        
        // We will go ahead and set the exists property to true, so that it is set when
        // the created event is fired, just in case the developer tries to update it
        // during the event. This will allow them to do so and run an update here.
        $this->exists = true;

        $this->wasRecentlyCreated = true;

        $this->fireModelEvent('created.main', false);
        
        return true;
    }
    
    /**
     * Fire the given event for the model.
     *
     * @param  string  $event
     * @param  bool  $halt
     * @return mixed
     */
    protected function fireModelEvent($event, $halt = true)
    {
        if (! isset(static::$dispatcher)) {
            return true;
        }

        // We will append the names of the class to the event to distinguish it from
        // other model events that are fired, allowing us to listen on each model
        // event set individually instead of catching event for all the models.
        $event = "eloquent.eav.{$event}: ".static::ENTITY;

        $method = $halt ? 'until' : 'fire';

        return static::$dispatcher->$method($event, $this);
    }

    /**
     * Register a model event with the dispatcher.
     *
     * @param  string  $event
     * @param  \Closure|string  $callback
     * @param  int  $priority
     * @return void
     */
    protected static function registerModelEvent($event, $callback, $priority = 0)
    {
        if (isset(static::$dispatcher)) {
            static::$dispatcher->listen("eloquent.eav.{$event}: ".static::ENTITY, $callback, $priority);
        }
    }

     /**
     * Remove all of the event listeners for the model.
     *
     * @return void
     */
    public static function flushEventListeners()
    {
        if (! isset(static::$dispatcher)) {
            return;
        }

        $instance = new static;

        foreach ($instance->getObservableEvents() as $event) {
            static::$dispatcher->forget("eloquent.eav.{$event}: ".static::ENTITY);
        }
    }
}
