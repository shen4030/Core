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

	private $limitPageNumber = 5;

	private $countPageNumber = 0;

	public function __construct($currentPageNumber = 1, $pageSizeNumber = 10, $dataCountNumber = 0, $limitPageNumber = 10, $param = [])
	{
		$this->currentPageNumber = intval($currentPageNumber);
		$this->pageSizeNumber = intval($pageSizeNumber);
		$this->limitPageNumber = intval($limitPageNumber);
		$this->dataCountNumber = intval($dataCountNumber);
		$this->lastPageNumber = $this->currentPageNumber <= 1 ? 1 : $this->currentPageNumber - 1;
		$countPageNumber = ceil($this->dataCountNumber / $this->pageSizeNumber);
		$this->countPageNumber = $countPageNumber;
		$this->nextPageNumber = $countPageNumber < $currentPageNumber + 1 ? $currentPageNumber : $currentPageNumber + 1;

		
		if($countPageNumber > $this->limitPageNumber){

			// 定位中间数 
			$middle = $this->currentPageNumber;

			// 计算中间数与两边距离
			$frontSize = $backSize = 0;

			if($this->limitPageNumber % 2 == 0){
				$size = $this->limitPageNumber / 2;
				$frontSize = $size - 1;
				$backSize = $size;
			}else{
				$frontSize = $backSize = ($this->limitPageNumber - 1) / 2;
			}

			if($middle - $frontSize < 1){
				$middle = $frontSize + 1;
			}
			if($middle + $backSize > $countPageNumber){
				$middle = $countPageNumber - $backSize;
			}

			$start = $middle - $frontSize;
			$end = $middle + $backSize; 

		}else{
			$start = 1;
			$end = $countPageNumber;			
		}

		for($i = $start; $i <= $end; $i++ ){
			array_push($this->realPageSize, $i);
		}

		if($param){
			$this->requestParam = $param;
		}
	}

	public function getBackPageHtml()
	{
		$html = '';

		foreach ($this->realPageSize as $page) {
			$params = array_merge($this->requestParam, ['currentPage' => $page]);
			$html .= '<li'.($page == $this->currentPageNumber ? ' class="active"' : '').'><a href="'.Router::url('', $params).'">'.$page.'</a></li>';
		}

		$html = '<div class="text-center"><ul class="pagination"><li><a href="'.Router::url('', ['currentPage'=>$this->lastPageNumber]).'">«</a></li>' .$html. '<li><a href="'.Router::url('', ['currentPage'=>$this->nextPageNumber]).'">»</a></li></ul></div>';

		return $html;
	}

	public function getFrontPageHtml()
	{
		$url = Router::getArticleUrl();
		$html = '<div id="page">';
		$html .= '<div class="next fr"><a href="' . $url . $this->nextPageNumber . '.html">»</a></div>';
		$html .= '<div class="last fr"><a href="' . $url . $this->countPageNumber . '.html">末页</a></div>';
		$html .= '<ul class="pagingUl">';

		foreach ($this->realPageSize as $page) {

			$html .= '<li'.($page === $this->currentPageNumber ? ' class="active"' : '').'><a href="'.Router::url('', $pageNumber).'">'.$page.'</a></li>';
		}

		$html .= '</ul>';
		$html .= '<div class="first fr"><a href="' . $url . '.html">首页</a></div>';
		$html .= '<div class="prv fr"><a href="' . $url . $this->lastPageNumber . '.html">«</a></div>';
		$html .= '</div>';

		return $html;		
	}
}