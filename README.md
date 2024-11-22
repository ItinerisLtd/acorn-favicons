# Itineris Acorn Example Package

Based off the [acorn example package](https://github.com/roots/acorn-example-package).
To setup for yourself run a search and replace for:
```
ExamplePackage
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
composer require itinerisltd/example-package
```

You can publish the config file with:

```shell
$ wp acorn vendor:publish --provider="ItinerisLtd\ExamplePackage\Providers\ExampleServiceProvider"
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
