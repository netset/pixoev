<?php
        error_reporting(E_ALL);
        ini_set("display_errors", 1);    
	require_once "../class/config.php";
	require_once dirname(__FILE__)."/web_functions.php";
	set_time_limit(0);	
	//$IMG_URL1=$URL."/web_service/images/thumb_";
	$IMG_URL=$URL."/web_service/images/";
        //echo $IMG_URL; die;
        $service_type=$_REQUEST['service_type']; 
        // echo $service_type=$_REQUEST['service_type']; 
	/*===========================================Pixovent===============================================*/

        if($service_type == 'group_detail')
	{
		if(!empty($_REQUEST['user_id']) && !empty($_REQUEST['group_id']))
		{                        
			
			$gid=explode(',',$_REQUEST['group_id']);
			
			$res=array();
			foreach($gid as $a)
			{
				$res[]=group_detail($_REQUEST['user_id'],$a,$IMG_URL);
			}
			if(count($res)>0)
			{
                               
				echo json_encode(array('Status'=>"true",'group_detail'=>$res));                              
			}
			else
			{
				echo json_encode(array('Status'=>"false",'group_detail'=>'Invalid group or user id'));
			}	
		}	
		else
	        {
			echo json_encode(array('Status'=>"false",'message'=>'enter valid parameters'));
		}		
	 }


       /************************FUNCTIONs*******************************/
         
        function group_detail($u,$g,$IMG_URL)
        {
		$query = " SELECT g.id, g.name, g.icon, g.user_id, gd.friend_id FROM `group` as g INNER JOIN groupdetails as gd ON g.id = gd.group_id WHERE g.user_id='".$u."' AND g.id='".$g."' ";		
$result=mysql_query($query) or mysql_error();
		if(mysql_num_rows($result)>0)
		{
			while($y=mysql_fetch_assoc($result))
			{
				$y1[]=$y;
                        }

			if($y1)
			{
				$e=0;
				$final=array();
				foreach($y1 as $z)
				{
				    if($details=userProfileDetail($y1[$e]['friend_id'],$IMG_URL))
                                    {						
						while($y2=mysql_fetch_assoc($details))
						{
							if(!empty($y2['profile_pic']))
							{                                         
								$y2['profile_pic']=$IMG_URL."thumb_".$y2['profile_pic'];
							}
							$z1[]=$y2;
						}					
				    }
                                    $final['group_id']=$y1[$e]['id'];
                                    $final['group_name']=$y1[$e]['name'];
                                    $final['group_icon_url']=$IMG_URL."thumb_".$y1[$e]['icon'];
                                    $final['friends']=$z1;
                                    if($final['friends']=='')
                                    {
			                    $final['friends']=array();
			                   //echo "<pre>"; print_r($final); die;
		                    } elseif($final['friends']=='null')
                                    {
			                // echo "<pre>"; echo "koko"; die;
			                   $final=array();

		                    }                     	
                                $e++;
			        }
                                
                                
                            

			}
		}
	   $errors = array_filter($final);
if(empty($errors)){
$ret=array();
     return $ret;
}else{
   return $errors;
}
//return $errors;

//return $final;
	}


        function userProfileDetail($u)
	{ 
	        $query=" SELECT user_id, uname as user_name, profile_pic FROM users WHERE user_id='".$u."' "; //die;
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

?>
     
                            
                            
                            
                            
