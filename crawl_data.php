<?php
$starttime=microtime(true);
?>
<!doctype html>
<html>
<head>
  <title>TS-N.NET Channeldeleter - Crawl Date</title>
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" href="style.css" type="text/css">
</head>  
<body>
<?php

require_once('config.php');
require_once('lang.php');
require_once('TeamSpeak3/TeamSpeak3.php');

try
{
	$ts3_ServerInstance = TeamSpeak3::factory("serverquery://".$cfg["user"].":".$cfg["pass"]."@".$cfg["host"].":".$cfg["query"]."/");
	$ts3_VirtualServer = TeamSpeak3::factory("serverquery://".$cfg["user"].":".$cfg["pass"]."@".$cfg["host"].":".$cfg["query"]."/?server_port=".$cfg["voice"]);
	
	require_once('mysql_connect.php');
	
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

	//Get the time
	$todaydate=time();

	$icontime=$todaydate-$warntime;
	//Get a list of all channels
	$tschanarr=$ts3_VirtualServer->channelList();

	//Guardar todos os Cid dentro de uma array
	foreach($tschanarr as $channel)
	{
		$tscid[]=$channel['cid'];
	}

	//Apagar todos os Icons
	if($deleteicons==1)
	{
		echo'<b>'.$lang['hldelicon'].'</b><br>';
		$count=0;
		foreach($tschanarr as $channel)
		{
			if($channel['channel_icon_id']==$iconId) {
				$channel->permRemoveByName('i_icon_id');
			}
		}
		if($count>0)
		{
			echo $count.$lang['icondel'].'<br><br>';
		}
		else
		{
			echo $lang['iconnodel'].'<br><br>';
		}
	}

	echo'<b>'.$lang['hlcrawl'].'</b><br>';
	echo'<table>';
	foreach($tschanarr as $channel)
	{
		$channelid=$channel['cid'];
		$channelname=$channel['channel_name'];
		$channelname=htmlspecialchars($channelname, ENT_QUOTES);
		$userinchannel=$channel['total_clients'];
		$chauserinchannelnnelpath=$channel->getPathway();
		$channelpath=htmlspecialchars($channelpath, ENT_QUOTES);
		
		echo'<tr><td>'.$lang['cid'].$channelid.' : </td><td>'.$channelname.'</td>';
		
		$cidexists=$mysqlcon->query("SELECT * FROM $table_channel WHERE cid='$channelid'");
		$cidexists=$cidexists->num_rows;

		if($cidexists>0)
		{
			if($userinchannel>0)
			{
				echo'<td><span class="green">'.sprintf($lang['cidup'],$userinchannel).'</span></td></tr>';
				$mysqlcon->query("UPDATE $table_channel SET lastuse='$todaydate',path='$channelpath' WHERE cid='$channelid'");
				if($seticon==1)
				{
					if($channel['channel_icon_id']==$iconId) {
						$channel->permRemoveByName('i_icon_id');
					}
				}
			}
			else
			{
				$lastusetime=$mysqlcon->query("SELECT lastuse FROM $table_channel WHERE cid='$channelid'");
				$lastusetime=$lastusetime->fetch_row();
				$mysqlcon->query("UPDATE $table_channel SET path='$channelpath' WHERE cid='$channelid'");
				echo'<td><span class="red">'.$lang['cidnoup'].'</span></td>';

				if($seticon==1 && !in_array($channelid, $nodelete) && $lastusetime[0]<$icontime && !$channel->isSpacer())
				{
					$children=$channel->getChildren();
					if($children=="")
					{
						echo'<td><span class="blue">'.$lang['seticon'].'</span></td>';
						$channel->permAssignByName('i_icon_id', $iconId);
					}
				}
				echo'</tr>';
			}	
		}
		else
		{
			echo'<td><span class="blue">'.$lang['record'].'</span></td></tr>';
			$mysqlcon->query("INSERT INTO $table_channel (cid, lastuse, path) VALUES ('$channelid','$todaydate','$channelpath')");
		}
	}
	echo'</table><br><b>'.$lang['hlcleandb'].'</b><br><table>';
	$count=1;
	$cidexists=$mysqlcon->query("SELECT * FROM $table_channel");
	while($row=$cidexists->fetch_row())
	{
		if(!in_array($row[0], $tscid))
		{
			echo'<tr><td>'.$lang['cid'].$row[0].' : </td><td>'.$row[2].'</td><td><span class="green">'.$lang['cleandb'].'</span><br></td></tr>';
			$count=$count+1;
			if(!$mysqlcon->query("DELETE FROM $table_channel WHERE cid=$row[0]"))
			{
				printf("Errormessage: %s\n", $mysqlcon->error);
			}
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
