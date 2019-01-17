# Attribute

[[toc]]

The Attributes are attached to the entity through relation.

```php
public function attributes()
{
    return $this->hasMany(Attribute::class, 'entity_id');
}
```

## Add

To create a migration, use the `eav:make:attribute`

You can create using one of the below options.

### --attributes

```bash
$ php artisan eav:make:attribute [entity_code]  --attributes [attr1:type,attr:type,...]
```

If you have few attribute, you can use `--attributes` option where you can mention attribute with their type as comma separated values. Once created remember to update migration if necessary like `backend_type`, `frontend_type` for the attributes.

eg :- 

```bash
$ php artisan eav:make:attribute product -A name:string,search:boolean,description:text 
```

### --source

If you have too many attibutes you can use a CSV file. <a :href="$withBase('/assets/attribute.csv')">Here</a> is a sample file with the header that can be used.

```bash
$ php artisan eav:make:attribute [entity_code]  --source [path/to/the/attribute.csv]
```
It will fill all the details from the file and if needed you can update the migration.

eg :-

```bash
$ php artisan eav:make:attribute product -S storage/attribute.csv
```


Both the options will also map it to the given entity. If you check the migration file it will have code that is similar to the code given below.

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
    'is_filterable' => 0,
    'is_searchable' => 0,
    'required_validate_class' =>  null
]);

Eav\EntityAttribute::map([
    'attribute_code' => 'status',
    'entity_code' => 'product',
    'attribute_set' => 'Default',
    'attribute_group' => 'General'
]);
```

The `Eav\Attribute::add` add's the attribute to the system and `Eav\EntityAttribute::map` will map the attribute to the entity and also assign to a [set](attribute-set.html) and [group](attribute-group.html).


| Field | Description |
| ------| ------- |
| attribute_code| Specify the code for the attribute.|
| entity_code| Specify the entity code for the attibute.|
| backend_class| When specified will be used to add aditional control to the attribute when it intracts with the database.|
| backend_type| Specify the column type. Supports [types](../configuration.html#field-types).|
| backend_table| When specified it will store the data to the given. [DOC](../custom-table.html)|
| frontend_class| When specified will be used to add aditional control to the attribute when is used in the frontend.|
| frontend_type| Specify the type of html field.|
| frontend_label| Specify the label.|
| source_class|  When specified will be used to populate a field’s default options, if the frontend_type is `select`.|
| default_value| Specify the default value that will stored if not given.|
| is_required| If enabled, value needs to given for the attribute.|
| is_filterable| If enabled, attribute are included in the [faceted navigation](../facet.html).|
| required_validate_class| Custom validation rules.|


## Retrieve

To retrieve the attributes related to a entity.

```php
$entity = \Eav\Entity::findByCode('code');

$attributes = $entity->attributes;
```

To retrive single attribute 

```php
$sizeAttribute =  Eav\Attribute::findByCode('size', 'product')
```