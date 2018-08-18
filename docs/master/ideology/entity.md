# Entity

[[toc]]

Entity actually refers to data item. For example product.

To create a Entity and store data, we need to create a table structure as shown in this [ER](../er.html#entity) diagram and to store values to these tables we need to create a model that does it.

This package provides artisan commands that will simplify the process of creating tables and models.

```bash
$ php artisan eav:make:entity [entity_code] [entity_class_name] 
```
The above command will create both the migration and model for the entity.

Two migrations will be created `main table` and `entity data type table`. The `main table` is the master table which hold the primary key, meta-data for the entity. 

You can also add additional columns to this `main table`, these columns are refered as [static](static-attribute.html) attibutes.


```bash
$ php artisan eav:make:model [entity_class_name] -e [entity_code]
```

The above command will create entity model file for the given entity code.


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

$product->baseEntity();