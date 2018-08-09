# Flat Table

Flat tables on the other hand means, storing all data in a single table instead of multiple tables. So this reduces the number of queries required and hence increases the speed.

So all attributes of an EAV entity become column name of a single table and all attribute data relating to an entity is stored in a single table.

Flat table can be activated by

```bash
$ php artisan eav:compile:entity [entity_code]
```