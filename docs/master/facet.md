# Faceted search

## Introduction

Faceted search is a technique which involves augmenting traditional search techniques with a faceted navigation system, allowing users to narrow down search results by applying multiple filters based on faceted classification of the items.


For a attribute to be qualified in faceted list the `is_filterable` must be set to `1`.


You can directly get the Facets from the query builder.

```php
$search = Products::whereAttribute('upc', 'like', 'SHNDUU%')
	->whereAttribute('color', 'like', 'Green%')
	->whereAttribute('size', '=', 's')
	->select(['*','color']);

$result = $search->get();
$facets = $search->getFacets();
```
Result of the facets.

```json
{
	"search": {
		"0": {
			"value":0,
			"label":"No"
		},
		"1": {
			"value":1,
			"label":"Yes"
		}
	},
	"size": {
		"s": {
			"value":"s",
			"label":"Small"
		},
		"m": {
			"value":"m",
			"label":"Medium"
		},
		"l": {
			"value":"l",
			"label":"Large"
		}
	}
}
```

## With Count

If you want the count for the values.

```php
$facets = $search->getFacets(true);
```

```json
{
	"search": {
		"1": {
			"value":1,
			"label":"Yes",
			"count":14
		}
	}
}
```


