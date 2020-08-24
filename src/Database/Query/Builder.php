<?php 
namespace Eav\Database\Query;

use Closure;
use Eav\Entity;
use Eav\ProcessAttributes;
use Eav\Traits\Attribute;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Database\Query\Processors\Processor;
use Illuminate\Database\Query\Builder as QueryBuilder;

class Builder extends QueryBuilder
{
    use Attribute;
    
    /**
     * Flag to indicate the current attribute for the query
     * is processed.
     *
     * @var boolean
     */
    protected $isProcessed = false;
    
    /**
     * Flag to indicate the query has conditions based on
     * attributes.
     *
     * @var boolean
     */
    public $hasAttributeConditions = false;
    
    /**
     * The where constraints for the query.
     *
     * @var array
     */
    public $attributeWheres = [];

    /**
     * Holds the column added to where constraints for the query.
     *
     * @var array
     */
    public $attributeWheresRef = [];

    /**
     * The order by attribute.
     *
     * @var array
     */
    public $attributeOrderBy = [];

    /**
     * The holds the attribute column.
     *
     * @var array
     */
    public $attributeColumns = [];

    /**
     * The holds the original select column.
     *
     * @var array
     */
    public $orginalColumns = [];
    
    /**
     * Holds Entity Instance.
     *
     * @var \Eav\Entity
     */
    protected $baseEntity;

    /**
     * Holds the ref to the attibute that is added to join clause.
     *
     * @var array
     */
    public $joinCache = [];

    
    /**
     * Create a new query builder instance.
     *
     * @param  \Illuminate\Database\ConnectionInterface  $connection
     * @param  \Illuminate\Database\Query\Grammars\Grammar  $grammar
     * @param  \Illuminate\Database\Query\Processors\Processor  $processor
     * @return void
     */
    public function __construct(
        ConnectionInterface $connection,
        Grammar $grammar = null,
        Processor $processor = null,
        Entity $baseEntity = null
    ) {
        $this->baseEntity = $baseEntity;
        parent::__construct($connection, $grammar, $processor);
    }
    
    /**
     * Get the Entity related to the model.
     *
     * @return \Eav\Entity
     * @throws \Exception.
     */
    public function baseEntity()
    {
        return $this->baseEntity;
    }
    
    /**
     * Check if the Entity can use flat table.
     *
     * @return bool
     */
    public function canUseFlat()
    {
        return ($this->baseEntity() && $this->baseEntity()->canUseFlat());
    }

    /**
     * Update a record in the database.
     *
     * @param  array  $values
     * @return int
     */
    public function update(array $values)
    {
        if ($this->canUseFlat()) {
            return parent::update($values);
        }

        $loadedAttributes = $this->loadAttributes(array_keys($values));
        
        $loadedAttributes->validate($values);

        $this->select($this->baseEntity()->getEntityKey());

        $loadedAttributes->each(function ($attribute, $code) {
            $attribute->setEntity($this->baseEntity())
                ->addAttributeJoin($this, 'left');
        });

        $entityIds = $this->pluck($this->baseEntity()->getEntityKey())->toArray();

        $loadedAttributes->each(function ($attribute, $code) use (&$values, $entityIds) {
            if (!$attribute->isStatic()) {
                $attribute->massUpdate($values[$attribute->getAttributeCode()], $entityIds);
                unset($values[$attribute->getAttributeCode()]);
            }
        });
        
        $query = $this->newQuery()->from($this->from)
            ->whereIn($this->baseEntity()->getEntityKey(), $entityIds);

        $sql = $this->grammar->compileUpdate($query, $values);

        return $query->connection->update($sql, $query->cleanBindings(
            $query->grammar->prepareBindingsForUpdate($query->bindings, $values)
        ));
    }
    
    /**
     * Insert a new record into the database.
     *
     * @param  array  $values
     * @return bool
     */
    public function getInsertSql(array $values)
    {
        if (empty($values)) {
            return true;
        }

        // Since every insert gets treated like a batch insert, we will make sure the
        // bindings are structured in a way that is convenient for building these
        // inserts statements by verifying the elements are actually an array.
        if (! is_array(reset($values))) {
            $values = [$values];
        }

        // Since every insert gets treated like a batch insert, we will make sure the
        // bindings are structured in a way that is convenient for building these
        // inserts statements by verifying the elements are actually an array.
        else {
            foreach ($values as $key => $value) {
                ksort($value);
                $values[$key] = $value;
            }
        }

        // We'll treat every insert like a batch insert so we can easily insert each
        // of the records into the database consistently. This will make it much
        // easier on the grammars to just handle one type of record insertion.
        $bindings = [];

        foreach ($values as $record) {
            foreach ($record as $value) {
                $bindings[] = $value;
            }
        }

        $sql = $this->grammar->compileInsert($this, $values);

        // Once we have compiled the insert statement's SQL we can execute it on the
        // connection and return a result as a boolean success indicator as that
        // is the same type of result returned by the raw connection instance.
        $bindings = $this->cleanBindings($bindings);
        
        
        foreach ($bindings as $binding) {
            $value = is_numeric($binding) ? $binding : "'".$binding."'";
            $sql = preg_replace('/\?/', $value, $sql, 1);
        }
        
        return $sql;
    }

    /**
     * Adds the requested attribute to the query.
     *
     * @param  Collection $loadedAttributes
     * @param  boolean    $noJoin
     * @return void
     */
    public function processAttributes($noJoin = false)
    {
        if (!$this->hasAttributeConditions || $this->isProcessed) {
            return;
        }
        
        $loadedAttributes = $this->loadAttributes($this->attributeColumns);
        ProcessAttributes::process($this, $loadedAttributes, $this->baseEntity(), $noJoin);
        
        $this->isProcessed = true;
    }

    /**
     * Reads the column that is added to the select clause and process it
     * based on the given sudo code eg : attr.*, *
     *
     * @return $this
     */
    protected function fixColumns()
    {
        if (!$this->baseEntity()) {
            return;
        }

        $loadedAttributes = null;
        $columns = $this->columns;

        if (is_null($columns)) {
            return $this;
        }

        $orgColumns = collect((array) $columns)->mapToGroups(function ($item, $key) {
            if (is_a($item, Expression::class)) {
                return ['expression' => $item];
            } else {
                return ['columns' => $item];
            }
        });

        if (is_null($orgColumns->get('columns'))) {
            $this->columns = $orgColumns->get('expression')->all();
            return $this;
        }

        $this->orginalColumns = $orgColumns->get('columns')->all();

        $columns = [];
        $removeCol = [
            'attr.*', '*', $this->baseEntity()->getEntityKey()
        ];

        $allAttr = $orgColumns->get('columns')->contains('attr.*');
        $allMain = $orgColumns->get('columns')->contains('*');
        $hasId =  $orgColumns->get('columns')->contains($this->baseEntity()->getEntityKey());

        // ->select(['*'])
        if ($allMain) {
            $columns[] = "{$this->from}.*";
        }
        // ->select(['attr.*']) or ->select(['id']) or ->select(['id', 'color'])
        elseif ($allAttr || $orgColumns->get('columns')->contains($this->baseEntity()->getEntityKey())) {
            $columns[] =  "{$this->from}.{$this->baseEntity()->getEntityKey()}";
        }       

        // We check if the select has only `*` or 'id', if so then we have nothing
        // to do.
        if (($allMain || $hasId) && $orgColumns->get('columns')->count() == 1) {
            $this->columns = $columns;
            return $this;
        }

        if ($allAttr) {
            $fetchAttr = [];
        } else {
            $fetchAttr = $orgColumns->get('columns')->filter(function ($value, $key) use ($removeCol) {
                return !(in_array($value, $removeCol));
            })->all();
        }

        $loadedAttributes = $this->loadAttributes($fetchAttr)
            ->each(function ($attribute, $key) use (&$columns, &$removeCol) {
                if ($attribute->isStatic()) {
                    $columns[] = "{$this->from}.{$attribute->getSelectColumn()}";
                } else {
                    $attribute->setEntity($this->baseEntity());
                    $attribute->addSubQuery($this);
                    //$columns[] = $attribute->getSelectColumn();
                }
                $removeCol[] = $attribute->getAttributeCode();
            });

        $newColumns = collect((array) $this->columns)->mapToGroups(function ($item, $key) {
            if (is_a($item, Expression::class)) {
                return ['expression' => $item];
            } else {
                return ['columns' => $item];
            }
        });

        $newExpression = $newColumns->get('expression');        

        // We might have join select clause in the original column
        $columns = $orgColumns
            ->get('columns')
            ->merge($columns)
            ->filter(function ($value, $key) use ($removeCol) {
                return !(in_array($value, $removeCol));
            })->unique()->toArray();

        if ($newExpression && $newExpression->isNotEmpty()) {
            $columns = $newExpression->merge($columns)->all();
        }

        $expression = $orgColumns->get('expression');
        // Merge the expression back to the query
        if ($expression && $expression->isNotEmpty()) {            
            $columns = $expression->merge($columns)->all();
        }

        $this->columns = $columns;
        return $this;
    }

    /**
     *
     * Reads the column that is added to the select clause and process it
     * based on the given sudo code eg : attr.*, * for flat table.
     *
     * @return void
     */
    protected function fixFlatColumns()
    {
        $columns = $this->columns;

        $orgColumns = collect((array) $columns)->mapToGroups(function ($item, $key) {
            if (is_a($item, Expression::class)) {
                return ['expression' => $item];
            } else {
                return ['columns' => $item];
            }
        });

        $columns = [];

        $removeCol = [
            'attr.*', '*', $this->baseEntity()->getEntityKey()
        ];

        $allAttr = $orgColumns->get('columns')->contains('attr.*');
        $allMain = $orgColumns->get('columns')->contains('*');

        // ->select(['*'])
        if ($allMain) {
            $columns[] = "{$this->from}.*";
        }
        // ->select(['attr.*']) or ->select(['id']) or ->select(['id', 'color'])
        elseif ($allAttr || $orgColumns->get('columns')->contains($this->baseEntity()->getEntityKey())) {
            $columns[] =  "{$this->from}.{$this->baseEntity()->getEntityKey()}";
        }

        $columns = $orgColumns
            ->get('columns')
            ->merge($columns)
            ->filter(function ($value, $key) use ($removeCol) {
                return !(in_array($value, $removeCol));
            })->unique()->toArray();

        // Merge the expression back to the query
        
        if ($expression = $orgColumns->get('expression')) {
            $columns = $expression->merge($columns)->all();
        }
         
        $this->columns = $columns;
    }
    

    /**
    * Get the SQL representation of the query.
    *
    * @return string
    */
    public function toSql()
    {
        if ($this->canUseFlat()) {
            $this->fixFlatColumns();
        } else {
            $this->fixColumns()
                ->processAttributes();
        }

        return parent::toSql();
    }

    /**
     * Get a new instance of the query builder.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function newQuery()
    {
        return new static($this->connection, $this->grammar, $this->processor, $this->baseEntity);
    }

    /**
     * Add a basic where clause to the query.
     *
     * @param  string $column
     * @param  array  $values
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function addWhereAttribute($column, $values)
    {
        $this->hasAttributeConditions = true;

        $this->attributeColumns = array_merge($this->attributeColumns, (array) $column);
        $this->attributeWheres['binding'][$values['type']][] = $values;

        foreach ((array) $column as $col) {
            $this->attributeWheresRef[$col][] = $values;
        }      

        return $this;
    }
    
    
    /**
     * Add a basic where clause to the query.
     *
     * @param  string|array|\Closure  $column
     * @param  string  $operator
     * @param  mixed   $value
     * @param  string  $boolean
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function whereAttribute($column, $operator = null, $value = null, $boolean = 'and')
    {
        if ($this->canUseFlat()) {
            return $this->where($column, $operator, $value, $boolean);
        }

        // If the column is an array, we will assume it is an array of key-value pairs
        // and can add them each as a where clause. We will maintain the boolean we
        // received when the method was called and pass it into the nested where.
        if (is_array($column)) {
            return $this->addArrayOfWhereAttributes($column, $boolean);
        }

        // If the columns is actually a Closure instance, we will assume the developer
        // wants to begin a nested where statement which is wrapped in parenthesis.
        // We'll add that Closure to the query then return back out immediately.
        if ($column instanceof Closure) {
            return $this->whereNestedAttribute($column, $boolean);
        }
        
        $type = 'Basic';

        // Here we will make some assumptions about the operator. If only 2 values are
        // passed to the method, we will assume that the operator is an equals sign
        // and keep going. Otherwise, we'll require the operator to be passed in.
        list($value, $operator) = $this->prepareValueAndOperator(
            $value,
            $operator,
            func_num_args() == 2
        );

        // If the given operator is not found in the list of valid operators we will
        // assume that the developer is just short-cutting the '=' operators and
        // we will set the operators to '=' and set the values appropriately.
        if ($this->invalidOperator($operator)) {
            list($value, $operator) = [$operator, '='];
        }


        // If the value is "null", we will just assume the developer wants to add a
        // where null clause to the query. So, we will allow a short-cut here to
        // that method for convenience so the developer doesn't have to check.
        if (is_null($value)) {
            return $this->whereNullAttribute($column, $boolean, $operator !== '=');
        }

        return $this->addWhereAttribute($column, compact('column', 'operator', 'value', 'boolean', 'type'));
    }

    /**
     * Add an array of where clauses to the query.
     *
     * @param  array  $column
     * @param  string  $boolean
     * @param  string  $method
     * @return $this
     */
    protected function addArrayOfWhereAttributes($column, $boolean, $method = 'whereAttribute')
    {
        return $this->whereNestedAttribute(function ($query) use ($column, $method, $boolean) {
            foreach ($column as $key => $value) {
                if (is_numeric($key) && is_array($value)) {
                    $query->{$method}(...array_values($value));
                } else {
                    $query->$method($key, '=', $value, $boolean);
                }
            }
        }, $boolean);
    }

    /**
     * Add a nested where statement to the query.
     *
     * @param  \Closure $callback
     * @param  string   $boolean
     * @return \Illuminate\Database\Query\Builder|static
     */
    public function whereNestedAttribute(Closure $callback, $boolean = 'and')
    {
        if ($this->canUseFlat()) {
            return $this->whereNested($callback, $boolean);
        }

        call_user_func($callback, $query = $this->forNestedWhere());

        return $this->addNestedWhereQueryAttribute($query, $boolean);
    }

    /**
     * Add another query builder as a nested where to the query builder.
     *
     * @param  \Illuminate\Database\Query\Builder|static $query
     * @param  string  $boolean
     * @return $this
     */
    public function addNestedWhereQueryAttribute($query, $boolean = 'and')
    {
        if ($query->hasAttributeConditions) {
            $type = 'Nested';
            $this->addWhereAttribute($query->attributeColumns, compact('type', 'query', 'boolean'));
        }

        return $this;
    }

    /**
     * Add an "or where" clause to the query.
     *
     * @param  string  $column
     * @param  string  $operator
     * @param  mixed   $value
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function orWhereAttribute($column, $operator = null, $value = null)
    {
        return $this->whereAttribute($column, $operator, $value, 'or');
    }

    /**
     * Add a where between statement to the query.
     *
     * @param  string  $column
     * @param  array   $values
     * @param  string  $boolean
     * @param  bool  $not
     * @return $this
     */
    public function whereBetweenAttribute($column, array $values, $boolean = 'and', $not = false)
    {
        if ($this->canUseFlat()) {
            return $this->whereBetween($column, $values, $boolean, $not);
        }
        
        $type = 'between';

        return $this->addWhereAttribute($column, compact('column', 'values', 'type', 'boolean', 'not', 'type'));
    }

    /**
     * Add an or where between statement to the query.
     *
     * @param  string  $column
     * @param  array   $values
     * @return \Illuminate\Database\Query\Builder|static
     */
    public function orWhereBetweenAttribute($column, array $values)
    {
        return $this->whereBetweenAttribute($column, $values, 'or');
    }

    /**
     * Add a where not between statement to the query.
     *
     * @param  string  $column
     * @param  array   $values
     * @param  string  $boolean
     * @return \Illuminate\Database\Query\Builder|static
     */
    public function whereNotBetweenAttribute($column, array $values, $boolean = 'and')
    {
        return $this->whereBetweenAttribute($column, $values, $boolean, true);
    }

    /**
     * Add an or where not between statement to the query.
     *
     * @param  string  $column
     * @param  array   $values
     * @return \Illuminate\Database\Query\Builder|static
     */
    public function orWhereNotBetweenAttribute($column, array $values)
    {
        return $this->whereNotBetweenAttribute($column, $values, 'or');
    }

    /**
     * Add a "where in" clause to the query.
     *
     * @param  string  $column
     * @param  mixed   $values
     * @param  string  $boolean
     * @param  bool    $not
     * @return $this
     */
    public function whereInAttribute($column, $values, $boolean = 'and', $not = false)
    {
        if ($this->canUseFlat()) {
            return $this->whereIn($column, $values, $boolean, $not);
        }
        
        $type = $not ? 'NotIn' : 'In';

        if ($values instanceof Arrayable) {
            $values = $values->toArray();
        }

        return $this->addWhereAttribute($column, compact('column', 'values', 'boolean', 'not', 'type'));
    }

    /**
    * Add an "or where in" clause to the query.
    *
    * @param  string  $column
    * @param  mixed   $values
    * @return \Illuminate\Database\Query\Builder|static
    */
    public function orWhereInAttribute($column, $values)
    {
        return $this->whereInAttribute($column, $values, 'or');
    }

    /**
     * Add a "where not in" clause to the query.
     *
     * @param  string  $column
     * @param  mixed   $values
     * @param  string  $boolean
     * @return \Illuminate\Database\Query\Builder|static
     */
    public function whereNotInAttribute($column, $values, $boolean = 'and')
    {
        return $this->whereInAttribute($column, $values, $boolean, true);
    }

    /**
     * Add an "or where not in" clause to the query.
     *
     * @param  string  $column
     * @param  mixed   $values
     * @return \Illuminate\Database\Query\Builder|static
     */
    public function orWhereNotInAttribute($column, $values)
    {
        return $this->whereNotInAttribute($column, $values, 'or');
    }

    /**
     * Add a "where null" clause to the query.
     *
     * @param  string  $column
     * @param  string  $boolean
     * @param  bool    $not
     * @return $this
     */
    public function whereNullAttribute($column, $boolean = 'and', $not = false)
    {
        if ($this->canUseFlat()) {
            return $this->whereNull($column, $boolean, $not);
        }
        
        $type = $not ? 'NotNull' : 'Null';

        return $this->addWhereAttribute($column, compact('column', 'boolean', 'not', 'type'));
    }

    /**
     * Add an "or where null" clause to the query.
     *
     * @param  string  $column
     * @return \Illuminate\Database\Query\Builder|static
     */
    public function orWhereNullAttribute($column)
    {
        return $this->whereNullAttribute($column, 'or');
    }

    /**
     * Add a "where not null" clause to the query.
     *
     * @param  string  $column
     * @param  string  $boolean
     * @return \Illuminate\Database\Query\Builder|static
     */
    public function whereNotNullAttribute($column, $boolean = 'and')
    {
        return $this->whereNullAttribute($column, $boolean, true);
    }

    /**
     * Add an "or where not null" clause to the query.
     *
     * @param  string  $column
     * @return \Illuminate\Database\Query\Builder|static
     */
    public function orWhereNotNullAttribute($column)
    {
        return $this->whereNotNullAttribute($column, 'or');
    }

    /**
     * Add a "where date" statement to the query.
     *
     * @param  string  $column
     * @param  string   $operator
     * @param  int   $value
     * @param  string   $boolean
     * @return \Illuminate\Database\Query\Builder|static
     */
    public function whereDateAttribute($column, $operator, $value = null, $boolean = 'and')
    {
        list($value, $operator) = $this->prepareValueAndOperator(
            $value,
            $operator,
            func_num_args() == 2
        );

        if ($this->canUseFlat()) {
            return $this->whereDate($column, $operator, $value, $boolean);
        }
        
        return $this->addDateBasedWhereAttribute('Date', $column, $operator, $value, $boolean);
    }

    /**
    * Add an "or where date" statement to the query.
    *
    * @param  string  $column
    * @param  string  $operator
    * @param  string  $value
    * @return \Illuminate\Database\Query\Builder|static
    */
    public function orWhereDateAttribute($column, $operator, $value)
    {
        return $this->whereDateAttribute($column, $operator, $value, 'or');
    }

    /**
     * Add a "where time" statement to the query.
     *
     * @param  string  $column
     * @param  string   $operator
     * @param  int   $value
     * @param  string   $boolean
     * @return \Illuminate\Database\Query\Builder|static
     */
    public function whereTimeAttribute($column, $operator, $value, $boolean = 'and')
    {
        return $this->addDateBasedWhereAttribute('Time', $column, $operator, $value, $boolean);
    }

    /**
     * Add an "or where time" statement to the query.
     *
     * @param  string  $column
     * @param  string   $operator
     * @param  int   $value
     * @return \Illuminate\Database\Query\Builder|static
     */
    public function orWhereTimeAttribute($column, $operator, $value)
    {
        return $this->whereTimeAttribute($column, $operator, $value, 'or');
    }

    /**
     * Add a "where day" statement to the query.
     *
     * @param  string  $column
     * @param  string   $operator
     * @param  int   $value
     * @param  string   $boolean
     * @return \Illuminate\Database\Query\Builder|static
     */
    public function whereDayAttribute($column, $operator, $value = null, $boolean = 'and')
    {
        list($value, $operator) = $this->prepareValueAndOperator(
            $value,
            $operator,
            func_num_args() == 2
        );

        if ($this->canUseFlat()) {
            return $this->whereDay($column, $operator, $value, $boolean);
        }
        
        return $this->addDateBasedWhereAttribute('Day', $column, $operator, $value, $boolean);
    }

    /**
     * Add a "where month" statement to the query.
     *
     * @param  string  $column
     * @param  string   $operator
     * @param  int   $value
     * @param  string   $boolean
     * @return \Illuminate\Database\Query\Builder|static
     */
    public function whereMonthAttribute($column, $operator, $value = null, $boolean = 'and')
    {
        list($value, $operator) = $this->prepareValueAndOperator(
            $value,
            $operator,
            func_num_args() == 2
        );

        if ($this->canUseFlat()) {
            return $this->whereMonth($column, $operator, $value, $boolean);
        }
        
        return $this->addDateBasedWhereAttribute('Month', $column, $operator, $value, $boolean);
    }

    /**
     * Add a "where year" statement to the query.
     *
     * @param  string  $column
     * @param  string   $operator
     * @param  int   $value
     * @param  string   $boolean
     * @return \Illuminate\Database\Query\Builder|static
     */
    public function whereYearAttribute($column, $operator, $value = null, $boolean = 'and')
    {
        list($value, $operator) = $this->prepareValueAndOperator(
            $value,
            $operator,
            func_num_args() == 2
        );

        if ($this->canUseFlat()) {
            return $this->whereYear($column, $operator, $value, $boolean);
        }
        
        return $this->addDateBasedWhereAttribute('Year', $column, $operator, $value, $boolean);
    }


    /**
     * Add a date based (year, month, day) statement to the query.
     *
     * @param  string  $type
     * @param  string  $column
     * @param  string  $operator
     * @param  int  $value
     * @param  string  $boolean
     * @return $this
     */
    protected function addDateBasedWhereAttribute($type, $column, $operator, $value, $boolean = 'and')
    {
        return $this->addWhereAttribute($column, compact('column', 'type', 'boolean', 'operator', 'value'));
    }

    /**
     * Add an "order by" clause to the query.
     *
     * @param  string  $column
     * @param  string  $direction
     * @return $this
     */
    public function orderByAttribute($column, $direction = 'asc')
    {
        if ($this->canUseFlat()) {
            return $this->orderBy($column, $direction);
        }
        
        $property = $this->unions ? 'unionOrders' : 'orders';
        $direction = strtolower($direction) == 'asc' ? 'asc' : 'desc';

        $this->hasAttributeConditions = true;

        $this->attributeColumns = array_merge($this->attributeColumns, (array)$column);
        $this->attributeOrderBy[$property][] = compact('column', 'direction');

        return $this;
    }
}
