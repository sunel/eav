---
home: true
heroImage: /assets/img/front.png
heroText: Entity–attribute–value
tagline: model for Laravel Artisan
actionText: Get Started →
actionLink: /master/
footer: MIT Licensed | Copyright © 2018-present Sunel Tr
---

Entity–attribute–value model (EAV) is a data model to encode, in a space-efficient manner, entities where the number of attributes (properties, parameters) that can be used to describe them is potentially vast, but the number that will actually apply to a given entity is relatively modest.

> [wikipedia](https://en.wikipedia.org/wiki/Entity%E2%80%93attribute%E2%80%93value_model)

## When to Use

* Let us consider a situation where a number of different attributes can be used to describe an entity, but only few attributes actually apply to each one. One option is to create a table with a column representing each attribute; this is suitable for entity with a fixed number of attributes, where all or most attributes have values for a most objects. However, in our case we would end up with records where **majority of columns would be empty**, because attributes may be unknown or inapplicable. To solve the above problem you can apply the EAV (Entity, Attribute, Value) model.

* Have a loose schema that is likely to change over time. Table holding attributes describing an entity is not limited to a specific number of columns, meaning that it doesn't require a schema redesign every time new attribute needs to be introduced. The number of attributes can grow vertically as the database evolves, without the need for structure changes.

## Limitation of EAV

* In EAV model the entity data is more fragmented and so selecting an entire entity record requires multiple table joins. Check Flat Table.
