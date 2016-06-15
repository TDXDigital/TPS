<?php

//http://dorian.frasermoore.com/works/201/php-validate-upc-and-ean13-barcodes

function validate_UPCABarcode($barcode)
{
  // check to see if barcode is 12 digits long

  if(!preg_match("/^[0-9]{12}$/",$barcode)) {
    return false;
  }
  $digits = $barcode;

  // 1. sum each of the odd numbered digits
  $odd_sum = $digits[0] + $digits[2] + $digits[4] + $digits[6] + $digits[8] + $digits[10];  

  // 2. multiply result by three
  $odd_sum_three = $odd_sum * 3;

  // 3. add the result to the sum of each of the even numbered digits
  $even_sum = $digits[1] + $digits[3] + $digits[5] + $digits[7] + $digits[9];
  $total_sum = $odd_sum_three + $even_sum;

  // 4. subtract the result from the next highest power of 10
  $next_ten = (ceil($total_sum/10))*10;
  $check_digit = $next_ten - $total_sum;
  
  // if the check digit and the last digit of the barcode are OK return true;
  if($check_digit == $digits[11]) {
     return true;
  } 
  return false;
}

function validate_EAN13Barcode($barcode)
{
  // check to see if barcode is 13 digits long
  if(!preg_match("/^[0-9]{13}$/",$barcode)) {
     return false;
  }
  $digits = $barcode;
  // 1. Add the values of the digits in the even-numbered positions: 2, 4, 6, etc.
  $even_sum = $digits[1] + $digits[3] + $digits[5] + $digits[7] + $digits[9] + $digits[11];
  // 2. Multiply this result by 3.
  $even_sum_three = $even_sum * 3;
  // 3. Add the values of the digits in the odd-numbered positions: 1, 3, 5, etc.
  $odd_sum = $digits[0] + $digits[2] + $digits[4] + $digits[6] + $digits[8] + $digits[10];
  // 4. Sum the results of steps 2 and 3.
  $total_sum = $even_sum_three + $odd_sum;
  // 5. The check character is the smallest number which, when added to the result in step 4,  produces a multiple of 10.
  $next_ten = (ceil($total_sum/10))*10;
  $check_digit = $next_ten - $total_sum;

  // if the check digit and the last digit of the barcode are OK return true;
  if($check_digit == $digits[12]) {
    return true;
  } 
  return false;
}