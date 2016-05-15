<?php
namespace Eav;

use Illuminate\Support\Arr;

class ProcessAttributes
{
    public static function process($query, $loadedAttributes, $baseEntity, $noJoin = false)
    {
        $filterAttr = $query->attributeColumns['columns'];

        foreach ((array) $query->attributeOrderBy['binding'] as $type => $bindings) {
            $usedAttributes = $loadedAttributes
                ->filter(function ($attribute) use ($filterAttr) {
                    return in_array($attribute->getAttributeCode(), $filterAttr);
                });

            array_map(function ($binding) use ($usedAttributes, $baseEntity, $query) {
               $attribute = Arr::first($usedAttributes->all(),
                function ($itemKey, $model) use ($binding) {
                    return $model->getAttributeCode() == $binding['column'];
                }, null);
                if ($attribute) {
                    $attribute->setEntity($baseEntity);
                    $attribute->addAttributeJoin($query);
                    $attribute->addAttributeOrderBy($query, $binding);
                }
            }, $bindings);
        }

        foreach ((array) $query->attributeWheres['binding'] as $type => $bindings) {
            switch ($type) {
                case 'Nested':
                    array_map(function ($binding) use ($loadedAttributes, $query) {

                        $binding['query']->processAttributes($loadedAttributes, true);
                        $query->addNestedWhereQuery($binding['query'], $binding['boolean']);
                    }, $bindings);

                    $loadedAttributes->each(function ($attribute, $key) use ($baseEntity, $query) {
                        $attribute->setEntity($baseEntity);
                        $attribute->addAttributeJoin($query);
                    });

                    break;
                case 'Basic':
                default:
                    $usedAttributes = $loadedAttributes
                        ->filter(function ($attribute) use ($filterAttr) {
                            return in_array($attribute->getAttributeCode(), $filterAttr);
                        });

                    array_map(function ($binding) use ($usedAttributes, $baseEntity, $query, $noJoin) {
                       $attribute = Arr::first($usedAttributes->all(),
                        function ($itemKey, $model) use ($binding) {
                            return $model->getAttributeCode() == $binding['column'];
                        }, null);
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
                    }, $bindings);

                    break;
            }
        }
    }
}
