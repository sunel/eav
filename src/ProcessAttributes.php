<?php

namespace Eav;

use Eav\Database\Query\Builder;
use Illuminate\Support\Collection;

class ProcessAttributes
{
    /**
     * Adds the requested attribute to the query.
     *
     * Until now we have not added the attribute to the query.
     * Here we have the list of query and bindings, now we will
     * inner or left join the attribute and add the querying
     * conditions.
     *
     * @param  Builder    $query
     * @param  Collection $loadedAttributes
     * @param  Entity     $baseEntity
     * @param  boolean    $noJoin
     * @return void
     */
    public static function process(
        Builder $query,
        Collection $loadedAttributes,
        Entity $baseEntity,
        $noJoin = false
    ) {
        $filterAttr = array_flip($query->attributeColumns);
        $orginalColumns = array_flip($query->orginalColumns);

        $usedAttributes = $loadedAttributes
            ->filter(function ($attribute) use ($filterAttr) {
                return isset($filterAttr[$attribute->code()]);
            });
            
        foreach ((array) $query->attributeOrderBy as $bindings) {
            foreach ($bindings as $binding) {
                $attribute = $usedAttributes->get($binding['column']);
                if ($attribute) {
                    $joinType = isset($orginalColumns[$attribute->code()])?'left':'inner';
                    $attribute->setEntity($baseEntity);
                    $attribute->addAttributeJoin($query, $joinType);
                    $attribute->addAttributeOrderBy($query, $binding);
                }
            }
        }

        if (!count($query->attributeWheresRef)) {
            return;
        }

        foreach ((array) $query->attributeWheres['binding'] as $type => $bindings) {
            switch ($type) {
                case 'Nested':
                    foreach ($bindings as $binding) {
                        $binding['query']->processAttributes(true);
                        $query->addNestedWhereQuery($binding['query'], $binding['boolean']);
                    }

                    $loadedAttributes->each(function ($attribute, $key) use ($binding, $baseEntity, $query, $orginalColumns) {
                        $joinType = isset($orginalColumns[$attribute->code()]) || ($binding['boolean'] == 'or')?'left':'inner';
                        $attribute->setEntity($baseEntity);
                        $attribute->addAttributeJoin($query, $joinType);
                    });

                    break;
                case 'Basic':
                default:
                    foreach ($bindings as $binding) {
                        $attribute = $usedAttributes->get($binding['column'], false);
                        if ($attribute) {
                            $attribute->setEntity($baseEntity);
                            if (!$noJoin) {
                                $joinType = isset($orginalColumns[$attribute->code()]) || ($binding['boolean'] == 'or')?'left':'inner';
                                $attribute->addAttributeJoin($query, $joinType);
                            }
                            $attribute->addAttributeWhere(
                                $query,
                                $binding
                            );
                        }
                    }

                    break;
            }
        }
    }
}
