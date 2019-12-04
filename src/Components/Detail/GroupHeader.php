<?php

namespace NocturnalSm\Reportgen\Components\Detail;

use NocturnalSm\Reportgen\Component;
use NocturnalSm\Reportgen\Variables;
use NocturnalSm\Reportgen\Datasource;

class GroupHeader extends Component 
{
	protected $height = 20;
	public function render()
	{
		$i = 0;
		foreach ($this->getExprs() as $expr){
			$key = $expr->getExpr();
			if (!$expr->isData()){
				$str = $expr->render(ariables::get($key));
			} else {
				$str = $expr->render(isset(Datasource::getData()->current()->$key) ? Datasource::getData()->current()->$key : '');
			}
			pq($this->dom)->find('expr')->eq(0)->replaceWith($str);
			$i += 1;
		}
		return pq($this->dom)->html();
	}
}