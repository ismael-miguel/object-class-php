# Object class for php


Object class to create dynamic objects with hooks to magic methods.

##About

This class is a simple project that allows you to create dynamic objects.

You can do this by casting an array into an object:

    $obj = (object)array( 'a' => 'property' );

But If you do `echo $obj;`, you can expect ugly results.<br>
Also, this helps with polymorphism.

<hr>

This class was mainly created for 2 reasons:

- To help me with Javascript+PHP polyglots<br>
  Might be usefull, who knows?
- To allow me to create dynamic `object`s for other projects, but still fire the right magic methods.

##Supported methods

Currently, the following are supported:

- `__construct`
- `__get`
- `__set`
- `__isset`
- `__unset`
- `__toString`

Support for more may be added.

The supported magic methods will be kept in a different space than all the other properties.

The magic methods are in everything equal to the original methods, but the first argument is an `object` with all the properties.<br>
This is due to the limitation of the language itself, which doesn't allow to dynamically set the variable `$this`.<br>
This is only true for when you pass a function name or when you use `create_function` as a magic method.<br>
Usage of the `$this` variable is allowed and acts as a regular object within a `Closure`.

##Usage

Example of usage and showcasing some features:

	<?php

		$obj = new Object(array(
			'__construct'=>function(){echo 'constructor executed', PHP_EOL;},
			'__isset'=>function($k){
				echo 'checking key: ', $k, PHP_EOL;
				return isset($this->{$k});//must use the array `props`
			},
			'__unset'=>create_function('$t,$k','unset($t->{$k});')//'classic' way
			'__toString'=>function(){
				echo 'converting to string: ', PHP_EOL;
				return 'string';
			},
			'test'=>5
		));
	
		if( isset($obj->test) )
		{
			echo $obj->test;
		}
	
		echo PHP_EOL, $obj, PHP_EOL;
		
		/*
			Expected output:
			
			constructor executed
			checking key: test
			5
			converting to string: 
			string
			
		*/

##Requirements

- PHP 5.3 (to use with closures)
- function `trigger_error` must be enabled (logging isn't required)

##Methods

Full list of methods:

- `Object::keys( Object )`<br>
  Expects an element of type `Object`.<br>
  Returns an array with all the keys, or an empty array, running `array_keys` when appropriate.
