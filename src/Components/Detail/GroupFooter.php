<?php

namespace NocturnalSm\Reportgen\Components\Detail;

use NocturnalSm\Reportgen\Components\Footer;
use NocturnalSm\Reportgen\Variables;

class GroupFooter extends Footer 
{
	protected $height = 20;
	private $runningValue;

	public function resetCalcFields()
	{
		$this->calcFields = Array();
	}
	public function setRunningValue($value)
	{
		$this->runningValue = $value;
	}
	public function render()
	{
		$i = 0;
		$html = phpQuery::newDocument(pq($this->dom)->html());
		foreach ($this->exprs as $expr){
				if ($expr->isData()){
					if (get_class($expr) == "Summary")
							$str = $expr->render($this->calcFields[$i]);
				}
				else {
						if ($expr->getExpr() == "GROUP_RUNNING")
							$str = $expr->render($this->runningValue);
						else
							$str = $expr->render(Variables::get($expr->getExpr()));
				}
				pq($html)->find('expr')->eq(0)->replaceWith($str);
				$i += 1;
		}
		return pq($html)->html();
	}
}