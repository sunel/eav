<?php
namespace Eav\Database\Eloquent;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class Builder extends EloquentBuilder
{
    public function getFacets($count = false)
    {
        $baseEntity = $this->getModel()->baseEntity();
        $filterable = $baseEntity
            ->attributes()
            ->with('optionValues')
            ->where('is_filterable', 1)
            ->get()
            ->patch();


        return $filterable->map(function ($filter, $code) use ($count) {
            $query = $this->query->newQuery()->from($this->query->from);


            foreach ($this->query->attributeWheresRef as $column => $values) {
                if ($filter->getAttributeCode() == $column) {
                    continue;
                }

                foreach ($values as $value) {
                    $query->addWhereAttribute($column, $value);
                }
            }

            $query->select($filter->getAttributeCode())
                ->groupBy($filter->getAttributeCode());

            if ($count) {
                $query->selectRaw('count(1) as count');
            }

            $options = $filter->options();

            return $query->get()->map(function ($option, $key) use ($filter, $options, $count) {
                $value = $option->{$filter->getAttributeCode()};
                if ($value === null) {
                    return null;
                }
                $data = [
                    'value' => $value,
                    'label' => isset($options[$value])?$options[$value]:$value
                ];

                if ($count) {
                    $data['count'] = $option->count;
                }

                return $data;
            })->filter()->mapWithKeys(function ($item) {
                return [$item['value'] => $item];
            });
        });
    }
}
