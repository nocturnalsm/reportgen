<?php

namespace NocturnalSm\Reportgen\Components\Page;

use NocturnalSm\Reportgen\Component;
use NocturnalSm\Reportgen\Variables;
use NocturnalSm\Reportgen\Datasource;

class Footer extends Component 
{
	public function render()
	{
		$defaultPageFooter = '
				<span style="text-align:right;float:right;padding-right:15px">
					Page <span class="pagenumber"></span> of <span class="pagecount"></span>
				</span>
				<span style="text-align: left">
					' .Date("d M Y H:i:s") 
					." - Printed by " .Variables::get("REPORT_USER_NAMA") 
					.'
				</span>						';
		$return = '<div class="page-footer">';

		if (isset($this->dom)){
				$i = 0;
				foreach ($this->getExprs() as $expr){
							$key = $expr->getExpr();
							if (!$expr->isData())
									$str = $expr->render(Variables::get($key));
							else
									$str = $expr->render(isset(Datasource::getData()->current()->$key) ? Datasource::getData()->current()->$key : '');
							pq($this->dom)->find('expr')->eq(0)->replaceWith($str);
							$i += 1;
				}
				$return .= pq($this->dom)->html();
		}
		else
				$return .= $defaultPageFooter;

		$return .= '
					</div>
		';
		return $return;
	}
}