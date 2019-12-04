<?php

namespace NocturnalSm\Reportgen;

class Expression 
{
	private $format;
	protected $type;
	protected $expr;
	protected $name;
	protected $height;
	private $dom;
	private $data = FALSE;
	protected $onRender;

	function __construct($dom)
	{
		$this->dom = $dom;
		$this->type = (null != pq($dom)->attr('type')) ? pq($dom)->attr('type') : "text";
		if (null != pq($dom)->attr('name')) $this->name = pq($dom)->attr('name');
		if (null != pq($dom)->attr('format')) $this->format = pq($dom)->attr('format');
		if (null != pq($dom)->attr('value')) $this->expr = pq($dom)->attr('value');
		if (null != pq($dom)->attr('data')){
				$this->data = TRUE;
				$this->expr = pq($dom)->attr('data');
		}
	}
	function isData()
	{
		return $this->data;
	}
	function getDom()
	{
		return $this->dom;
	}
	function getName()
	{
		return $this->name;
	}
	function getExpr()
	{
		return $this->expr;
	}
	function getHeight()
	{
		return $this->height;
	}
	function getType()
	{
		return $this->type;
	}
	function setOnRender($function)
	{
		$this->onRender =$function;
	}
	function render($value){
		if ($this->type == "text"){
			if ($this->format == "uppercase")
				return strtoupper($value);
			else if ($this->format == "lowercase")
				return strtolower($value);
			else
				return $value;
		}
		else if ($this->type == "date"){
			if ($this->format)
				return Date($this->format, strtotime($value));
			else
				return Date("d M Y", strtotime($value));
		}
		else if ($this->type == "number" || $this->type == "currency"){
			$decimal = null != pq($this->dom)->attr('decimal') ? pq($this->dom)->attr('decimal') : 2;
			if (isset($this->symbol))
				return $symbol ." " .number_format($value, $decimal,".",",");
			else {						
				return number_format(floatval($value), $decimal, ".",",");
			}
		}
		else if ($this->type == "custom"){
			if (is_callable($this->onRender)){
					return call_user_func($this->onRender, $value);
			}
		}
	}
}