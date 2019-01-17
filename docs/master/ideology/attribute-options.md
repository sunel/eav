# Options

[[toc]]

## Add Options

To add option there are few ways to do that.

### Use Source Class

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

### Through Migration

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
    'source_class' => null,
    'options' => [
       '1' => 'Yes',
       '0'  => 'No'
     ],
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


## To Get the Options Values

```php

$statusAttr = Eav\Attribute::findByCode('size', 'product');

$statusAttr->load('optionValues');

$statusAttr->frontend_type // This will return the type in this case 'select'

$statusAttr->options();

```

## Remove Options
```php
Eav\AttributeOption::remove(Eav\Attribute::findByCode('size', 'product'), [
    's' => 'Small'
]);
```