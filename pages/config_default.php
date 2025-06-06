<?php
$config_default = [];
if ($username and $user['role'] >= 1) {
  $config_default = [
    'nominal_pencairan_min' => [
      'value' => 2000,
      'min' => 2000,
      'max' => 5000,
      'step' => 1000,
    ],
    'nominal_pencairan_max' => [
      'value' => 50000,
      'min' => 5000,
      'max' => 1000000,
      'step' => 1000,
    ],
    'max_pencairan_daily' => [
      'value' => 1,
      'min' => 1,
      'max' => 3,
    ],
    'max_play_daily' => [
      'value' => 100,
      'min' => 1,
      'max' => 100,
    ],
  ];
}
