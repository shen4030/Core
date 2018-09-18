<?php
namespace Core\Tool;

use Core\Http\Router;

class Page{

	private $currentPageNumber = 1;

	private $pageSizeNumber = 10;

	private $dataCountNumber = 0;

	private $nextPageNumber = 0;

	private $lastPageNumber = 0;

	private $realPageSize = [];

	private $requestParam = [];

	private $limitPageNumber = 4;

	public function __construct($currentPageNumber = 1, $pageSizeNumber = 10, $dataCountNumber = 0, $param = [])
	{
		$this->currentPageNumber = intval($currentPageNumber);
		$this->pageSizeNumber = intval($pageSizeNumber);
		$this->dataCountNumber = intval($dataCountNumber);
		$this->lastPageNumber = $this->currentPageNumber <= 1 ? 1 : $this->currentPageNumber - 1;
		$countPageNumber = ceil($this->dataCountNumber / $this->pageSizeNumber);
		$this->nextPageNumber = $countPageNumber < $currentPageNumber + 1 ? $currentPageNumber : $currentPageNumber + 1;

		
		
		if($countPageNumber > $this->limitPageNumber){
			$size = $this->limitPageNumber - 3;dump($size);
			if($size % 2 == 0){
				$frontLength = $backLength = $size / 2;
			}else{
				$frontLength = ($size + 1) / 2 ;
				$backLength = ($size - 1) / 2;
			}
// dump($frontLength);dump($backLength);
			array_push($this->realPageSize, 1);

			$start = $this->currentPageNumber - $frontLength > 1 ? $this->currentPageNumber - $frontLength : 2;
			$end = $this->currentPageNumber + $backLength > $countPageNumber -1  ? $this->currentPageNumber + $backLength - 1 : $this->limitPageNumber - 2;

			dump($start);dump($end);

			for($i = $start; $i <= $end; $i++ ){
				array_push($this->realPageSize, $i);
				if($start == $end){
					if($start == 2){
						array_push($this->realPageSize, $countPageNumber - 1);
					}else{
						array_push($this->realPageSize, 2);
					}
				}
			}

			array_push($this->realPageSize, $countPageNumber);
		}else{
			$start = 1;
			$end = $countPageNumber;
			for($i = $start; $i <= $end; $i++ ){
				array_push($this->realPageSize, $i);
			}
		}
		

		

		if($param){
			$this->requestParam = $param;
		}
	}

	public function getPageHtml()
	{
		$html = '';
		foreach ($this->realPageSize as $page) {
			$pageNumber = ['currentPage' => $page];
			$html .= '<li'.($page == $this->currentPageNumber ? ' class="active"' : '').'><a href="'.Router::url('', $pageNumber).'">'.$page.'</a></li>';
		}
		// for($page = 1 ; $page <= $this->realPageSize ; $page++){
			
		// }
		$html = '<div class="text-center"><ul class="pagination"><li><a href="'.Router::url('', ['currentPage'=>$this->lastPageNumber]).'">«</a></li>' .$html. '<li><a href="'.Router::url('', ['currentPage'=>$this->nextPageNumber]).'">»</a></li></ul></div>';
		return $html;
	}
}