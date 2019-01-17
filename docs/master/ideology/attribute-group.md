# Attribute group

[[toc]]

The attributes are organized into groups.

`General` is the group that is create initialy.


The Attributes Group are attached to the Set through relation.

```php
public function groups()
{
    return $this->hasMany(AttributeGroup::class, 'attribute_set_id');
}
```

The Attributes are attached to the Group through relation.

```php
public function attributes()
{
    return $this->hasManyThrough(
    	Attribute::class, EntityAttribute::class, 
    	'attribute_group_id', 'attribute_id'
    );
}
```

## Add

To create a new group

```php
Eav\AttributeGroup::create([
    'attribute_group_name' => 'Metadata',
    'attribute_set_id' => $attributeSet->attribute_set_id,
]);
```

A group should be attached to a Set.

## Retrive

To retrieve the group related to the set.

```php
$entity = Eav\Entity::findByCode('code');

$sets = $entity->attributeSet;

$groups = $sets->first()->attributeGroup;
```

To retrieve the attributes related to the group.

```php
$entity = Eav\Entity::findByCode('code');

$sets = $entity->attributeSet;

$groups = $sets->first()->attributeGroup;

$groups->first()->attributes
```