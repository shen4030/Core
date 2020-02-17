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
		$url = 'article';

		$mark = array_key_exists('markId', $this->requestParam) ? '_' . $this->requestParam['markId'] : '';

		$param = [];
		if(array_key_exists('search', $this->requestParam)){
			$param['search'] = $this->requestParam['search'];
		}
		
		;
		$html = '<div id="page">';
		$html .= '<div class="next fr"><a href="' . Router::url($url . $this->nextPageNumber . $mark . '.html', $param) .'">»</a></div>';
		$html .= '<div class="last fr"><a href="' . Router::url($url . $this->countPageNumber . $mark .'.html', $param).'">末页</a></div>';
		$html .= '<ul class="pagingUl">';

		foreach ($this->realPageSize as $page) {
			$href = Router::url($url.$page.$mark . '.html', $param);
			if(intval($page) === intval($this->currentPageNumber)){
				$html .= '<li style="background-color: black;"><a style="color: white;" href="'.$href.'">'.$page.'</a></li>';
			}else{
				$html .= '<li><a href="'.$href.'">'.$page.'</a></li>';
			}
		}

		$html .= '</ul>';
		$html .= '<div class="first fr"><a href="' . Router::url($url . 1 .$mark .'.html', $param).'">首页</a></div>';
		$html .= '<div class="prv fr"><a href="' . Router::url($url . $this->lastPageNumber . $mark .'.html', $param).'">«</a></div>';
		$html .= '</div>';

		return $html;		
	}
}