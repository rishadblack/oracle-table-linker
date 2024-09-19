# Oracle Table Linker

**Oracle Table Linker** is a Laravel package specifically designed for managing database-linked tables and aliases in Oracle databases. This package provides a convenient trait that simplifies the handling of table names and aliases, ensuring seamless integration with Laravel models.

## Description

Oracle Table Linker enhances Laravel's capabilities by allowing developers to manage database-linked tables more efficiently. The package provides a trait, `HasDbLink`, which handles dynamic aliasing and first-call handling. This feature is particularly useful for Oracle databases where table names may include database links.

## Features

-   **Dynamic Aliasing**: Automatically handles table aliasing based on database link presence.
-   **First-Call Handling**: Manages the first call to ensure proper alias usage.
-   **Easy Integration**: Easily integrates with Laravel models using a simple Artisan command or manual addition.

## Installation

### Via Composer

To install Oracle Table Linker, use Composer to add it to your Laravel project. Run the following command:

```bash
composer require rishadblack/oracle-table-linker
```

### Usage

## Automatic Package Discovery

This package uses Laravel's automatic package discovery, so no additional configuration is required. Once installed, it will be automatically discovered and registered in your Laravel application.
Adding the Trait to a Model

You can add the HasDbLink trait to your model either manually or using the Artisan command.

## Manually

To add the trait manually, include the following line at the top of your model file:

```php
use Rishadblack\OracleTableLinker\Traits\HasDbLink;
```

Then, add the HasDbLink trait to the class:

```php

class YourModel extends Model
{
    use HasDbLink;
}
```

## Using Artisan Command

You can also use the Artisan command to automatically add the trait to your model. Run the following command:

```bash

php artisan model:dblink YourModel
```

Replace YourModel with the name of your model. This command will add the HasDbLink trait to the specified model.
