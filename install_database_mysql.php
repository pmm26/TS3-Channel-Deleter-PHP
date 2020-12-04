<!doctype html>
<html>
<head>
  <title>TS-N.NET Channeldeleter - Install MySQL Database</title>
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" href="style.css" type="text/css">
</head>  
<body>
<?php

require_once('config.php');
require_once('lang.php');

$mysqlcon=mysqli_connect($mysqldbhost, $mysqldblogin, $mysqldbpasswd);

if(mysqli_connect_errno())
{
	echo $lang['dbconerr'].mysqli_connect_error();
}

echo $lang['instdb'].'<br>';

// if(!$mysqlcon->query("CREATE DATABASE $mysqldbname"))
// {
// 	echo $lang['instdberr'].'<span class="red">'.$mysqlcon->error.'</span>';
// }
// else
// {
// 	echo'<span class="green">'.sprintf($lang['instdbsuc'],$mysqldbname).'</span>';
// }


echo '<br><br>'.$lang['insttb'].'<br>';

if(!$mysqlcon->query("CREATE TABLE $mysqldbname.$table_channel(cid int(11) NOT NULL,lastuse int(10) NOT NULL,path text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, PRIMARY KEY (cid))"))
{
	echo $lang['insttberr'].'<span class="red">'.$mysqlcon->error.'.</span><br>';
}
else
{
	echo '<span class="green">'.sprintf($lang['insttbsuc'],$table_channel).'</span><br>';
}

if(!$mysqlcon->query("CREATE TABLE $mysqldbname.$table_update (timestamp int(10) NOT NULL)"))
{
	echo $lang['insttberr'].'<span class="red">'.$mysqlcon->error.'.</span>';
}
else
{
	echo '<span class="green">'.sprintf($lang['insttbsuc'],$table_update).'</span><br>';
}
$mysqlcon->query("INSERT INTO $mysqldbname.$table_update (timestamp) VALUES ('1')");

?>
</body>
</html>
