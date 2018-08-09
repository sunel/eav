# Static Attribute

Static attributes are attributes stored in the main table of an entity. Static attributes are always loaded and are useful especially if you want to retrieve information quickly or to optimize lookup of data.

If you want to use static attributes, you have to do 2 things in your migration script. First, you need to add a column to the main entity table, with the correct column definition. Next, you need to add the attribute using the `Eav\Attribute::add` method, and define your attribute as `'backend_type' => 'static'`. 