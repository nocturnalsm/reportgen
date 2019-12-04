<?php

namespace NocturnalSm\Reportgen;

use NocturnalSm\Reportgen\Expression;

class Component 
{
	protected $height = 5;
	protected $exprs;
	protected $dom;
	protected $name;

	function __construct($dom, $height = 5)
	{
		$this->dom = $dom;
		$this->height = $height;
		if (null != pq($dom)->attr('name')) $this->name = pq($dom)->attr('name');
	}
	function getName()
	{
		return $this->name;
	}		
	function scan()
	{
		foreach (pq($this->dom)->find('expr') as $expr){
			$this->exprs[] = new Expression($expr);
		}
	}
	function getExprs($index = NULL)
	{
		if (!isset($index)){
			return $this->exprs;
		}
		else {
			return $this->exprs[$index];
		}
	}
	function getExprByName($name)
	{
		foreach ($this->exprs as $expr){
			if ($expr->getName() == $name){
				return $expr;
			}
		}
	}
	function setHeight($height)
	{
		$this->height = $height;
	}
	function getHeight()
	{
		return $this->height;
	}
	function getDom()
	{
		return $this->dom;
	}
}