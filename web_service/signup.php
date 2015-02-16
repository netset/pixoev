                                <?php
   error_reporting(E_ALL);
    ini_set("display_errors", 1);    
	require_once "../class/config.php";
	require_once dirname(__FILE__)."/web_functions.php";
	set_time_limit(0);	
	$IMG_URL=$URL."/web_service/images/";
        $service_type=$_REQUEST['service_type'];
	/*===========================================Pixovent===============================================*/
        if($service_type == 'register')
	{
		if(!empty($_REQUEST['uname']) && !empty($_REQUEST['email']) && !empty($_REQUEST['password']))   
		{
                        // var_dump($_REQUEST); die;
			if(!empty($_FILES["profile_pic"]["name"]))
			{
				$uploadfile = "images/thumb_". basename($_FILES["profile_pic"]['name']);
				move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $uploadfile);
		        }
			        $data=array('uname'=>$_REQUEST['uname'],
				          'email'=>$_REQUEST['email'],
				          'age'=>$_REQUEST['age'],
				          'profile_pic'=>$_FILES['profile_pic']['name'],
				          'reg_since'=>date("Y-m-d"),
					  'password'=>md5($_REQUEST['password'])
					);
                           echo "<pre>"; print_r($data); die;
			if(!check_email($data['email']))
			{
				//var_dump($data);//die;
				insert($data,'users');
				$id=mysql_insert_id();
                                $id1=(string)$id;
				$usr_detail= u_detail($data['email']);
				if(!empty($usr_detail['profile_pic']))
				{
					$usr_detail['profile_pic']=$IMG_URL."thumb_".$usr_detail['profile_pic'];
				}					
				echo json_encode(array('Status'=>"true",'message'=>'User successfully registered','user_id'=>$id1,'user_name'=>$data['uname'],'Age'=>$usr_detail['age'],'profile_picture'=>$usr_detail['profile_pic']));
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
                            