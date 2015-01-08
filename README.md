Laravel 5 - Asset Processor
========

[![Build Status](https://api.travis-ci.org/awjudd/l4-assetprocessor.png)](https://travis-ci.org/awjudd/l4-assetprocessor)
[![ProjectStatus](http://stillmaintained.com/awjudd/l4-assetprocessor.png)](http://stillmaintained.com/awjudd/l4-assetprocessor)

A quick and easy way to manage and process assets in **Laravel 5**

## Features

 - Easy to add assets to the project (able to add assets single files at a time or in a folder)
 - Supports asset pre-processing and generation for the following:
    - [LESS](http://lesscss.org/) (via leafo/lessphp)
    - [SASS](http://sass-lang.com/) (via leafo/scssphp)
    - [CoffeeScript](http://coffeescript.org/) (via coffeescript/coffeescript)
 - Supports asset minimizing:
    - CSS (via natxet/CssMin)
    - JavaScript (via werkint/jsmin)
 - Allows for asset bundling by type
 - Environment aware to allow for easier debugging
 - Assets are cached until changes are made to the file and then automatically updated
    - Users get a unique URL each build, so you never have to worry about user's seeing older cached assets
 - Easy to extend in order to add in your own processors
 - Built-in commands to help clean up files that are no longer being used

## Quick Start

In the `require` key of `composer.json` file add the following

```json
"awjudd/assetprocessor": "1.0.*"
```

Run the Composer update command

```
$ composer update
```

In your `config/app.php` add `'Awjudd\AssetProcessor\AssetProcessorServiceProvider'` to the end of the `$providers` array

```php
'providers' => array(

    'Illuminate\Foundation\Providers\ArtisanServiceProvider',
    'Illuminate\Auth\AuthServiceProvider',
    ...
    'Illuminate\Html\HtmlServiceProvider',
    'Awjudd\AssetProcessor\AssetProcessorServiceProvider',

),
```

Also update `aliases` part of the `config/app.php` to include `'Awjudd\AssetProcessor\Facades\AssetProcessorFacade'`.

```php
'aliases' => array(

    'App'             => 'Illuminate\Support\Facades\App',
    'Artisan'         => 'Illuminate\Support\Facades\Artisan',
    ...

    'HTML'      => 'Illuminate\Html\HtmlFacade',
    // To make this line, and your code even shorter, you could alias this to 'Asset' instead.
    'AssetProcessor'  => 'Awjudd\AssetProcessor\Facades\AssetProcessorFacade',
    
),
```

## Setup

### Controller Route

This plugin uses the storage folder fairly heavily while processing the assets, in order to get each step of the process, a route is needed in order to actually emit the generated assets (pointing at a controller in the package).

To add this in, add the following line of code to your `routes.php` file.

```php
Route::get('/assets/{type}/{name}', \Config::get('assetprocessor::config.controller.name') . '@' . \Config::get('assetprocessor::config.controller.method'));
```

### Publishing the Configuration

The next step to installing this plugin is to publish the configuration file by doing the following:

```
$ php artisan config:publish awjudd/assetprocessor
```

### Configuration Values

Once the configuration is published, you may need to update some parts of it to better suit how your application is set up.

Within the `'cache'` section of the `config.php` file, you may want to adjust the `'singular'` value.  By default it is set to `true`.  This will make it so that when you are in the live environment, all assets that have been added to the package (depending on the type) will be returned in a single file.  This will help in reducing the number of page requests done in an effort to help pages to render faster.  However, if you want to disable this you can by flipping the field to `false`.

The only other configuration values that one generally needs to be aware of are the ones in the `'enabled'` section.

```php
'enabled' => array(

    /**
     * What environments is this processing enabled in?  The environment name
     * here should match that from App::environment()
     * 
     * @var array
     */
    'environments' => array(
        'production'
    ),

    /**
     * Should we force the processor to process the files?
     * 
     * @var boolean
     */
    'force' => false,
),
```

Any environments that are set up in the `'environments'` section will automatically have all assets processed on them.  Any other environments will only process the files that need pre-processing (i.e. LESS, SASS and CoffeeScript) in order to be executed.  However, if you want to, you are able to force the processing of all of the files by flipping the `'force'` value to true.

### Adding CDN Assets

Need to use a file that is stored on a Content Delivery Network (CDN)?  No problem!  Just use:

```php
AssetProcessor::cdn('name', 'http://full/url/to/cdn')
```

It will automatically be emitted at the very beginning of the corresponding asset section.

### Adding Local Assets

Adding assets to use is fairly straightforward.  All you need to do is call the following:

```php
// Works with both files and directories
AssetProcessor::add('name', '/path/to/asset/file');
```

### Asset Auto-Loading

If you know that you need to assets that are automatically loaded on every page (i.e. assets that are core to your layout), then you can use the new section in the configuration file to load them.

```php
/**
 * Any assets that should be auto-loaded.
 * 
 * @var array
 */
'autoload' => array(

    /**
     * Any CDN-type assets to auto-load
     * 
     * @var array
     */
    'cdn' => array(
    ),

    /**
     * Any local assets that will need processing.
     * 
     * @var array
     */
    'local' => array(
    ),
),

```

### Emitting Assets

This will then automatically add in and determine the type of the file that was just loaded.  Once all of your assets are loaded into the plugin, in order to emit them to the browser (with the appropriate tags) you will need to call the following (in your view):

```php
{!! AssetProcessor::styles() !!}
{!! AssetProcessor::scripts() !!}
```

### Asset Groups

If you want to group a bunch of assets together, you are able to provide a third parameter to the `add` method.  The third parameter is an "attributes" array.  In order to use an asset group, you can provide the "group".  For example:

```php
AssetProcessor::add('name', '/path/to/asset/file', ['group' => 'foo']);
```

This will make the asset file provided only available in that asset group.  To retrieve the asset, you will need to do the following (assuming the file was a CSS file)s:

```php
{!! AssetProcessor::styles('foo') !!}
```

### File Caching

** All of the files that are generated are stored in the `app/storage/assets` folder by default **

Once an asset file is generated (for the last time), the file name it is given is a hash of the actual contents of the file.  This means that if the contents change, so does the file name.  Because of this, it will automatically force the browser to read the new asset instead of the old file (eliminating the need to have users to refresh cache).

Each step of the processing is also cached in order to help to reduce the overhead of regenerating the files.  This means that the file is only read IF the actual asset file has changed.

### External-Access File Caching

There were a few minor performance issues if you were to retrieve assets through the controller (caused by the fact that Laravel had to boot up), so now Asset Processor will be able to push files out into the web root (assuming you give the folder the proper permissions).  This will mean your pages will load a lot faster!

By default it writes to: `public_path('assets/generated')` however, you are able to modify this in the `'cache'` section of your configuration file.

```php
/**
 * The external path that is the final resting spot for the assets.
 * 
 * @var string
 */
'external' => public_path('assets/generated'),
```

### File Pre-Processing

In order to support the manual pre-processing of the asset files required, you are now able to use the following command:

```
$ php artisan assetprocessor:process
```

This accepts an array of files to process.  To run, use the following command:

```
$ php artisan assetprocessor:process --file=/path/to/file1.css --file=/path/to/file2.css
```

This will complete any processing that is required and then for all of the selected files, combine them into a singule file (by asset type).

### File Cleanup

As assets change, and so do the generated asset files.  Because of this there is a built-in cleanup command that will remove any assets which were not touched within the last day (duration can be changed in either the configuration variable, or the `--duration` flag that is provided).

In order to run the cleanup, all you need to do is run the following command:

```
$ php artisan assetprocessor:cleanup
```

Or if you want to change the duration:

```
$ php artisan assetprocessor:cleanup --duration=60
```

The previous command will make it so that it will remove any assets that were not touched within the last minute.

### Current Processors And Their Associated File Extensions

 - Coffeescript - coffee
 - JsMinifier - coffee, js
 - SCSS - scss
 - LESS - less
 - CSSMinifier - scss, less, css

## License

Asset Processor is free software distributed under the terms of the MIT license

## Additional Information

Any issues, please [report here](https://github.com/awjudd/l4-assetprocessor/issues)

## Release Notes:

1.0.0:
 - Initial Release

1.0.1:
 - Adding in a new command to allow for the pre-processing of asset files (assetprocessor:process)
 - Bug Fixes:
   - Windows support - resolving the issue where on Windows it wouldn't give you the correct link
   - File caching issue resolved

1.0.2:
 - Adding in support for asset groups
 ** Please Note: **
  This release adds an 'attributes' array into the configuration file.  You will need to re-publish the configuration files if you are using a previous version.

1.0.3:
 - Added in the processing of internal asset files to make them available when not doing any other processing on them.

1.0.4:
 - Changing arrays to use array() syntax
 - Bug fix which was introduced with the asset bundles (default value was erroring if nothing was provided)

1.0.5:
 -  Adding in better cache support for the assets

1.0.6:
 - CDN Support - You are now able to used the `AssetProcessor::cdn()` function in order to use a CDN instead of relying on the actual asset processor for everything
  - This section will automatically be emitted if it exists and you call the `AssetProcessor::scripts()` or `AssetProcessor::styles()` function with it's default values
 - Added a configuration value `'file.error-on-missing-group'` to allow you to bypass the error message if there is a missing asset group requested
 - Asset Auto-Loading - added in a new section to the config file which will allow you to specify any CDN/local assets to auto-load (cleans up your BaseController)
 - Asset Caching - added in the ability to have the assets available outside of your actual application, this will allow you to have assets available without booting up Laravel
