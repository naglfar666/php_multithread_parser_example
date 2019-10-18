<?php
function generateRandomString($length = 10, $chars = 'all') {

  if($chars == 'all'){
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  }

  if($chars == 'digits'){
    $characters = '0123456789';
  }

  if($chars == 'letters'){
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  }

  $charactersLength = strlen($characters);
  $randomString = '';
  for ($i = 0; $i < $length; $i++) {
    $randomString .= $characters[rand(0, $charactersLength - 1)];
  }
  return $randomString;
}

$file = fopen('test.csv', 'a');
fwrite($file, 'Код;Цена'.PHP_EOL);
for ($i = 0; $i < 1000000; $i++) {
  fwrite($file, generateRandomString(3, 'digits').'-'.generateRandomString(4, 'letters') .';'.rand(100,300).PHP_EOL);
}
fclose($file);
?>
