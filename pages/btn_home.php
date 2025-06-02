<?php
if ($param != '' and $param != 'dashboard' and $param != 'welcome' and $param != 'quiz-started') {
  echo "
    <style>
      .btn-home {
        position: fixed;
        z-index: 99;
        top: 15px;
        left: 15px;
      }
    </style>
    <div class='btn-home'>
      <a href='?'>" . img_icon('home') . "</a>
    </div>  
  ";
}
