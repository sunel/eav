![EAV](https://i.imgur.com/FmFQX8E.png)

Entity–attribute–value model (EAV) is a data model to encode, in a space-efficient manner, entities where the number of attributes (properties, parameters) that can be used to describe them is potentially vast, but the number that will actually apply to a given entity is relatively modest.

> [wikipedia](https://en.wikipedia.org/wiki/Entity%E2%80%93attribute%E2%80%93value_model)

## When to Use

* Let us consider a situation where a number of different attributes can be used to describe an entity, but only few attributes actually apply to each one. One option is to create a table with a column representing each attribute; this is suitable for entity with a fixed number of attributes, where all or most attributes have values for a most objects. However, in our case we would end up with records where **majority of columns would be empty**, because attributes may be unknown or inapplicable. To solve the above problem you can apply the EAV (Entity, Attribute, Value) model.

* Have a loose schema that is likely to change over time. Table holding attributes describing an entity is not limited to a specific number of columns, meaning that it doesn't require a schema redesign every time new attribute needs to be introduced. The number of attributes can grow vertically as the database evolves, without the need for structure changes.

## Limitation of EAV

* In EAV model the entity data is more fragmented and so selecting an entire entity record requires multiple table joins. [Piss Check this ](#flat)


| [Usage](#usage)| [Inserting & Updating Entity](#inserting--updating-entity)| [Retrieving Models](#retrieving-models)|
| -------------- | --------------| ------|

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


To create a [Attribute](#attribute)

```bash
$ php artisan eav:make:attribute sku,name,search,description product 
```

Here ```name,sku,upc,description,search``` are the attributes that needs to be added to  ```product``` entity.

This is will create the migration that is needed to create the attibute and map it to the entity.


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

#### Retrieving Models

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



## EAV Concepts

### Entity

### Attribute

| [Add](#add-attribute)| [Update](#update-attribute)| [Add Options](#add-options) |
| -------------- | --------------| ------|


#### Add

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


#### Attribute Set

#### Attribute group 

### Value



## ER Diagram for Core EAV 
![ER](https://i.imgur.com/O5O5egA.png)

## ER Diagram for ENTITY

![Entity ER](https://i.imgur.com/fzGWljm.png)
