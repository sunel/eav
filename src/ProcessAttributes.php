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
     * He have the list of query and bindinds, now we will
     * inner join the attribute and add the querying
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

        $usedAttributes = $loadedAttributes
            ->filter(function ($attribute) use ($filterAttr) {
                return isset($filterAttr[$attribute->getAttributeCode()]);
            });  
            
        foreach ((array) $query->attributeOrderBy as $bindings) {
            foreach ($bindings as $binding) {
                $attribute = $usedAttributes->get($binding['column']);
                if ($attribute) {
                    $attribute->setEntity($baseEntity);
                    $attribute->addAttributeJoin($query);
                    $attribute->addAttributeOrderBy($query, $binding);
                }
            }
        }

        foreach ((array) $query->attributeWheres['binding'] as $type => $bindings) {
            switch ($type) {
                case 'Nested':
                    foreach ($bindings as $binding) {
                        $binding['query']->processAttributes(true);
                        $query->addNestedWhereQuery($binding['query'], $binding['boolean']);
                    }

                    $loadedAttributes->each(function ($attribute, $key) use ($baseEntity, $query) {
                        $attribute->setEntity($baseEntity);
                        $attribute->addAttributeJoin($query);
                    });

                    break;
                case 'Basic':
                default:
                    foreach ($bindings as $binding) {
                        $attribute = $usedAttributes->get($binding['column'], false);
                        if ($attribute) {
                            $attribute->setEntity($baseEntity);
                            if (!$noJoin) {
                                $attribute->addAttributeJoin($query);
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
