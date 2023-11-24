<?php
  include('functions.php');
  include('database.php');

  ob_start(); // output buffering is turned on
  session_start(); // turn on sessions

  $db =  db_connect('localhost', 'root', '', 'vancouver_airbnb');
?>