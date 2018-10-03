# Usage

To create a [Entity](ideology/entity.html)

```bash
$ php artisan eav:make:entity product \\App\\Products 
```

Here ```product``` is the entity code and ```\\App\\Products``` is the model related to the entity.

This will create the ```Products``` Model file and the migration for the entity [ER](#er-diagram-for-entity)

The migration contains schema for creating different data type like `varchar`, `text`, `int`, `decimal`, `datetime`. We will also have schema to create default [attribute set](#attribute-set) `Default` and [attribute group](#attribute-group) `General`.


To create a [Attribute](ideology/attribute.html)

```bash
$ php artisan eav:make:attribute product --attributes sku:string,name:string,search:boolean,description:text 
```

Here ```sku:string,name:string,search:boolean,description:text``` are the attributes that needs to be added to  ```product``` entity.

This is will create the migration that is needed to create the attibute and map it to the entity. 

::: warning
**YOU NEED TO EDIT THE ATTRIBUTES INFO IN THE MIGRATION**

If the `type` are left empty it is considered as [Static Attributes](ideology/static-attribute.html)

Refer [Add Attribute](ideology/attribute.html#add) for more info.
:::



After editing run the migration.

```bash
$ php artisan migrate
```

Thats it, your EAV based model is ready now and you can start doing CRUD oporations.
