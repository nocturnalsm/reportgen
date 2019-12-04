<?php

namespace NocturnalSm\Reportgen;

class Datasource 
{
	private static $_db;
	private static $_data;	

	public static function query($query)
	{
		self::$_db = new dbSM("");
		self::$_data = self::$_db->query($query);
	}
	public static function getData() 
	{
		return self::$_data;
	}
	public static function setData($data)
	{				
		self::$_data = new ArrayIterator($data);
	}
}