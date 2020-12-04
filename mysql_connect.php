<?php
$mysqlcon=mysqli_connect($mysqldbhost, $mysqldblogin, $mysqldbpasswd, $mysqldbname);

if (mysqli_connect_errno())
	{
	echo "Failed to connect to MySQL-Database: ".mysqli_connect_error();
	}
?>
