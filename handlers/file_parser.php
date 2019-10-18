<?php
// Получаем ключевые аргументы
foreach ($argv as $key => $value) {
  if (mb_stristr($argv[$key], '--file')) {
    $File_Array = explode('=', $argv[$key]);
    $filename = $File_Array[1];
  } else if (mb_stristr($argv[$key], '--th')) {
    $Thread_Array = explode('=', $argv[$key]);
    $thread = $Thread_Array[1];
  } else if (mb_stristr($argv[$key], '--pr-i')) {
    $Price_Array = explode('=', $argv[$key]);
    $price_index = (integer) $Price_Array[1];
  } else if (mb_stristr($argv[$key], '--c-i')) {
    $Code_Array = explode('=', $argv[$key]);
    $code_index = (integer) $Code_Array[1];
  } else if (mb_stristr($argv[$key], '--c')) {
    $Course_Array = explode('=', $argv[$key]);
    $course = (double) $Course_Array[1];
  }
}

/**
 * Генератор, возвращающий строку
 *
 * @param string $filename - Местоположение файла
 *
 * @yield string - Строка из файла
 */
$File_Generator = function() use ($filename) {
  $file = fopen($filename, 'r');

  if (!$file) {
    echo 'Невозможно открыть файл';
    exit;
  }
  while (($line = fgets($file)) !== false) {
    yield $line;
  }

  fclose($file);
};

/**
 * Подготавливаем код под требуемый формат
 *
 * @param string $data - Входящий код товара
 *
 * @return string - Код товара в нужном формате
 */
$Prepare_Code = function ($data) {
  // Если строка начинается с числа, меняем числа и буквы местами
  if (preg_match('/[0-9]/', substr($data, 0, 1))) {
    // Итоговые строки чисел и букв
    $string_result = '';
    $numbers_result = '';
    // Разбиваем строку в массив и определяем, является ли символ числом, либо буквой
    $Data_Array = str_split($data);
    for ($i = 0; $i < count($Data_Array); $i++) {
      if (preg_match('/[0-9]/', $Data_Array[$i])) {
        $numbers_result .= $Data_Array[$i];
      } else if (preg_match('/[a-zA-Zа-яА-ЯЁё]/u', $Data_Array[$i])) {
        $string_result .= $Data_Array[$i];
      }
    }
    // Если имеется тире, возвращаем код с тире, в других случаях возвращаем просто подготовленный код
    if (stristr($data, '-')) {
      return $string_result.'-'.$numbers_result;
    }
    return $string_result.$numbers_result;
  }
  // Если строка не начинается с числа, возвращаем код
  return $data;
};

/**
 * Подготовка цены
 *
 * @param double $data - стоимость товара
 * @param double $course - Курс для конвертации
 *
 * @return double - Результат конвертации с округлением до сотых
 */
$Prepare_Price = function ($data) use ($course) {
  print_r($course);
  return round($data * $course, 2);
};

// Открываем файл для записи
$file_to_write = fopen('buffer_result/'.$thread.'.xml', 'w');
if (!$file_to_write) {
  echo 'Невозможно открыть файл на запись';
  exit;
}
// Для каждой возвращенной генератором строки записываем ее в файл
foreach ($File_Generator() as $line) {
  if (mb_stristr($line, 'Код')) {
    fwrite($file_to_write,''.PHP_EOL);
    continue;
  }
  // Убираем перенос строки
  $line = str_replace("\n", '', $line);
  $line_array = explode(';', $line);
  // Собираем итоговую строку для xml
  $result_line = '<item><code>'.$Prepare_Code($line_array[$code_index]).'</code><price>'.$Prepare_Price((double) $line_array[$price_index]).'</price></item>'.PHP_EOL;
  fwrite($file_to_write, $result_line);
}
fclose($file_to_write);

?>
