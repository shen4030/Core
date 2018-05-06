<?php
namespace Core\Tool;

use Core\Http\Router;

class Page{

	private $currentPageNumber = 1;

	private $pageSizeNumber = 10;

	private $dataCountNumber = 0;

	private $nextPageNumber = 0;

	private $lastPageNumber = 0;

	private $realPageSize = 0;

	private $requestParam = [];

	public function __construct($currentPageNumber = 1, $pageSizeNumber = 10, $dataCountNumber = 0, $param = [])
	{
		$this->currentPageNumber = intval($currentPageNumber);
		$this->pageSizeNumber = intval($pageSizeNumber);
		$this->dataCountNumber = intval($dataCountNumber);
		$this->lastPageNumber = $this->currentPageNumber <= 1 ? 1 : $this->currentPageNumber - 1;
		$countPageNumber = ceil($this->dataCountNumber / $this->pageSizeNumber);
		$this->nextPageNumber = $countPageNumber < $currentPageNumber + 1 ? $currentPageNumber : $currentPageNumber + 1;
		$this->realPageSize = $countPageNumber;
		if($param){
			$this->requestParam = $param;
		}
	}

	public function getPageHtml()
	{
		$html = '';
		for($page = 1 ; $page <= $this->realPageSize ; $page++){
			$pageNumber = ['currentPage' => $page];
			$html .= '<li'.($page == $this->currentPageNumber ? ' class="active"' : '').'><a href="'.Router::url('', $pageNumber).'">'.$page.'</a></li>';
		}
		$html = '<div class="text-center"><ul class="pagination"><li><a href="'.Router::url('', ['currentPage'=>$this->lastPageNumber]).'">«</a></li>' .$html. '<li><a href="'.Router::url('', ['currentPage'=>$this->nextPageNumber]).'">»</a></li></ul></div>';
		return $html;
	}
}