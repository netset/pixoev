                                                                                                <?php
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
    
	require_once "../class/config.php";
	//require_once dirname(__FILE__)."/facebook.php";
	require_once dirname(__FILE__)."/web_functions.php";
	//require_once dirname(__FILE__).'/Swift-5.0.2/lib/swift_required.php';
	set_time_limit(0);
	
	$IMG_URL=$URL."/web_service/images/thumb_";
        $service_type=$_REQUEST['service_type']; 
     // echo $service_type=$_REQUEST['service_type']; 
	/*===========================================Pixovent===============================================*/
	if($service_type == 'register')
	{
		if(!empty($_REQUEST['uname']) && !empty($_REQUEST['email']) && !empty($_REQUEST['password']))   
		{
			if(!empty($_FILES["profile_pic"]["name"]))
			{
				$uploadfile = "images/thumb_". basename($_FILES["profile_pic"]['name']);
				move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $uploadfile);
		    }
			$data=array('uname'=>$_REQUEST['uname'],
				    'email'=>$_REQUEST['email'],
				    'age'=>$_REQUEST['Age'],
				    'profile_pic'=>$_FILES['profile_pic']['name'],
				    'reg_since'=>date("Y-m-d"),
					'time_zone'=>get_timezone(),
					//'server_time'=>get_server_date(),
					'password'=>md5($_REQUEST['password'])
					);
			if(!check_email($data['email']))
			{
				var_dump($data);die;
				insert($data,'users');
				$id=mysql_insert_id();
                                //echo $id="'".$id1."'";die;
				$usr_detail= u_detail($data['email']);
                                //echo "<pre>"; print_r($usr_detail); die;
				if(!empty($usr_detail['profile_pic']))
				{
					$usr_detail['profile_pic']=$IMG_URL.$usr_detail['profile_pic'];
				}					
				echo json_encode(array('Status'=>"True",'message'=>'User successfully registered','user_id'=>$id,'user_name'=>$data['uname'],'Age'=>$usr_detail['age'],'profile_picture'=>$usr_detail['profile_pic']));
			}
			else
			{
				echo json_encode(array('Status'=>"false",'message'=>'Email already exist'));
			}
		}
		else
		{
			echo json_encode(array('Status'=>"false",'message'=>'Enter proper input'));
		}
	}
	
	/*=-start=-=-=-=--=login=-=-=-=-=-=-=-*/
	
	if($service_type == 'login')
	{
		if(!empty($_REQUEST['email']) &&  !empty($_REQUEST['password']))
		{
			$email=$_REQUEST['email'];
			$password=$_REQUEST['password'];
		   // echo "<pre>"; var_dump($_REQUEST); 
			if($res = login($email,$password))
			{
				//echo "<pre>"; var_dump($res);
				update_login($res['id']);
				echo json_encode(array('Status'=>"True",'message'=>'User successfully logged in','user_id'=>$res['user_id'],'user_name'=>$res['uname'],'age'=>$res['age']));  
			}
			
		}
		else
		{
			echo json_encode(array('Status'=>"false",'message'=>'Enter proper input'));
		}
	}
	
	
	function login($email,$password)
	{
		$password1=md5($password);
		if($em=!u_detail($email))
		{
			echo json_encode(array('Status'=>"false",'message'=>'User doesnot exist'));
		}
		else
		{
			$query=" SELECT user_id, uname,age, password FROM users WHERE email='$email' ";
			$result=mysql_query($query);
	        	$row_1=mysql_fetch_assoc($result);
			if($password1 == $row_1['password'])
			{
				return $row_1;
			}
			else
			{
				echo json_encode(array('Status'=>"false",'message'=>'You have entered the wrong password'));
			}
		}			
	}
	/*=-end=-=-=-=--=login=-=-=-=-=-=-=-*/
	// print_r($service_type);
	if($service_type == 'list_of_events')
	{		
		if(!empty($_REQUEST['user_id']))
		{
			//echo $_REQUEST['user_id']; 
			if($res=list_evnts($_REQUEST['user_id']))
			{
				echo json_encode(array('Status'=>true,'events_list'=>$res));
			}
			else
			{
				$events=array();
				echo json_encode(array('Status'=>"false",'events_list'=>$events));
			}
		}
		else
		{
			echo json_encode(array('Status'=>"false",'events_list'=>'Enter proper Input'));
		}
	}
	
	//=================================================
	
	if($service_type == 'show_gallery')
	{		
		if(!empty($_REQUEST['user_id']) && !empty($_REQUEST['event_id']))
		{
			//echo $_REQUEST['user_id']; 
			if($res=shw_gall($_REQUEST['user_id'],$_REQUEST['event_id']))
			{
				while($row=mysql_fetch_assoc($res))
				{
					/*if(!empty($row['image']))
					{
						$row['image']=$IMG_URL.$row['image'];
					} */
					if(!empty($row['image']))
					{
						$row['image']=$IMG_URL.$row['image'];
					} 

					if(!empty($row['thumb_img']))
					{
						$row['thumb_img']=$IMG_URL.$row['thumb_img'];
					} 
					else
					{
						$row['thumb_img']="NA";
					}
					/*if(!empty($row['file2']))
					{
						$row['file2']=$IMG_URL.$row['file2'];
					} 
				    if(!empty($row['file3']))
					{
						$row['file3']=$IMG_URL.$row['file3'];
					} 
					if(!empty($row['file4']))
					{
						$row['file4']=$IMG_URL.$row['file4'];
					} 
					if(!empty($row['file5']))
					{
						$row['file5']=$IMG_URL.$row['file5'];
					} 	
					if(!empty($row['file6']))
					{
						$row['file6']=$IMG_URL.$row['file6'];
					} 
					if(!empty($row['file7']))
					{
						$row['file7']=$IMG_URL.$row['file7'];
					} */	
				
					$gallery[]=$row;
				}
				echo json_encode(array('Status'=>true,'gallery'=>$gallery));
			}
			else
			{
				echo json_encode(array('Status'=>"false",'gallery'=>'Empty'));
			}
		}
		else
		{
			echo json_encode(array('Status'=>"false",'gallery'=>'Enter proper Input'));
		}
	}
	//=================================================
	
	if(($_REQUEST['service_type'])=='all_imgs_vids_by_usr')
{   
	if(!empty($_REQUEST["user_id"]))
	{   
  		if($data= image_video_by_user($_REQUEST["user_id"]))
     		{
     						
     			while($row=mysql_fetch_assoc($data))
       			{
                  if(!empty($row['date_time']))
       				{	
       					$row['date_time']=get_time_diff_in_str($row['date_time']);
      				}	 	
       			   if(!empty($row['profile_pic']))
       				{	
       					$row['profile_pic'] = $IMG_URL."images/".$row['profile_pic'];
      				}	     
      				if($row['cover_image'])
       				{	
       					$row['cover_image'] = $IMG_URL."images/".$row['cover_image'];
      				}
      				if($row['file1'])
      				{
       					$row['file1'] = $IMG_URL."images/".$row['file1'];
       				}
               			if($row['file2'])
                		{
               			    	$row['file2'] = $IMG_URL."images/".$row['file2'];
              	        	}
               			if($row['file3'])
               			{
              	 			$row['file3'] =$IMG_URL."images/".$row['file3'];
              			}
		        	if($row['file4'])
                        	{
 				        $row['file4'] = $IMG_URL."images/".$row['file4'];
                        	}
               			if($row['file5'])
                        	{
                   		        $row['file5'] = $IMG_URL."images/".$row['file5'];
                       		}
                        	if($row['file6'])
                        	{
                        	   	$row['file6'] = $IMG_URL."images/".$row['file6'];
                       	 	}
                        	if($row['file7'])
                        	{
                         		$row['file7'] = $IMG_URL."images/".$row['file7'];  
                        	}  
                  		$res[]=$row;
	        	}	
			echo json_encode(array('status'=>"true",'result'=>$res));
			}
			
	 	else
		{
			echo json_encode(array('Status'=>"false",'message'=>'No file Yet'));
		}
	}
	else
	{
		echo json_encode(array('Status'=>"false",'message'=>'Enter Proper input'));
	}
  }
//------------------/* all_img_vid _by _usr */-------------------------------------------------------

	if($service_type == 'f_list')
	{
		if(!empty($_REQUEST['u_id']))
		{
			if($data= friend_list($_REQUEST['u_id']))
			{
				while($row=mysql_fetch_assoc($data))
       			{
					//echo "<pre>"; print_r($row); die;
				/*(	if(!empty($row['uname']))
       				{	
       					$row['user_name'] = $row['uname'];
      				} */
      				$frnd[]=$row;	
				}
				echo json_encode(array('Status'=>"True",'friend_list'=>$frnd));
			}
			else
			{
				echo json_encode(array('Status'=>"false",'message'=>'No friend yet'));
			}
		}
		else
		{
			echo json_encode(array('Status'=>"false",'message'=>'Please enter proper input'));
		}		
	}
		
    if($service_type == 'ad_friend_req')
	{
		if(!empty($_REQUEST['user_id']) && !empty($_REQUEST['friend_id']) && !empty($_REQUEST['response']))
		{	 
			if($_REQUEST['response']==1)
            {	
				insert_or_delFreq($_REQUEST['user_id'],$_REQUEST['friend_id'],1);
			    $ad=last_ad_u_dtails($_REQUEST['user_id'],$_REQUEST['friend_id'],1,1);
			    echo json_encode(array('Status'=>"True",'request_accepted'=>$ad));
            }
            elseif($_REQUEST['response']==2)
			{
				$ad=last_ad_u_dtails($_REQUEST['user_id'],$_REQUEST['friend_id'],2,2);
				$in_3=insert_or_delFreq($_REQUEST['user_id'],$_REQUEST['friend_id'],2);			   
			   	echo json_encode(array('Status'=>"True",'request_rejected'=>$ad));
			}
		}
		else
		{
			echo json_encode(array('Status'=>"false",'message'=>'Enter proper Input'));
		}
	}
	
	if($service_type == 'search_friends')
	{
		if(!empty($_REQUEST['fname']))
		{
			if($data=search_f($_REQUEST['fname'],1))
			{
				if($row=mysql_fetch_assoc($data))
       			{
					$frnd[]=$row;	
				}
				echo json_encode(array('Status'=>"True",'friend'=>$frnd));
			}
			else
			{
				echo json_encode(array('Status'=>"false",'message'=>'Sorry!,No user exits by that name'));
			}			
		}
		else
		{
			echo json_encode(array('Status'=>"false",'message'=>'Please enter proper input'));
		}		
	}
        
        /*===start==forgot==password===*/

        if($service_type=='forgot_password')
	{
		$email=$_REQUEST['email'];
		if(!empty($email))
		{
			if(check_email($email))
			{
				if($password=forgot_password($email))
				{
					$to = "$email";
					$subject = "Password Recovery";
					$from = "pixovent@pixovent.com";
					$headers = "Content-type: text/html\r\n"; 
					$headers .= "From:" . $from;
					$message = "<html><body>Hi, \n \t, <p>Your password is ".$password."</p><p>Thanks</p><p>Pixovent Team</p></body></html>";
					if(mail($to,$subject,$message,$headers))
					{
						echo json_encode(array('Status'=>True,'message'=>'New Password has been send successfully at your email id'));
					}
					else
					{
						echo json_encode(array('Status'=>True,'message'=>'Mail function could not instantiate'));
					}
				}
				else
				{
					echo json_encode(array('Status' =>false,'message'=>'Some error occur'));
				}
			}
			else
			{
				echo json_encode(array('Status'=>false,'message'=>'Invalid Email'));
			}
		}
		else
		{
			echo json_encode(array('Status'=>false,'message'=>'Enter Email'));
		}
	}
       /*===end==forgot==password===*/






	/*==========|<<<<<<<<FUNCTIONS><Pixovent>>>>|===================*/
	function search_f($fname)
	{
		$query=" SELECT * FROM users WHERE uname LIKE '%$fname%' ";
		$result=mysql_query($query) or mysql_error();			
		if(mysql_num_rows($result) >0)
		{
			return $result;
		}
		else
		{
			return false;
		}
	} 
	
    function u_detail($email)
	{
		//$password=md5($password);
		$query=" SELECT * FROM users WHERE email='$email' ";
	    $result=mysql_query($query);
		$row=mysql_fetch_array($result);
		return $row;
	}
	
	function last_ad_u_dtails($u_id,$f_id,$identifier)
	{
	   if($identifier==1)
	   {
			$fd=f_dtail($f_id);
		//echo "<pre>"; echo $identifier; 
		    $query=" SELECT * FROM friends WHERE (user_id=$u_id AND friend_id=$f_id) AND (user_id=$f_id AND friend_id=$u_id)"; //die("asdasjdkahd");
			$result=mysql_query($query);
	   }
	   elseif($identifier==2)
	   {
			$fd=f_dtail($f_id);
			//echo "<pre>"; echo $identifier; 
		    $query=" SELECT * FROM friends WHERE user_id=$u_id AND friend_id=$f_id ";  //die("ssssssssssss");
			$result=mysql_query($query);
		}
		$row=mysql_fetch_assoc($result);
		$row['friend_name']=$fd['uname'];
		return $row;		
	}
	
	function insert_or_delFreq($u_id,$f_id,$h1)
	{
		if($h1==1)
	    {
			$query=" INSERT into friends (req_id, user_id, friend_id) VALUES ('','".$f_id."','".$u_id."') "; 
			$result=mysql_query($query) or mysql_error();
			return $result;	
		}
	    elseif($h1==2)
	    {
		 echo   $query =" DELETE FROM friends WHERE  user_id=$u_id and friend_id=$f_id "; 
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
	}
	
	    function f_dtail($f_id)
	    {
		    $query="select user_id, uname from users where user_id=".$f_id ;
			$res= mysql_query($query);
			if(mysql_num_rows($res)>0)
			{
				$row=mysql_fetch_assoc($res);
				return $row;
			}
			else
			{
				return false;
			}			
		}
	
		function del_Freq($u_id,$frnd,$resp)
		{
		 echo   $query =" DELETE FROM friends WHERE  user_id=$u_id and friend_id=$frnd "; die;
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
 
     function FrndReq_exist($usr_id,$frnd)
     {   
		        $query = "SELECT req_id FROM friends WHERE user_id='".$usr_id."' AND friend_id='".$frnd."' "; 
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
	 
	function alrdy_acceptd_frnd_req($usr_id,$frnd,$resp,$h1)
    {   
		if($resp==1 && $h1==1)
	    {
		    //$query =" SELECT req_id FROM friends WHERE user_id='".$usr_id."' AND friend_id='".$frnd."'  "; 
		 echo  $query =" SELECT req_id FROM friends WHERE (user_id=$usr_id AND friend_id=$frnd) AND (user_id=$frnd AND friend_id=$usr_id) "; 
		    $result=mysql_query($query) or mysql_error();
		}
		elseif($resp==2 && $h1==2)
		{
		echo $query =" SELECT req_id FROM friends WHERE user_id='".$frnd."' AND friend_id='".$usr_id."'  "; 
			$result=mysql_query($query) or mysql_error();	
	    }
			if(mysql_num_rows($result)>0)
			{
				return true;
			}
			else
			{
				return false;
			}
	}

	function list_evnts($user_id,$IMG_URL)
	{              
               //$query="SELECT event_id FROM invitation WHERE guest_id=$user_id AND accepted=1"; 
         echo  $query="SELECT * FROM invitation WHERE user_id=$user_id"; die; 
	       if( $res= mysql_query($query))
               {       
                       while($row=mysql_fetch_assoc($res))
		       {
			   /*	if(!empty($row['image']))
				{
					$row['image']=$IMG_URL.$row['image'];
				} 
				if(!empty($row['event_icon']))
				{
					$row['event_icon']=$IMG_URL.$row['event_icon'];
				} */
                                $events[]=$row;                                                            
                       }                         
                }
                //echo "<pre>";  print_r($events); die;
                

                $i=0;       
                foreach($events[$i] as $e)
                {
                       $list_e=list_evnts1($user_id,$e[$i]['event_id']);                    
                       $evList[]=$list_e;
                $i++;
                }
                              
                      echo "<pre>"; print_r($evList); die;
                      //$list_e=list_evnts1($user_id,$row['event_id']);
                      // echo "<pre>"; print_r($evList); die;    
		      
                       //$list_e;
                if(!empty($list_e))
   	 	{
   	              return $list_e;
 	 	}else{
   	  	      return false;
 		}
	       
         }

       function list_evnts1($user_id,$event_id,$IMG_URL)
       {              
               $query="SELECT ev_id, user_id, event_name, locations, date_from, date_to, event_icon, image FROM mange_event WHERE ev_id='".$event_id."' OR user_id='".$user_id."'";  
                $res= mysql_query($query);
                if(mysql_num_rows($res)>0)
   	        {
                        while($res1=mysql_fetch_assoc($res))
                        {
   		            $res2[]=$res1;
                        }
                        return $res2;
 	 	}
   	 	else
  		{
   	  		return false;
 		}
       }
        
	function shw_gall($u_id,$ev_id)
	{
	 //  $query=" SELECT image, file1, file2, file3, file4, file5, file6, file7 from mange_event where user_id=$u_id AND ev_id=$ev_id ";
		$query="select img.user_id,img.ev_id as event_id,img.image,img.thumb_img,img.type,u.user_id,u.uname from users as u,event_images as img where img.user_id=u.user_id and img.ev_id='".$ev_id."'";
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
	
	function friend_list($u_id)
	{
	    /*$query=" SELECT f.id,u.uname as user_name from friends as f LEFT JOIN users as u ON u.user_id=f.friend_id 
	    Where f.user_id='".$u_id."' AND u.user_id=f.friend_id AND status_u=1 order by id "; */
	    
	    $query=" SELECT f.req_id,u.uname as user_name from friends as f LEFT JOIN users as u ON u.user_id=f.friend_id 
	    where f.user_id='".$u_id."' AND u.user_id=f.friend_id order by f.req_id " ;
  		$result=mysql_query($query) or mysql_error();			
		if(mysql_num_rows($result) >0)
		{
			return $result;
		}
		else
		{
			return false;
		} 
	}
	
	
	
	
	
	//=======================Pixovent========================/
	
	
	
/*===========================================Pixovent===============================================*/
	
	
 	

	
	
?>


                            
                            
                            
                            
                            
                            