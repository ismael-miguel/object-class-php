<?php

	class Object {
		private $props = array();
		private $methods = array(
			'__construct'=>null,
			'__get'=>null,
			'__set'=>null,
			'__isset'=>null,
			'__unset'=>null,
			'__toString'=>null
		);
		private function _run_magic_method( $method, $args=null )
		{
			if( is_a( $this->methods[$method], 'Closure' ) )
			{
				return call_user_func_array($this->methods[$method], (array)$args );
			}
			else
			{
				$obj = (object)$this->props;
				$return = call_user_func_array( $this->methods[$method], array_merge( array(&$obj), (array)$args ) );
				$this->props = (array)$obj;
				return $return;
			}
		}
		private function _has_magic_method( $method )
		{
			return isset( $this->methods[$method] ) && is_callable( $this->methods[$method] );
		}
		function __construct( array $props )
		{
			foreach( array_keys( $this->methods ) as $method )
			{
				if( isset( $props[$method] ) )
				{
					if( is_a( $props[$method], 'Closure' ) )
					{
						$this->methods[$method] = $props[$method]->bindTo( $this, $this );
					}
					else
					{
						$this->methods[$method] = $props[$method];
					}
					unset( $props[$method] );
				}
			}
			$this->props = $props;
			if( $this->_has_magic_method( '__construct' ) )
			{
				$this->_run_magic_method( '__construct' );
			}
		}
		function __get( $key )
		{
			if($this->_has_magic_method('__get' ) )
			{
				return $this->_run_magic_method( '__get', array($key) );
			}
			else
			{
				return $this->props[$key];
			}
		}
		function __set( $key, $value )
		{
			if( $this->_has_magic_method( '__set' ) )
			{
				$this->_run_magic_method( '__set', array( $key, $value ) );
			}
			else
			{
				$this->props[$key] = $value;
			}
		}
		function __isset( $key )
		{
			if( $this->_has_magic_method( '__isset' ) )
			{
				return $this->_run_magic_method( '__isset', array($key) );
			}
			else
			{
				return isset( $this->props[$key] );
			}
		}
		function __unset( $key )
		{
			if( $this->_has_magic_method( '__unset' ) )
			{
				return $this->_run_magic_method( '__unset', array($key) );
			}
			else
			{
				unset( $this->props[$key] );
			}
		}
		function __call( $key, $args )
		{
			if( isset( $this->props[$key] ) && is_callable( $this->props[$key] ) )
			{
				return call_user_func_array( $this->props[$key], $args );
			}
			else
			{
				trigger_error( 'The key "'.addslashes($key).'" is not a callable', E_USER_ERROR );
			}
		}
		function __toString( )
		{
			if( $this->_has_magic_method( '__toString' ) )
			{
				return $this->_run_magic_method( '__toString' ) . '';
			}
			else
			{
				trigger_error( 'There is no defined __toString method', E_USER_ERROR );
			}
		}
		private function _keys()
		{
			return array_keys( $this->props );
		}
		static function keys( Object $obj=null )
		{
			if( $obj === null )
			{
				return array();
			}
			else
			{
				return $obj->_keys();
			}
		}
	}
