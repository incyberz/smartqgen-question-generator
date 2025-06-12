<?php
if ($username and $param != 'quiz-started') {
  $role_show = '';
  if ($user['is_pengajar']) $role_show = 'Pengajar';
  if ($user['is_admin']) $role_show = 'Admin';
  echo "
    <style>
      .btn-logout {
        position: fixed;
        z-index: 99;
        top: 15px;
        right: 15px;
        display:flex;
        gap:10px
      }
      .btn-logout a{
        display:block;
        transition: .3s;
      }
      .btn-logout a:hover{
        display:block;
        font-size:18px;
      }
    </style>
    <div class='btn-logout'>
      <a href='?my_profile'>$role_show 👤</a>
      <a href='?logout' onclick='return confirm(`Logout?`)'>❌</a>
    </div>  
  ";
}
