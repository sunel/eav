# Custom Table or New Field Type

To register new Field Type or to store data in Custom Table you can create the Schema as follows.

```php
Schema::create('[field_type_table_name]', function (Blueprint $table) {
    $table->increments('value_id')->comment('Value ID');
    $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
    $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
    $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
    
    $table->[FILED_TYPE]('value')->nullable()->comment('Value'); // update the type

    // Any additional fields
    // ....

    
    $table->foreign('entity_id')
          ->references('id')->on('[ENTITY]') // changes this (this is main entity table )
          ->onDelete('cascade');
    
    $table->unique(['entity_id','attribute_id']);
    $table->index('attribute_id');
    $table->index('entity_id');           
});
```
In case of Custom Table,

In the Attribute Migration file you can find `backend_table`, it is empty. If you provide a table name, it will store the value in that table.

```php
Eav\Attribute::add([
    'attribute_code' => 'inventory',
    'entity_code' => 'product',
    'backend_class' => null,
    'backend_type' => 'int',
    'backend_table' =>  'product_stock', // Custom Table
    'frontend_class' =>  null,
    'frontend_type' => 'input',
    'frontend_label' => 'Inventory',
    'source_class' =>  null,
    'default_value' => 0,
    'is_required' => 0,
    'is_filterable' => 0,
    'required_validate_class' =>  null
]);

Eav\EntityAttribute::map([
    'attribute_code' => 'inventory',
    'entity_code' => 'product',
    'attribute_set' => 'Default',
    'attribute_group' => 'General'
]);
```