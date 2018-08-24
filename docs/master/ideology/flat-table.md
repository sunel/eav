# Flat Table

Flat tables on the other hand means, storing all data in a single table instead of multiple tables. So this reduces the number of queries required and hence increases the speed.

So all attributes of an EAV entity become column name of a single table and all attribute data relating to an entity is stored in a single table.

Flat table can be created by

```bash
$ php artisan eav:compile:entity [entity_code]
```

This will collect all attribute, build the scheme and then insert values into it.

To Activate the Flat table for the entity

```bash
$ php artisan eav:flat:entity [entity_code] -E true
```

To Deactivate the Flat table for the entity

```bash
$ php artisan eav:flat:entity [entity_code] -E false
```