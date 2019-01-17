# Attribute Set

[[toc]]

Its used as a template for the enity. The attribute set determines the fields that are available during data entry, and the values that appear when retrieved.

`Default` is the set that is create initialy.

A set is created while mapping a attribute to a entity. If a set already exists it will be used or else created. Attribute set will be unique for entity. A Entity can have many set's.

The Attributes Sets are attached to the entity through relation.

```php
public function sets()
{
    return $this->hasMany(AttributeSet::class, 'entity_id');
}
```

The Attributes are attached to the Set through relation.

```php
public function attributes()
{
    return $this->hasManyThrough(
    	Attribute::class, EntityAttribute::class, 
    	'attribute_set_id', 'attribute_id'
    );
}
```

## Add

To create a new set

```php
$entity = Eav\Entity::findByCode('code');

Eav\AttributeSet::create([
    'attribute_set_name' => 'kids_clothing',
    'entity_id' => $entity->entity_id,
]);
```

## Retrive

To retrive the set related to a entity.

```php
$entity = Eav\Entity::findByCode('code');

$sets = $entity->attributeSet;

```
To retrieve the attributes related to the set.

```php
$sets = $entity->attributeSet;

$sets->first()->attributes;
```