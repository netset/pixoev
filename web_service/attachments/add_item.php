                                                                <?php
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
    require_once "../class/config.php";
    require_once dirname(__FILE__)."/web_functions.php";
    $IMG_URL=$URL."/web_service/";
    // echo $service_type=$_REQUEST['service_type'];
    $service_type=$_REQUEST['service_type']; 
	//==================================================New PIXOVENT=====================================================================
	if($_REQUEST['service_type'] == 'create_event')
	{	
		$w=$_FILES["event_icon"]["name"]; 
		//echo "<pre>"; var_dump(); die;
		if(!empty($_REQUEST['user_id']) 
		&& !empty($_REQUEST['event_name'])
		&& !empty($_REQUEST['date_from']) 
		//&& !empty($_REQUEST['date_to'])
		//&& !empty($_REQUEST['location']) 
		//&& !empty($_REQUEST['auto_delete']) 
		//&& !empty($_REQUEST['timer']) 
		//&& !empty($_REQUEST['description'])
		&& !empty($_REQUEST['invite']))
		{
		$list_invite=$_REQUEST['invite'];
		$host=$_REQUEST['user_id'];
		    if(!empty($_FILES["event_icon"]["name"]))
		    {
				$uploadfile = "images/thumb_". basename($_FILES["event_icon"]['name']);
				move_uploaded_file($_FILES["event_icon"]["tmp_name"], $uploadfile);
		    }
		/*	if(!empty($_FILES["image"]["name"]))
			{
				$uploadfile = "images/thumb_". basename($_FILES["image"]['name']);
			        move_uploaded_file($_FILES["image"]["tmp_name"], $uploadfile);
			} */
			$date_time=date('Y-m-d H:i:s');
			$date_from=$_REQUEST['date_from'];				
			$date_to=$_REQUEST['date_to'];
		
					 	//'description'=>$_REQUEST['description'],
                                                if(!empty($_REQUEST['location']))
                                                {
                                                     $data=array('user_id'=>$_REQUEST['user_id'],
						            'event_name'=>$_REQUEST['event_name'],
						            'date_from'=>$date_from,
						            'date_to'=>$date_to,			                                                     
						            'locations'=>$_REQUEST['location'],
                                                            'date_time'=>$date_time,
						            'event_icon'=>$_FILES["event_icon"]["name"],						        'image'=>$_FILES["image"]["name"]);

                                                }
                                                else
                                                {
                                                     $data=array('user_id'=>$_REQUEST['user_id'],
						            'event_name'=>$_REQUEST['event_name'],
						            'date_from'=>$date_from,
						            'date_to'=>$date_to,			                                                     
						        //    'locations'=>$_REQUEST['location'],
                                                            'date_time'=>$date_time,
						            'event_icon'=>$_FILES["event_icon"]["name"],						        'image'=>$_FILES["image"]["name"]);
						     
                                                }
												                                              
					//echo "<pre>";print_r($data); die;		 
 					if($w=event_1($_REQUEST['user_id'],$_REQUEST['event_name'],$date_from))
					{
					
						if($res=mysql_fetch_assoc($w))
						{
							if($res['user_id']==$_REQUEST['user_id'] && $res['event_name']==$_REQUEST['event_name'])
							{
								echo json_encode(array('Status'=>"false",'message'=>'You already created this event before'));
							}
							else
							{
								echo json_encode(array('Status'=>"false",'message'=>'There is already an Event at same date-time at that Location'));
							}
						}
				        }			    
					else
					{     /*  if($cre_new_ev=event($_REQUEST['event_name'],$_REQUEST['user_id'],$date_from,$date_to))
						{
							if($re=mysql_fetch_assoc($cre_new_ev))
							{   
								$re=mysql_fetch_assoc($cre_new_ev);
								echo "<pre>"; print_r($re); die('kjdshjhjkhdsjhdsjhdhj');
								
							} 
						}
						else
						{*/
							if(insert($data,'mange_event'))
							{
								$id=mysql_insert_id();
                                                                $id2=(string)$id;
								$list_values=explode(",",$list_invite);
								foreach($list_values as $u_list)
								{
									//************************
								 $invite_users="INSERT INTO invitation (host_id,guest_id,event_id,accepted) VALUES('$host','$u_list','$id','0')";//die;
								$invite_s=mysql_query($invite_users);					
									//************************
								}
								echo json_encode(array('Status'=>"true",'message'=>'Event created successfully','event_id'=>$id2));
								
								
							}
							else
							{
								echo json_encode(array('Status'=>"False",'message'=>'Event not created.'));
							} 
						/*}*/ 
					}	   
		}	
		else
		{
			echo json_encode(array('Status'=>"false",'message'=>'All fields required'));
		}	
	}
	
	//==================================================New PIOXOVENT=====================================================================
	
	     /*edit_profile*/
        if($_REQUEST['service_type'] =='edit_event')
		{
			$list_invite=$_REQUEST['invite'];
			$host=$_REQUEST['user_id'];
			$id=$_REQUEST['event_id'];
			$date_from=$_REQUEST['date_from'];
		        $date_to=$_REQUEST['date_to'];
			if(!empty($_REQUEST['event_id']) && !empty($_REQUEST['user_id']))
			{ 
                if(!empty($_FILES["event_icon"]["name"]))
		        {				       
				    $uploadfile = "images/thumb_". basename($_FILES["event_icon"]['name']);
				    move_uploaded_file($_FILES["event_icon"]["tmp_name"], $uploadfile);
				}
				if(!empty($_FILES["image"]["name"]))
				{
					$uploadfile = "images/thumb_". basename($_FILES["image"]['name']);
					move_uploaded_file($_FILES["image"]["tmp_name"], $uploadfile);
				} 
			
			      	//pr($_FILES); die;
				$data=array('user_id'=>$_REQUEST['user_id'],
							'event_name'=>$_REQUEST['event_name'],
							'date_from'=>$date_from,
							'date_to'=>$date_to,
							//'description'=>$_REQUEST['description'],
							'locations'=>$_REQUEST['locations'],
							//'auto_delete'=>$_REQUEST['auto_delete'],
							//'timer'=>$_REQUEST['timer'],
							'date_time'=>date('Y-m-d H:i:s'),
							'event_icon'=>$_FILES["event_icon"]["name"],
						//	'image'=>$_FILES["image"]["name"]
						   );
						//pr($data); die;   
						if($res=updat_evnt($data))
						{
							$list_values=explode(",",$list_invite);
								foreach($list_values as $u_list)
								{
									//************************
								$invite_users="INSERT INTO invitation (host_id,guest_id,event_id,accepted)
VALUES('$host','$u_list','$id','0')";
								$invite_s=mysql_query($invite_users);					
									//************************
								}
							echo json_encode(array('Status'=>"true",'message'=>'Event Updated successfully'));
						}
						else
						{
							echo json_encode(array('Status'=>"false",'message'=>'Event not created'));
						}
			}
			else
			{
				echo json_encode(array('Status'=>"false",'message'=>'Enter Proper Input'));
			}  
			 
		}
       /*edit_profile*/ 
                
    /*========START============add_IMAGES/VIDEOs for an Event==================*/
    if($service_type =='add_images_videos')
	{
		if(!empty($_REQUEST["user_id"])  && !empty($_REQUEST["event_id"]) && !empty($_REQUEST["type"])) 
    	        {
			$type=$_REQUEST['type']; 
			//echo $type; die;
			if(!empty($_FILES["file1"]["name"])) 
			{ 
				//echo"Coming"; die;
				if(!empty($_FILES["file1"]["name"]))
				{
					$uploadfile = "images/thumb_". basename($_FILES["file1"]['name']);
					move_uploaded_file($_FILES["file1"]['tmp_name'], $uploadfile);
				}
				
				if($type=="image")
				{
					$thumb='NA';
				}
				elseif($type=="video")
				{
					if(!empty($_FILES["t_file1"]["name"]))
					{
						$uploadfile = "images/thumb_". basename($_FILES["t_file1"]['name']);
						move_uploaded_file($_FILES["t_file1"]['tmp_name'], $uploadfile);
					}
					$thumb=$_FILES["t_file1"]["name"];
				}
				$data=array('user_id'=>$_REQUEST['user_id'],							
						        'ev_id'=>$_REQUEST['event_id'],
							'image'=>$_FILES["file1"]["name"],
							'thumb_img'=>$thumb,
							'type'=>$_REQUEST['type']);
				//pr($data);
						if($res=update_event_files($data))
						{
							echo json_encode(array('Status'=>"true",'message'=>'Pixos added successfully'));
						}
						else
						{
							echo json_encode(array('Status'=>"false",'message'=>'Files cannot be Added'));
						}
				 }
				else
				{
					echo json_encode(array('Status'=>"false",'message'=>'You did not select any file yet'));
				}
			
			}
			else
			{
				echo json_encode(array('Status'=>"false",'message'=>'Enter Proper Input'));
			}
	}
  /*==========END============add_IMAGES/VIDEOs for an Event==================*/  
      
      if($service_type =='delete_event')
	  {
		if(!empty($_REQUEST['user_id']) && !empty($_REQUEST['event_id']))
		{
			if(del_event($_REQUEST['user_id'],$_REQUEST['event_id']))
			{
				echo json_encode(array('Status'=>"true",'message'=>'Desired event deleted successfully'));
			}
			else
			{
				echo json_encode(array('Status'=>"false",'message'=>'Event is not deleted'));
			}
		}
		else
		{
			echo json_encode(array('Status'=>"false",'message'=>'Enter Proper Input'));
		}  
	  }
        
        
     /*--------Functions-----------Functions----------Functions------------Functions--------Functions----------Functions--------*/
	  function update_event_files($data)
      { 
		$u_id = $_REQUEST['user_id'];
		$e_id = $_REQUEST['event_id'];  
		$type = $_REQUEST['type'];  
		$f1 = $_FILES["file1"]["name"]; 

		if(type=="image")
		{
			$f2="NA";
		}
		elseif($type=="video")
		{      
			$f2 = $_FILES["t_file1"]["name"];
		}                                                                                                                                                                                                                                                                                        
		
		$query="insert into event_images(user_id,ev_id,image,thumb_img,type)values('$u_id','$e_id','$f1','$f2','$type')";
			$result=	mysql_query($query) or mysql_error();
			if($result)
			{
				return true;
			}
			else
			{
				return false;
			}  
		//pr($data);
		//echo "yes"; die;  
		
      }
 
 //------------------------------------------------------------------------------------ 
      function updat_evnt($data)
      { 
		$u_id = $_REQUEST['user_id'];
		$e_id = $_REQUEST['event_id'];
        $ei=$_FILES["event_icon"]["name"];                                                                                                                                                                                                                                                                                                 
	//	$image = $_FILES["image"]["name"];                                             event_name=$_REQUEST['event_name'];                                                                                                                                                                                                                                                                                                 
		$date_from = $_REQUEST['date_from'];
		$date_to = $_REQUEST['date_to']; 
		$locations = $_REQUEST['locations'];
		//$auto_delete = $_REQUEST['auto_delete']; 
		//$timer = $_REQUEST['timer'];
		//$description = $_REQUEST['description'];
		$updation_dat_tim=date('Y-m-d H:i:s');
		
		if(!empty($_REQUEST['user_id']) && !empty($_REQUEST['event_id'])) 
		{
				$fields ='';
			if(!empty($_REQUEST['event_name']))
			{
				$findme='=';
				if(!$pos = strpos($fields, $findme))
				{
					$event_name = mysql_real_escape_string($_REQUEST['event_name']);
					$fields .="event_name='".$event_name."' " ;
			    }
			    else
			    {
					$fields .=",event_name='".$event_name."' " ;
				}
			}
			if(!empty($_FILES["event_icon"]["name"]))
			{
				$findme='=';
				if(!$pos = strpos($fields, $findme))
				{
					$fields .="event_icon='".$ei."' " ;
			    }
			    else
			    {
					$fields .=",event_icon='".$ei."' " ;
				}
			}
		/*	if(!empty($_FILES["image"]["name"]))
			{
				$findme='=';
				if(!$pos = strpos($fields, $findme))
				{
					$fields .="image='".$image."' " ;
			    }
			    else
			    {
					$fields .=",image='".$image."' " ;
				}
			}*/				
			if(!empty($_REQUEST['date_from']))
			{
				$findme='=';
				if(!$pos = strpos($fields, $findme))
				{
					$fields .="date_from='".$date_from."' " ;
			    }
			    else
			    {
					$fields .=",date_from='".$date_from."' " ;
				}
			}
			if(!empty($_REQUEST['date_to']))
			{
				$findme='=';
				if(!$pos = strpos($fields, $findme))
				{
					$fields .="date_to='".$date_to."' " ;
			    }
			    else
			    {
					$fields .=",date_to='".$date_to."' " ;
				}
			}
			if(!empty($_REQUEST['locations']))
			{
				$findme='=';
				if(!$pos = strpos($fields, $findme))
				{
				      $locations = mysql_real_escape_string($_REQUEST['locations']);
					$fields .="locations='".$locations."' " ;
			        }
			        else
			        {
					$fields .=",locations='".$locations."' " ;
				}
			}
                        else
                        {
                                        $na=NA;
					$fields .=",locations='".$na."'" ;
                        }
		/*	if(!empty($_REQUEST['description']))
			{
				$findme='=';
				if(!$pos = strpos($fields, $findme))
				{
				      $description = mysql_real_escape_string($_REQUEST['description']);
					$fields .="description='".description."' " ;
			    }
			    else
			    {
					$fields .=",description='".$description."' " ;
				}
			}
			if(!empty($_REQUEST['auto_delete']))
			{
				$findme='=';
				if(!$pos = strpos($fields, $findme))
				{
					$fields .="auto_delete='".$auto_delete."' " ;
			    }
			    else
			    {
					$fields .=",auto_delete='".$auto_delete."' " ;
				}
			}*/
			/*if(!empty($_REQUEST['timer']))
			{
				$findme='=';
				if(!$pos = strpos($fields, $findme))
				{
					$fields .="timer='".$timer."' " ;
			    }
			    else
			    {
					$fields .=",timer='".$timer."' " ;
				}
			}*/
			if(!empty($_REQUEST['date_time']))
			{
				$findme='=';
				if(!$pos = strpos($fields, $findme))
				{
					$fields .="date_time='".$updation_dat_tim."' " ;
			    }
			    else
			    {
					$fields .=",date_time='".$updation_dat_tim."' " ;
				}
			}
            //echo "<pre>"; echo $fields; 
		} 
		$query = "UPDATE mange_event SET $fields WHERE user_id = '$u_id' AND ev_id = '$e_id' "; 
		$result= mysql_query($query) or mysql_error();
		if(mysql_affected_rows() == 0)
		{
			return false;
		}
		else
		{
			return true;
		} 
      }
 //--------------------------------------------------------------------------event_1---------- 
	
	
	
	function event_1($uId,$evName,$dFrom)
	{
	   /* if($ev_name = mysql_real_escape_string($ev_name))
	    { */
                    
            $query = "SELECT * FROM mange_event WHERE  user_id='".$uId."' AND event_name='".$evName."' AND date_from='".$dFrom."' ";   //die('fdgsdgdashkjdsjkhdsjknhfvd');                   
            $result=mysql_query($query) or mysql_error();
			//echo mysql_num_rows($result);
			//echo "<pre>";print_r($result);
			if(mysql_num_rows($result)>0)
			{
                return $result;
			}
			else
			{
				return false;
			}
			
	/*	}  */
	}
	
        function event($ev_name,$u_id,$d_frm,$d_to)
	{
		
	    $ev_name = addslashes($ev_name);
	    $sameloc = addslashes($sameloc);
	    if(!empty($d_to))
	    {			  	
			$query="SELECT * FROM mange_event WHERE (('$d_frm'<=date_to and '$d_frm'>=date_from) or ('$d_to'<=date_to and '$d_to'>=date_from) or ('$d_frm'<=date_from and '$d_to'>=date_to)) ";
			$result=mysql_query($query) or die(mysql_error());
                        if(mysql_num_rows($result)>0)
			{
				return $result;
			}
			else
			{
				return false;
			}
		}
		/*elseif(!empty($d_to))
		{
			$query1="SELECT * FROM mange_event WHERE (('$d_frm'<=date_to and '$d_frm'>=date_from) or ('$d_to'<=date_to and '$d_to'>=date_from) or ('$d_frm'<=date_from and '$d_to'>=date_to)) ";
			$result1=mysql_query($query1) or die(mysql_error());
                        if(mysql_num_rows($result1)>0)
			{
				return $result1;
			}
			else
			{
				return false;
			}
		}
		elseif(empty($d_to))
		{
                	$query2="SELECT * FROM mange_event WHERE '$d_frm' ='".date_from."' ";
			$result2=mysql_query($query2) or die(mysql_error());
			if(mysql_num_rows($result2)>0)
			{
			//echo mysql_num_rows($result2); die;
				return $result2;
			}
			else
			{
				return false;
			}
               }*/
	}	
	
	function del_event($u_id,$ev_id)
	{	
		 $query=" DELETE FROM mange_event WHERE user_id=$u_id and ev_id=$ev_id "; 
	 	$result=mysql_query($query);
	 	if(mysql_query($query) or mysql_error())
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	
    
	
    
	
?>



                            
                            
                            
                            
                            
                            
                            
                            
                            
                            

                            
                            
                            
                            