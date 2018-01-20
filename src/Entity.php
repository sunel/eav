<?php

namespace Eav;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
    protected $primaryKey = 'entity_id';
    
    protected static $baseEntity = [];
    protected static $entityIdCache = [];
    
    protected $fillable = [
        'entity_code', 'entity_class', 'entity_table',
        'default_attribute_set_id', 'additional_attribute_table',
        'is_flat_enabled'
    ];
    
    public $timestamps = false;
    
    public function canUseFlat()
    {
        return $this->getAttribute('is_flat_enabled');
    }
    
    public function getEntityTablePrefix()
    {
        $tableName = Str::singular($this->getAttribute('entity_table'));
        $tablePrefix = $this->getConnection()->getTablePrefix();
        if ($tablePrefix != '') {
            $tableName = "$tablePrefix.$tableName";
        }
        return $tableName;
    }
        
    public function attributeSet()
    {
        return $this->hasMany(AttributeSet::class, 'entity_id');
    }
        
    public function attributes()
    {
        return $this->hasMany(Attribute::class, 'entity_id');
    }
    
    public static function findByCode($code)
    {
        if (!isset(static::$entityIdCache[$code])) {
            $entity= static::where('entity_code', '=', $code)->firstOrFail();
                                            
            static::$entityIdCache[$entity->getAttribute('entity_code')] = $entity->getKey();
            
            static::$baseEntity[$entity->getKey()] = $entity;
        }
                    
        return static::$baseEntity[static::$entityIdCache[$code]];
    }
    
    public static function findById($id)
    {
        if (!isset(static::$baseEntity[$id])) {
            $entity = static::findOrFail($id);
            
            static::$entityIdCache[$entity->getAttribute('entity_code')] = $entity->getKey();
            
            static::$baseEntity[$id] = $entity;
        }
                    
        return static::$baseEntity[$id];
    }
    
    public function defaultAttributeSet()
    {
        return $this->hasOne(AttributeSet::class, 'attribute_set_id', 'default_attribute_set_id');
    }
    
    public function describe()
    {
        $table = $this->getAttribute('entity_table');
        
        $connection = \DB::connection();
        
        $database = $connection->getDatabaseName();

        $table = $connection->getTablePrefix().$table;
        
        $result = \DB::table('information_schema.columns')
                ->where('table_schema', $database)
                ->where('table_name', $table)
                ->get();
                
        return new Collection(json_decode(json_encode($result), true));
    }
}
