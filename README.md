# laravel-html-purifier
An HTML Purifier for Laravel

[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[![Software License][ico-license]](LICENSE)
[![Packagist](https://img.shields.io/packagist/v/hdvinnie/laravel-html-purifier)](https://packagist.org/packages/hdvinnie/laravel-html-purifier/)
[![Packagist Downloads](https://img.shields.io/packagist/dm/hdvinnie/laravel-html-purifier.svg?label=packagist%20downloads)](https://packagist.org/packages/hdvinnie/laravel-html-purifier)

A simple [Laravel](http://www.laravel.com/) service provider for easily using [HTMLPurifier](http://htmlpurifier.org/) inside Laravel. From their website:

> HTML Purifier is a standards-compliant HTML filter library written in PHP. HTML Purifier will not only remove all malicious code (better known as XSS) with a thoroughly audited, secure yet permissive whitelist, it will also make sure your documents are standards compliant, something only achievable with a comprehensive knowledge of W3C's specifications. Tired of using BBCode due to the current landscape of deficient or insecure HTML filters? Have a WYSIWYG editor but never been able to use it? Looking for high-quality, standards-compliant, open-source components for that application you're building? HTML Purifier is for you!

## Installation

### For Laravel 5.5+

Require this package with composer:
```
composer require hdvinnie/laravel-html-purifier
```

The service provider will be auto-discovered. You do not need to add the provider anywhere.

### For Laravel 5.0 to 5.4

Require this package with composer:
```
composer require hdvinnie/laravel-html-purifier
```

Find the `providers` key in `config/app.php` and register the HTMLPurifier Service Provider.

```php
    'providers' => [
        // ...
        HDVinnie\Purifier\PurifierServiceProvider::class,
    ]
```

Find the `aliases` key in `config/app.php` and register the Purifier alias.

```php
    'aliases' => [
        // ...
        'Purifier' => HDVinnie\Purifier\Facades\Purifier::class,
    ]
```

## Usage


Use these methods inside your requests or middleware, wherever you need the HTML cleaned up:

```php
\clean(Input::get('inputname'));
```
or

```php
Purifier::clean(Input::get('inputname'));
```

dynamic config
```php
\clean('This is my H1 title', 'titles');
\clean('This is my H1 title', array('Attr.EnableID' => true));
```
or

```php
Purifier::clean('This is my H1 title', 'titles');
Purifier::clean('This is my H1 title', array('Attr.EnableID' => true));
```

use [URI filter](http://htmlpurifier.org/docs/enduser-uri-filter.html)

```php
Purifier::clean('This is my H1 title', 'titles', function (HTMLPurifier_Config $config) {
    $uri = $config->getDefinition('URI');
    $uri->addFilter(new HTMLPurifier_URIFilter_NameOfFilter(), $config);
});
```

Alternatively, in Laravel 7+, if you're looking to clean your HTML inside your Eloquent models, you can use our custom casts:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use HDVinnie\Purifier\Casts\CleanHtml;
use HDVinnie\Purifier\Casts\CleanHtmlInput;
use HDVinnie\Purifier\Casts\CleanHtmlOutput;

class User extends Model
{
    protected $casts = [
        'bio'            => CleanHtml::class, // cleans both when getting and setting the value
        'description'    => CleanHtmlInput::class, // cleans when setting the value
        'history'        => CleanHtmlOutput::class, // cleans when getting the value
    ];
}
```

## Configuration

To use your own settings, publish config.

```
php artisan vendor:publish --provider="HDVinnie\Purifier\PurifierServiceProvider"
```

Config file `config/purifier.php` should like this

```php

return [
    'encoding'           => 'UTF-8',
    'finalize'           => true,
    'ignoreNonStrings'   => false,
    'cachePath'          => storage_path('app/purifier'),
    'cacheFileMode'      => 0755,
    'settings'      => [
        'default' => [
            'HTML.Doctype'             => 'HTML 4.01 Transitional',
            'HTML.Allowed'             => 'div,b,strong,i,em,u,a[href|title],ul,ol,li,p[style],br,span[style],img[width|height|alt|src]',
            'CSS.AllowedProperties'    => 'font,font-size,font-weight,font-style,font-family,text-decoration,padding-left,color,background-color,text-align',
            'AutoFormat.AutoParagraph' => true,
            'AutoFormat.RemoveEmpty'   => true,
        ],
        'test'    => [
            'Attr.EnableID' => 'true',
        ],
        "youtube" => [
            "HTML.SafeIframe"      => 'true',
            "URI.SafeIframeRegexp" => "%^(http://|https://|//)(www.youtube.com/embed/|player.vimeo.com/video/)%",
        ],
        'custom_definition' => [
            'id'  => 'html5-definitions',
            'rev' => 1,
            'debug' => false,
            'elements' => [
                // http://developers.whatwg.org/sections.html
                ['section', 'Block', 'Flow', 'Common'],
                ['nav',     'Block', 'Flow', 'Common'],
                ['article', 'Block', 'Flow', 'Common'],
                ['aside',   'Block', 'Flow', 'Common'],
                ['header',  'Block', 'Flow', 'Common'],
                ['footer',  'Block', 'Flow', 'Common'],
				
				// Content model actually excludes several tags, not modelled here
                ['address', 'Block', 'Flow', 'Common'],
                ['hgroup', 'Block', 'Required: h1 | h2 | h3 | h4 | h5 | h6', 'Common'],
				
				// http://developers.whatwg.org/grouping-content.html
                ['figure', 'Block', 'Optional: (figcaption, Flow) | (Flow, figcaption) | Flow', 'Common'],
                ['figcaption', 'Inline', 'Flow', 'Common'],
				
				// http://developers.whatwg.org/the-video-element.html#the-video-element
                ['video', 'Block', 'Optional: (source, Flow) | (Flow, source) | Flow', 'Common', [
                    'src' => 'URI',
					'type' => 'Text',
					'width' => 'Length',
					'height' => 'Length',
					'poster' => 'URI',
					'preload' => 'Enum#auto,metadata,none',
					'controls' => 'Bool',
                ]],
                ['source', 'Block', 'Flow', 'Common', [
					'src' => 'URI',
					'type' => 'Text',
                ]],

				// http://developers.whatwg.org/text-level-semantics.html
                ['s',    'Inline', 'Inline', 'Common'],
                ['var',  'Inline', 'Inline', 'Common'],
                ['sub',  'Inline', 'Inline', 'Common'],
                ['sup',  'Inline', 'Inline', 'Common'],
                ['mark', 'Inline', 'Inline', 'Common'],
                ['wbr',  'Inline', 'Empty', 'Core'],
				
				// http://developers.whatwg.org/edits.html
                ['ins', 'Block', 'Flow', 'Common', ['cite' => 'URI', 'datetime' => 'CDATA']],
                ['del', 'Block', 'Flow', 'Common', ['cite' => 'URI', 'datetime' => 'CDATA']],
            ],
            'attributes' => [
                ['iframe', 'allowfullscreen', 'Bool'],
                ['table', 'height', 'Text'],
                ['td', 'border', 'Text'],
                ['th', 'border', 'Text'],
                ['tr', 'width', 'Text'],
                ['tr', 'height', 'Text'],
                ['tr', 'border', 'Text'],
            ],
        ],
        'custom_attributes' => [
            ['a', 'target', 'Enum#_blank,_self,_target,_top'],
        ],
        'custom_elements' => [
            ['u', 'Inline', 'Inline', 'Common'],
        ],
    ],

];
```
