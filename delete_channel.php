<?php
$starttime=microtime(true);
?>
<!doctype html>
<html>
<head>
  <title>TS-N.NET Channeldeleter - Deletion</title>
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" href="style.css" type="text/css">
</head>  
<body>
<?php
require_once("config.php");
require_once("lang.php");
require_once("ts3_lib/TeamSpeak3.php");

if($accesswithurl==1)
{
	if (isset($_GET['user'])) { $inuser=$_GET['user']; } else { $inuser=false; }
	if (isset($_GET['pass'])) { $inpass=$_GET['pass']; } else { $inpass=false; }
}
else
{
	$inuser=false;
	$inpass=false;
}

if($secure==1)
{
	if($inuser!=false && $inpass!=false)
	{
		$md5pass=md5($inpass);
		if($inuser==$username and $md5pass==$password)
		{
			$loginstatus=1;
		}
		else
		{
			echo'<span class="red">'.$lang['errlogin'].'</span>';
		}
	}
	elseif(isset($_POST['abschicken']))
	{
		$inuser=$_POST["username"];
		$inpass=$_POST["password"];
		$md5pass=md5($inpass);
		if($inuser==$username and $md5pass==$password)
		{
			$loginstatus=1;
		}
		else
		{
			echo'<span class="red">'.$lang['errlogin'].'</span>';
		}
	}
	else
	{
		echo'<form name="form" method="post">
		<table class="tablelogin">
		<tr><td class="tdleft">'.$lang['user'].'</td><td class="tdright"><input type="text" name="username"></td></tr>
		<tr><td class="tdleft">'.$lang['pass'].'</td><td class="tdright"><input type="password" name="password"></td></tr>
		<tr><td class="center" colspan="2"><input type="submit" name="abschicken" class="button" value="',$lang['login'],'" style="width:150px"></td></tr>
		</table></form>
		<script type="text/javascript" language="JavaScript">document.forms["form"].elements["username"].focus();</script>';
	}
}
else
{
	$loginstatus=1;
}


if($loginstatus==1)
{
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

		$tschanarr=$ts3_VirtualServer->channelList();

		foreach($tschanarr as $channel)
		{
			$tscid[]=$channel['cid'];
		}
	
		$todaydate=time();
		$todeletetime=$todaydate-$unusedtime;
		$sqlchanarr=$mysqlcon->query("SELECT * FROM $table_channel WHERE lastuse<$todeletetime $sum");

		echo'<b>'.$lang['hlcleandb'].'</b><br>'.sprintf($lang['cleanch'],date($dateformat,$todeletetime)).'<br><table>';
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
		                echo'<tr><td>'.$lang['cid'].$row[0].' : </td>
				<td>'.substr($row[2], 0, $len).'<b>'.$tschanarr[$row[0]]['channel_name'].'</b></td>';
				try
				{
					$ts3_VirtualServer->channelDelete($row[0], $force=FALSE);
					echo'<td><span class="green">'.$lang['delch'].'</span></td></tr>';
					if(!$mysqlcon->query("DELETE FROM $table_channel WHERE cid=$row[0]"))
					{
						printf($lang['error']."%s\n", $mysqlcon->error);
					}
				}
				catch(Exception $e)
				{
					echo'<td><span class="red"><b>'.$lang['error'].$e->getCode().':</b> '.$e->getMessage().'</span><br></td></tr>';
				}				
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
}

$buildtime=microtime(true)-$starttime;
echo'<br>'.sprintf($lang['sitegen'],$buildtime);
?>
</body>
</html>
