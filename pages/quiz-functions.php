<?php
function generateRandomValue($range)
{
  list($min, $max) = explode("-", $range);
  return rand((int)$min, (int)$max);
}

function evaluateFormula($formula, $vars)
{
  extract($vars);

  $allowed_functions = ['sqrt', 'pow', 'sin', 'cos', 'tan', 'log', 'exp', 'abs', 'round', 'ceil', 'floor'];

  $formula_php = preg_replace_callback('/\b([a-zA-Z_][a-zA-Z0-9_]*)\b/', function ($match) use ($allowed_functions) {
    $word = $match[1];
    return in_array(strtolower($word), $allowed_functions) ? $word : '$' . $word;
  }, $formula);

  return eval("return $formula_php;");
}
