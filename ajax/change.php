<?php
require_once '../database.php';
session_start();
$connect = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$flag=1;
if(isset($_SESSION['start']) && isset($_POST['q']))
{
	$last_time=json_decode($_POST['q']);
	$last_time=$last_time->last_time;
	$id=$_SESSION['start'];
	$query="SELECT * FROM total_message WHERE user1='$id' or user2='$id'  ORDER BY id DESC";
	// $query="SELECT * FROM total_message WHERE identifier like '%:$id' or '$id:%'";
	if($result=$connect->query($query)) 
	{
		if ($result->num_rows > 0) 
		{
			$array = array();
			$ln=strlen($id);
    		while($row = $result->fetch_assoc()) 
    		{
    			if($row['id']==$last_time && $flag!=0)
    			{
    				break;
    			}
    			else if($flag!=0)
    			{
    				$last_time=$row['id'];
    				$flag=0;
    			}

				$value=$row['identifier'];
				$st=substr($value, 0,$ln);
				if($st!=$id)
				{
					$query="SELECT username,name from login where login_id='$st'";
					if($result1=$connect->query($query)) 
					{
						if($result1->num_rows>0)
						{
							$fetch=$result1->fetch_assoc();
							if(substr($row['time'],4,11)==date("d M Y", time()+12600))
								$row['time']=substr($row['time'],16,5);
   							else if(substr($row['time'],7,8)==date("M Y", time()+12600) && substr($row['time'], 4,2)-date("d")<7)
								$row['time']=substr($row['time'],0,3);
							else if(substr($row['time'],11,4)==date("Y", time()+12600))
								$row['time']=substr($row['time'],4,6);
							else
								$row['time']=substr($row['time'],4,11);
							$fetch=array_merge($fetch,['time'=>$row['time']]);
							$array=array_merge($array,[$fetch]);
						}
					}
				}
				
				else
				{
					$st=substr($value,$ln+1);
					$query="SELECT username,name from login where login_id='$st'";
					if($result1=$connect->query($query)) 
					{
						if($result1->num_rows>0)
						{
							$fetch=$result1->fetch_assoc();
							if(substr($row['time'],4,11)==date("d M Y", time()+12600))
								$row['time']=substr($row['time'],16,5);
   							else if(substr($row['time'],7,8)==date("M Y", time()+12600) && substr($row['time'], 4,2)-date("d")<7)
								$row['time']=substr($row['time'],0,3);
							else if(substr($row['time'],11,4)==date("Y", time()+12600))
								$row['time']=substr($row['time'],4,6);
							else
								$row['time']=substr($row['time'],4,11);
							$fetch=array_merge($fetch,['time'=>$row['time']]);
							$array=array_merge($array,[$fetch]);
						}
					}
				}
			}
			$array=array_merge($array,[['last_time'=>$last_time]]);
			echo json_encode($array);
				// var_dump($array);
		}
		else
		{
			echo json_encode(null);
		}
	}
	else{
		echo "Query Failed";
	}
}
else{
	header('Location:../login.php');
}
?>