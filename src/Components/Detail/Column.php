<?php

namespace NocturnalSm\Reportgen\Detail;

use NocturnalSm\Reportgen\Expression;

class Column extends Expression 
{
	public $width;
	public $header;
	public $class;	
	protected $height = 0.6;

	public function __construct($dom)
	{
			parent::__construct($dom);
			$this->width = pq($dom)->attr('width');
			$this->header = pq($dom)->attr('header');
			if (null != pq($dom)->attr('class')) $this->class = pq($dom)->attr('class');
	}
	public function getWidth()
	{
			return $this->width;
	}
	public function renderExpr($value)
	{
			$str = parent::render($value);
			$class = (isset($this->class)) ? $this->class ." " : "";
			$class .= ($this->type == 'number' || $this->type == 'currency') ? 'number' : "";
			return '<td style="height:' .$this->height .'cm;" width="' .$this->width .'%"'
							.(trim($class) != "" ? ' class="' .trim($class) .'"' : '') .'>'
							.$str
							.'</td>';
	}
}