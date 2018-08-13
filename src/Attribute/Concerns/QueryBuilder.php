<?php

namespace Eav\Attribute\Concerns;

trait QueryBuilder
{
    /**
     * Retrives the insert query.
     *
     * @param  mixed $value
     * @param  int $entityId
     * @return string
     */
    public function getAttributeInsertQuery($value, $entityId)
    {
        $insertData = [
            'entity_type_id' => $this->getEntity()->getKey(),
            'attribute_id' => $this->getKey(),
            'entity_id' => $entityId,
            'value' => $value
        ];
        
        return $this->newBaseQueryBuilder()
            ->from($this->getBackendTable())
            ->getInsertSql($insertData);
    }
    
    /**
     * Add a new select column to the query.
     *
     * @param Illuminate\Database\Query\Builder $query
     * @param string $joinType
     * @param \Closure $callback
     */
    public function addToSelect($query, $joinType = 'inner', $callback = null)
    {
        if ($this->isStatic()) {
            return $this;
        }

        $this->addAttributeJoin($query, $joinType, $callback);

        $query->addSelect([$this->getSelectColumn()]);
    }
    
    /**
     * Retrive the select column for the attribute.
     *
     * @return string
     */
    public function getSelectColumn()
    {
        if ($this->isStatic()) {
            return $this->getAttributeCode();
        }

        return "{$this->getAttributeCode()}_attr.value as {$this->getAttributeCode()}";
    }

    /**
     * Retrive the select column without `alias` for the attribute.
     *
     * @return string
     */
    public function getRawSelectColumn()
    {
        if ($this->isStatic()) {
            return $this->getAttributeCode();
        }

        return "{$this->getAttributeCode()}_attr.value";
    }
    
    
    /**
     * Add a join clause to the query for the attribute.
     *
     * @param Illuminate\Database\Query\Builder $query
     * @param string $joinType
     * @param \Closure $callback
     */
    public function addAttributeJoin($query, $joinType = 'inner', $callback = null)
    {
        if ($this->isStatic() || isset($query->joinCache[$this->getAttributeCode()])) {
            return $this;
        }

        $query->joinCache[$this->getAttributeCode()] = 1;
        
        if (is_callable($callback)) {
            $callback = function ($join) use ($query) {
                $callback($join, $query, "{$this->getAttributeCode()}_attr");
            };
            
            if ($joinType == 'left') {
                $query->leftJoin("{$this->getBackendTable()} as {$this->getAttributeCode()}_attr", $callback);
            } else {
                $query->join("{$this->getBackendTable()} as {$this->getAttributeCode()}_attr", $callback);
            }
        } else {
            if ($joinType == 'left') {
                $query->leftJoin("{$this->getBackendTable()} as {$this->getAttributeCode()}_attr", function ($join) use ($query) {
                    $join->on("{$query->from}.{$this->getEntity()->getEnityKey()}", '=', "{$this->getAttributeCode()}_attr.entity_id")
                        ->where("{$this->getAttributeCode()}_attr.attribute_id", "=", $this->getAttributeId());
                });
            } else {
                $query->join("{$this->getBackendTable()} as {$this->getAttributeCode()}_attr", function ($join) use ($query) {
                    $join->on("{$query->from}.{$this->getEntity()->getEnityKey()}", '=', "{$this->getAttributeCode()}_attr.entity_id")
                        ->where("{$this->getAttributeCode()}_attr.attribute_id", "=", $this->getAttributeId());
                });
            }
        }
        
        return $this;
    }

    /**
     * Add an "order by" clause to the query.
     *
     * @param Illuminate\Database\Query\Builder $query
     * @param array  $binding
     * @return $this
     */
    public function addAttributeOrderBy($query, $binding)
    {
        if ($this->isStatic()) {
            $query->orderBy("{$query->from}.{$binding['column']}", $binding['direction']);
        } else {
            $query->orderBy("{$this->getAttributeCode()}_attr.value", $binding['direction']);
        }

        return $this;
    }
    

    /**
     * Add a basic where based on the type.
     *
     * @param Illuminate\Database\Query\Builder $query
     * @param array  $binding
     * @return $this
     */
    public function addAttributeWhere($query, $binding)
    {
        $method = 'where'.lcfirst($binding['type']);
        $this->$method($query, $binding);

        return $this;
    }

    /**
     * Add a basic where clause to the query.
     *
     * @param Illuminate\Database\Query\Builder $query
     * @param array  $binding
     * @return void
     */
    protected function whereBasic($query, $binding)
    {
        if ($this->isStatic()) {
            $query->where("{$query->from}.{$binding['column']}", $binding['operator'], $binding['value'], $binding['boolean']);
        } else {
            $query->where("{$this->getAttributeCode()}_attr.value", $binding['operator'], $binding['value'], $binding['boolean']);
        }
    }

    /**
     * Add a where between statement to the query.
     *
     * @param Illuminate\Database\Query\Builder $query
     * @param array  $binding
     * @return void
     */
    protected function whereBetween($query, $binding)
    {
        if ($this->isStatic()) {
            $query->whereBetween("{$query->from}.{$binding['column']}", $binding['values'], $binding['boolean'], $binding['not']);
        } else {
            $query->whereBetween("{$this->getAttributeCode()}_attr.value", $binding['values'], $binding['boolean'], $binding['not']);
        }
    }

    /**
     * Add a where in statement to the query.
     *
     * @param Illuminate\Database\Query\Builder $query
     * @param array  $binding
     * @return void
     */
    protected function whereIn($query, $binding)
    {
        if ($this->isStatic()) {
            $query->whereIn("{$query->from}.{$binding['column']}", $binding['values'], $binding['boolean'], $binding['not']);
        } else {
            $query->whereIn("{$this->getAttributeCode()}_attr.value", $binding['values'], $binding['boolean'], $binding['not']);
        }
    }

    /**
     * Add a where not in statement to the query.
     *
     * @param Illuminate\Database\Query\Builder $query
     * @param array  $binding
     * @return void
     */
    protected function whereNotIn($query, $binding)
    {
        if ($this->isStatic()) {
            $query->whereNotIn("{$query->from}.{$binding['column']}", $binding['values'], $binding['boolean'], $binding['not']);
        } else {
            $query->whereNotIn("{$this->getAttributeCode()}_attr.value", $binding['values'], $binding['boolean'], $binding['not']);
        }
    }

    /**
     * Add a where null statement to the query.
     *
     * @param Illuminate\Database\Query\Builder $query
     * @param array  $binding
     * @return void
     */
    protected function whereNull($query, $binding)
    {
        if ($this->isStatic()) {
            $query->whereNull("{$query->from}.{$binding['column']}", $binding['boolean'], $binding['not']);
        } else {
            $query->whereNull("{$this->getAttributeCode()}_attr.value", $binding['boolean'], $binding['not']);
        }
    }

    /**
     * Add a where not null statement to the query.
     *
     * @param Illuminate\Database\Query\Builder $query
     * @param array  $binding
     * @return void
     */
    protected function whereNotNull($query, $binding)
    {
        if ($this->isStatic()) {
            $query->whereNotNull("{$query->from}.{$binding['column']}", $binding['boolean'], $binding['not']);
        } else {
            $query->whereNotNull("{$this->getAttributeCode()}_attr.value", $binding['boolean'], $binding['not']);
        }
    }

    /**
     * Add a where date statement to the query.
     *
     * @param Illuminate\Database\Query\Builder $query
     * @param array  $binding
     * @return void
     */
    protected function whereDate($query, $binding)
    {
        if ($this->isStatic()) {
            $query->whereDate("{$query->from}.{$binding['column']}", $binding['operator'], $binding['value'], $binding['boolean']);
        } else {
            $query->whereDate("{$this->getAttributeCode()}_attr.value", $binding['operator'], $binding['value'], $binding['boolean']);
        }
    }

    /**
     * Add a where day statement to the query.
     *
     * @param Illuminate\Database\Query\Builder $query
     * @param array  $binding
     * @return void
     */
    protected function whereDay($query, $binding)
    {
        if ($this->isStatic()) {
            $query->whereDay("{$query->from}.{$binding['column']}", $binding['operator'], $binding['value'], $binding['boolean']);
        } else {
            $query->whereDay("{$this->getAttributeCode()}_attr.value", $binding['operator'], $binding['value'], $binding['boolean']);
        }
    }

    /**
     * Add a where month statement to the query.
     *
     * @param Illuminate\Database\Query\Builder $query
     * @param array  $binding
     * @return void
     */
    protected function whereMonth($query, $binding)
    {
        if ($this->isStatic()) {
            $query->whereMonth("{$query->from}.{$binding['column']}", $binding['operator'], $binding['value'], $binding['boolean']);
        } else {
            $query->whereMonth("{$this->getAttributeCode()}_attr.value", $binding['operator'], $binding['value'], $binding['boolean']);
        }
    }

    /**
     * Add a where year statement to the query.
     *
     * @param Illuminate\Database\Query\Builder $query
     * @param array  $binding
     * @return void
     */
    protected function whereYear($query, $binding)
    {
        if ($this->isStatic()) {
            $query->whereYear("{$query->from}.{$binding['column']}", $binding['operator'], $binding['value'], $binding['boolean']);
        } else {
            $query->whereYear("{$this->getAttributeCode()}_attr.value", $binding['operator'], $binding['value'], $binding['boolean']);
        }
    }

    /**
     * Add a where time statement to the query.
     *
     * @param Illuminate\Database\Query\Builder $query
     * @param array  $binding
     * @return void
     */
    protected function whereTime($query, $binding)
    {
        if ($this->isStatic()) {
            $query->whereTime("{$query->from}.{$binding['column']}", $binding['operator'], $binding['value'], $binding['boolean']);
        } else {
            $query->whereTime("{$this->getAttributeCode()}_attr.value", $binding['operator'], $binding['value'], $binding['boolean']);
        }
    }
}
