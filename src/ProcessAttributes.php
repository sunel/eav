<?php

namespace Eav;

class ProcessAttributes
{
    public static function process($query, $loadedAttributes, $baseEntity, $noJoin = false)
    {
        $filterAttr = array_flip($query->attributeColumns['columns']);

        $usedAttributes = $loadedAttributes
            ->filter(function ($attribute) use ($filterAttr) {
                return isset($filterAttr[$attribute->getAttributeCode()]);
            });

        foreach ((array) $query->attributeOrderBy['binding'] as $bindings) {
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
                        $binding['query']->processAttributes($loadedAttributes, true);
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
                        $attribute = $usedAttributes->get($binding['column']);
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
