<?php

namespace Eav\Attribute\Concerns;

use DB;

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
            'entity_type_id' => $this->entity()->getKey(),
            'attribute_id' => $this->getKey(),
            'entity_id' => $entityId,
            'value' => $value
        ];
        
        return $this->newBaseQueryBuilder()
            ->from($this->backendTable())
            ->getInsertSql($insertData);
    }

    public function addSubQuery($query)
    {   
        $subQuery = DB::table($this->backendTable())->select("value")
            ->where('attribute_id', $this->attributeId())
            ->whereColumn("{$query->from}.{$this->entity()->entityKey()}", "entity_id");

        $query->selectSub($subQuery, $this->code());
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
            return $this->code();
        }

        return "{$this->code()}_attr.value as {$this->code()}";
    }

    /**
     * Retrive the select column without `alias` for the attribute.
     *
     * @return string
     */
    public function getRawSelectColumn()
    {
        if ($this->isStatic()) {
            return $this->code();
        }

        return "{$this->code()}_attr.value";
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
        if ($this->isStatic() || isset($query->joinCache[$this->code()])) {
            return $this;
        }

        $query->joinCache[$this->code()] = 1;
        
        if (is_callable($callback)) {
            $callback = function ($join) use ($query) {
                $callback($join, $query, "{$this->code()}_attr");
            };
            
            if ($joinType == 'left') {
                $query->leftJoin("{$this->backendTable()} as {$this->code()}_attr", $callback);
            } else {
                $query->join("{$this->backendTable()} as {$this->code()}_attr", $callback);
            }
        } else {
            if ($joinType == 'left') {
                $query->leftJoin("{$this->backendTable()} as {$this->code()}_attr", function ($join) use ($query) {
                    $join->on("{$query->from}.{$this->entity()->entityKey()}", '=', "{$this->code()}_attr.entity_id")
                        ->where("{$this->code()}_attr.attribute_id", "=", $this->attributeId());
                });
            } else {
                $query->join("{$this->backendTable()} as {$this->code()}_attr", function ($join) use ($query) {
                    $join->on("{$query->from}.{$this->entity()->entityKey()}", '=', "{$this->code()}_attr.entity_id")
                        ->where("{$this->code()}_attr.attribute_id", "=", $this->attributeId());
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
            $query->orderBy("{$this->code()}_attr.value", $binding['direction']);
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
            $query->where("{$this->code()}_attr.value", $binding['operator'], $binding['value'], $binding['boolean']);
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
            $query->whereBetween("{$this->code()}_attr.value", $binding['values'], $binding['boolean'], $binding['not']);
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
            $query->whereIn("{$this->code()}_attr.value", $binding['values'], $binding['boolean'], $binding['not']);
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
            $query->whereNotIn("{$this->code()}_attr.value", $binding['values'], $binding['boolean'], $binding['not']);
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
            $query->whereNull("{$this->code()}_attr.value", $binding['boolean'], $binding['not']);
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
            $query->whereNotNull("{$this->code()}_attr.value", $binding['boolean'], $binding['not']);
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
            $query->whereDate("{$this->code()}_attr.value", $binding['operator'], $binding['value'], $binding['boolean']);
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
            $query->whereDay("{$this->code()}_attr.value", $binding['operator'], $binding['value'], $binding['boolean']);
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
            $query->whereMonth("{$this->code()}_attr.value", $binding['operator'], $binding['value'], $binding['boolean']);
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
            $query->whereYear("{$this->code()}_attr.value", $binding['operator'], $binding['value'], $binding['boolean']);
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
            $query->whereTime("{$this->code()}_attr.value", $binding['operator'], $binding['value'], $binding['boolean']);
        }
    }
}
