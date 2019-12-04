<?php

namespace NocturnalSm\Reportgen\Components\Page;

use NocturnalSm\Reportgen\Component;

class Header extends Component
{
	protected $height = 50;
	
	public function __construct($template, $data)
	{
		parent::__construct($template, $data, 5);
	}
	public function render($text){

	}
}