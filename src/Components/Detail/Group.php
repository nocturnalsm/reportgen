<?php

namespace NocturnalSm\Reportgen\Components\Detail;

use NocturnalSm\Reportgen\Component;
use NocturnalSm\Reportgen\Component\Detail\GroupHeader;
use NocturnalSm\Reportgen\Component\Detail\GroupFooter;

class Group extends Component 
{
	protected $footer;
	protected $header;
	protected $name;
	protected $groupBy;

	public function __construct($dom, $height)
	{
		parent::__construct($dom, $height);
		$name = pq($dom)->attr("name");
		if (isset($name) && $name != "") $this->name = $name;
		$this->groupBy = pq($dom)->attr("by");
	}
	public function scan()
	{
		$header = pq($this->dom)->find("> header");
		if (pq($header)->html() != ""){
				$this->header = new GroupHeader($header);
				$this->header->scan();
		}
		$footer = pq($this->dom)->find("> footer");
		if (pq($footer)->html() != "") {
				$this->footer = new GroupFooter($footer);
				$this->footer->scan();
		}
	}
	public function getGroupBy()
	{
		return $this->groupBy;
	}
	public function getHeader()
	{
		return $this->header;
	}
	public function getFooter()
	{
		return $this->footer;
	}
}