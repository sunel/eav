# Flat Table

Flat tables on the other hand means, storing all data in a single table instead of multiple tables. So this reduces the number of queries required and hence increases the speed.

So all attributes of an EAV entity become column name of a single table and all attribute data relating to an entity is stored in a single table.

**To create Flat table.**

```bash
$ php artisan eav:compile:entity [entity_code]
```
This will collect all attribute, build and run the scheme.


**To updated the table with value.**

```bash
$ php artisan eav:compile:updater [entity_code] -C 100
```
By default it will insert 100 records in a single insert. You can increase the value using `-C` option.


**To Activate the Flat table for the entity**

```bash
$ php artisan eav:flat:entity [entity_code] -E true
```

**To Deactivate the Flat table for the entity**

```bash
$ php artisan eav:flat:entity [entity_code] -E false
```

::: warning
When Flat table is enabled and if you try to insert or update a entity, it will update only the flat table.

You can temporarily enabled or disable flat table though the code.

```php

$product = new Product();

// Disable
$product->setUseFlat(false);

// Enable
$product->setUseFlat(true);
```
:::