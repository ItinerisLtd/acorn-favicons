# Acorn Favicons

## Installation

You can install this package with Composer:

```bash
composer require itinerisltd/acorn-favicons
```

You can publish the PHP and JSON config files with:

```shell
$ wp acorn vendor:publish --provider="ItinerisLtd\AcornFavicons\Providers\AcornFaviconsServiceProvider"
```

## Generating required favicons
To generate the full range of favicons, use https://github.com/ItinerisLtd/bin/pull/98
Just provide an SVG file and the script will convert it to all the required formats.
