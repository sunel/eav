# Installation

Via [composer](http://getcomposer.org):

```bash
$ composer require sunel/eav
```

You'll need to register the service provider, in your `config/app.php`:

```php
'providers' => [
	...
	Eav\Providers\LaravelServiceProvider::class,
]
```


## Api

You'll need to register the service provider if you need to use the api, in your `config/app.php`:

```php
'providers' => [
    ...
    Eav\Api\ServiceProvider::class,
]
```