<?php
// Получаем ключевые аргументы
foreach ($argv as $key => $value) {
  if (mb_stristr($argv[$key], '--start')) {
    $Start_Array = explode('=',$argv[$key]);
    $start = (integer) $Start_Array[1];
  } else if (mb_stristr($argv[$key], '--end')) {
    $End_Array = explode('=', $argv[$key]);
    $end = (integer) $End_Array[1];
  } else if (mb_stristr($argv[$key], '--file')) {
    $File_Array = explode('=', $argv[$key]);
    $filename = $File_Array[1];
  } else if (mb_stristr($argv[$key], '--th')) {
    $Thread_Array = explode('=', $argv[$key]);
    $thread = $Thread_Array[1];
  }
}

/**
 * Генератор, возвращающий строку
 *
 * @param string $filename - Местоположение файла
 * @param integer $start - Начало файла для считывания
 * @param integer $end - Окончание файла для считывания
 *
 * @yield string - Строка файла
 */
$File_Generator = function() use ($filename, $start, $end) {
  $file = fopen($filename, 'r');

  if (!$file) {
    echo 'Невозможно открыть файл';
    exit;
  }
  $iterator = 0;
  // Пока итератор вход в рамки между первой и последней строкой, возвращаем строку
  while (($line = fgets($file)) !== false) {
    if ($start <= $iterator && $iterator <= $end) {
      yield $line;
    }
    $iterator++;
  }

  fclose($file);
};
// Открываем файл для записи
$file_to_write = fopen('buffer/'.$thread.'.csv', 'w');
if (!$file_to_write) {
  echo 'Невозможно открыть файл на запись';
  exit;
}
// Для каждой возвращенной генератором строки записываем ее в файл
foreach ($File_Generator() as $line) {
  fwrite($file_to_write, $line);
}
fclose($file_to_write);

?>
