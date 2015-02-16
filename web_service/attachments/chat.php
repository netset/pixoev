<?php
	require_once "../class/config.php";
	require_once "web_functions.php";
	require_once "apn.php";
	$IMG_URL=$URL."/web_service/images/";
	$ATT_URL=$URL."/web_service/attachments/";
	
	if(($_REQUEST['service_type'])=='send_message')
	{	
  		$message_time =date('Y-m-d H:i:s');
  		if(!empty($_POST['user_id']) && !empty($_POST['frnd_id']) && !empty($_POST['message']) && !empty($message_time))
  		{
  			
  			 $data=array('user_id'=>$_POST['user_id'],
					'frnd_id'=>$_POST['frnd_id'],
					'message'=>$_POST['message'],
					'time_zone'=>get_timezone(),
					'message_time'=>$message_time,
					'server_time'=>get_server_date()
					);
			/*if(!empty($_FILES['attach']['name']))
			{
				if(upload_attachment($_FILES['attach']))
				{
					$data['attach_name']=str_replace(" ","",$_FILES["attach"]["name"]);
				}
				else
				{
					echo json_encode(array('status'=>'false','message'=>'File with same name already exist.already exist.Please rename your file.'));
					exit;
				}
			}*/
			
  			if(insert($data,'users_chat'))
  			{
  				$result=get_user_detail($data['user_id']);
                                $user_detail=mysql_fetch_assoc($result);
                                $message=$user_detail['forename']." sent you a private message";
                                iphonepush($data['frnd_id'],$message);
				echo json_encode(array('status'=>'true','message'=>'your message sent successfully'));
			}
			else
			{
				echo json_encode(array('status'=>'false','message'=>'message not sent'));
			}
		}
		else
		{
			 echo json_encode(array('status'=>'false','message'=>'Enter Proper Input'));
		}
	}

	if(($_REQUEST['service_type'])=='get_messages')
	{
  		if(!empty($_POST['user_id']) && !empty($_POST['frnd_id']))
  		{
  			if($result=get_users_msgs($_POST['user_id'],$_POST['frnd_id']))
  			{
  				$friend_detail = mysql_fetch_assoc(get_friend_detail($_POST['frnd_id']));
  				$friend_name=$friend_detail['forename'];
				$friend_detail['last_login']=get_date_acc_to_ip($friend_detail['last_login'],$friend_detail['time_zone']);
				$last_login=date('jS M `y ga', strtotime($friend_detail['last_login']));	
				if($friend_detail['online_status']==1)
				{
					$is_online = '1';
				}
				else
				{
					$is_online = '0';
				}
  				$total_count=mysql_num_rows($result);
			        $page_count=ceil($total_count/30);	
			    	$i=0;
				while($v=mysql_fetch_assoc($result))
				{
					if($v['u_id']==$_POST['user_id'])
				    	{
				    		
				       		$final_messages[$i]['id']=$v['id'];
						$final_messages[$i]['user']=$v['message'];
						if(!empty($v['attach_name']))
						{
							$final_messages[$i]['attachment']=$ATT_URL.$v['attach_name'];
						}
						else
						{
							$final_messages[$i]['attachment']='';
						}
						$final_messages[$i]['message_time']=get_date_acc_to_ip($v['message_time'],$v['time_zone']);
						
					}else{
						
					     	$final_messages[$i]['id']=$v['id'];
						 $final_messages[$i]['friend']=$v['message'];
						 if(!empty($v['attach_name']))
						{
							$final_messages[$i]['attachment']=$ATT_URL.$v['attach_name'];
						}
						else
						{
							$final_messages[$i]['attachment']='';
						}
						$final_messages[$i]['message_time']=get_date_acc_to_ip($v['message_time'],$v['time_zone']);
					} 
					$i++; 

				}

				if(!empty($final_messages))
				{		     		       		$returndata=array_merge(array('status'=>'true'),array('Friendname'=>$friend_name,'last_login_time'=>$last_login,'is_online'=>$is_online),array('total_messages'=>$total_count),array('messages'=>$final_messages),array('page_count'=>$page_count));
		   			echo  json_encode($returndata);
 				 }
				 else
				 { 
				 	$final_message = array();	$returndata=array_merge(array('status'=>'false'),array('Friend_name'=>$friend_name,'last_login_time'=>$last_login_tm,'is_online'=>$is_online),array('total_messages'=>'0'),array('messages'=>$final_message),array('page_count'=>'0'));
		  		 	echo  json_encode($returndata);
				 } 
			}
			else
			{
				echo json_encode(array('status'=>'false','message'=>'No message'));
			}
		}
		else
		{
			 echo json_encode(array('status'=>'false','message'=>'Enter Proper Input'));
		}
	}
	
	if(($_REQUEST['service_type'])=='loadMoreChat')
	{
	 	$user_id =  $_POST['user_id'];
 		$frnd_id =  $_POST['frnd_id'];
 		$page_no =  $_POST['page_no'];
  		if(!empty($_POST['user_id']) && !empty($_POST['frnd_id']) && !empty($_POST['page_no']))
  		{
  			if($messages=loadMoreChat_service($user_id,$frnd_id,$page_no))
  			{
				$page_count1 = no_pages($user_id,$frnd_id);
  				$total_count=mysql_num_rows($page_count1);
				$page_count=ceil($total_count/30);
				if(!empty($messages))
				{
			    		$i=0;
					while($v=mysql_fetch_assoc($messages))
					{
				    		if($v['user_id']==$user_id)
				    		{
						$final_messages[$i]['message_time']=get_date_acc_to_ip($v['message_time'],$v['time_zone']);
							$final_messages[$i]['user']=$v['message'];
						
						}else{
						$final_messages[$i]['message_time']=get_date_acc_to_ip($v['message_time'],$v['time_zone']);
							$final_messages[$i]['friend']=$v['message'];	
						}  
						$i++;
					}
				}
	
				if(!empty($final_messages))
				{
	       $returndata=array_merge(array('status'=>'true'),array('total_messages'=>$total_count),array('messages'=>$final_messages),array('page_count'=>$page_count));
		 			echo json_encode($returndata);
 				}
				else
				{
					echo json_encode(array('status'=>'true','user_chat'=>'You do not have any chat yet'));
				}
			}
			else
			{
				echo json_encode(array('status'=>'false','user_chat'=>'message no'));
			}
		}
		else
		{
			 echo json_encode(array('status'=>'false','user_chat'=>'Enter Proper Input'));
		}
	}

	
///////////////////////////////////////////////////////////////////////////////////////////////////// function to get messages of users chat

function get_users_msgs($user_id,$frnd_id)
{
 $query = "select *,uc.user_id as u_id  from  users_chat as uc INNER JOIN web_users as u  ON uc.frnd_id = u.id where (uc.user_id='".$user_id."' AND uc.frnd_id='".$frnd_id."') OR (uc.user_id='".$frnd_id."' AND uc.frnd_id='".$user_id."') ORDER BY uc.id DESC LIMIT 30";
   $res= mysql_query($query);
   if(mysql_num_rows($res)>0)
   {
   	    return $res;
   }
   else
   {
   	    return false;
   }
}


 // function to load previous chat of users
 function loadMoreChat_service($user_id,$frnd_id,$page_no)
 {
			$offset='';
			$limit=5; //No. of Records
			$offset=($page_no-1)*$limit;  
			$final_messages=array();
			//Getting all last five messages of the conversation
			$query = "select * from  users_chat where user_id='".$user_id."' AND frnd_id='".$frnd_id."' OR user_id='".$frnd_id."' AND frnd_id='".$user_id."' ORDER BY id DESC LIMIT $limit OFFSET $offset";
			$res = mysql_query($query);
			if($res)
			{
			 	return $res;
			}
			else
			{
				return false;
			}
     	
 } 


//function to get no of pages of conversation
 
 function no_pages($user_id,$frnd_id)
 {
 	 $query1 = "select * from  users_chat where user_id='".$user_id."' AND frnd_id='".$frnd_id."' OR user_id='".$frnd_id."' AND  frnd_id='".$user_id."'  ORDER BY id DESC";
 	 $q = mysql_query($query1);
   	 return $q;	
 }
 
 
 function get_friend_detail($frnd_id)
 {
 	 $query1 = "select * from web_users where id=".$frnd_id;
 	 $q = mysql_query($query1);
   	 return $q;	
 }
 
 function get_user_detail($user_id)
	{
		$query = "SELECT * FROM web_users WHERE id=".$user_id;
		$result=mysql_query($query) or mysql_error();
		if(mysql_num_rows($result)>0)
		{
			return $result;
		}else
		{
			return false;
		}
	} 

 function  upload_attachment($file)
 {
	 $file["name"]=str_replace(" ","",$file["name"]);
 	 //$allowedExts = array("jpg", "jpeg", "gif", "png");
	//$extension = end(explode(".", $file['name']));
		if ($file["error"] > 0)
		{
			//echo "Return Code: " . $image["error"] . "<br>";
			return false;
		}
		else
		{
			if (file_exists("attachments/" . $file["name"]))
			{
				//echo $image["name"] . " already exists. ";
				return false;
			}
				if(move_uploaded_file($file["tmp_name"],"attachments/" . $file["name"]))
				{
					return true;
				}
				else
				{
					return false;
				}
		}
 }

                            