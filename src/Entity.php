<?php

namespace Eav;

use DB;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
    /**
     * @{inheriteDoc}
     */
    protected $primaryKey = 'entity_id';

    /**
     * @{inheriteDoc}
     */
    public $timestamps = false;

    /**
     * @{inheriteDoc}
     */
    protected $fillable = [
        'entity_code', 'entity_class', 'entity_table',
        'default_attribute_set_id', 'additional_attribute_table',
        'is_flat_enabled'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_flat_enabled' => 'boolean',
    ];

    /**
     * Holds the primary key name of Orginal Entity instance.
     *
     * @var string
     */
    protected $entityKey;

    /**
     * Holds the custom table name of Orginal Entity instance.
     *
     * @var string
     */
    protected $entityCustomTable;
    
    /**
     * Holds instance of all loaded Entity.
     *
     * @var array
     */
    protected static $baseEntity = [];

    /**
     * Holds id of all loaded Entity for ref.
     *
     * @var array
     */
    protected static $entityIdCache = [];


    /**
     * Use this method only if nessary.
     *
     * @return void
     */
    public static function clearStaticCache()
    {
        static::$baseEntity = [];
        static::$entityIdCache = [];
    }

    /**
     * @alias getCode()
     */
    public function code()
    {
        return $this->getCode();
    }

    /**
     * Get the entity code.
     *
     * @return string
     */
    public function getCode()
    {
        return $this->getAttribute('entity_code');
    }

    /**
     * @alias getEntityKey()
     */
    public function entityKey()
    {
        return $this->getEntityKey();
    }

    /**
     * Get the primary key for the Entity.
     *
     * @return string
     */
    public function getEntityKey()
    {
        return $this->entityKey;
    }

    /**
     * Set the primary key for the Entity.
     *
     * @param  string  $key
     * @return $this
     */
    public function setEntityKey(string $key)
    {
        $this->entityKey = $key;
        return $this;
    }

    /**
     * Set the table name for the Entity.
     *
     * @param  string|null  $key
     * @return $this
     */
    public function setEntityCustomTable($key)
    {
        $this->entityCustomTable = $key;
        return $this;
    }
    
    /**
     * Check if the Entity can use flat table.
     *
     * @return bool
     */
    public function canUseFlat()
    {
        return $this->getAttribute('is_flat_enabled');
    }

    /**
     * @alias getEntityTableName()
     */
    public function entityTableName()
    {
        return $this->getEntityTableName();
    }
    
    /**
     * Get the table with prefix if it has one.
     *
     * @return string
     */
    public function getEntityTableName()
    {
        if(!is_null($this->entityCustomTable)) {
            return $this->entityCustomTable;
        }

        return str_replace(
            '\\', '', Str::snake(Str::plural(class_basename($this->entity_class)))
        );
    }

    /**
     * @alias attributeSet()
     */
    public function sets()
    {
        return $this->attributeSet();
    }
    
    /**
     * Define a one-to-many relationship for attribute set.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attributeSet()
    {
        return $this->hasMany(AttributeSet::class, 'entity_id');
    }
    
    /**
     * Define a one-to-many relationship for attribute.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attributes()
    {
        return $this->hasMany(Attribute::class, 'entity_id');
    }
    
    /**
     * Define a one-to-many relationship for attribute.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function unassignedAttributes($set = null)
    {
        return $this->hasMany(Attribute::class, 'entity_id')
            ->whereNotExists(function ($query) use ($set) {
                $query->select(DB::raw(1))
                      ->from('entity_attributes')
                      ->whereRaw('attributes.attribute_id = entity_attributes.attribute_id')
                      ->whereRaw('attributes.entity_id = entity_attributes.entity_id');

                    if(!is_null($set)) {
                        $query->whereRaw("entity_attributes.attribute_set_id = {$set}");
                    }
            });
    }

    /**
     * Find the entity by code.
     *
     * @param  string $code
     * @return \Eav\Entity
     */
    public static function findByCode(string $code)
    {
        if (!isset(static::$entityIdCache[$code])) {
            $entity= static::where('entity_code', '=', $code)->firstOrFail();
                                            
            static::$entityIdCache[$entity->getAttribute('entity_code')] = $entity->getKey();
            
            static::$baseEntity[$entity->getKey()] = $entity;
        }
                    
        return static::$baseEntity[static::$entityIdCache[$code]];
    }
    
    /**
     * Find the entity by id.
     *
     * @param  int $id
     * @return \Eav\Entity
     */
    public static function findById(int $id)
    {
        if (!isset(static::$baseEntity[$id])) {
            $entity = static::findOrFail($id);
            
            static::$entityIdCache[$entity->getAttribute('entity_code')] = $entity->getKey();
            
            static::$baseEntity[$id] = $entity;
        }
                    
        return static::$baseEntity[$id];
    }
    
    /**
     * Get the default attribute set using one-to-one relationship for attribute.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function defaultAttributeSet()
    {
        return $this->hasOne(AttributeSet::class, 'attribute_set_id', 'default_attribute_set_id');
    }
}
