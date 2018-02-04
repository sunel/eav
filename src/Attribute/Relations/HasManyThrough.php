<?php

namespace Eav\Attribute\Relations;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasManyThrough as Relation;

class HasManyThrough extends Relation
{
    /**
     * Get all of the primary keys for an array of models.
     *
     * @param  array   $models
     * @param  string  $key
     * @return array
     */
    protected function getKeys(array $models, $key = null)
    {
        return collect($models)->filter(function ($value) {
            return $this->canRelate($value);
        })->map(function ($value) use ($key) {
            return $key ? $value->getAttribute($key) : $value->getKey();
        })->values()->unique()->sort()->all();
    }

    /**
     * Initialize the relation on a set of models.
     *
     * @param  array   $models
     * @param  string  $relation
     * @return array
     */
    public function initRelation(array $models, $relation)
    {
        foreach ($models as $model) {
            if ($this->canRelate($model)) {
                $model->setRelation($relation, $this->related->newCollection());
            }
        }

        return $models;
    }

    /**
     * Match the eagerly loaded results to their parents.
     *
     * @param  array   $models
     * @param  \Illuminate\Database\Eloquent\Collection  $results
     * @param  string  $relation
     * @return array
     */
    public function match(array $models, Collection $results, $relation)
    {
        $dictionary = $this->buildDictionary($results);

        // Once we have the dictionary we can simply spin through the parent models to
        // link them up with their children using the keyed dictionary to make the
        // matching very convenient and easy work. Then we'll just return them.
        foreach ($models as $model) {
            if ($this->canRelate($model)) {
                if (isset($dictionary[$key = $model->getAttribute($this->localKey)])) {
                    $model->setRelation(
                        $relation,
                        $this->related->newCollection($dictionary[$key])
                    );
                }
            }
        }

        return $models;
    }
    
    private function canRelate($model)
    {
        return (($model->getFrontendInput() == 'select' || $model->getAttribute('frontend_type') === 'multiselect')
            && empty($model->getAttribute('source_class')));
    }
}
