<?php

namespace NocturnalSm\Reportgen;

class Variables 
{
	private static $_variables = Array();

	public static function write($key, $value) 
	{
      self::$_variables[$key] = $value;
  	}
	public static function get($key) 
	{
		return self::$_variables[$key];
	}
}