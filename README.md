# Itineris Acorn Example Package

Based off the [acorn example package](https://github.com/roots/acorn-acorn-favicons).
To setup for yourself run a search and replace for:
```
AcornFavicons
```
- Changing it to your package name.

```shell
$ mv src/Example.php src/YourPackageName.php
```

```shell
$ mv src/Providers/ExampleServiceProvider.php src/Providers/YourPackageNameServiceProvider.php
```

## Installation

You can install this package with Composer:

```bash
composer require itinerisltd/acorn-favicons
```

You can publish the config file with:

```shell
$ wp acorn vendor:publish --provider="ItinerisLtd\AcornFavicons\Providers\ExampleServiceProvider"
```

## Usage

From a Blade template:

```blade
@include('Example::example')
```

From WP-CLI:

```shell
$ wp acorn example
```
