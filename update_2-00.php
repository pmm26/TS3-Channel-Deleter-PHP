<!doctype html>
<html>
<head>
  <title>TS-N.NET Channeldeleter - Update</title>
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" href="style.css" type="text/css">
</head>  
<body>

<?php

require_once('config.php');
require_once('lang.php');
require_once('mysql_connect.php');

if(!$mysqlcon->query("SELECT count(active) FROM $table_channel"))
{
	echo "You already updated your database. Please delete this file from your webspace.";
} else
{
	echo 'You have only to run this, if you want to update the Channeldeleter from an older version to 2.00!<br><br>Run this once time and delete the update_2-00.php file after from your webserver.<br><br><br>';
	$olddata=$mysqlcon->query("SELECT * FROM $table_channel");
	echo 'Update Database:<br>';

	if(!$mysqlcon->query("DELETE FROM $table_channel"))
	{
		printf("Errormessage: %s\n", $mysqlcon->error);
	}
	if(!$mysqlcon->query("DROP TABLE $table_channel"))
	{
		printf("Errormessage: %s\n", $mysqlcon->error);
	}
	if(!$mysqlcon->query("CREATE TABLE $table_channel (cid int(11) NOT NULL,lastuse int(10) NOT NULL,path text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, PRIMARY KEY (cid))"))
	{
		printf("Errormessage: %s\n", $mysqlcon->error);
	}
	if(!$mysqlcon->query("CREATE TABLE $table_update (timestamp int(10) NOT NULL,  PRIMARY KEY (timestamp))"))
	{
		printf("Errormessage: %s\n", $mysqlcon->error);
	}
	$mysqlcon->query("INSERT INTO $table_update (timestamp) VALUES ('1')");
	while($row=$olddata->fetch_row())
	{
		$time=strtotime($row[2]);
		$cid=$row[1];
		$path=$row[4];
		echo 'Convert Date for ChannelID '.$cid.' from '.$row[2].' to '.$time.'<br>';
		if(!$mysqlcon->query("INSERT INTO $table_channel (cid,lastuse,path) VALUES($cid,$time,'$path')"))
		{
			printf("Errormessage: %s\n", $mysqlcon->error);
		}
	}
}
?>
</body>
</html>
