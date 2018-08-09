# Value

Value refers to the actual value of the attribute of the entity. Like color has value red, price has value $25, etc.

The value are stored in tables corresponding to the data types such as `product_varchar, product_int, product_decimal, product_datetime, product_text`.

```php
$product = Products::all(['attr.*']);

$product->name
$product->sku
$product->upc
$product->description
```