# Model
[[toc]]

## Introduction

A eloquent model with additional sweet to support EAV.

We make it easy for developers to interact with attributes by defining simple models without writing relationships or long SQL queries.

## Defining Models

All EAV models extend `Eav\Model` class.

To create a model instance

```bash
php artisan eav:make:model -e product
```

Below will create migration and model class. 

```bash
$ php artisan eav:make:entity product \\App\\Products 
```

## Model Conventions

Look at an example `Products` model.

```php
namespace App;

use Eav\Model;

class Products extends Model
{
    const ENTITY  = 'product';

    //
}
```

`const ENTITY` is required value, this will be filled automaticaly when the entity is created. Under the hood the property is used to map the current model to the corresponding ENTITY.

### Table Names

This works same as Eloquent. By convention, the "snake case", plural name of the class will be used as the table name unless another name is explicitly specified.

But when [Flat Table](ideology/flat-table.html) is enabled the table name is prefixed with `_flat`.

## Inserting & Updating Entity

### Insert

To create a new record in the database, create a new model instance, set attributes on the model, then save. It is same as creating a new record in eloquent. 

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

### Update

Again it is same as eloquent.


```php
use App\Products;

$product = Products::find(1);

$product->name = 'Not a Flamethrower';

$product->save();

```

### Mass Assignment

As the Attributes for the entity are unpredictable, we have made the model unguarded by default.

```php
/**
 * Indicates if all mass assignment is enabled.
 *
 * @var bool
 */
protected static $unguarded = true;
```
::: warning
It is responsiblity of the developer to take care of mass-assignment vulnerability.
:::

### Mass Updates

Updates can also be performed against any number of entity that match a given query.

```php
$p = Products::whereNullAttribute('search')
				->whereAttribute('active', 1);

$p->update(['search' => 1]);
```
        
