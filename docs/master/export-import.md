# Export & Import

## Export

To export a entites data.

```bash
$ php artisan eav:entity:export [entity_code] --path [path/to/the/location/] -C 100
```

By default it will export 100 records in a single query. You can increase the value using `-C` option.

Genrated CSV file can be stored in custom location using the `--path` option.

eg :- 

```bash
$ php artisan eav:entity:export product --path storage/export/ -C 500
```

## Import

> WIP