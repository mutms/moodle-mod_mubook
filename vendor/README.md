# Bundled libraries of mod_mubook

This directory holds the third party PHP libraries used by mod_mubook, installed by Composer
and committed to the repository, so the plugin works on sites installed from a ZIP file as well
as on sites that manage plugins with Composer.

Libraries: league/commonmark with its dependencies, pomodocs/commonmark-alert and mutms/commonmark-extra.

## Why the Composer autoloader is not used

Never include `vendor/autoload.php` of this directory. Every Composer autoloader registers itself
in front of all other class loaders:

* the plugin then becomes the Composer root package for the rest of the request,
  `Composer\InstalledVersions::getRootPackage()` returns this directory instead of Moodle and
  core environment checks inspect the wrong installation,
* copies of packages that Moodle installs too (psr/*, symfony/*, ...) are loaded from here
  instead of from the Moodle vendor directory, possibly in a different version,
* the Composer runtime classes of two Composer versions get mixed.

Moodle 5.3 requires admins to run `composer install` in the Moodle root with production flags,
the Moodle vendor directory must stay the only Composer installation that PHP sees.

## How the libraries are loaded

`tool_mulib\local\vendor_loader::register()` reads the maps Composer generates in
`vendor/composer/autoload_classmap.php`, `autoload_psr4.php`, `autoload_namespaces.php` and
`autoload_files.php` and appends a plain class loader after all existing loaders:

* Moodle always wins, a class Moodle can load is never loaded from here,
* Composer runtime state (registered loaders, installed packages, root package) is not touched,
* autoloaded files are included once, shared with Composer through
  `$GLOBALS['__composer_autoload_files']`,
* registering the same directory again does nothing.

The plugin calls it right before the libraries are needed:

```php
\tool_mulib\local\vendor_loader::register(__DIR__ . '/../../vendor');
```

## Upgrading the libraries

Composer runs inside this directory, `composer.json` sets `"vendor-dir": "."`.

1. Check what is outdated:
   ```
   cd public/mod/mubook/vendor
   composer outdated
   ```
2. Update, always without development packages and with an optimised class map:
   ```
   composer update --no-dev --optimize-autoloader --no-plugins --no-scripts
   ```
   To allow a new major version edit the constraint in `composer.json` first.
3. Update the versions in `thirdpartylibs.xml` of the plugin, add or remove libraries there
   when the dependency tree changed (`composer show --tree`).
4. Remove files Composer does not need for runtime only when they are not referenced by the
   generated maps, the maps are the only thing the loader uses.
5. Run the plugin PHPUnit and Behat tests, then commit the whole directory including
   `composer.json`, `composer.lock`, `composer/` and this README.

The pomodocs/commonmark-alert and mutms/commonmark-extra packages come from the MuTMS forks listed in
the `repositories` section of `composer.json`, updating them needs SSH access to those GitHub
repositories.

Do not add the libraries to the Moodle root `composer.json` and do not require
`vendor/autoload.php` anywhere.
