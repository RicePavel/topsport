<?php

define('MAX_FILE_SIZE', 10000000); 
set_time_limit(1000);

// PHP Simple HTML DOM Parser
require('simple_html_dom.php');
require('PHPExcel.php');

$bom = "\xEF\xBB\xBF";

//var_dump($models);

// rzr ------------------------------------------------------------------------------------------------------------------------------
 
$headArray = [];
$headArray[] = 'name';
$headArray[] = 'article';

$headArray[] = 'Производитель техники';
$headArray[] = 'Год модели техники';
$headArray[] = 'Модель техники';

//$fileName = "topsport";

//$fileNames = ["Выхлопная система", "Гидроподъем", "Гребные винты", "Двигатель", "Корпус", "Мебель судовая", "Насосы топливные", "Система охлаждения", "Струбцина и поворотный механизм", "Топливная система", "Трансмиссия", "Управление", "Электрика"];

$fileNames = ["Помпы и баки аккумулирующие", "Вентиляторы", "Защита анодная", "Инструменты", "Каяки рыбацкие", "Насосы для надувных лодок", "Насосы для перекачки топлива", "Опрыскиватели", "Система пресной воды", "Спасательное оборудование", "Сувениры", "Фановая система", "Якорно-швартовое"];

$resultArray = array();

foreach ($fileNames as $fileName) {
	$resultArray = array_merge($resultArray, getParsingResultNew($fileName, $headArray));
}

	$sheet = array(
		$headArray
	);
	foreach ($resultArray as $row) {
		$rowArray = array();
		foreach($headArray as $specName) {
			$rowArray[] = $row[$specName];
		}
		$sheet[] = $rowArray;
	}
	$doc = new PHPExcel();
	$doc->setActiveSheetIndex(0);
	$doc->getActiveSheet()->fromArray($sheet, null, 'A1');
	$writer = PHPExcel_IOFactory::createWriter($doc, 'Excel5');
	//$writer->save('total_result.xls');
	$writer->save('total_result_2.xls');

function getParsingResultNew($fileName, &$headArray) {
	$filePath = "C:\\OpenServer\\domains\\localhost\\topsports\\html_files\\" . $fileName . ".html";
	$result = file_get_contents($filePath);
	$html = str_get_html($result);

	$resultArray = [];
	foreach($html->find('div.product_info') as $element) {
		$name = '';
		$article = '';
		$newArray = [];
		$titleElement = $element->find('div.pr-title')[0];
		$name = trim($titleElement->plaintext);
		
		$articleElement = $element->find('.articul .title')[0];
		$article = $articleElement->plaintext;
			
		$productOptionsElement = $element->find('.product_options')[0];
		foreach($productOptionsElement->find('.item') as $optionElement) {
	
				$titleElement = $optionElement->find('.title')[0];
				$valueElement = $optionElement->find('.value')[0];
				
				if (!$titleElement || !$valueElement) {
					continue;
				}
				
				$title = $titleElement->plaintext;
				$value = $valueElement->plaintext;
				/*
				if (!in_array($title, $headArray)) {
					$headArray[] = $title;
				}
				*/
				$newArray[$title] = $value;
		}
		$newArray['name'] = $name;
		$newArray['article'] = $article;	
		
		$resultArray[] = $newArray;
	}
	$html->clear(); 
	
	return $resultArray;
}


unset($html);
return;