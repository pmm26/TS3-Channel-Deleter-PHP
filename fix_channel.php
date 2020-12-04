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
require_once('ts3_lib/TeamSpeak3.php');

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


  $channels = $ts3_VirtualServer->channelList();

  foreach ($channels as $channel) {
    var_dump($channel->permAssignByName('i_channel_needed_permission_modify_power', 75));
  }

} catch(Exception $e)
{
  echo $e;
}

?>

