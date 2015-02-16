                                <?php	
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
    
   
    require_once "../class/config.php";
	//require_once dirname(__FILE__)."/facebook.php";
	require_once dirname(__FILE__)."/web_functions.php";
	set_time_limit(0);
	$IMG_URL=$URL."/web_service/images/thumb_";
	$service_type=$_REQUEST['service_type'];
	//echo $service_type=$_REQUEST['service_type'];
	/*==================pixovent=======invitation==========================*/
	
	if(($_REQUEST['service_type']) =='send_friend_req')
	{
		if(!empty($_REQUEST['user_id']) && !empty($_REQUEST['friend_id']))
		{
			if($_REQUEST['user_id'] != $_REQUEST['friend_id'])
			{
				if(check_friend_exist($_REQUEST['friend_id']))
				{
					$data1=array('user_id'=>$_REQUEST['user_id'],
							'friend_id'=>$_REQUEST['friend_id']
							);
					if(alrdy_sent_frnd_req($_REQUEST['user_id'],$_REQUEST['friend_id']))
					{			
						if(insert($data1,'friends'))
						{
							$id=mysql_insert_id();
							//update_FrndReq($id);
							echo json_encode(array('status'=>"true",'message'=>'Request sent successfully','request_id'=>$id)); //,'friend_id'=>$_REQUEST['f_id']
						}
						else
						{
							echo json_encode(array('status'=>"false",'message'=>'Request not send'));
						} 
					}
					else
					{
						echo json_encode(array('status'=>"false",'message'=>'Already sent Friend Request to this user'));
					}
				} 
				else
				{
					echo json_encode(array('status'=>"false",'message'=>'User not exist'));
				}
			}
			else
			{
				echo json_encode(array('Status'=>"false",'message'=>'You cannot send friend request to yourself'));
			}	 		  
		}
		else
		{
			echo json_encode(array('Status'=>"false",'message'=>'Please enter proper input'));
		}
	}
	 
	
	 
	function update_FrndReq($id)
	{
		    $query =" UPDATE friends SET status_u='1' WHERE req_id='".$id."' ";
			$result=mysql_query($query) or mysql_error();
			if(mysql_affected_rows())
			{
				return true;
			}
			else
			{
				return false;
			}
	}
	/*--start--check_friend_exist--*/
	function check_friend_exist($f_id)
	{
		   //echo $f_id; die;
			 $query = "SELECT user_id FROM users WHERE user_id='".$f_id."' "; 
			$result=mysql_query($query) or mysql_error();
			if(mysql_num_rows($result)>0)
			{
				return true;
			}
			else
			{
				return false;
			}
	}
	/*---end--check_friend_exist--*/
	
	/*--start--alrdy_sent_frnd_req--*/
        function alrdy_sent_frnd_req($u_id,$f_id)
	{
		   //echo $f_id; die;
			 $query = "SELECT * FROM friends WHERE ((user_id='".$u_id."' AND friend_id='".$f_id." ') OR (user_id='".$f_id."' AND friend_id='".$u_id."'))"; 
			$result=mysql_query($query) or mysql_error();
			if(mysql_num_rows($result)>0)
			{
				return false;
			}
			else
			{
				return true;
			}
	}
	/*---end----alrdy_sent_frnd_req*/
	
	

	/*==================pixovent=======invitaion==========================*/



?>

                            
                            