<?php

namespace NocturnalSm\Reportgen\Components\Detail;

use NocturnalSm\Reportgen\Component;
use NocturnalSm\Reportgen\Components\Summary;
use NocturnalSm\Reportgen\Expression;
use NocturnalSm\Reportgen\Variables;

class Footer extends Component 
{
	protected $height = 20;
	protected $calcFields = Array();
	protected $onSummary;
	protected $onRender;

	public function setOnSummary($function)
	{
		$this->onSummary = $function;
	}
	function scan()
	{
		$i = 0;
		foreach (pq($this->dom)->find('expr') as $expr){
				$function = pq($expr)->attr('function');
				if (isset($function) && trim($function) != "")
						$this->exprs[$i] = new Summary($expr);
				else
						$this->exprs[$i] = new Expression($expr);
				$i += 1;
		}
	}
	public function calculate($data)
	{
		$i = 0;
		foreach ($this->exprs as $expr){
			$key = $expr->getExpr();
			if (get_class($expr) == "Summary"){
				$function = $expr->getFunction();
				if (strtoupper($function) == 'SUM'){
					if (isset($this->calcFields[$i]))
						$this->calcFields[$i] += $data->$key;
					else
						$this->calcFields[$i] = $data->$key;
				}
				else if (strtoupper($function) == 'MAX'){
					if (isset($this->calcFields[$i])){
							if ($data->$key > $this->calcFields[$i]){
									$this->calcFields[$i] = $data->$key;
							}
					}
					else
						$this->calcFields[$i] = $data->$key;
				}
				else if (strtoupper($function) == 'CUSTOM'){
					if (is_callable($this->onSummary)){
							if (!isset($this->calcFields[$i])) $this->calcFields[$i] = NULL;
							$this->calcFields[$i] = call_user_func_array($this->onSummary, Array($expr,$this->calcFields[$i], $data));
					}
				}
			}
			$i += 1;
		}
	}
	public function setOnRender($function)
	{
		$this->onRender = $function;
	}
	public function render()
	{
		if (isset($this->onRender) && is_callable($this->onRender)){
				$render = $this->onRender();
				return $render;
		}
		else {
				$i = 0;
				$html = phpQuery::newDocument(pq($this->dom)->html());
				foreach ($this->exprs as $expr){
						if ($expr->isData()){
							if (get_class($expr) == "Summary")
									$str = $expr->render($this->calcFields[$i]);
						}
						else
							$str = $expr->render(Variables::get($expr->getExpr()));
						pq($html)->find('expr')->eq(0)->replaceWith($str);
						$i += 1;
				}
				return pq($html)->html();
		}
	}
}