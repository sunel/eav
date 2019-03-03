<?php

namespace Eav;

use Illuminate\Database\Eloquent\Model;

class EntityAttribute extends Model
{
    /**
     * @{inheriteDoc}
     */
    protected $primaryKey = 'attribute_id';
    
    /**
     * @{inheriteDoc}
     */
    public $timestamps = false;

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'sequence' => 0,
    ];
    
    /**
     * @{inheriteDoc}
     */
    protected $fillable = [
        'entity_id', 'attribute_set_id', 'attribute_group_id',
        'attribute_id', 'sequence'
    ];
    
    /**
     * Attach the attribute to the entity.
     *
     * @param  array $data
     * @return bool
     */
    public static function map(array $data)
    {
        $instance = new static;
                
        $entity = $instance->findEntity($data['entity_code']);
        
        $attribute = $instance->findAttribute($data['attribute_code'], $entity);
        
        $set = $instance->findOrCreateSet($data['attribute_set'], $entity);
        
        $group = $instance->findOrCreateGroup($data['attribute_group'], $set);
        
        $instance->fill([
            'entity_id' => $entity->entity_id,
            'attribute_set_id' => $set->attribute_set_id,
            'attribute_group_id' => $group->attribute_group_id,
            'attribute_id' => $attribute->attribute_id
        ])->save();
    }
    
    
    /**
     * Un attach the attribute to the entity.
     *
     * @param  array $data
     * @return bool
     */
    public static function unmap(array $data)
    {
        $instance = new static;
                
        $entity = $instance->findEntity($data['entity_code']);
        
        $attribute = $instance->findAttribute($data['attribute_code'], $entity);
        
        $instance->where([
            'entity_id' => $entity->entity_id,
            'attribute_id' => $attribute->attribute_id
        ])->delete();
    }
       
    private function findEntity($code)
    {
        try {
            return Entity::findByCode($code);
        } catch (ModelNotFoundException $e) {
            throw new \Exception("Unable to load Entity : ".$code);
        }
    }
        
    private function findAttribute($code, $entity)
    {
        try {
            return Attribute::where([
                'attribute_code'=> $code,
                'entity_id' => $entity->entity_id,
            ])->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new \Exception("Unable to load Attribute : ".$code);
        }
    }

    private function findOrCreateSet($code, $entity)
    {
        return AttributeSet::firstOrCreate([
            'attribute_set_name' => $code,
            'entity_id' => $entity->entity_id,
        ]);
    }
    
    private function findOrCreateGroup($code, $set)
    {
        return AttributeGroup::firstOrCreate([
            'attribute_set_id' => $set->attribute_set_id,
            'attribute_group_name' => $code,
        ]);
    }

     /**
     * Sync the tables with a list of IDs or collection of models.
     *
     * @param  \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|array  $ids
     * @param  bool   $detaching
     * @return array
     */
    public static function sync($entity, $set, $group, $ids, $detaching = true)
    {
        $changes = [
            'attached' => [], 'detached' => [], 'updated' => [],
        ];

        $instance = new static;

        // First we need to attach any of the associated models that are not currently
        // in this joining table. We'll spin through the given IDs, checking to see
        // if they exist in the array of current ones, and if not we will insert.
        $current = $instance->where([
            'entity_id' => $entity->entity_id,
            'attribute_set_id' => $set->attribute_set_id,
            'attribute_group_id' => $group->attribute_group_id,
        ])->pluck(
            'attribute_id'
        )->all();

        $detach = array_diff($current, array_keys(
            $records = $instance->formatRecordsList($ids)
        ));

        // Next, we will take the differences of the currents and given IDs and detach
        // all of the entities that exist in the "current" array but are not in the
        // array of the new IDs given to the method which will complete the sync.
        if ($detaching && count($detach) > 0) {
            $instance->detach($entity, $set, $group, $detach);

            $changes['detached'] = $detach;
        }

        // Now we are finally ready to attach the new records. Note that we'll disable
        // touching until after the entire operation is complete so we don't fire a
        // ton of touch operations until we are totally done syncing the records.
        $changes = array_merge(
            $changes, $instance->attachNew($entity, $set, $group, $records, $current)
        );

        return $changes;
    }

    /**
     * Detach models from the relationship.
     *
     * @param  mixed  $ids
     * @param  bool  $touch
     * @return int
     */
    public function detach($entity, $set, $group, $ids = null)
    {
        $query = $this->where([
            'entity_id' => $entity->entity_id,
            'attribute_set_id' => $set->attribute_set_id,
            'attribute_group_id' => $group->attribute_group_id,
        ]);

        // If associated IDs were passed to the method we will only delete those
        // associations, otherwise all of the association ties will be broken.
        // We'll return the numbers of affected rows when we do the deletes.
        if (! is_null($ids)) {           

            if (empty($ids)) {
                return 0;
            }

            $query->whereIn('attribute_id', (array) $ids);
        }

        // Once we have all of the conditions set on the statement, we are ready
        // to run the delete on the pivot table. Then, if the touch parameter
        // is true, we will go ahead and touch all related models to sync.
        $results = $query->delete();

        return $results;
    }

    /**
     * Attach all of the records that aren't in the given current records.
     *
     * @param  array  $records
     * @param  array  $current
     * @param  bool   $touch
     * @return array
     */
    protected function attachNew($entity, $set, $group, array $records, array $current)
    {
        $changes = ['attached' => [], 'updated' => []];

        foreach ($records as $id => $attributes) {
            // If the ID is not in the list of existing pivot IDs, we will insert a new pivot
            // record, otherwise, we will just update this existing record on this joining
            // table, so that the developers will easily update these records pain free.
            if (! in_array($id, $current)) {
                $this->attach($entity, $set, $group, $id, $attributes);

                $changes['attached'][] = $id;
            }

            // Now we'll try to update an existing pivot record with the attributes that were
            // given to the method. If the model is actually updated we will add it to the
            // list of updated pivot records so we return them back out to the consumer.
            elseif (count($attributes) > 0 &&
                $this->updateExistingPivot($entity, $set, $group, $id, $attributes)) {
                $changes['updated'][] = $id;
            }
        }

        return $changes;
    }

    /**
     * Attach a model to the parent.
     *
     * @param  mixed  $id
     * @param  array  $attributes
     * @param  bool   $touch
     * @return void
     */
    public function attach($entity, $set, $group, $id, array $attributes = [])
    {
        // Here we will insert the attachment records into the pivot table. Once we have
        // inserted the records, we will touch the relationships if necessary and the
        // function will return. We can parse the IDs before inserting the records.
        $this->insert(array_merge([
            'entity_id' => $entity->entity_id,
            'attribute_set_id' => $set->attribute_set_id,
            'attribute_group_id' => $group->attribute_group_id,
            'attribute_id' => $id
        ], $attributes));
    }

    /**
     * Update an existing pivot record on the table.
     *
     * @param  mixed  $id
     * @param  array  $attributes
     * @param  bool   $touch
     * @return int
     */
    public function updateExistingPivot($entity, $set, $group, $id, array $attributes)
    {
        $updated = $this->where([
            'entity_id' => $entity->entity_id,
            'attribute_set_id' => $set->attribute_set_id,
            'attribute_group_id' => $group->attribute_group_id,
            'attribute_id' => $id
        ])->update($attributes);

        return $updated;
    }

    /**
     * Format the sync / toggle record list so that it is keyed by ID.
     *
     * @param  array  $records
     * @return array
     */
    protected function formatRecordsList(array $records)
    {
        return collect($records)->mapWithKeys(function ($attributes, $id) {
            if (! is_array($attributes)) {
                list($id, $attributes) = [$attributes, []];
            }

            return [$id => $attributes];
        })->all();
    }
}
