<?php

namespace NocturnalSm\Reportgen\Components;

use NocturnalSm\Reportgen\Component;
use NocturnalSm\Reportgen\Variables;
use NocturnalSm\Reportgen\Datasource;

class Header extends Component 
{	
	public function render()
	{
		$return = '<div class="report-title" style="width:100%;height:' .$this->height .'cm;" data-height="' .$this->height .'">';
		$header = $this->getDom();
		$headerExprs = $this->getExprs();
		$i = 0;
		foreach ($headerExprs as $expr){
					$key = $expr->getExpr();
					if (!$expr->isData())
							$str = $expr->render(Variables::get($key));
					else
							$str = $expr->render(isset(Datasource::getData()->current()->$key) ? Datasource::getData()->current()->$key : '');
					pq($header)->find('expr')->eq(0)->replaceWith($str);
					$i += 1;
		}
		$return .= pq($header)->html();
		$return .= '
					</div>
		';
		return $return;
	}
}