<?php 

function find_key_value($file_name, $key) {

	$fp = fopen($file_name, 'r');
	
	if ($fp){
		$ch = NULL;
		
		$start = 0;
		$end = filesize($file_name);
		$middle = $end/2;
		
		$current_key = find_current_key($fp); // находим самый первый ключ 
		echo $current_key;
		if (strcmp($key, $current_key)==0){ // сравниваем 
				return find_value($fp, $ch);  
		}

		$value = find_new_line($fp, $end, $middle); // ищем любой первый ключ во второй половине 
		
		if ($value==NULL) {  // если ключ не нашли во второй половине, ищем в первой 
			$end = $middle;
			$middle = $start;
			$value = find_new_line($fp, $end, $start);
			if ($value==NULL) return "undef";  
		};
		
		$current_key = find_current_key($fp);  // получаем значение найденного ключа
		
		while (($end-$start)>1){
		echo "key ". $key. "current_key".$current_key."</br>";
			if (strcmp($key, $current_key)==0){ // если найденный ключ и нужный совпадают
				return find_value($fp, $ch);  // находим значение соответствующее ключу
			}
			if (strcmp($key, $current_key)<0){  // если найденный ключ меньше нужного, ищем в первой половине
				$end = $middle; 
				$middle = $start + ($end - $start)/2;
				$value = find_new_line($fp, $end, $middle);
				if ($value==NULL) return "undef";
				$current_key = find_current_key($fp);
			}
			if (strcmp($key, $current_key)>0){  // если найденный ключ больше нужного, ищем во второй половине
				$start = $middle;
				$middle = $start + ($end - $start)/2;
				$value = find_new_line($fp, $end, $middle);
				if ($value==NULL) return "undef";
				$current_key = find_current_key($fp);
			}
		} 
		if (($end-$start)<1) return "undef";
		fclose($fp);
	}
	else return "Ошибка при открытии файла";
}

function find_new_line($fp, $end, $middle){
	fseek($fp, $middle);  // переходим в середину файла 
	$ch = ord(fgetc($fp));
		while ($ch != 10) {  // переходим к переносу строки, чтобы дойти до ключа
			$ch = ord(fgetc($fp));  
			if (feof($fp)) return NULL;
			if (ftell($fp) == $end) return NULL;
		};	
		return ftell($fp);
}

function find_current_key($fp){
	$current_key = ""; 
	$letter = fgetc($fp);
	$ch = ord($letter); 
	while ($ch != 9) {   // узнаем ключ 
		$current_key = $current_key.$letter;
		$letter = fgetc($fp);
		$ch = ord($letter); 
		if (feof($fp)) return NULL;
		}
		return $current_key;
}

function find_value($fp, $ch){
	$result = "";
	while ($ch != 10 ){
		$letter = fgetc($fp);
		$ch = ord($letter);  
		$result = $result.$letter;
	}
	return $result;
}

?>