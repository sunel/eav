> WIP

![EAV](https://i.imgur.com/FmFQX8E.png)

Entity–attribute–value model (EAV) is a data model to encode, in a space-efficient manner, entities where the number of attributes (properties, parameters) that can be used to describe them is potentially vast, but the number that will actually apply to a given entity is relatively modest.

> [wikipedia](https://en.wikipedia.org/wiki/Entity%E2%80%93attribute%E2%80%93value_model)

## When to Use

* Let us consider a situation where a number of different attributes can be used to describe an entity, but only few attributes actually apply to each one. One option is to create a table with a column representing each attribute; this is suitable for entity with a fixed number of attributes, where all or most attributes have values for a most objects. However, in our case we would end up with records where **majority of columns would be empty**, because attributes may be unknown or inapplicable. To solve the above problem you can apply the EAV (Entity, Attribute, Value) model.

* Have a loose schema that is likely to change over time. Table holding attributes describing an entity is not limited to a specific number of columns, meaning that it doesn't require a schema redesign every time new attribute needs to be introduced. The number of attributes can grow vertically as the database evolves, without the need for structure changes.

## Limitation of EAV

* In EAV model the entity data is more fragmented and so selecting an entire entity record requires multiple table joins. [Piss Check this ](#flat-table)


___

| [Usage](#usage)| [Inserting & Updating Entity](#inserting--updating-entity)| [Retrieving Models](#retrieving-models)| [Where Clauses](#where-clauses)|
| -------------- | --------------| ------|------|

## Installation

Via [composer](http://getcomposer.org):

```bash
$ composer require sunel/eav
```

You'll need to register the service provider, in your `config/app.php`:

```php
'providers' => [
	...
	Eav\Providers\LaravelServiceProvider::class,
]
```

## Usage

To create a [Entity](#entity)

```bash
$ php artisan eav:make:entity product \\App\\Products 
```

Here ```product``` is the entity code and ```\\App\\Products``` is the model related to the entity.

This will create the ```Products``` Model file and the migration for the entity [ER](#er-diagram-for-entity)

The migration contains schema for creating different data type like `varchar`, `text`, `int`, `decimal`, `datetime`. We will aslo have schema to create default [attribute set](#attribute-set) `Default` and [attribute group](#attribute-group) `General`.


To create a [Attribute](#attribute)

```bash
$ php artisan eav:make:attribute sku,name,search,description product 
```

Here ```name,sku,upc,description,search``` are the attributes that needs to be added to  ```product``` entity.

This is will create the migration that is needed to create the attibute and map it to the entity. **YOU NEED TO EDIT THE ATTRIBUTES INFO IN THE MIGRATION**

If the type are left empty it is considered as [Static Attributes](#static-attribute)

> Refer [Add Attribute](#add-attribute) for more info.

Now run the migration

```bash
$ php artisan migrate
```


## Inserting & Updating Entity


#### Insert

```php
use App\Products;

Products::create([
    'name' => 'Flamethrower',
    'sku'  => '1HJK92',
    'upc'  => 'SHNDUU451888',
    'description' => 'Not a Flamethrower',
    'search' => 1
]);

```

#### Update


```php
use App\Products;

$product = Products::find(1);

$product->name = 'Not a Flamethrower';

$product->save();

```

## Retrieving Models

You are ready to start retrieving data from your database. Think of each Eav model as a Eloquent model.

```php
use App\Products;

$product = Products::all();

```

In the example above, will retrieve data only from the entity table. Why? Retrieving all the attributes for the entity is a expensive operation.


```php
$product = Products::all(['attr.*']);
```

Above will retrieve data containing all the attributes value for the entity.

```php
$product = Products::all(['upc','color']);
```

Above will retrieve data containing only ```'upc'``` & ```'color'```.

```php
$product = Products::all(['*', 'upc', 'color']);
```

Above will retrieve data from the entity table and also ```'upc'``` & ```'color'```.

```php
$product = Products::all(['id', 'upc', 'color']);
```

You can also select any field from the main entity table as shown in above example.


## Where Clauses

#### Simple Where Clauses

You may use the `whereAttribute` method on a query builder instance to add `where` clauses to the query. This has the same arguments as the `where`.

```php
$product = Products::whereAttribute('upc', 'SHNDUU451885')->get();
```

Of course, you may use a variety of other operators when writing a `whereAttribute` clause ```>=, <> , =<, like ```.


#### Or Statements

```php
$product = Products::whereAttribute('upc', 'SHNDUU451885')
    	->orWhereAttribute('color', 'like', 'Green%')
        ->get();
```

add `or` clauses to the query.


#### Additional Where Clauses

- **whereBetweenAttribute / orWhereBetweenAttribute**

> The `whereBetweenAttribute` method verifies that a attribute's value is between two values

- **whereNotBetweenAttribute / orWhereNotBetweenAttribute**

> The `whereNotBetweenAttribute` method verifies that a attribute's value lies outside of two values

- **whereInAttribute / orWhereInAttribute**

> The `whereInAttribute` method verifies that a given attribute's value is contained within the given array

- **whereNotInAttribute / orWhereNotInAttribute**

> The `whereNotInAttribute` method verifies that the given attribute's value is **not** contained in the given array

- **whereNullAttribute / orWhereNullAttribute**

> The `whereNullAttribute` method verifies that the value of the given attribute is `NULL`

- **whereNotNullAttribute / orWhereNotNullAttribute**

> The `whereNotNullAttribute` method verifies that the attribute's value is not `NULL`

- **whereDateAttribute / orWhereDateAttribute / whereDayAttribute / whereMonthAttribute / whereYearAttribute / whereTimeAttribute / orWhereTimeAttribute**

> The `whereDateAttribute` method may be used to compare a attribute's value against a date

> The `whereMonthAttribute` method may be used to compare a attribute's value against a specific month of a year

> The `whereDayAttribute` method may be used to compare a attribute's value against a specific day of a month

> The `whereYearAttribute` method may be used to compare a attribute's value against a specific year

> The `whereTimeAttribute` method may be used to compare a attribute's value against a specific time

- **whereNestedAttribute**

- **orderByAttribute**

> The `orderByAttribute` method allows you to sort the result of the query by a given attribute. 

```php
$search = Products::whereAttribute('upc', 'like', 'SHNDUU%')
	->whereAttribute('color', 'like', 'Green%')
	->whereAttribute('size', '=', 's')
	->select(['*','color']);
```


## EAV Concepts

### Entity

Entity actually refers to data item. For example product.

To create a Entity and store data, we need to create a table structure as shown in this [ER](#er-diagram-for-entity) diagram and to store values to these tables we need to create a model that does it.

This package provides artisan commands that will simplify the process of creating tables and models.

```bash
$ php artisan eav:make:entity [entity_code] [entity_class_name] 
```
The above command will create both the migration and model for the entity.

Two migrations will be created `main table` and `entity data type table`. The `main table` is the master table which hold the primary key, meta-data for the entity. 

You can also add additional columns to this `main table`, these columns are refered as [static](#static) attibutes.


```bash
$ php artisan eav:make:model [entity_class_name] -e [entity_code]
```

The above command will create entity model file for the given entity code. This is just a eloquent model with additional logics to support EAV.


```php
namespace App;

use Eav\Model;

class Products extends Model
{
    const ENTITY  = 'product';

    //
}
```

To view the Entity model 

```php
$product = Products::find(1);

# instance of Eav\Entity

$product->baseEntity;
```

### Attribute

Refers to the different attributes of the Entity. Like for example product have different attributes like color, size, price, etc.

| [Add](#add-attribute)| [Add Options](#add-options) | [Remove Options](#remove-options) |
| -------------------- | --------------------------- | --------------------------------- |


<a name="add-attribute"></a>
#### Add

To create a migration, use the `eav:make:attribute`

```bash
$ php artisan eav:make:attribute [n,number,of,attibutes] [entity_code] 
```

This will create the attibutes and also map it to the given entity. Once the code is genrated you need to update `backend_type`, `frontend_type` for the attributes. If you check the migration file it will have code that is similar to the code given below.

```php
Eav\Attribute::add([
    'attribute_code' => 'status',
    'entity_code' => 'product',
    'backend_class' => null,
    'backend_type' => 'int',
    'backend_table' =>  null,
    'frontend_class' =>  null,
    'frontend_type' => 'select',
    'frontend_label' => 'Status',
    'source_class' =>  Eav\Attribute\Source\Boolean::class,
    'default_value' => 0,
    'is_required' => 0,
    'required_validate_class' =>  null
]);

Eav\EntityAttribute::map([
    'attribute_code' => 'status',
    'entity_code' => 'product',
    'attribute_set' => 'Default',
    'attribute_group' => 'General'
]);
```

The `Eav\Attribute::add` add's the attribute to the system and `Eav\EntityAttribute::map` will map the attribute to the entity and also assign to a set and group.


| Field | Description |
| ------| ------- |
| attribute_code| Specify the code for the attribute.|
| entity_code| Specify the entity code for the attibute.|
| backend_class| When specified will be used to add aditional control to the attribute when it intracts with the database.|
| backend_type| Specify the column type. Supports `int`, `varchar`, `text`, `datetime`, `decimal`.|
| backend_table| When specified it will store the data to the given.|
| frontend_class| When specified will be used to add aditional control to the attribute when is used in the frontend.|
| frontend_type| Specify the type of html field.|
| frontend_label| Specify the label.|
| source_class|  When specified will be used to populate a field’s default options, if the frontend_type is `select`.|
| default_value| Specify the default value that will stored if not given.|
| is_required| If enabled, value needs to given for the attribute.|
| required_validate_class| Custom validation rules.|



```php
# To retrive the attributes related to a entity

$entity = Eav\Entity::findByCode('code');

$attributes = $entity->attributes;

````

#### Attribute Set

Its used as a template for the enity. The attribute set determines the fields that are available during data entry, and the values that appear when retrieved.

`Default` is the set that is create initialy.

A set is created while mapping a attribute to a entity. If a set already exists it will be used or else created. Attribute set will be unique for entity. A Entity can have many set's.

To create a new set

```php
Eav\AttributeSet::create([
    'attribute_set_name' => $code,
    'entity_id' => $entity->entity_id,
]);
```

```php
# To retrive the set related to a entity

$entity = Eav\Entity::findByCode('code');

$sets = $entity->attributeSet;

# To retrieve the attributes related to the set.

$sets->first()->attributes;

```

#### Attribute group

The attributes are organized into groups.

`General` is the set that is create initialy.

To create a new set

```php
Eav\AttributeGroup::create([
    'attribute_group_name' => $code,
    'attribute_set_id' => $attributeSet->attribute_set_id,
]);
```

```php
# To retrieve the group related to the set.

$groups = $sets->first()->attributeGroup;

# To retrieve the attributes related to the group.

$groups->first()->attributes

```


#### Static Attribute

Static attributes are attributes stored in the main table of an entity. Static attributes are always loaded and are useful especially if you want to retrieve information quickly or to optimize lookup of data.

If you want to use static attributes, you have to do 2 things in your migration script. First, you need to add a column to the main entity table, with the correct column definition. Next, you need to add the attribute using the `Eav\Attribute::add` method, and define your attribute as `'backend_type' => 'static'`.  

### Add Options

To add option there are few ways to do that.

#### Use Source Class

```php
Eav\Attribute::add([
    'attribute_code' => 'status',
    'entity_code' => 'product',
    'backend_class' => null,
    'backend_type' => 'int',
    'backend_table' =>  null,
    'frontend_class' =>  null,
    'frontend_type' => 'select', // Assgin the type "select"
    'frontend_label' => 'Status',
    'source_class' =>  Eav\Attribute\Source\Boolean::class, // check the [1]
    'default_value' => 0,
    'is_required' => 0,
    'required_validate_class' =>  null
]);
```

1. `source_class` is one way of getting the options values. Here you can assign a class that extends `Eav\Attribute\Source` 

check this [eav/src/Attribute/Source/Boolean.php](https://github.com/sunel/eav/blob/master/src/Attribute/Source/Boolean.php) file, this will provide 'yes' or 'No' options.

#### Through Migration

```php
Eav\Attribute::add([
    'attribute_code' => 'status',
    'entity_code' => 'product',
    'backend_class' => null,
    'backend_type' => 'int',
    'backend_table' =>  null,
    'frontend_class' =>  null,
    'frontend_type' => 'select', // Assgin the type "select"
    'frontend_label' => 'Status',
    'source_class' => null
    'options' => [
       '1' => 'Yes',
       '0'  => 'No'
     ]
    'default_value' => 0,
    'is_required' => 0,
    'required_validate_class' =>  null
]);
```

Or else you can also add directly to 

```php

$statusAttr = Eav\Attribute::findByCode('size', 'product');

Eav\AttributeOption::add($statusAttr, [
    's' => 'Small',
    'm' => 'Medium',
    'l' => 'Large',
    'xl' => 'Xtra Large',
]);
```


### To Get the Options Values

```php

$statusAttr = Eav\Attribute::findByCode('size', 'product');

$statusAttr->frontend_type // This will return the type in this case 'select'

$statusAttr->options();

```

### Remove Options
```php
Eav\AttributeOption::remove(Eav\Attribute::findByCode('code'), [
    's' => 'Small'
]);
```


### Value

Value refers to the actual value of the attribute of the entity. Like color has value red, price has value $25, etc.

The value are stored in tables corresponding to the data types such as `product_varchar, product_int, product_decimal, product_datetime, product_text`.


### Flat Table

Flat tables on the other hand means, storing all data in a single table instead of multiple tables. So this reduces the number of queries required and hence increases the speed.

So all attributes of an EAV entity become column name of a single table and all attribute data relating to an entity is stored in a single table.

Flat table can be activated by

```bash
$ php artisan eav:compile:entity [entity_code]
```

## ER Diagram for Core EAV 
![ER](https://i.imgur.com/O5O5egA.png)

## ER Diagram for ENTITY

![Entity ER](https://i.imgur.com/fzGWljm.png)
