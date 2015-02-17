<?php


	require_once "../class/config.php";
	require_once "web_functions.php";
	

	

        //require_once "apn.php";
        //echo $URL; die;
	$IMG_URL=$URL."/web_service/images/thumb_";
        // echo $IMG_URL; die;
	$service_type=$_REQUEST['service_type']; 

	if(($_REQUEST['service_type']) =='event_invitation')
	{
		if(!empty($_REQUEST['host_id']) && !empty($_REQUEST['guest_id']) && !empty($_REQUEST['event_id']))
		{
			if($_REQUEST['host_id'] != $_REQUEST['guest_id'])
			{
				if(check_creator_inv_exist($_REQUEST['host_id'],$_REQUEST['event_id']))
				{
				 
						$data1=array('host_id'=>$_REQUEST['host_id'],
							'guest_id'=>$_REQUEST['guest_id'],
							'event_id'=>$_REQUEST['event_id']
							);
						if(!alrdy_sent_invit($_REQUEST['host_id'],$_REQUEST['guest_id'],$_REQUEST['event_id']))
						{		
							if(insert($data1,'invitation'))
							{
								$id=mysql_insert_id();
								$gd=last_inv_details($id);
								if($row=mysql_fetch_assoc($gd))
								{
									if($gd1=dtail_g($data1['guest_id'],2))
									{
										$row_1=mysql_fetch_assoc($gd1);
										echo json_encode(array('status'=>"true",'message'=>'Invitation sent successfully','invitation_id'=>$id,'Guest_details'=>$row_1));
									}
								}
							}
							else
							{
								echo json_encode(array('status'=>"false",'message'=>'Invitation not sent'));
							} 
						}
						else
						{
							echo json_encode(array('status'=>"false",'message'=>'Already Invited this user for same Event'));
						}
					
			  
				} 
				else
				{
					echo json_encode(array('status'=>"false",'message'=>'The event does not exist for this user'));
				}
			}
			else
			{
				echo json_encode(array('Status'=>"false",'message'=>'You can not invite yourself'));
			}	 		  
		}
		else
		{
			echo json_encode(array('Status'=>"false",'message'=>'Please enter proper input'));
		}
	}
	 
	
	
	if(($_REQUEST['service_type']) == 'event_detail')
	{
		if(!empty($_REQUEST['event_id'])) 
		{
			if($res=event_detail($_REQUEST['event_id'])) 
			{
				$i=0;
				$guest=array();                                                   
				while($row=mysql_fetch_assoc($res))
				{
					
                    if(!empty($row['event_id']))
                    {
                        $guest[$i]['event_id']=$row['event_id'];
                    } 
                    if(!empty($row['event_name']))
                    {
                        $guest[$i]['event_name']=$row['event_name'];
		    }
                    if(!empty($row['user_id']))
                    {
                        $guest[$i]['user_id']=$row['user_id'];
		    } 
		    if(!empty($row['date_from']))
                    {
                        $guest[$i]['date_from']=$row['date_from'];
		    } 
		    if(!empty($row['date_to']))
                    {
                        $guest[$i]['date_to']=$row['date_to'];
		    }					 
                    if(!empty($row['locations']))
                    {
                                                $guest[$i]['locations']=$row['locations'];
					}					 
					if(!empty($row['event_icon']))
					{
						$guest[$i]['event_icon']=$IMG_URL.$row['event_icon'];
					}
					if(!empty($row['image']))
					{
						$guest[$i]['image']=$IMG_URL.$row['image'];
					}
					if(!empty($row['user_id']))
					{
						$guest[$i]['event_creator_id']=$row['user_id'];
					}
										
					if($gccg = get_CountofComingGuest($_REQUEST['event_id']))
					{
						 $cmngC=mysql_fetch_assoc($gccg);						
						$guest[$i]['guest_comingcount']=$cmngC['guest_count'];
					}
					else
					{
					    $guest[$i]['guest_comingcount']='1';
					}
					if($getGuestDetail = comingGuestDetailArray($_REQUEST['event_id'],$IMG_URL))
				{


						$g=0;
						foreach($getGuestDetail as $gGD[$g])
						{
							$getGuestDetail['profile_pic'] = $IMG_URL.$gGD[$g]['profile_pic']; 
						$g++;
						}
						
						$guest[$i]['guest_invited']=$gGD;						
					}
					else
					{
						$guest[$i]['guest_invited']=[];
					}
					if($tei1=get_total_EventImage($row['user_id'],$row['event_id']))
					{ 
						$px1=mysql_fetch_assoc($tei1);
			         //   echo "<pre>"; print_r($px1['image_count']); //die;											
						$tei2=get_Event_Image($row['user_id'],$row['event_id']);
						$px2=mysql_fetch_assoc($tei2);						
			           // echo "<pre>"; print_r($px2['defaultimage']); //die;				
						
						if($px1['image_count']!=0) 
						{                                      
							$guest[$i]['total_pixos']=$px1['image_count'] + $px2['defaultimage'];
						}
						else
						{
							$guest[$i]['total_pixos']=$px2['defaultimage']; 
						} 
                    }
             
                $i++;                                       
				}
				//echo "<pre>"; print_r($guest); die;
				echo json_encode(array('Status'=>"true",'event_detail'=>$guest));                              
			}
			else
			{
				echo json_encode(array('Status'=>"false",'event_detail'=>'Invalid Event id'));
			}	
		}	
		else
		{
			echo json_encode(array('Status'=>"false",'message'=>'Enter proper input'));
		}		
	}

	
	if($service_type == 'response_of_invitation')
	{

		if(!empty($_REQUEST['host_id']) && !empty($_REQUEST['guest_id']) && !empty($_REQUEST['event_id']) && !empty($_REQUEST['response']))
		{

			if($s= !alrdy_acceptd_inv($_REQUEST['host_id'],$_REQUEST['guest_id'],$_REQUEST['event_id'],$_REQUEST['response']))
			{
                if($_REQUEST['response']==1)
                {	
				    $w=ad_inv($_REQUEST['host_id'],$_REQUEST['guest_id'],$_REQUEST['event_id'],1);			      
				    $ad=last_inv_added_u_dtails($_REQUEST['host_id'],$_REQUEST['guest_id'],$_REQUEST['event_id'],1);
				    echo json_encode(array('Status'=>"true",'accepted_by'=>$ad));
				}
                elseif($_REQUEST['response']==2)
			    {
					$x=ad_inv($_REQUEST['host_id'],$_REQUEST['guest_id'],$_REQUEST['event_id'],2);
			        $ad=last_inv_added_u_dtails($_REQUEST['host_id'],$_REQUEST['guest_id'],$_REQUEST['event_id'],2);
		            echo json_encode(array('Status'=>"true",'rejected_by'=>$ad));
			    }
				else
				{
					echo json_encode(array('Status'=>"false",'message'=>'Either the guest is not invited or event is not created yet'));
				}
		    }
	        elseif($_REQUEST['response']==1)
	        {
			    echo json_encode(array('status'=>"false",'message'=>'Already Accepted invitation'));
		    }
		    elseif($_REQUEST['response']==2)
		    {
			    echo json_encode(array('status'=>"false",'message'=>'Already Rejected invitation'));
		    }
        }
	    else
		{
		    echo json_encode(array('Status'=>"false",'message'=>'Enter proper Input'));
		}
			
	}
	
	if($service_type == 'coming')
	{
		if(!empty($_REQUEST['event_id']) && !empty($_REQUEST['guest_type']))
		{
			//var_dump($_REQUEST); 
			$guest=array();
			if($_REQUEST['guest_type'] == 1)
			{
				//var_dump($_REQUEST); die("Here it is in 1");
				if($data=coming($_REQUEST['event_id']))
				{ 
					//var_dump($_REQUEST); die;
					while($row=mysql_fetch_assoc($data))
					{
						$ud=dtail_g($row['guest_id'],1);
						$ud1=mysql_fetch_assoc($ud);	 	
						$guest[]=$ud1;	
				        }
					echo json_encode(array('Status'=>"true",'Guest coming to the Event'=>$guest));
				}
				else
				{
					$guest['coming']='1';
				}
			}
			elseif($_REQUEST['guest_type'] == 2)
			{
				//var_dump($_REQUEST); die("Here it is in 2");
				if($data=not_coming($_REQUEST['event_id']))
				{ 
					//var_dump($data); die;
					while($row=mysql_fetch_assoc($data))
					{
						//var_dump($row); die;
						$ud=dtail_g($row['guest_id'],2);
						$ud2=mysql_fetch_assoc($ud);
						//var_dump($ud2); die;	 	
						$guest[]=$ud2;	
					}
					echo json_encode(array('Status'=>"true",'Guest Not coming to the Event'=>$guest));
				}
				else
				{
					$guest['not_coming']='0';
				}
			}
			else
			{
				echo json_encode(array('Status'=>"false",'message'=>'No one is Invited Yet'));
			} 
		}
		else
		{
			echo json_encode(array('Status'=>"false",'message'=>'Please enter proper input'));
		}		
	}

         

	if($service_type == 'inv_i_recvd')
	{
		if(!empty($_REQUEST['user_id']))
		{
			$uid=$_REQUEST['user_id'];                    
			$invitations=InvIrecvd($uid,$IMG_URL);  
		        //echo "<pre>"; print_r($invitations) ; die;                
			foreach($invitations as $inv)
			{
				$result[]=array('invitation_id'=>$inv['inv_id'],'host_id'=>$inv['host_id'],'host_name'=>$inv['uname'],'event_id'=>$inv['event_id'],
				'event_name'=>$inv['event_name'],'locations'=>$inv['locations'],
				'date_from'=>$inv['date_from'],'date_to'=>$inv['date_to'],
				'event_icon'=>$inv['event_icon'],'friends_coming'=>$inv['friends_coming'],'total_pixos'=>$inv['total_pixos']);
			}
                       if($result)
                       {
			        echo json_encode(array('Status'=>"true",'requests'=>$result)); 
                       }
                       else
		       {
			 echo json_encode(array('Status'=>"false",'requests'=>[]));    
		       }                   
		}        
	}  
        
        if($service_type == 'fReqIRecvd')
	{
		if(!empty($_REQUEST['user_id']))
		{
			//echo "kkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkk"; die;
			$uid=$_REQUEST['user_id'];                    
			$invitations=fReqIRecvd($uid,$IMG_URL);  
		    //echo "<pre>"; print_r($invitations) ; //die;                
			foreach($invitations as $inv)
			{
				$result[]=array('friend_id'=>$inv['friend_id'],'user_name'=>$inv['user_name'],'profile_pic'=>$inv['profile_pic'],'age'=>$inv['age']);
			}
			if($result)
			{
				echo json_encode(array('Status'=>"true",'requests'=>$result)); 
			}
			else
			{
				echo json_encode(array('Status'=>"false",'requests'=>[]));    
			}                   
		}        
	} 
	

	function userProfileDetail($u)
	{ 
	    $query=" SELECT uname, profile_pic FROM users WHERE user_id='".$u."' "; //die;
		$result=mysql_query($query) or mysql_error();			
		if(mysql_num_rows($result)>0)
		{
			while($row=mysql_fetch_assoc($result))
			{
				$row1[]=$row;
			}
			return $row1; 
		}
		else
		{
			return false;
		}   
	}
	
	
    function fReqIRecvd($g_id,$IMG_URL)
	{
        $query="select req_id, user_id as friend_id from friends where friend_id='".$g_id."' AND freq_status=0";	//OR friend_id='".$g_id."'	
        $res= mysql_query($query);
		if(mysql_num_rows($res)>0)
		{
			$i=0;
			$final=array();
			while( $row=mysql_fetch_assoc($res))
			{
				//echo "<pre>"; print_r($row); die;
				$final[$i]['friend_id']=$row['friend_id'];
				if($result11=f_dtail($row['friend_id']))
				{ 
					//echo "<pre>"; print_r($result11); die;
				    $final[$i]['user_name']=$result11[0]['uname'];
				    $final[$i]['profile_pic']=$IMG_URL.$result11[0]['profile_pic'];               
				    $final[$i]['age']=$result11[0]['age'];
					//echo "<pre>"; print_r($final); die;                  
				}
           	$i++;
			}
			//	echo "<pre>";print_r($final);die;
		    return $final;
		}
		else
		{
			return false;
		}			
	}
	
    
	function InvIrecvd($g_id,$IMG_URL)
	{
		$query="select inv_id, host_id, event_id from invitation where guest_id='".$g_id."' AND accepted=0" ; 
		$res= mysql_query($query);
		if(mysql_num_rows($res)>0)
		{
			$i=0;
			$final=array();
			while( $row=mysql_fetch_array($res))
			{
				$final[$i]['inv_id']=$row['inv_id'];
				$final[$i]['host_id']=$row['host_id'];
				$final[$i]['event_id']=$row['event_id'];
				if($result11=f_dtail($row['host_id']))
				{ 
				    $final[$i]['uname']=$result11[0]['uname'];               
				}                              
			    if($result1=event_detail($row['event_id'],$row['host_id']))
			    { 
					$row1=mysql_fetch_assoc($result1);
					$final[$i]['event_id']=$row['event_id'];                               
					$final[$i]['event_name']=$row1['event_name']; 
					$final[$i]['locations']=$row1['locations'];
					$final[$i]['date_from']=$row1['date_from'];
					$final[$i]['date_to']=$row1['date_to'];
					$final[$i]['event_icon']="$IMG_URL".$row1['event_icon'];
					
								
 					if($comingCount=get_CountofComingGuest($row['event_id']))
					{
                        if($cmngC=mysql_fetch_assoc($comingCount))
                        {
							if($cmngC['guest_count'])
							{
							//	echo "<pre>"; echo $cmngC['guest_count']; die('llllllllllljkjk');
								if($cmngC['guest_count']==0)
								{
									$final[$i]['friends_coming']='1';
								}
								else
								{
									$final[$i]['friends_coming']=$cmngC['guest_count'];
								}
							}
							else
							{
								$final[$i]['friends_coming']='1';
							}							
						}
					}
					
					
					if($tei1=get_total_EventImage($row['host_id'],$row['event_id']))
					{ 
						$px1=mysql_fetch_assoc($tei1);
						
						$tei2=get_Event_Image($row['host_id'],$row['event_id']);
						$px2=mysql_fetch_assoc($tei2);
						if($px1['image_count']!=0) 
						{                                      
							$final[$i]['total_pixos']=$px1['image_count'] + $px2['defaultimage'];
						}
						else
						{
							$final[$i]['total_pixos']=$px2['defaultimage']; 
						} 
                                        }
				} 
				$i++;
			}
				//echo "<pre>";print_r($final);die;
		    return $final;
		}
		else
		{
			return false;
		}			
	}
	
	function get_CountofComingGuest($ev_id)
	{
                $query = "SELECT Count(guest_id) as guest_count from invitation where accepted=1 AND event_id=$ev_id "; // die;
		$result=mysql_query($query) or mysql_error();
		if(mysql_num_rows($result)>0)
		{
			//$result2=mysql_fetch_assoc($result);
                        return $result ;
		}
		else
		{
			return false;
		}
	}
 

	

	function get_total_EventImage($u_id,$ev_id)
	{ 
   	    $query = "SELECT id, Count(image) as image_count from event_images WHERE user_id=$u_id AND ev_id=$ev_id ";  
		$result=mysql_query($query) or mysql_error();
		if(mysql_num_rows($result)>0)
		{
			//echo mysql_num_rows($result); die;
			return $result ;
		}
		else
		{
			return false;
		}
	}
	
        function get_Event_Image($u_id,$ev_id)
        {
          $query = "SELECT ev_id,count(image) as defaultimage from mange_event WHERE user_id=$u_id AND ev_id=$ev_id And image!=''";  
		$result=mysql_query($query) or mysql_error();

		if(mysql_num_rows($result)>0)
		{			
		//	return mysql_num_rows($result) ;
			return $result;			
		}
		else
		{
			return false;
		}              


        }
       

	    
        

	function alrdy_acceptd_inv($h_id,$g_id,$e_id,$resp)
	{   
		$query =" SELECT inv_id FROM invitation WHERE host_id='".$h_id."' AND guest_id='".$g_id."' AND  event_id='".$e_id."' AND accepted='".$resp."'  "; 
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
	
	function ad_inv($h_id,$g_id,$e_id,$resp)
	{
		$query ="UPDATE invitation SET accepted='".$resp."' WHERE host_id='".$h_id."' AND guest_id='".$g_id."' AND event_id='".$e_id."'"; //die;

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
		
	function last_inv_added_u_dtails($u_id,$f_id,$e_id,$identifier)
	{
		if($identifier==1)
		{
			$fd=f_dtail($f_id);
			//echo "<pre>"; pr($fd); exit;
			$query=" SELECT * FROM friends WHERE user_id=$u_id AND friend_id=$f_id  AND event_id=$e_id AND  status_f ='1' AND  status_u ='1' ";
			$result=mysql_query($query);
		}
		elseif($identifier==2)
		{
			$fd=f_dtail($f_id);
			//echo "<pre>"; pr($fd); exit; 
			$query=" SELECT * FROM friends WHERE user_id=$u_id AND friend_id=$f_id AND event_id=$e_id AND  status_f ='2' AND  status_u ='1' ";
			$result=mysql_query($query);
		}
		$row=mysql_fetch_assoc($result);
		$row['friend_name']=$fd['uname'];
		return $row;		
	}
	
	function f_dtail($f_id)
	{
		$query="select user_id, uname, age, profile_pic from users where user_id=".$f_id ; //die;
		$res= mysql_query($query);
		if(mysql_num_rows($res)>0)
		{			
			while($row=mysql_fetch_assoc($res))
			{
			    $row1[]=$row; //echo "<pre>";print_r($row);// die;
			}
			return $row1;
		}
		else
		{
			return false;
		}			
	}
    
   
	function event_imgs($u_id,$ev_id)
	{
        $query = "SELECT id,image from event_images WHERE ev_id=$ev_id ";   
		$result=mysql_query($query) or mysql_error();
		if(mysql_num_rows($result)>0)
		{
			return $result;
		}else
		{
			return false;
		}
	}

	function event_detail($ev_id)
	{
	 	$query = "SELECT ev_id as event_id, user_id, event_name, date_from, date_to, locations, event_icon, image from mange_event WHERE ev_id=$ev_id "; 
		$result=mysql_query($query) or mysql_error();                
		if(mysql_num_rows($result)>0)
		{       
                        return $result;
		}
		else
		{
			return false;
		}
	}

	function event_detail2($u_id,$ev_id)
	{
	 	$query = "SELECT ev_id, user_id, event_name, date_from, date_to, locations, event_icon, description, image from mange_event WHERE user_id=$u_id AND ev_id=$ev_id "; 
		$result=mysql_query($query) or mysql_error();
		if(mysql_num_rows($result)>0)
		{
			return $result;
		}
		else
		{
			return false;
		}
	}


	function last_inv_details($id)
	{
	    $query = "SELECT * from invitation WHERE inv_id=".$id ;
		$result=mysql_query($query) or mysql_error();
		if(mysql_num_rows($result)>0)
		{
			return $result;
		}
		else
		{
			return false;
		}
	}
	
	function g1($id)
	{
		$query=" SELECT uname FROM users WHERE user_id='$id' ";
	    $result=mysql_query($query);
		$row=mysql_fetch_array($result);
		return $row;
	}
	
	
	function dtail_g($u_id,$a)
	{
		//$u_id=g1($id);
		//echo "<pre>"; var_dump(); die;
		if($a==1)
		{
		   $query=" SELECT i.guest_id,u.uname as guest_name from invitation as i LEFT JOIN users as u ON u.user_id=i.guest_id 
			         Where i.guest_id=$u_id AND u.user_id=i.guest_id AND accepted=1 order by inv_id ";
	    }
	    elseif($a==2)
	    {
		    $query=" SELECT i.guest_id,u.uname as guest_name from invitation as i LEFT JOIN users as u ON u.user_id=i.guest_id 
			         Where i.guest_id=$u_id AND u.user_id=i.guest_id AND accepted=2 order by inv_id ";
			
		}
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
	
	function update_inv($id)
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

	function check_creator_inv_exist($creator_id,$ev_id)
	{
	   //echo $f_id; die;
		$query = "SELECT event_name FROM mange_event WHERE ev_id='".$ev_id."' AND user_id='".$creator_id."' "; 
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
	
    function alrdy_sent_invit($h_id,$g_id,$ev_id)
	{
	   //echo $f_id; die;
		$query = "SELECT * FROM invitation WHERE event_id='".$ev_id."' AND host_id='".$h_id."' AND guest_id='".$g_id."' "; 
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
	
	
    function comingGuestDetailArray($ev_id,$IMG_URL)
	{

         $query1="SELECT i.guest_id, u.uname as user_name, u.profile_pic, u.age FROM invitation AS i	RIGHT JOIN users AS u 
                ON i.guest_id = u.user_id WHERE  i.event_id ='".$ev_id."' ";
		$r = mysql_query($query1) or mysql_error();
		//$i=0;
		if(mysql_num_rows($r)>0)
		{		
			while($arrayGuestDetail = mysql_fetch_assoc($r))
			{
                                if($arrayGuestDetail['profile_pic'])
                        	{
                   		        $arrayGuestDetail['profile_pic'] = $IMG_URL.$arrayGuestDetail['profile_pic'];
                       		}
				$agd[]=$arrayGuestDetail;
			}		
			//echo "<pre>"; print_r($agd); die;
            return $agd ; //die('ja reha');
		}
		else
		{
			return false;
		}
	}	

	function coming($ev_id)
	{
		$query=" SELECT guest_id from invitation where accepted=1 AND event_id=$ev_id ";
  		$result=mysql_query($query) or mysql_error();			
		if(mysql_num_rows($result)>0)
		{
			return $result;
		}
		else
		{
			return false;
			//echo json_encode(array('Status'=>"false",'You didnot invite any guest for this event'=>$ev_id));
		} 
	}
        
        
	
	function not_coming($ev_id)
	{	    
	    $query=" SELECT guest_id from invitation where accepted=2 AND event_id=$ev_id "; 
  		$result=mysql_query($query) or mysql_error();			
		if(mysql_num_rows($result) >0)
		{
			return $result;
		}
		else
		{
			return false;
			//echo json_encode(array('Status'=>"false",'You didnot invite any guest for this event'=>$ev_id));
		} 
	}
	
	
        function frndsComng($ev_id,$imgPath,$fc)
	{
		    if($fc==1)
		    {
		  echo      $q1="SELECT guest_id as user_id from invitation where accepted=1 and event_id=".$ev_id;
			}
			if($fc==2)
			{
		echo		$q1="SELECT guest_id as user_id from invitation where accepted=2 and event_id=".$ev_id;

			}
//echo $q1="SELECT (SELECT guest_id from invitation where accepted=1 and event_id='".$r['ev_id']."') as attending_id, uname, profile_pic from users where user_id='".$r['user_id']."' "; 
					if($res1=mysql_query($q1))
					{				
	                    $count=mysql_num_rows($res1);					
						if($count>0)
						{
							$k=0;
							while($w=mysql_fetch_assoc($res1))
							{	
								//$y[]=$w;					
								//echo "<pre>"; print_r($y); die;							
						//   echo $res_detail="SELECT uname, profile_pic from users where user_id='".$y[$k]['user_id']."'";
	                            $res_detail="SELECT uname, profile_pic from users where user_id='".$w['user_id']."'";
								$rdtail=mysql_query($res_detail);
								while($resDtail=mysql_fetch_assoc($rdtail))
								{
									//$gallery[$i]['friends_coming'][]=$resDtail;
									$resDtail['profile_pic']=$imgPath.$resDtail['profile_pic'];
									$ntAccptd[]=$resDtail;
								}
								$k++;
							}
							
						}
						else
						{
							return false; //"all coming";
						}
						return $ntAccptd;
					}
					else
					{
						return false;

					}
	}
	
	echo "comp";
?>


                            
