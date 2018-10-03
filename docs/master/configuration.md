# Configuration

Publish the configuration file.

```bash
php artisan vendor:publish --tag="eav.config"
```

By default, following fields are enabled.

```php
'fieldTypes' => [
    'boolean', 'date', 'dateTime', 'double', 
    'integer', 'text', 'string', 
],
```

## Field Types

Currenlty we support the below types.

```php
'bigInteger', 'binary', 'boolean',
'char', 'date', 'dateTime', 'dateTimeTz',
'decimal', 'double', 'float', 'geometry',
'geometryCollection', 'integer', 'ipAddress', 
'json', 'jsonb', 'lineString', 'longText', 
'macAddress', 'mediumInteger', 'mediumText', 
'multiLineString', 'multiPoint', 'multiPolygon', 
'point', 'polygon', 'smallInteger', 'string', 
'text', 'time', 'timeTz', 'timestamp', 'timestampTz',
'tinyInteger', 'unsignedBigInteger', 'unsignedInteger', 
'unsignedMediumInteger','unsignedSmallInteger', 
'unsignedTinyInteger', 'uuid', 'year',
```
