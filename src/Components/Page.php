<?php

namespace NocturnalSm\Reportgen\Components;

use NocturnalSm\Reportgen\Component;
use NocturnalSm\Reportgen\Components\Detail;
use NocturnalSm\Reportgen\Components\Page\Header;
use NocturnalSm\Reportgen\Components\Page\Footer;

class Page extends Component 
{
	private $header;
	private $details;
	private $footer;

	function __construct($dom)
	{
		parent::__construct($dom, 21);
	}
	function scan()
	{
		// find details, required
		$pageDetails = pq($this->dom)->find('detail');
		if (count($pageDetails) == 0) {
			return "DETAIL tag is required";
		}
		// find page footer -- only one is allowed
		$pageFooter = pq($this->dom)->find('>footer');
		if (count($pageFooter) > 1) {
			return "Only one PAGE FOOTER tag is allowed";
		}
		$i = 0;
		syslog(LOG_INFO, "Check error:");
		syslog(LOG_INFO, $pageDetails);
		foreach ($pageDetails as $dets){
			$this->details[] = new Detail($dets);
			$this->details[$i]->scan();
			$i += 1;
		}
		if (count($pageFooter) == 1){
			$this->footer = new Footer($pageFooter);
			$this->footer->scan();
		}
		else {
			$this->footer = new Footer(null);
		}
		return TRUE;
	}
	function hasFooter()
	{
		return $this->footer;
	}
	function hasHeader()
	{
		return $this->header;
	}
	function getFooter()
	{
		return $this->footer;
	}
	function getHeader()
	{
		return $this->header;
	}
	function getDetails($index = NULL)
	{
		if (!isset($index)){
			return $this->details;
		}
		else {
			return $this->details[$index];
		}
	}
}