<h1 align="center">Laravel Language Manager</h1>

<p align="center">
Langman is a GUI for managing your JSON language files in a Laravel project.
<br>
<br>

<img src="https://s16.postimg.org/eevhxpl2d/Screen_Shot_2017-04-25_at_9.47.48_AM.png" alt="Laravel Langman">
</p>

## Installation

Begin by installing the package through Composer. Run the following command in your terminal:

```
$ composer require themsaid/laravel-langman-gui
```

Once done, add the following line in your providers array of `config/app.php`:

```php
Themsaid\LangmanGUI\LangmanServiceProvider::class
```

Then publish the configuration file:

```
php artisan vendor:publish --provider="Themsaid\LangmanGUI\LangmanServiceProvider"
```

## Usage

