<?php

namespace NocturnalSm\Reportgen;

use phpQuery\phpQuery;
use NocturnalSm\Reportgen\Variables;
use NocturnalSm\Reportgen\Datasource;
use NocturnalSm\Reportgen\Expression;


class Generator 
{

	private $projectName;	
	private $layout;	
	private $reportName;
	protected $report;
	protected $query;
	public $filters;
	private $reportTitle;
	protected $script;
	protected $data;
	protected $content;
	protected $customField;
	protected $sortArray;
	protected $forcedSort;
	protected $prepareTime;
	protected $sortOptions = Array();
	protected $html;
	protected $header;
	protected $page;
	protected $footer;
	protected $style;
	private $customFilterView;
	private $columns;
	private $user;	
	private $paperSize = Array("width" => 21.5, "height" => 29.2);
	private $paperMargin = Array("top" => 1, "right" => 1, "bottom" => 1, "left" => 1);
	const TYPE_DATE = 1;
	const TYPE_LOOKUP = 2;
	const TYPE_TEXT = 3;
	const TYPE_CHECKBOX = 4;
	const TYPE_RADIO = 5;
	const TYPE_CUSTOM = 6;

	function __construct($layout, $data)
	{
		$this->layout = $layout;
		$this->data = $data;
		$scan = $this->scanLayout();
		if ($scan != TRUE) {
			$this->error();
		}
	}
	function setPaperSize($options)
	{
		if (is_array($options)){
			if (isset($options["width"])){
				$this->paperSize["width"] = $options["width"];
			}
			if (isset($options["height"])){
				$this->paperSize["height"] = $options["height"];
			}
		}
	}
	function setPaperMargin($options)
	{
		if (is_array($options)){
			if (isset($options["top"])){
				$this->paperMargin["top"] = $options["top"];
			}
			if (isset($options["right"])){
				$this->paperMargin["right"] = $options["right"];
			}
			if (isset($options["bottom"])){
				$this->paperMargin["bottom"] = $options["bottom"];
			}
			if (isset($options["left"])){
				$this->paperMargin["left"] = $options["left"];
			}
		}
	}
	function getScript()
	{
		return $this->script;
	}
	function setQuery($query)
	{
		$this->query = $query;
	}
	function getHeader()
	{
		return $this->header;
	}
	function getFooter()
	{
		return $this->footer;
	}
	function getPageHeader()
	{
		return $this->page->getHeader();
	}
	function getPageFooter()
	{
		return $this->page->getFooter();
	}
	function setLayout($layout)
	{
		$this->layout = dirname(dirname(dirname(__FILE__))) .$layout;
		$scan = $this->scanLayout();
		if ($scan != TRUE) {
				$this->error();
				//throw new Exception($scan);
		}
	}
	function getContent()
	{
		if (isset($this->content))
		{
			return $this->content;
		}
	}
	function setVariable($field, $value)
	{
		Variables::write($field, $value);
	}
	function getVariable($field)
	{
		Variables::get($field);
	}
	function setCustomFilterView($html)
	{
		$this->customFilterView = $html;
	}
	function sortByColumn($sort)
	{
		$this->sortArray = $sort;
	}
	function getUser()
	{
		return $this->user;
	}
	function sortForce($array)
	{
		$this->forcedSort = $array;
	}
	function getDetails($index = NULL)
	{
		if ($index >= 0){				
			return $this->page->getDetails($index);
		}
		else {
			return $this->page->getDetails();
		}
	}	
	function scanLayout()
	{
		$this->html = phpQuery::newDocumentFileHTML($this->layout);
		// find style tag, if any
		$styleDom = pq($this->html)->find('style');
		foreach ($styleDom as $style){
				$this->style .= pq($style)->html();
		}
		// find report tag -- required only one
		$report = pq($this->html)->find('report');
		if (count($report) == 0) return "No REPORT tag found";
		if (count($report) > 1) return "Only one REPORT tag is allowed";
		// find header tag -- not required; if any, only one is allowed
		$header = pq($this->html)->find('report > header');
		if (count($header) > 1) return "Only one HEADER tag is allowed";
		if (count($header) == 1){
			$this->header = new Header($header, $header->attr("data-height"));
			$this->header->scan();
		}
		// find page tag -- required only one
		$page = pq($this->html)->find('report page');
		if (count($page) == 0) return "No PAGE tag found";
		if (count($page) > 1) return "Only one PAGE tag is allowed";
		syslog(LOG_INFO, count($page));
		if (count($page) == 1){
			$this->page = new Page($page);
			$scan = $this->page->scan();
			if ($scan != TRUE) return $scan;
		}
		// find footer tag -- not required; if any, only one is allowed
		$footer = pq($this->html)->find('report > footer');
		if (count($footer) > 1) return "Only one FOOTER tag is allowed";
		if (count($footer) == 1){
			$this->footer = new Footer($footer, $footer->attr("data-height"));
			$this->footer->scan();
		}
		return TRUE;
	}
	function generate()
	{
		// generate header
		$pages = 0;
		$height = 0;
		$content = '<div id="report">';
		if ($this->header) {			
			$content .= $this->header->render();			
			$height += $this->header->getHeight();			
		}		
			
		// generate detail
		$details = $this->page->getDetails();
		for ($iDet=0;$iDet<count($details);$iDet++){
			$str = "";
			if ($details[$iDet]->getQuery() != ""){
				Datasource::query($details[$iDet]->getQuery());
			}
			$runningGroup = Array();
			if ($details[$iDet]->groupCount() > 0){
				for($ctr=0;$ctr < $details[$iDet]->groupCount();$ctr++){
						$runningGroup[$ctr] = "";
				}
			}
			$rowCount = Datasource::getData()->count();										
			if ($rowCount > 0){
				Datasource::getData()->rewind();
				$footer = $details[$iDet]->getFooter();
				for($row=0;$row < $rowCount;$row++){
					$current = Datasource::getData()->current();
					// print group footers, if exist
					$groupKeys = Array();
					for($ctr=count($runningGroup)-1;$ctr>=0;$ctr--){											
						$groupFooter = $details[$iDet]->getGroup($ctr)->getFooter();
						$groupKey = $details[$iDet]->getGroup($ctr)->getGroupBy();
						if ($runningGroup[$ctr] != "" && $runningGroup[$ctr] != $current->$groupKey){
							if (isset($groupFooter)) {
								$groupFooter->setRunningValue($runningGroup[$ctr]);
								$runningFooter = $details[$iDet]->getGroup($ctr)->getFooter()->render();															
								$str .= "<tbody>";
								$str .= $runningFooter;
								$str .= "</tbody>";
								$groupFooter->resetCalcFields();
							}
						}
						if (isset($groupFooter)){
							$groupFooter->calculate($current);
						}
						if  ($runningGroup[$ctr] !== $current->$groupKey){
							$runningGroup[$ctr] = $current->$groupKey;
							$printData[$groupKey] = true;
						}
						else {
							$printData[$groupKey] = false;
						}
						$groupKeys[] = $groupKey;
					}
					if (isset($footer)) $footer->calculate($current);
					$strRow = "<tr>";
					$detailHeight = 0;
					foreach($details[$iDet]->getCols() as $col){
						$key = $col->getExpr();
						if (!$col->isData())
								$strRow .= $col->renderExpr(Variables::get($key));
						else {
							if (isset($printData[$key]) && $printData[$key] == false){
									$strRow .= $col->renderExpr("");
							}
							else {													
								$strRow .= $col->renderExpr(null !== Datasource::getData()->current()[$key] ? Datasource::getData()->current()[$key] : '');
							}
						}
						if ($col->getHeight() > $detailHeight){
							$detailHeight = $col->getHeight();
						}
					}
					$strRow .= "</tr>";																		
					if ($height + $detailHeight > $this->paperSize["height"] - $this->paperMargin["top"] - $this->paperMargin["bottom"] - $details[$iDet]->getTableHeaderHeight()){																										
						$height = 0;
						$str .= '<tr class="pagebreak"></tr>';
					}					
					$str .= $strRow;
					$height += $detailHeight;
					if ($row < $rowCount-1){
						Datasource::getData()->next();
					}
				}
				for($ctr=count($runningGroup)-1;$ctr>=0;$ctr--){
					$groupFooter = $details[$iDet]->getGroup($ctr)->getFooter();
					if (isset($groupFooter)) {
						//$groupFooter->calculate($current);
						$groupFooter->setRunningValue($runningGroup[$ctr]);
						$runningFooter = $details[$iDet]->getGroup($ctr)->getFooter()->render();
						$str .= "</tbody>
									<tbody>";
						$str .= $runningFooter;
						$str .= "</tbody>";
					}
				}
				$content .= $details[$iDet]->render($str);
			}
			else {
				$content .= $details[$iDet]->render(NULL);
			}
		}

		// generate footer
		if ($this->footer) {
			if ($height + $this->footer->getHeight() > $this->paperSize["height"] - $this->paperMargin["top"] - $this->paperMargin["bottom"]){
				$height = 0;
				$content .= '<div class="pagebreak"></div>';
			}
			$height += $this->footer->getHeight();				
			$content .= $this->footer->render();
		}

		// generate page footer
		$content .= $this->page->getFooter()->render();
		//$this->html->clear();
		return $content ."</div>";
	}
	function error()
	{
		//$this->html->clear();
	}
	function run()
	{
		$startTime = microtime(true);
		$options = Array('cssInfoBlock : "tablesorter-no-sort"','widgets:["staticRow"]');
		if (isset($this->sortArray)){
			$this->sortOptions[] = 'sortList: ' .json_encode($this->sortArray);
		}
		if (isset($this->forcedSort)){
			$this->sortOptions[] = 'sortForce: ' .json_encode($this->forcedSort);
		}			
		if (isset($this->query)){
			Datasource::query($this->query);
		}
		else if (isset($this->data)){								
			Datasource::setData($this->data);
		}
		$this->setVariable("REPORT_TITLE", $this->reportTitle);		
		$endTime = microtime(true);
		$this->prepareTime = $endTime - $startTime;
		$content = "<style>" .$this->style ."</style>";
		$content .= $this->generate();
		return $content;
	}	
}







