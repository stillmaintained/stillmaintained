[![Build Status](https://travis-ci.org/PLUGIN_USER/whoops.png?branch=master)](https://travis-ci.org/PLUGIN_USER/whoops) [![Coverage Status](https://coveralls.io/repos/PLUGIN_USER/whoops/badge.png?branch=master)](https://coveralls.io/r/PLUGIN_USER/whoops?branch=master) [![Total Downloads](https://poser.pugx.org/PLUGIN_USER/whoops/d/total.png)](https://packagist.org/packages/PLUGIN_USER/whoops) [![Latest Stable Version](https://poser.pugx.org/PLUGIN_USER/whoops/v/stable.png)](https://packagist.org/packages/PLUGIN_USER/whoops)

# Whoops

Built to seamlessly integrate [Whoops][whoops] with [CakePHP][cakephp].

__This is an unstable repository and should be treated as an alpha.__

## Requirements

* CakePHP 3.x

## Install

Add the plugin to your project's `composer.json` - something like this:

```json
{
	"require": {
		"gourmet/whoops": "*"
	}
}
```

Because this plugin has the type `cakephp-plugin` set in its own `composer.json`,
[Composer][composer] will install it inside your /Plugins directory, rather than
in your `vendor-dir`. It is recommended that you add /Plugins/Whoops to your
`.gitignore` file and here's [why][composer:ignore].

As this plugin only offers a Whoops handler for CakePHP, there is no need to
enable it per se. You only need to configure that handler instead of Cake's own
`ErrorHandler`:

```php
(new \Gourmet\Whoops\Error\WhoopsHandler([
    'errorLevel' => E_ALL & ~E_DEPRECATED,
    'exceptionRenderer' => 'Cake\Error\ExceptionRenderer',
    'skipLog' => [],
    'log' => true,
    'trace' => true,
]));
```

That's it!

## License

Copyright (c) 2014, Jad Bitar and licensed under [The MIT License][mit].

[cakephp]:http://cakephp.org
[composer]:http://getcomposer.org
[composer:ignore]:http://getcomposer.org/doc/faqs/should-i-commit-the-dependencies-in-my-vendor-directory.md
[mit]:http://www.opensource.org/licenses/mit-license.php
[whoops]:http://filp.github.io/whoops/
