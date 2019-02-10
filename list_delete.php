<?php
$starttime=microtime(true);
?>
<!doctype html>
<html>
<head>
  <title>TS-N.NET Channeldeleter - List next deletes</title>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <link rel="stylesheet" href="style.css" type="text/css">
</head>  
<body>
<?php

require_once("config.php");
require_once("lang.php");
require_once("ts3_lib/TeamSpeak3.php");

try
{
	$ts3_ServerInstance = TeamSpeak3::factory("serverquery://".$cfg["user"].":".$cfg["pass"]."@".$cfg["host"].":".$cfg["query"]."/");
	$ts3_VirtualServer = TeamSpeak3::factory("serverquery://".$cfg["user"].":".$cfg["pass"]."@".$cfg["host"].":".$cfg["query"]."/?server_port=".$cfg["voice"]);
	
	require_once("mysql_connect.php");

	try
	{
		$ts3_VirtualServer->selfUpdate(array('client_nickname'=>$queryname));
	}
	catch(Exception $e)
	{
		try
		{
			$ts3_VirtualServer->selfUpdate(array('client_nickname'=>$queryname2));
		}
		catch(Exception $e)
		{
			echo'<span class="red"><b>'.$lang['error'].$e->getCode().':</b> '.$e->getMessage().'</span><br>';
		}
	}

	if (isset($_GET['sort'])) { $sort1 = $_GET['sort']; }
	if ($sort1=="name") { $sort="ORDER BY path"; }
	elseif ($sort1=="date")	{ $sort="ORDER BY lastuse"; } else { $sort=""; }

	if (isset($_GET['order'])) { $order1=$_GET['order']; }
	if ($order1=="asc") { $order=" ASC"; }
	elseif ($order1=="desc") { $order=" DESC"; } else { $order=""; }

	$sum=$sort.$order;

	$tschanarr=$ts3_VirtualServer->channelList();

	foreach($tschanarr as $channel)
	{
		$tscid[]=$channel['cid'];
	}
	
	$todaydate=time();
	$todeletetime=$todaydate-$warntime;
	$sqlchanarr=$mysqlcon->query("SELECT * FROM $table_channel WHERE lastuse<$todeletetime $sum");
	
	echo'<table><tr><th><a href="?sort=name&amp;order=';
	switch($order1)
		{
		case "": echo "asc"; break;
		case "asc": echo "desc"; break;
		case "desc": echo "asc";
		}
	echo'">'.$lang['hlpath'].'</a></th><th colspan="2"><a href="?sort=date&amp;order=';
	switch($order1)
		{
		case "": echo "asc"; break;
		case "asc": echo "desc"; break;
		case "desc": echo "asc";
		}
	echo'">'.$lang['hltime'].'</a></th></tr>';
	$count=1;
	while($row=$sqlchanarr->fetch_row())
	{
		if(in_array($row[0], $tscid) && !in_array($row[0], $nodelete) && substr_count(strtolower($tschanarr[$row[0]]['channel_name']),"spacer")==0 && $tschanarr[$row[0]]->getChildren()=="")
		{
			$test=$row[1]+$unusedtime;
			$deletiontime=date($dateformat,$test);
			$lents=strlen($tschanarr[$row[0]]['channel_name']);
			$lendb=strlen($row[2]);
			$len=$lendb-$lents;
                        echo'<tr><td>'.substr($row[2], 0, $len).'<b>'.$tschanarr[$row[0]]['channel_name'].'</b></td>
			<td><span class="green">'.$lang['deltime'].'</span><br></td>
			<td>'.$deletiontime.'</td></tr>';
			$count=$count+1;
		}
	}
	echo'</table>';
	if($count==1)
	{
		echo'<span class="red">'.$lang['nodel2'].'</span><br>';
	}
}

catch(Exception $e)
{
	echo'<span class="red"><b>'.$lang['error'].$e->getCode().':</b> '.$e->getMessage().'</span><br>';
}
$buildtime=microtime(true)-$starttime;
echo'<br>'.sprintf($lang['sitegen'],$buildtime);
?>
</body>
</html>
