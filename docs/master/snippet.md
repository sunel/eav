# Snippet

[[toc]]

## Custom Query

```php
$products = Products::select('*');

$attribute = Eav\Attribute::findByCode('inventory', 'product');
$attribute->setEntity($products->baseEntity());

// Joining the attribute to the query, by default it will use inner join.
// In this case we need a left join.
$attribute->addAttributeJoin($products->getQuery(), 'left');

$attributeColumn = $attribute->getRawSelectColumn(); // inventory_attr.value

$products->selectRaw("COUNT({$attributeColumn}) as total");

$products->get();
```