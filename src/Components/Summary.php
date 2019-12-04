<?php

namespace NocturnalSm\Reportgen\Components;

use NocturnalSm\Reportgen\Expression;

class Summary extends Expression 
{
	protected $height = 20;
	private $function;
	public function __construct($dom)
	{
			parent::__construct($dom);
			$this->function = pq($dom)->attr('function');
	}
	public function getFunction()
	{
			return $this->function;
	}
}