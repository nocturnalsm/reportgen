<?php

namespace NocturnalSm\Reportgen\Components;

use NocturnalSm\Reportgen\Component;
use NocturnalSm\Reportgen\Variables;
use NocturnalSm\Reportgen\Datasource;

class Footer extends Component 
{		
		
	public function render()
	{
		$return = '<div class="report-footer" style="margin-top:0.2cm;height:' .$this->height .'cm;" data-height="' .$this->height .'">';
		$footer = $this->getDom();
		$footerExprs = $this->getExprs();
		$i = 0;
		foreach ($footerExprs as $expr){
			$key = $expr->getExpr();
			if (!$expr->isData())
					$str = $expr->render(Variables::get($key));
			else
					$str = $expr->render(isset(Datasource::getData()->current()->$key) ? Datasource::getData()->current()->$key : '');
			pq($footer)->find('expr')->eq(0)->replaceWith($str);
			$i += 1;
		}
		$return .= pq($footer)->html();
		$return .= '
					</div>
		';
		return $return;
	}
}