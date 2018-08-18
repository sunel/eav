# Query Builder

[[toc]]

## Selects

Think of each EAV model as a Eloquent model.

This will retrieve data only from the entity table. Why? Retrieving all the attributes for the entity is a expensive operation.

```php
use App\Products;

$product = Products::all();
```

If you want toretrieve data containing all the attributes value for the entity.

```php
$product = Products::all(['attr.*']);
```

This will retrieve data containing only `'upc'` & `'color'`.
```php
$product = Products::all(['upc','color']);
```

This will retrieve data from the entity table and also `'upc'` & `'color'`.
```php
$product = Products::all(['*', 'upc', 'color']);
```

You can also select any field from the main entity table as shown in above example.
```php
$product = Products::all(['id', 'upc', 'color']);
```

The same can done with the `get` & `select`

```php
Products::whereAttribute('upc', 'SHNDUU451885')
	->get(['color'])
```

```php
Products::whereAttribute('upc', 'SHNDUU451885')
	->select(['attr.*'])
	->get()
```

## Where Clauses

### Simple Where Clauses

You may use the `whereAttribute` method on a query builder instance to add `where` clauses to the query. This has the same arguments as the `where`.

```php
$product = Products::whereAttribute('upc', 'SHNDUU451885')->get();
```

Of course, you may use a variety of other operators when writing a `whereAttribute` clause ```>=, <> , =<, like ```.


### Or Statements

```php
$product = Products::whereAttribute('upc', 'SHNDUU451885')
    	->orWhereAttribute('color', 'like', 'Green%')
        ->get();
```

add `or` clauses to the query.


### Additional Where Clauses

**whereBetweenAttribute / orWhereBetweenAttribute**

The `whereBetweenAttribute` method verifies that a attribute's value is between two values

```php
$product = Products::whereBetweenAttribute('votes', [1, 100])
    	->orWhereBetweenAttribute('age', [18, 100])
        ->get();
```

**whereNotBetweenAttribute / orWhereNotBetweenAttribute**

The `whereNotBetweenAttribute` method verifies that a attribute's value lies outside of two values

```php
$product = Products::whereNotBetweenAttribute('votes', [1, 100])
    	->orWhereNotBetweenAttribute('age', [18, 100])
        ->get();
```

**whereInAttribute / orWhereInAttribute**

The `whereInAttribute` method verifies that a given attribute's value is contained within the given array

```php
$product = Products::whereInAttribute('id', [1, 2, 3])
    	->orWhereInAttribute('id', [4, 5, 6])
        ->get();
```

**whereNotInAttribute / orWhereNotInAttribute**

The `whereNotInAttribute` method verifies that the given attribute's value is **not** contained in the given array

```php
$product = Products::whereNotInAttribute('id', [1, 2, 3])
    	->orWhereNotInAttribute('id', [4, 5, 6])
        ->get();
```


**whereNullAttribute / orWhereNullAttribute**

The `whereNullAttribute` method verifies that the value of the given attribute is `NULL`

```php
$product = Products::whereNullAttribute('search')
    	->orWhereNullAttribute('color')
        ->get(['*', 'search', 'color']);
```

In this case we need to specifiy the attribute in the `get` call, this is due to the behaviour of table joins.


**whereNotNullAttribute / orWhereNotNullAttribute**

The `whereNotNullAttribute` method verifies that the attribute's value is not `NULL`

```php
$product = Products::whereNotNullAttribute('search')
    	->whereNotNullAttribute('color')
        ->get(['*', 'search', 'color']);
```

In this case we need to specifiy the attribute in the `get` call, this is due to the behaviour of table joins.

**whereDateAttribute / orWhereDateAttribute / whereDayAttribute / whereMonthAttribute / whereYearAttribute / whereTimeAttribute / orWhereTimeAttribute**

- The `whereDateAttribute` method may be used to compare a attribute's value against a date.
- The `whereMonthAttribute` method may be used to compare a attribute's value against a specific month of a year.
- The `whereDayAttribute` method may be used to compare a attribute's value against a specific day of a month.
- The `whereYearAttribute` method may be used to compare a attribute's value against a specific year.
- The `whereTimeAttribute` method may be used to compare a attribute's value against a specific time.


```php
$product = Products::whereDateAttribute('purchased_at', '2017-08-09')
    	->orWhereDateAttribute('purchased_at', '2017-08-09')
    	->whereYearAttribute('created_at', '2016')
        ->get();
```


**whereNestedAttribute**

```php
$search = Products::whereAttribute('upc', 'like', 'SHNDUU%')
	->whereAttribute('color', 'like', 'Green%')
	->whereNested(function (QueryBuilder $query) use($size) {
		$query->whereAttribute('size', '=', $size);
	})	
	->orderByAttribute('name', 'desc')
	->select(['*','color']);
```

**Parameter Grouping**

```php
$search = Products::whereAttribute('upc', 'like', 'SHNDUU%')
	->whereAttribute('color', 'like', 'Green%')
	->whereAttribute(function ($query) use($size) {
		$query->whereAttribute('size', '=', $size);
	})	
	->orderByAttribute('name', 'desc')
	->select(['*','color']);
```


**orderByAttribute**

The `orderByAttribute` method allows you to sort the result of the query by a given attribute. 

```php
$search = Products::whereAttribute('upc', 'like', 'SHNDUU%')
	->whereAttribute('color', 'like', 'Green%')
	->whereAttribute('size', '=', 's')
	->orderByAttribute('name', 'desc')
	->select(['*','color']);
```

