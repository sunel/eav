# Dashboard

- [Introduction](#introduction)
- [Upgrading Dashboard](#upgrading)
- [Installation](#installation)
    - [Configuration](#configuration)
    - [Dashboard Authorization](#dashboard-authorization)

<a name="introduction"></a>
## Introduction

EAV Dashboard provides a beautiful dashboard for your EAV package. Dashboard allows you to easily add, update, sort and remove attributes, set, group.

<p align="center">
<iframe width="560" height="315" src="https://www.youtube.com/embed/YK7XAKwXelA" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
</p>
<p><a href="https://www.youtube.com/watch?v=YK7XAKwXelA" target="_blank">&quot;Eav Dashboard&quot; Preview</a> on YouTube.</p>

<a name="installation"></a>
## Installation

You may use Composer to install Dashboard into your Laravel project:
```bash
composer require sunel/eav-dashboard
```
After installing Dashboard, publish its assets using the `eav-dash:publish` Artisan command:
```bash
php artisan eav-dash:publish
```

::: warning
**YOU NEED TO ADD THE API SERVICE PROVIDER**

```php
'providers' => [
    ...
    Eav\Api\ServiceProvider::class,
]
```
:::

<a name="upgrading"></a>
#### Upgrading Dashboard

When upgrading you should re-publish Dashboard's assets:
```bash
php artisan eav-dash:publish --clean
```
<a name="configuration"></a>
### Configuration

> Yet to be added

<a name="dashboard-authorization"></a>
### Dashboard Authorization

EAV Dashboard exposes a dashboard at `/eav/dashboard/`. By default, you will only be able to access this dashboard in the `local` environment. Within your `app/Providers/EAVDashboardServiceProvider.php` file, there is a `gate` method. This authorization gate controls access to Dashboard in **non-local** environments. You are free to modify this gate as needed to restrict access to your Dashboard installation:

```php
    /**
     * Register the Dashboard gate.
     *
     * This gate determines who can access Dashboard in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewEavDash', function ($user) {
            return in_array($user->email, [
                'sunelbe@gmail.com',
            ]);
        });
    }
```