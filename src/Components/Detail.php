<?php

namespace NocturnalSm\Reportgen\Components;

use NocturnalSm\Reportgen\Component;
use NocturnalSm\Reportgen\Components\Detail\Header;
use NocturnalSm\Reportgen\Components\Detail\Column;
use NocturnalSm\Reportgen\Components\Detail\Footer;
use NocturnalSm\Reportgen\Components\Detail\Group;

class Detail extends Component {
	private $cols;
	private $footer;
	private $header;
	private $group;
	private $tableHeaderVisible = TRUE;
	private $tableHeaderHeight = 0.7;
	protected $query;
	protected $data;
	protected $height = 5;
	private $showNoDataWarning = TRUE;

	public function setQuery($query)
	{
		$this->query = $query;
	}
	public function getQuery()
	{
		return $this->query;
	}
	public function setData($data){
		$this->data = $data;
	}
	public function getData(){
		return $this->data;
	}
	public function groupCount(){
		return (isset($this->group)) ? count($this->group) : 0;
	}
	public function columnCount(){
		return count($this->cols);
	}
	public function setTableHeaderHeight($value)
	{
		$this->tableHeaderHeight = $value;
	}
	public function getTableHeaderHeight()
	{
		return $this->tableHeaderHeight;
	}
	public function getGroup($index = NULL)
	{
		if ($index !== NULL){
				return $this->group[$index];
		}
		else {
				return $this->group;
		}
	}
	public function hideNoDataWarning($value = TRUE)
	{
		$this->showNoDataWarning = !$value;
	}
	public function scan()
	{
		$header = pq($this->dom)->find("> header");
		if (pq($header)->html() != ""){
				$this->header = new Header($header);
				$this->header->scan();
		}
		$group = pq($this->dom)->find("group");
		if (count($group) > 0){
			$i = 0;
			foreach (pq($group) as $grp){
					$this->group[$i] = new Group($grp);
					$this->group[$i]->scan();
					$i += 1;
			}
		}
		foreach (pq($this->dom)->find("col") as $col){
				$this->cols[pq($col)->attr('data')] = new Column($col);
		}
		$footer = pq($this->dom)->find("> footer");
		if (pq($footer)->html() != "") {
				$this->footer = new Footer($footer);
				$this->footer->scan();
		}
	}
	public function hideTableHeader($hide = TRUE)
	{
		$this->tableHeaderVisible = !$hide;
	}
	public function getCols()
	{
		return $this->cols;
	}
	public function getFooter()
	{
		return $this->footer;
	}
	public function render($text)
	{
		if ($text == ""){
			return "";
		}
		else {
			$return = '<div class="report-content">';
			if ($text == NULL){
				if ($this->showNoDataWarning == TRUE){
					$return .= '<div class="row" style="margin-top:10px">
												<div class="col-md-12 nodata">Tidak ada data</div>
										</div>';
				}
			}
			else {
					if ($this->header) $return .= $this->header->render();

					$return .= '<table class="table-detail" width="100%">';
					if ($this->tableHeaderVisible){	
							$return .= '<thead>';
							foreach ($this->cols as $col){
									$class = isset($col->class) ? $col->class ." " : "";
									$class .= ($col->getType() == 'number' || $col->getType() == 'currency') ? 'number sorter-digit' : "";
									$return .= '<th style="' .$this->tableHeaderHeight .'cm;" width="' .$col->getWidth() .'%"'
															.(trim($class) != "" ? ' class="' .trim($class) .'"' : '') ."><span>" .$col->header ."</span></th>";
							}
							$return .= '</thead>';
					}
					$return .= '<tbody>
												' .$text
											.'</tbody>';
					if ($this->footer) {
							$return .= '<tfoot>
														<tr>
															<td style="padding:0px!important" width="100%" colspan="' .count($this->cols) .'">';
							$return .= $this->footer->render();
							$return .= '		</td>
														</tr>
													</tfoot>';
					}
					$return .= '
										</table>';
			}
			$return .= '
						</div>
			';
			return $return;
		}
		
	}
}