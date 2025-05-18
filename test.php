<?php
$db = new mysqli('127.0.0.1','root','', 'ultradb');
var_dump($db->query("SELECT COUNT(*) FROM tevent")->fetch_row());
