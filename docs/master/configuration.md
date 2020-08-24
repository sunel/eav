# Configuration

Publish the configuration file.

```bash
php artisan vendor:publish --tag="eav.config"
```

By default, following database fields are enabled.

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

## Element Types

List of html element type

```php
'elementTypes' => [
    'text', 'select', 'number', 'textarea',
    'integer', 'date', 'time', 'dateTime',
    'boolean'
],
```

## Select Sources

List of option that are used when 'select' is used as frontend type.

```php
'selectSources' => [
    'database',
    \Eav\Attribute\Source\Boolean::class,
],
```

You can add additional type ref `\Eav\Attribute\Source\Boolean::class`


## Api Middleware

Add the middleware that needed to be used when using api. 

```php
'api' => [
    'middleware' => [
    	'web'
    ]
],
```