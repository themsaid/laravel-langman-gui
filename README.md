<h1 align="center">Laravel Language Manager</h1>

<p align="center">
Langman is a GUI for managing your JSON language files in a Laravel project.
<br>
<br>

<img src="https://s12.postimg.org/le9m20025/Screen_Shot_2017-04-25_at_10.10.17_AM.png" alt="Laravel Langman">
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

Once you have added the Service Provider point your browser to `http://project.dev/langman`, using this interface you'll be able to
browse translation keys in different languages, add/remove keys, scan your project files for missing translations, and finally save
your changes to the language JSON files.

<img src="https://s10.postimg.org/km3yx5a8p/Screen_Shot_2017-04-25_at_10.11.08_AM.png" alt="Laravel Langman">

<img src="https://s7.postimg.org/ca7ys45qj/Screen_Shot_2017-04-25_at_10.11.42_AM.png" alt="Laravel Langman">

## Backup

Langman stores a backup of your entire `resources/lang` directory into `storage/langmanGUI`, you can use restore the original files
using this backup in case anything went wrong.