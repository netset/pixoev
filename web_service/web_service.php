<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
require_once "../class/config.php";
//require_once dirname(__FILE__)."/facebook.php";
require_once dirname(__FILE__) . "/web_functions.php";
//require_once dirname(__FILE__).'/Swift-5.0.2/lib/swift_required.php';
set_time_limit(0);
//$IMG_URL1=$URL."/web_service/images/thumb_";
$IMG_URL      = $URL . "/web_service/images/";
$service_type = $_REQUEST['service_type'];
// echo $service_type=$_REQUEST['service_type']; 
/*===========================================Pixovent===============================================*/
if ($service_type == 'register')
  {
    if (!empty($_REQUEST['uname']) && !empty($_REQUEST['email']) && !empty($_REQUEST['password']))
      {
        if (!empty($_FILES["profile_pic"]["name"]))
          {
            $uploadfile = "images/thumb_" . basename($_FILES["profile_pic"]['name']);
            move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $uploadfile);
          }
        if ($_FILES["profile_pic"]["name"] == '')
          {
            //$empty ='';
            $data = array(
                'uname' => $_REQUEST['uname'],
                'email' => $_REQUEST['email'],
                'age' => $_REQUEST['age'],
                'uniqueusername' => $_REQUEST['uniqueusername'],
                'reg_since' => date("Y-m-d"),
                'password' => md5($_REQUEST['password'])
            );
          }
        else
          {
            $data = array(
                'uname' => $_REQUEST['uname'],
                'email' => $_REQUEST['email'],
                'uniqueusername' => $_REQUEST['uniqueusername'],
                'age' => $_REQUEST['age'],
                'profile_pic' => $_FILES['profile_pic']['name'],
                'reg_since' => date("Y-m-d"),
                'password' => md5($_REQUEST['password'])
            );
          }

        if (!check_email($data['email']))
          {
            //echo "<pre>"; print_r($data);//die;
            if ($yes = !uniqueUserName($data['uniqueusername']))
              {
                //echo "<pre>"; print_r($data); //die('guy');				
                insert($data, 'users');
                $id         = mysql_insert_id();
                $id1        = (string) $id;
                $usr_detail = u_detail($data['email']);
                //echo "<pre>"; print_r($usr_detail); die;
                if (!empty($usr_detail['profile_pic']))
                  {
                    $usr_detail['profile_pic'] = $IMG_URL . "thumb_" . $usr_detail['profile_pic'];
                  }
                echo json_encode(array(
                    'Status' => "true",
                    'message' => 'User successfully registered',
                    'user_id' => $id1,
                    'unique_username' => $data['uniqueusername'],
                    'user_name' => $data['uname'],
                    'Age' => $usr_detail['age'],
                    'profile_picture' => $usr_detail['profile_pic']
                ));
              }
            else
              {
                echo json_encode(array(
                    'Status' => "false",
                    'message' => 'Unique Username already exist'
                ));
              }
          }
        else
          {
            echo json_encode(array(
                'Status' => "false",
                'message' => 'Email already exist'
            ));
          }
      }
    else
      {
        echo json_encode(array(
            'Status' => "false",
            'message' => 'Enter proper input'
        ));
      }
  }
/*=-start=-=-=-=--=login=-=-=-=-=-=-=-*/
if ($service_type == 'login')
  {
    if (!empty($_REQUEST['email']) && !empty($_REQUEST['password']))
      {
        $email    = $_REQUEST['email'];
        $password = $_REQUEST['password'];
       //echo "<pre>"; var_dump($_REQUEST); die;
        if ($res = login($email, $password, $IMG_URL))
          {
            if (!empty($res['profile_pic']))
              {
                //echo "<pre>"; var_dump($res);
                $res['profile_pic'] = $IMG_URL . "thumb_" . $res['profile_pic'];
                //echo $res['profile_pic']; die;
              }
          
            $jsonArr = json_encode(array(
                'Status' => "true",
                'message' => 'User successfully logged in',
                'user_id' => $res['user_id'],
                'user_name' => $res['uname'],
                'age' => $res['age'],
                'profile_pic' => $res['profile_pic']
            ));
            preg_replace("/([A-Z]{2})\s+(\d{4})/", "$1$2", $jsonArr);
            echo $jsonArr;
          }
      }
    else
      {
        echo json_encode(array(
            'Status' => "false",
            'message' => 'Enter proper input'
        ));
      }
  }
function login($email, $password)
  {
    $password1 = md5($password);
    if ($em = !u_detail($email))
      {
        echo json_encode(array(
            'Status' => "false",
            'message' => 'User does not exist'
        ));
      }
    else
      {
        $query  = " SELECT user_id, uname,age, password,profile_pic FROM users WHERE email='$email' ";
        $result = mysql_query($query);
        $row_1  = mysql_fetch_assoc($result);
        if ($password1 == $row_1['password'])
          {
            return $row_1;
          }
        else
          {
            echo json_encode(array(
                'Status' => "false",
                'message' => 'You have entered the wrong password'
            ));
          }
      }
  }
/*=-end=-=-=-=--=login=-=-=-=-=-=-=-*/





if($service_type == 'regThroughFb')	{		
	if(!empty($_REQUEST['fbdata'])) {
		$arrFB=json_decode($_REQUEST['fbdata']); 
		$res=regORlogin($arrFB);			
	} else {
		echo json_encode(array('Status'=>"false",'message'=>'Enter proper input'));
	}
}






if ($service_type == 'deleteUser')
  {
    if (!empty($_REQUEST['user_id']))
      {
        if (deleteUser($_REQUEST['user_id']))
          {
            echo json_encode(array(
                'Status' => 'true',
                'message' => 'Account and shared pixos deleted successfully'
            ));
          }
      }
    else
      {
        echo json_encode(array(
            'Status' => 'false',
            'message' => 'Empty proper input'
        ));
      }
  }
// print_r($service_type);
if ($service_type == 'list_of_events')
  {
    if (!empty($_REQUEST['user_id']))
      {
        $res = list_evnts($_REQUEST['user_id']);
        if ($res1 = list_evnts1($_REQUEST['user_id'])) /*----------(2)----------*/ 
          {
            $k = $last_i = count($res);
            foreach ($res1 as $re1)
              {
                $res[$k] = $re1[0];
                $k++;
              }
          }
        if ($res)
          {
            $i = 0;
            foreach ($res as $r)
              {
                if ($jas = frndsComng($r['ev_id'], $IMG_URL, 1))
                  {
                    $cComing               = count($jas);
                    $cComing1              = count($jas) + 1;
                    $res[$i]['no_of_user'] = (string) $cComing1;
                  }
                else
                  {
                    $res[$i]['no_of_user'] = '1';
                  }
                /*count of all extra images of an event*/
                $q2                     = "SELECT count(image) as imagecount FROM `event_images` WHERE `ev_id` = " . $r['ev_id'];
                $res12                  = mysql_query($q2);
                $count1                 = mysql_fetch_assoc($res12);
                /*one default image of an event*/
                $q3                     = "SELECT count(image) as defaultimage FROM mange_event where ev_id='" . $r['ev_id'] . "' AND image!='' ";
                $res3                   = mysql_query($q3);
                $count2                 = mysql_fetch_assoc($res3);
                $res[$i]['no_of_pixos'] = (string) ($count1['imagecount'] + $count2['defaultimage']);
                $i++;
              }
            $l = count($res);
            $j = 0;
            while ($j < $l)
              {
                $res_f    = $res[$j];
                $long     = strtotime($res_f['date_from']); // which results to 1332866820
                $long_end = strtotime($res_f['date_to']);
                $cur_long = strtotime(date("Y-m-d H:i:s"));
                if ($cur_long < $long OR ($cur_long > $long AND $cur_long < $long_end))
                  {
                    unset($res_f['timer']);
                    if (!empty($res_f['image']))
                      {
                        $res_f['image'] = $IMG_URL . "thumb_" . $res_f['image'];
                        $im12           = $IMG_URL . $res_f['image'];
                      }
                    if (!empty($res_f['event_icon']))
                      {
                        $res_f['event_icon'] = $IMG_URL . "thumb_" . $res_f['event_icon'];
                      }
                    $events_upcomng_ongng2day[] = $res_f;
                  }
                elseif ($cur_long > $long_end)
                  {
                    $res_f['date_from'];
                    unset($res_f['timer']);
                    if (!empty($res_f['image']))
                      {
                        $res_f['image'] = $IMG_URL . "thumb_" . $res_f['image'];
                        $im123          = $IMG_URL . $res_f['image'];
                      }
                    if (!empty($res_f['event_icon']))
                      {
                        $res_f['event_icon'] = $IMG_URL . "thumb_" . $res_f['event_icon']; //$res_f['event_icon']=$IMG_URL.$res_f['event_icon'];																
                      }
                    $events_earlier[] = $res_f;
                  }
                $j++;
              }
            if (empty($events_earlier))
              {
                $events_earlier = array();
              }
            else
              {
                usort($events_earlier, 'compare1');
                //echo "<pre>"; print_r($events_earlier); //die;
              }
            if (empty($events_upcomng_ongng2day))
              {
                $events_upcomng_ongng2day = array();
              }
            else
              {
                usort($events_upcomng_ongng2day, 'compare2');
                //echo "<pre>"; print_r($events_upcomng_ongng2day); die;
              }
            echo json_encode(array(
                'Status' => "true",
                'upcoming_ongoing_events' => $events_upcomng_ongng2day,
                'past_events' => $events_earlier
            ));
          }
        else
          {
            echo json_encode(array(
                'Status' => "false",
                'message' => 'event does not exist'
            ));
          }
      }
    else
      {
        echo json_encode(array(
            'Status' => "false",
            'events_list' => 'Enter proper Input'
        ));
      }
  }
/*edit_group*/
if ($_REQUEST['service_type'] == 'editgroup')
  {
    $uid         = $_REQUEST['user_id'];
    $gid         = $_REQUEST['group_id'];
    $name        = $_REQUEST['name'];
    $icon        = $_REQUEST['icon'];
     $list_invite = $_REQUEST['friend_id'];
    if (!empty($_REQUEST['group_id']) && !empty($_REQUEST['user_id']))
      {
        if (!empty($_FILES["icon"]["name"]))
          {
            $uploadfile = "images/thumb_" . basename($_FILES["icon"]['name']);
            move_uploaded_file($_FILES["icon"]["tmp_name"], $uploadfile);
          }
        //pr($_FILES); die;
        $data = array(
            'user_id' => $_REQUEST['user_id'],
            'name' => $_REQUEST['name'],
            'icon' => $_FILES["icon"]["name"],
            'datetime' => date('Y-m-d H:i:s')
        );
        //pr($data); //die('kanth');   
        if ($res = editgroup($data))
          {
            if (empty($list_invite))
              {
				  
                echo json_encode(array(
                    'Status' => "true",
                    'message' => 'Group Updated successfully'
                ));
              }
            elseif (!empty($list_invite))
              {
				 
                $list_values = explode(",", $list_invite);
               // echo "Delete from `groupdetails` where group_id=$gid and user_id=$uid and friend_id NOT IN ($list_invite)";die;
                mysql_query("Delete from `groupdetails` where group_id=$gid and user_id=$uid and friend_id NOT IN ($list_invite)");
                foreach ($list_values as $u_list)
                  {
					  $qr="SELECT * from groupdetails where user_id='$uid' and group_id='$gid' and friend_id='$u_list'";
					  $qwe=mysql_query($qr);
					  if(mysql_num_rows($qwe)=='0')
					  {
						//************************
						$invite_users = "INSERT INTO groupdetails (user_id,group_id,friend_id)VALUES('$uid','$gid','$u_list')";
						/* $invite_users="UPDATE groupdetails SET user_id='$uid', group_id='$gid',friend_id='$u_list' ";*/
						$invite_s     = mysql_query($invite_users);
					  }
						//************************
                  }
                echo json_encode(array(
                    'Status' => "true",
                    'message' => 'Group Updated successfully'
                ));
              }
          }
        else
          {
            echo json_encode(array(
                'Status' => "false",
                'message' => 'Group not created'
            ));
          }
      }
    else
      {
        echo json_encode(array(
            'Status' => "false",
            'message' => 'Enter Proper Input'
        ));
      }
  }
//=================================================
if ($service_type == 'show_gallery')
  {
    if (!empty($_REQUEST['user_id']) && !empty($_REQUEST['event_id']))
      {
        $u_id  = $_REQUEST['user_id'];
        $ev_id = $_REQUEST['event_id'];
        //for getting images
       $query = "SELECT a.user_id,b.uname,b.profile_pic,a.image from mange_event as a INNER JOIN users as b on a.user_id=b.user_id where a.ev_id=$ev_id ";
        $res   = mysql_query($query);

        if (mysql_num_rows($res) > 0)
          {
            $result = mysql_fetch_assoc($res);
            //echo "<pre>";print_r($result);die; 
            //echo $result['profile_pic'];   die;
            if ($result['image'] != '')
              {
                $a     = array(
                    'id' => '0',
                    'uploader_id' => $result['user_id'],
                    'uploader_name' => $result['uname'],
                    'uploader_image' => $IMG_URL . 'thumb_' .  $result['profile_pic'],
                    'url' => $IMG_URL . 'thumb_' . $result['image'],
                    'thumb_url' => 'NA',
                    'type' => 'image'

                );
                $row[] = $a;
              }
            $query1 = "SELECT a.id,a.user_id as uploader_id,b.uname as uploader_name,b.profile_pic as uploader_image,a.image as url,a.thumb_img as thumb_url,a.type from event_images as a INNER JOIN users as b on a.user_id=b.user_id where a.ev_id='" . $ev_id . "'";
            $res1   = mysql_query($query1);
            //echo "<pre>"; print_r($res1); die;
            if (mysql_num_rows($res1) > 0)
              {
                while ($result1 = mysql_fetch_assoc($res1))
                  {

$f_id = $result1['uploader_id'];

if($u_id == $f_id)
{
$result1['friends'] = 'YES';
}
else{
 $query4 = "SELECT * FROM friends WHERE ((user_id='".$u_id."' AND friend_id='".$f_id." ') OR (user_id='".$f_id."' AND friend_id='".$u_id."'))"; 
			$result4=mysql_query($query4) or mysql_error();
			if(mysql_num_rows($result4)>0)
			{
				$result1['friends'] = 'Yes';
			}
			else
			{
				$result1['friends'] = 'No';
			}

	}
                    if ($result1['url'] == '')
                      {
                        $result1['url'] = 'NA';
                      }
                    else
                      {
                        $result1['url'] = $IMG_URL . 'thumb_' . $result1['url'];
                        $result1['uploader_image'] = $IMG_URL . 'thumb_' . $result1['uploader_image'];
                      }
                    if ($result1['thumb_url'] == '')
                      {
                        $result1['thumb_url'] = 'NA';
                      }
                    else
                      {
                        $result1['thumb_url'] = $IMG_URL . 'thumb_' . $result1['thumb_url'];
                        //$result1['uploader_image'] = $IMG_URL . 'thumb_' . $result1['uploader_image'];
                      }
                    $row[] = $result1;
                  }
                echo json_encode(array(
                    'Status' => "true",
                    'event_id' => $ev_id,
                    'event_creator_id' => $result['user_id'],
                    'pixos' => $row
                ));
              }
            else if (!empty($row))
              {
                //print_r($row); die;
                echo json_encode(array(
                    'Status' => "true",
                    'event_id' => $ev_id,
                    'event_creator_id' => $result['user_id'],
                    'pixos' => $row
                ));
              }
            else
              {
                echo json_encode(array(
                    'Status' => "false",
                    'pixos' => []
                ));
              }
            //  echo "<pre>";print_r($row);die;
          }
        else
          {
            echo json_encode(array(
                'Status' => "false",
                'message' => 'user not created any event'
            ));
          }
      }
    else
      {
        echo json_encode(array(
            'Status' => "false",
            'gallery' => 'Enter proper Input'
        ));
      }
  }
//=================================================
if (($_REQUEST['service_type']) == 'all_imgs_vids_by_usr')
  {
    if (!empty($_REQUEST["user_id"]))
      {
        if ($data = image_video_by_user($_REQUEST["user_id"]))
          {
            while ($row = mysql_fetch_assoc($data))
              {
                if (!empty($row['date_time']))
                  {
                    $row['date_time'] = get_time_diff_in_str($row['date_time']);
                  }
                if (!empty($row['profile_pic']))
                  {
                    $row['profile_pic'] = $IMG_URL . "images/" . $row['profile_pic'];
                  }
                if ($row['cover_image'])
                  {
                    $row['cover_image'] = $IMG_URL . "images/" . $row['cover_image'];
                  }
                if ($row['file1'])
                  {
                    $row['file1'] = $IMG_URL . "images/" . $row['file1'];
                  }
                if ($row['file2'])
                  {
                    $row['file2'] = $IMG_URL . "images/" . $row['file2'];
                  }
                if ($row['file3'])
                  {
                    $row['file3'] = $IMG_URL . "images/" . $row['file3'];
                  }
                if ($row['file4'])
                  {
                    $row['file4'] = $IMG_URL . "images/" . $row['file4'];
                  }
                if ($row['file5'])
                  {
                    $row['file5'] = $IMG_URL . "images/" . $row['file5'];
                  }
                if ($row['file6'])
                  {
                    $row['file6'] = $IMG_URL . "images/" . $row['file6'];
                  }
                if ($row['file7'])
                  {
                    $row['file7'] = $IMG_URL . "images/" . $row['file7'];
                  }
                $res[] = $row;
              }
            echo json_encode(array(
                'status' => "true",
                'result' => $res
            ));
          }
        else
          {
            echo json_encode(array(
                'Status' => "false",
                'message' => 'No file Yet'
            ));
          }
      }
    else
      {
        echo json_encode(array(
            'Status' => "false",
            'message' => 'Enter Proper input'
        ));
      }
  }


if ($service_type == 'f_list')
  {
    if (!empty($_REQUEST['user_id']))
      {
        $user = $_REQUEST['user_id'];
        if ($data = friend_list($_REQUEST['user_id']))
          {
            while ($row = mysql_fetch_assoc($data))
              {
                unset($row['req_id']);
                if (!empty($row['profile_pic']))
                  {
                    $row['profile_pic'] = $IMG_URL . "thumb_" . $row['profile_pic'];
                  }
                $frnd[] = $row;
              }
            if (!empty($frnd))
              {
                $j     = 0;
                $k     = 0;
                $final = array();
                foreach ($frnd as $f)
                  {
                    /*count of Invitations sent by the user*/
                    $invIrecvCount                      = countREQiRecvFrmUsR($user, $f['user_id']);
                    /*RESULTANT Blocked verdict( 'blocked' OR 'notblocked' ) for both user AND each friend in his/her friend-list*/
                    $finalblockstatus                   = blocked($user, $f['user_id']);
                    $final[$k]['user_id']               = $frnd[$j]['user_id'];
                    $final[$k]['user_name']             = $frnd[$j]['user_name'];
                    $final[$k]['profile_pic']           = $frnd[$j]['profile_pic'];
                    $final[$k]['age']                   = $frnd[$j]['age'];
                    $final[$k]['block_status_for_both'] = $finalblockstatus['block_status_for_both'];
                    $final[$k]['blockedby_id']          = $finalblockstatus['blockedby_id'];
                    $final[$k]['invitation_sent']       = $invIrecvCount['countInvitation'];
                    $k++;
                    $j++;
                  }
              }
            usort($final, 'compare');
            //echo "<pre>"; print_r($final); 				
            $c = count($final);
            foreach ($final as $finel)
              {
                if ($c <= 10 && $finel['invitation_sent'] > 0)
                  {
                    $best_friend[] = $finel;
                  }
                if ($c)
                  {
                    $friend[] = $finel;
                  }
              }
            if (!empty($best_friend))
              {
                usort($best_friend, 'compass');
              }
            if (!empty($friend))
              {
                usort($friend, 'compass');
              }
 
            if (@$best_friend == 'null' || @$best_friend == '')
              {
                $best_friend = [];
              }
            if (@$friend == 'null' || $friend == '')
              {
                $friend = [];
              }
            echo json_encode(array(
                'Status' => "true",
                'bestfriend_list' => $best_friend,
                'friend' => $friend
            ));
          }
        else
          {
            echo json_encode(array(
                'Status' => "false",
                'message' => 'No friend yet'
            ));
          }
      }
    else
      {
        echo json_encode(array(
            'Status' => "false",
            'message' => 'Please enter proper input'
        ));
      }
  }
if ($service_type == 'ad_friend_req')
  {

    if (!empty($_REQUEST['user_id']) && !empty($_REQUEST['friend_id']) && !empty($_REQUEST['response']))
      {
        if ($s = !alrdy_acceptd_frndReq($_REQUEST['user_id'], $_REQUEST['friend_id'], $_REQUEST['response']))
          { //print_r($_REQUEST['response']);die;
            //var_dump($s); die;
            if ($_REQUEST['response'] == 1)
              {
                //var_dump($w); die;
                $w  = ad_fReq($_REQUEST['user_id'], $_REQUEST['friend_id'], 1);
                $ad = last_freqAdded_u_dtails($_REQUEST['user_id'], $_REQUEST['friend_id'], 1);
                echo json_encode(array(
                    'Status' => "true",
                    'accepted_by' => $ad
                ));
              }
            elseif ($_REQUEST['response'] == 2)
              {
                $x  = ad_fReq($_REQUEST['user_id'], $_REQUEST['friend_id'], 2);
                //var_dump($x); die;		
                $ad = last_freqAdded_u_dtails($_REQUEST['user_id'], $_REQUEST['friend_id'], 2);
                echo json_encode(array(
                    'Status' => "true",
                    'rejected_by' => $ad
                ));
              }
            else
              {
                echo json_encode(array(
                    'Status' => "false",
                    'message' => 'Either the guest is not invited or event is not created yet'
                ));
              }
          }
        elseif ($_REQUEST['response'] == 1)
          {
            echo json_encode(array(
                'status' => "false",
                'message' => 'Already accepted friend request'
            ));
          }
        elseif ($_REQUEST['response'] == 2)
          {
            echo json_encode(array(
                'status' => "false",
                'message' => 'Already Rejected friend request'
            ));
          }
      }
    else
      {
        echo json_encode(array(
            'Status' => "false",
            'message' => 'Enter proper Input'
        ));
      }
  }
if ($service_type == 'search_friends')
  {
    if (!empty($_REQUEST['fname']) && !empty($_REQUEST['user_id']))
      {
        if ($data = searchUniqueUserName($_REQUEST['fname'], $_REQUEST['user_id']))
          {
            $u = $_REQUEST['user_id'];
            while ($row = mysql_fetch_assoc($data))
              {
                $row['profile_pic'] = @$IMG_URL . "thumb_" . $row['profile_pic'];
                unset($row['password']);
                $frnd[] = $row;
              }
            if ($frnd)
              {
                $i = 0;
                foreach ($frnd as $f)
                  {
                    if ($o = frndOrNOt($u, $f['user_id']))
                      {
                        //echo print_r($o);die;
                        if ($o[0]['freq_status'] == '0')
                          {
                            $frnd[$i]['friendship_status'] = 'pending_request';
                          }
                        elseif ($o[0]['freq_status'] == 1)
                          {
                            $frnd[$i]['friendship_status'] = 'friend';
                          }
                        elseif ($o[0]['freq_status'] == 2)
                          {
                            $frnd[$i]['friendship_status'] = 'rejected_request';
                          }
                      }
                    else
                      {
                        $frnd[$i]['friendship_status'] = 'friend_request_not_sent';
                      }
                    $i++;
                  }
              }
            echo json_encode(array(
                'Status' => "true",
                'friend' => $frnd
            ));
          }
        else
          {
            echo json_encode(array(
                'Status' => "false",
                'message' => 'user does not exist'
            ));
          }
      }
    else
      {
        echo json_encode(array(
            'Status' => "false",
            'message' => 'Please enter proper input'
        ));
      }
  }

/*===start==forgot==password===*/
if ($service_type == 'forgot_password')
  {
    $email = $_REQUEST['email'];
    if (!empty($email))
      {
        if (check_email($email))
          {
            //echo "G1"; //die;
            if ($password = forgot_password($email))
              {
                //echo "G2"; //die;
                $to      = "$email";
                $subject = "Password Recovery";
                $from    = "pixovent@pixovent.com";
                $headers = "Content-type: text/html\r\n";
                $headers .= "From:" . $from;
                $message = "<html><body>Hi, \n \t <p>Your new password is " . $password . "</p>
                                        <p>Thanks</p><p>Pixovent Team</p></body></html>";
                //   echo "G3"; //die;
                if (mail($to, $subject, $message, $headers))
                  {
                    //       echo "G4"; die;
                    echo json_encode(array(
                        'Status' => "true",
                        'message' => 'New Password has been send successfully at your email id'
                    ));
                  }
                else
                  {
                    echo json_encode(array(
                        'Status' => "true",
                        'message' => 'Mail function could not instantiate'
                    ));
                  }
              }
            else
              {
                echo json_encode(array(
                    'Status' => "false",
                    'message' => 'Some error occur'
                ));
              }
          }
        else
          {
            echo json_encode(array(
                'Status' => "false",
                'message' => 'Invalid Email'
            ));
          }
      }
    else
      {
        echo json_encode(array(
            'Status' => "false",
            'message' => 'Enter Email'
        ));
      }
  }
/*===end==forgot==password===*/
if ($service_type == 'create_group')
  {
    $fid = $_REQUEST['f_id'];
    $g   = $_REQUEST['user_id'];
    if (!empty($_FILES["icon"]["name"]))
      {
        $uploadfile = "images/thumb_" . basename($_FILES["icon"]['name']);
        move_uploaded_file($_FILES["icon"]["tmp_name"], $uploadfile);
        $fi = $_FILES['icon']['name'];
      }
    else
      {
        $fi = '';
      }
    $data = array(
        'user_id' => $_REQUEST['user_id'],
        'name' => $_REQUEST['name'],
        'icon' => $fi,
        'datetime' => date('Y-m-d H:i:s')
    );
    if (insert($data, 'group'))
      {
        $id          = mysql_insert_id();
        $id2         = (string) $id;
        $list_values = explode(",", $fid);
        //echo "<pre>";print_r($list_values); die;                               
        foreach ($list_values as $u_list)
          {
            //********************
            $grpDetailss = "INSERT INTO groupdetails set user_id='" . $g . "', group_id='" . $id . "', friend_id='" . $u_list . "'";
            $grp = mysql_query($grpDetailss) or die(mysql_error());
            //************************
          }
        echo json_encode(array(
            'Status' => "true",
            'group_id' => $id2,
            'message' => 'Group Created Succesfully'
        ));
      }
  }
if ($service_type == 'group_list')
  {
    $q   = "SELECT * from `group` where user_id=" . $_POST['user_id'];
    $res = mysql_query($q);
    if (mysql_num_rows($res) > 0)
      {
        while ($row = mysql_fetch_assoc($res))
          {
            $row['icon'] = $IMG_URL . 'thumb_' . $row['icon'];
            $data[]      = $row;
          }
        echo json_encode(array(
            'Status' => "true",
            'data' => $data
        ));
      }
    else
      {
        echo json_encode(array(
            'Status' => "false",
            'message' => 'that user is not created any group'
        ));
      }
  }
if ($service_type == 'user_profile')
  {
    if (!empty($_REQUEST['user_id']) && !empty($_REQUEST['friend_id']))
      {
        if ($data = userProfileDetail($_REQUEST['friend_id']))
          {
            $i     = 0;
            $final = array();
            if ($row = mysql_fetch_assoc($data))
              {
                $frnd[]                 = $row;



                /*---for fetching-------(user_name,user_profile_pic)------*/
                $final[$i]['user_name'] = $frnd[0]['uname'];
                
                if($frnd[0]['uniqueusername']) {
                    $final[$i]['uniqueusername'] = $frnd[0]['uniqueusername'];   
              } elseif(!$frnd[0]['uniqueusername']) {
                    $final[$i]['uniqueusername'] ='';   
              }

     
                if (!empty($frnd[0]['profile_pic']))
                  {
                    $final[$i]['profile_pic'] = $IMG_URL . "thumb_" . $frnd[0]['profile_pic'];
                  }
                /*==total image count==*/
                if ($data1 = uProfDetail2($_REQUEST['friend_id']))
                  {
                    if ($row1 = mysql_fetch_assoc($data1))
                      {
                        $final[$i]['total_pixos'] = $row1['total_pixos'];
                      }
                  }
                else
                  {
                    $final[$i]['image_count'] = '1';
                  }
                /*invitation send count*/
                if ($data2 = uProfCountInvSent($_REQUEST['friend_id']))
                  {
                    if ($InvSent = mysql_fetch_assoc($data2))
                      {
                        $detail2[]                          = $InvSent;
                        $final[$i]['invitation_send_count'] = $detail2[0]['inv_send_count'];
                      }
                  }
                else
                  {
                    $final[$i]['invitation_send_count'] = '0';
                  }
                /*invitation recieved count*/
                if ($data3 = uProfCountInvRecvd($_REQUEST['friend_id']))
                  {
                    if ($InvRecvd = mysql_fetch_assoc($data3))
                      {
                        $detail3[]                             = $InvRecvd;
                        $final[$i]['invitation_recieve_count'] = $detail3[0]['inv_recv_count'];
                      }
                  }
                else
                  {
                    $final[$i]['invitation_recieve_count'] = '0';
                  }
                /*blockStatus*/
                if ($data3 = blocked($_REQUEST['user_id'], $_REQUEST['friend_id']))
                  {
                    //echo "<pre>"; print_r($data3); die;
                    $al=alreadyblocked($_REQUEST['user_id'], $_REQUEST['friend_id']);
                     if($al) {
                           $y='Y';
                      }
                           elseif(!$al) {
                          $y='N';
                          }
                    
                      if ($data3)
                      {
                        $final[$i]['blockedby_id'] = $data3['blockedby_id'];
                        $final[$i]['blockedstatus'] = $y;
                      }
                    if ($data3 = '')
                      {
                        $final[$i]['blockedby_id'] = $data3['blockedby_id'];
                        $final[$i]['blockedstatus'] = $y;
                      }
				  }
                /*events in common*/
                if ($data4 = comnEvntsMeMyFrnd($_REQUEST['user_id'], $_REQUEST['friend_id']))
                  {
                    $gn = 0;
                    foreach ($data4 as $d4[$gn])
                      {
						
                        $y = event_detail($d4[$gn]['event_id']);
                        if ($y)
                          {
                            if (!empty($y['event_icon']))
                              {
                                $y['event_icon'] = $IMG_URL . "thumb_" . $y['event_icon'];
                              }
                            if (!empty($z['image']))
                              {
                                $y['image'] = $IMG_URL . "thumb_" . $y['image'];
                              }
                            $yz[] = $y;
                          }
                        $gn++;
                      }
                    $final[$i]['events_in_common'] = $yz;
                  }
                else
                  {
                    $final[$i]['events_in_common'] = [];
                  }
                $i++;
              }
            echo json_encode(array(
                'Status' => "true",
                'user_detail' => $final
            ));
          }
        else
          {
            echo json_encode(array(
                'Status' => "false",
                'message' => 'User does not exist'
            ));
          }
      }
    else
      {
        echo json_encode(array(
            'Status' => "false",
            'message' => 'Please enter proper input'
        ));
      }
  }
if ($service_type == 'group_detail')
  {
    if (!empty($_REQUEST['user_id']) && !empty($_REQUEST['group_id']))
      {
        if ($res = group_detail($_REQUEST['user_id'], $_REQUEST['group_id'], $IMG_URL))
          {
            echo json_encode(array(
                'Status' => "true",
                'group_detail' => $res
            ));
          }
        else
          {
            echo json_encode(array(
                'Status' => "false",
                'group_detail' => 'Invalid group or user id'
            ));
          }
      }
    else
      {
        echo json_encode(array(
            'Status' => "false",
            'message' => 'Enter valid parameters'
        ));
      }
  }
if ($service_type == 'deleteImg')
  {
    if (!empty($_REQUEST['user_id']) && !empty($_REQUEST['event_id']))
      {
        if (delete_item($_REQUEST['user_id'], $_REQUEST['event_id'], $_REQUEST['pic_id']))
          {
            echo json_encode(array(
                'Status' => 'true',
                'message' => 'Pixo deleted successfully'
            ));
          }
        else
          {
            echo json_encode(array(
                'Status' => 'false',
                'message' => 'An error occured'
            ));
          }
      }
    else
      {
        echo json_encode(array(
            'Status' => 'false',
            'message' => 'Enter proper input'
        ));
      }
  }
if ($service_type == 'deleteReportedPixo')
  {
    if (!empty($_REQUEST['user_id']) && !empty($_REQUEST['event_id']) && !empty($_REQUEST['reportedpic_id']))
      {
        if (delete_item($_REQUEST['user_id'], $_REQUEST['event_id'], $_REQUEST['reportedpic_id'], 2))
          {
            echo json_encode(array(
                'Status' => 'true',
                'message' => 'Reported pixo deleted successfully'
            ));
          }
        else
          {
            echo json_encode(array(
                'Status' => 'false',
                'message' => 'An error occured'
            ));
          }
      }
    else
      {
        echo json_encode(array(
            'Status' => 'false',
            'message' => 'Enter proper input'
        ));
      }
  }
if ($service_type == 'deleteAllReportedPixo')
  {
    if (!empty($_REQUEST['user_id']) && !empty($_REQUEST['event_id']))
      {
        if (deleteAllReportedPixo($_REQUEST['user_id'], $_REQUEST['event_id']))
          {
            echo json_encode(array(
                'Status' => 'true',
                'message' => 'All reported pixos deleted successfully'
            ));
          }
        else
          {
            echo json_encode(array(
                'Status' => 'false',
                'message' => 'An error occured'
            ));
          }
      }
    else
      {
        echo json_encode(array(
            'Status' => 'false',
            'message' => 'Enter proper input'
        ));
      }
  }
if ($service_type == 'deleteFriend')
  {
    if (!empty($_REQUEST['user_id']) && !empty($_REQUEST['friend_id']))
      {
        if (delete_friend($_REQUEST['user_id'], $_REQUEST['friend_id']))
          {
            echo json_encode(array(
                'Status' => 'true',
                'message' => 'Friend deleted from friend list'
            ));
          }
        else
          {
            echo json_encode(array(
                'Status' => 'false',
                'message' => 'Friend not found'
            ));
          }
      }
    else
      {
        echo json_encode(array(
            'Status' => 'false',
            'message' => 'Enter proper input'
        ));
      }
  }
if ($service_type == 'deleteGroup')
  {
    if (!empty($_REQUEST['user_id']) && !empty($_REQUEST['group_id']))
      {
        if (deleteGroup($_REQUEST['user_id'], $_REQUEST['group_id']))
          {
            echo json_encode(array(
                'Status' => 'true',
                'message' => 'Group deleted '
            ));
          }
        else
          {
            echo json_encode(array(
                'Status' => 'false',
                'message' => 'Group not found'
            ));
          }
      }
    else
      {
        echo json_encode(array(
            'Status' => 'false',
            'message' => 'Enter proper input'
        ));
      }
  }
if ($service_type == 'deleteUserfrmfeed')
  {
    if (!empty($_REQUEST['user_id']) && !empty($_REQUEST['event_id']))
      {
        if (deleteUserfrmfeed($_REQUEST['user_id'], $_REQUEST['event_id']))
          {
            echo json_encode(array(
                'Status' => 'true',
                'message' => 'Friend deleted from your feed list'
            ));
          }
        else
          {
            echo json_encode(array(
                'Status' => 'false',
                'message' => 'Friend not found'
            ));
          }
      }
    else
      {
        echo json_encode(array(
            'Status' => 'false',
            'message' => 'Enter proper input'
        ));
      }
  }
if ($service_type == 'allreportImg')
  {
    if (!empty($_REQUEST['event_id']))
      {
        if ($r = allreportimage($_REQUEST['event_id'], $IMG_URL))
          {
            //echo "<pre>"; print_r($r); die;
            echo json_encode(array(
                'Status' => 'true',
                'reported_image_list' => $r
            ));
          }
        else
          {
            echo json_encode(array(
                'Status' => 'false',
                'message' => 'No reported pixos yet'
            ));
          }
      }
    else
      {
        echo json_encode(array(
            'Status' => 'false',
            'message' => 'Enter proper input'
        ));
      }
  }
if ($service_type == 'dwmloadImg')
  {
    if (!empty($_REQUEST['user_id']) && !empty($_REQUEST['event_id']))
      {
        if ($dwnld = download_item($_REQUEST['user_id'], $_REQUEST['event_id'], $_REQUEST['pic_id']))
          {
            $url1 = mysql_fetch_assoc($dwnld);
            if ($url1['image'] == 'NA')
              {
                echo json_encode(array(
                    'Status' => 'true',
                    'url' => ''
                ));
              }
            elseif ($url1['image'])
              {
                if (!empty($url1['image']))
                  {
                    $url = $IMG_URL . "thumb_" . $url1['image'];
                  }
                echo json_encode(array(
                    'Status' => 'true',
                    'url' => $url
                ));
              }
          }
        else
          {
            echo json_encode(array(
                'Status' => 'false',
                'message' => 'Some error occured'
            ));
          }
      }
    else
      {
        echo json_encode(array(
            'Status' => 'false',
            'message' => 'Enter proper input'
        ));
      }
  }
if ($service_type == 'reportImg')
  {
    if (!empty($_REQUEST['user_id']) && !empty($_REQUEST['event_id']) && !empty($_REQUEST['report']))
      {
        if (imgexist($_REQUEST['event_id'], $_REQUEST['pic_id']))
          {
            $data = array(
                'imageid' => $_REQUEST['pic_id'],
                'eventid' => $_REQUEST['event_id'],
                'reportedbyid' => $_REQUEST['user_id']
            );
            if ($d = !alreadyreported($_REQUEST['user_id'], $_REQUEST['event_id'], $_REQUEST['pic_id']))
              {
                if ($dwnld = report_item($data))
                  {
                    echo json_encode(array(
                        'Status' => 'true',
                        'message' => 'Pixo reported successfully'
                    ));
                  }
              }
            else
              {
                echo json_encode(array(
                    'Status' => 'false',
                    'message' => 'Pixo already reported'
                ));
              }
          }
        else
          {
            echo json_encode(array(
                'Status' => 'false',
                'message' => 'Pixo not found'
            ));
          }
      }
    else
      {
        echo json_encode(array(
            'Status' => 'false',
            'message' => 'Enter proper input'
        ));
      }
  }
if ($service_type == 'editProfilePic')
  {
    if (!empty($_REQUEST['user_id']) && !empty($_FILES['profile_pic'])&& !empty($_REQUEST['user_name']))
      {
        if (f_dtail($_REQUEST['user_id']))
          {
            if (!empty($_FILES["profile_pic"]["name"]))
              {
                $uploadfile = "images/thumb_" . basename($_FILES["profile_pic"]['name']);
                if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $uploadfile))
                  {
                    if ($res = edit_ProfilePic($_REQUEST['user_id'], $_FILES['profile_pic']['name'], $IMG_URL,$_REQUEST['user_name']))
                      {
                        echo json_encode(array(
                            'Status' => 'true',
                            'message' => 'Profile picture updated successfully'
                        ));
                      }
                  }
                else
                  {
                    echo json_encode(array(
                        'Status' => 'false',
                        'message' => 'Some error occured'
                    ));
                  }
              }
          }
        else
          {
            echo json_encode(array(
                'Status' => 'false',
                'message' => 'User does not exist'
            ));
          }
      }
    else
      {
        echo json_encode(array(
            'Status' => 'false',
            'message' => 'Enter proper input'
        ));
      }
  }
if ($service_type == 'blockUser')
  {
    if (!empty($_REQUEST['user_id']) && !empty($_REQUEST['friend_id']))
      {
        if (!alreadyblocked($_REQUEST['user_id'], $_REQUEST['friend_id']))
          {
            if (blockUser($_REQUEST['user_id'], $_REQUEST['friend_id']))
              {
                echo json_encode(array(
                    'Status' => 'true',
                    'message' => 'Friend blocked successfully'
                ));
              }
            else
              {
                echo json_encode(array(
                    'Status' => 'false',
                    'message' => ' Either Friend or User does not exist'
                ));
              }
          }
        else
          {
            unblockUser($_REQUEST['user_id'], $_REQUEST['friend_id']);
            echo json_encode(array(
                'Status' => 'false',
                'message' => 'Friend Unblocked'
            ));

            
          }
      }
    else
      {
        echo json_encode(array(
            'Status' => 'false',
            'message' => 'Enter proper input'
        ));
      }
  }
  
  /*********************************************
  *$service_type=facebook_verify_id
  *Description:-This is used for verfying fb id exist or not
  ********************************************/
  if ($service_type == 'facebook_verify_id'){
	$fb_id=$_POST['fb_id'];
	if($fb_id){
		$sql="SELECT fbid FROM users WHERE fbid='$fb_id'";
		$res=mysql_query($sql)or die(mysql_error());
                $row=mysql_fetch_assoc($res);
		$count=mysql_num_rows($res);
		if($count>0){
			echo json_encode(array('Status' => 'true','message' => 'Fb Id Already Exist'
				));
		}else{
			echo json_encode(array('Status' => 'false','message' => 'Fb Id Not Exist'
				));
		}
	}
  }

/*====================|<<<<<<<<FUNCTIONS><Pixovent>>>>|=========================*/
/*for user registeration OR USER-LOGIN*/
	function regORlogin($data)
	{
		 $fbid = $data->id;
           $profilepic = $data->link;
	        $email = $data->email;
                 $age  = $data->age;
                $uname = $data->uname;
       $uUN = $data->uniqueusername;
       $regsince = date('y-m-d H:i:s');

	    $query =" SELECT * FROM users WHERE  fbid='".$fbid."' AND email='".$email."' AND uniqueusername='".$uUN."'";	
	    $result=mysql_query($query) or mysql_error();
	    $res1=mysql_fetch_assoc($result);
	    if($res1) 
              {
             if(!empty($res['profile_pic']))
              {
                $res1['profile_pic'] = $IMG_URL . "thumb_" . $res['profile_pic'];
              }
          
            $jsonArr = json_encode(array(
                'Status' => "true",
                'message' => 'User successfully logged in',
                'user_id' => $res1['user_id'],
                'user_name' => $res1['uname'],
                'unique_username' => $res1['uniqueusername'],
                'age' => $res1['age'],
                'profile_pic' => $res1['profile_pic']
            ));
            preg_replace("/([A-Z]{2})\s+(\d{4})/", "$1$2", $jsonArr);
            echo $jsonArr;

            			
		} elseif(!$res1) {  
			/************
			 * register
                       *************/	
		    
  $queryx =" SELECT * FROM users WHERE uniqueusername='$uUN'";	
	   $resultt=mysql_query($queryx) ;
if(mysql_num_rows($resultt)>0)
{ 
  $result_array = array('status'=>'false','message'=>'Unique Username already Exist');
				echo  json_encode($result_array);
				die;

}

$data1=array('fbid'=>$fbid,'email'=>$email,'profile_pic'=>$profilepic,'age'=>$age,'uname'=>$uname,'uniqueusername'=>$uUN,'reg_since'=>$regsince);		//print_r($data1);die;
   			insert($data1,'users');
   			$q="SELECT * FROM users WHERE fbid='". $fbid ."' AND email='". $email ."'";
		        $res = mysql_query($q) or mysql_error();
	               $response=mysql_fetch_assoc($res);		    
		    //echo "<pre>"; print_r($response);
			if($response) {	
			    //$result_array = array('status'=>'true','message'=>'Successfully Registered','userid'=>$response['user_id']);
			   // echo  json_encode($result_array);
  $jsonArr = json_encode(array(
                'Status' => "true",
                'message' => 'User Registration successful',
                'user_id' => $response['user_id'],
                'user_name' => $response['uname'],
                'unique_username' => $response['uniqueusername'],
                'age' => $response['age'],
                'profile_pic' => $response['profile_pic']
            ));
            preg_replace("/([A-Z]{2})\s+(\d{4})/", "$1$2", $jsonArr);
            echo $jsonArr;
			
			} elseif($response =='0') {
			    $result_array = array('status'=>'false','message'=>'registration Unsuccessfull');
				echo  json_encode($result_array);
			}
		
		
		}			
	}
		    






function compass($x, $y)
  {
    if ($x['invitation_sent'] == $y['invitation_sent'])
        return 0;
    else if ($x['invitation_sent'] > $y['invitation_sent'])
        return -1;
    else
        return 1;
  }
function compare($x, $y)
  {
    if ($x['countInvitations'] == $y['countInvitations'])
        return 0;
    else if ($x['countInvitations'] > $y['countInvitations'])
        return -1;
    else
        return 1;
  }
function compare1($x, $y)
  {
    if ($x['date_from'] == $y['date_from'])
        return 0;
    else if ($x['date_from'] > $y['date_from'])
        return -1;
    else
        return 1;
  }
function compare2($x, $y)
  {
    if ($x['date_from'] == $y['date_from'])
        return 0;
    else if ($x['date_from'] < $y['date_from'])
        return -1;
    else
        return 1;
  }
function countREQiRecvFrmUsR($senderId, $fid)
  {
    $query1  = "SELECT COUNT(guest_id) as countInvitation FROM invitation WHERE guest_id='" . $fid . "' AND host_id='" . $senderId . "' ";
    $result1 = mysql_query($query1);
    $y       = mysql_fetch_assoc($result1);
    //	echo "<pre>"; print_r($y['countInvitation']); //die('koiji');	
    //return $y['countInvitation'];
    return $y;
  }
function allreportimage($eid, $IMG_URL)
  {
    /*Event all Images*/
    /* echo     $query1 = " SELECT imageid from `reportimage` WHERE eventid ='".$eid."' GROUP BY imageid  "; //die; */
    $query1  = " SELECT ri.imageid, ei.image, ei.thumb_img, ei.type 
                from `reportimage` as ri RIGHT JOIN event_images as ei ON ei.id = ri.imageid 
                WHERE ri.eventid ='" . $eid . "' GROUP BY imageid ";
    $result1 = mysql_query($query1);
    while ($y = mysql_fetch_assoc($result1))
      {
        $z[] = $y;
      }
    //echo "<pre>"; print_r($z); die('uuuuu');
    $i     = 0;
    $final = array();
    foreach ($z as $zz)
      {
        if (!empty($zz['imageid']))
          {
            $resp2                = f_dtail2($zz['imageid'], $IMG_URL);
            $final[$i]['imageid'] = $zz['imageid'];
            $final[$i]['image']   = $IMG_URL . "thumb_" . $zz['image'];
            $final[$i]['type']    = $zz['type'];
            if ($zz['thumb_img'])
              {
                $final[$i]['thumb_img'] = $IMG_URL . "thumb_" . $zz['thumb_img'];
              }
            else
              {
                $final[$i]['thumb_img'] = 'NA';
              }
            $final[$i]['reportedby'] = $resp2;
          }
        $i++;
      }
    //echo "<pre>"; print_r($final); die('kdgfdjcdsn'); 			
    return $final;
  }
function deleteAllReportedPixo($uid, $eid)
  {
    $query  = "SELECT imageid FROM reportimage WHERE eventid='" . $eid . "' "; //die(jojo);
    $result = mysql_query($query);
        while ($y = mysql_fetch_assoc($result))
        {
           $z[] = $y;
        }
        $i = 0;
       $final = array();
      foreach ($z as $zz)
      {
          if (!empty($zz['imageid']))
          {
               $query1 = "DELETE FROM event_images WHERE id='" . $zz['imageid'] . "' AND ev_id='" . $eid . "' AND user_id='" . $uid . "' "; //die(jojo);
               mysql_query($query1);
            
               $query2 = "DELETE FROM reportimage WHERE eventid='" . $eid . "' AND imageid='" . $zz['imageid'] . "' "; //die(jojo);
               mysql_query($query2);
          }
      }
    if (mysql_affected_rows()) {
        return true;
      }
    else
      {
        return false;
      }
  }

function f_dtail2($iId, $IMG_URL)
  {
    $query = "select us.user_id, us.uname as user_name, us.profile_pic from users as us LEFT JOIN reportimage as ri ON us.user_id = ri.reportedbyid  
        where  ri.imageid =$iId ";
    $res   = mysql_query($query);
    while ($row = mysql_fetch_assoc($res))
      {
        if (!empty($row['profile_pic']))
          {
            $row['profile_pic'] = $IMG_URL . "thumb_" . $row['profile_pic'];
          }
        $y1[] = $row;
      }
    return $y1;
  }
function forgot_password($email)
  {
    $password = genRandomString();
    $pass     = md5($password);
    $query    = "UPDATE users SET password='" . $pass . "' WHERE email='" . $email . "'";
    $result = mysql_query($query) or mysql_error();
    if (mysql_affected_rows())
      {
        return $password;
      }
    else
      {
        return false;
      }
  }
function genRandomString()
  {
    $length     = 7;
    $characters = "0123456789abcdefghijklmnopqrstuvwxyz";
    $string     = '';
    for ($p = 0; $p < $length; $p++)
      {
        $st = mt_rand(0, 30);
        $string .= $characters[$st];
      }
    return $string;
  }
function deleteUser($uid)
  {
    // echo "llllllllllll"; die;
    $query = "DELETE FROM users WHERE  user_id='" . $uid . "' "; //die;('hhhhhhhhhh');  
    if (mysql_query($query) or mysql_error())
      {
        $query4 = "DELETE FROM mange_event WHERE user_id=" . $uid;
        mysql_query($query4);
        $query1 = "DELETE FROM groupdetails WHERE user_id=" . $uid;
        mysql_query($query1);
        $query2 = "DELETE FROM friends WHERE (user_id='" . $uid . "' OR friend_id='" . $uid . "')";
        mysql_query($query2);
        $query3 = "DELETE FROM group WHERE user_id='" . $uid . "'"; //die; 
        mysql_query($query3);
        $query5 = "DELETE FROM invitation WHERE (host_id='" . $uid . "' OR guest_id='" . $uid . "') ";
        mysql_query($query5);
        $query6 = "DELETE FROM event_images WHERE user_id='" . $uid . "'";
        mysql_query($query6);
        return true;
      }
    else
      {
        return false;
      }
  }
function blockUser($uid, $fid)
  {
    $data = array(
        'blocked_id' => $uid,
        'blockedby_id' => $fid
    );
    insert($data, 'blockuser');
    $f = mysql_insert_id();
    if (mysql_affected_rows())
      {
        return true;
      }
    else
      {
        return false;
      }
  }

function unblockUser($uid, $fid)
  {
    $query2 = "DELETE FROM blockuser WHERE blocked_id='" . $uid . "' AND blockedby_id='". $fid ."'"; //die(jojo);
    mysql_query($query2);
    if (mysql_affected_rows())
      {
        return true;
      }
    else
      {
        return false;
      }
  }


function alreadyblocked($uid, $fid)
  {
    $query = "SELECT bid FROM blockuser WHERE  blocked_id='" . $uid . "' AND blockedby_id='" . $fid . "' "; //die;
    $result = mysql_query($query) or mysql_error();
    if (mysql_num_rows($result) > 0)
      {
        return true;
      }
    else
      {
        return false;
      }
  }
function blocked($uid, $fid)
  {
    $y1    = array();
    $query = " SELECT * FROM blockuser WHERE (blocked_id='" . $fid . "' AND blockedby_id='" . $uid . "') OR (blocked_id='" . $uid . "' AND blockedby_id='" . $fid . "') "; //die;
    $result = mysql_query($query) or mysql_error();
    $y = mysql_fetch_assoc($result);
    if ($y)
      {
        $y['block_status_for_both'] = 'blocked';
        return $y;
      }
    elseif ($y == null || $y != '')
      {
        //echo "jiji"; die('kiki');
        $y1['block_status_for_both'] = 'NA';
        $y1['blockedby_id']          = 'NA';
        return $y1;
      }
  }
function edit_ProfilePic($uid, $pic,$purl,$uName)
  {
    $query = "UPDATE users SET profile_pic='" . $pic . "',uname='" . $uName . "' WHERE user_id='" . $uid . "' "; //  die;
    $result = mysql_query($query) or mysql_error();
    if (mysql_affected_rows())
      {
        return true;
      }
    else
      {
        return false;
      }
  }
function download_item($uid, $eid, $pid)
  {
    if ($pid == 0)
      {
        $query = "SELECT image FROM mange_event WHERE  ev_id='" . $eid . "' AND user_id='" . $uid . "' ";
      }
    else
      {
        $query = "SELECT image FROM event_images WHERE id='" . $pid . "' AND ev_id='" . $eid . "' AND user_id='" . $uid . "' ";
      }
    if (mysql_query($query) or mysql_error())
      {
        $result = mysql_query($query) or mysql_error();
      }
  }
function delete_friend($uid, $fid)
  {
    $query = "DELETE FROM friends WHERE (friend_id='" . $fid . "' AND user_id='" . $uid . "') OR (friend_id='" . $uid . "' AND user_id='" . $fid . "') ";
    if (mysql_query($query) or mysql_error())
      {
        $query4 = "DELETE FROM groupdetails WHERE (user_id='" . $uid . "' AND friend_id='" . $fid . "') OR (user_id='" . $fid . "' AND friend_id='" . $uid . "') "; // die;
        mysql_query($query4);
        $query1 = "DELETE FROM invitation WHERE (host_id='" . $fid . "' AND guest_id='" . $uid . "') OR (host_id='" . $uid . "' AND guest_id='" . $fid . "') AND accepted=0 ";
        mysql_query($query1);
        $query2 = "DELETE FROM invitation WHERE (host_id='" . $fid . "' AND guest_id='" . $uid . "') OR (host_id='" . $uid . "' AND guest_id='" . $fid . "') AND accepted=1 ";
        mysql_query($query2);
        /*$query3 = "DELETE FROM group WHERE user_id='".$uid."'";	//die; 
        mysql_query($query3);*/
        return true;
      }
    if (mysql_affected_rows())
      {
        return true;
      }
    else
      {
        return false;
      }
  }
function deleteUserfrmfeed($uid, $eid)
  {
    $query2 = "DELETE FROM invitation WHERE	guest_id='" . $uid . "' AND event_id='" . $eid . "' AND accepted=1 ";
    mysql_query($query2);
    if (mysql_affected_rows())
      {
        return true;
      }
    else
      {
        return false;
      }
  }
function delete_item($uid, $eid, $pid)
  {
    $query = "DELETE FROM event_images WHERE id='" . $pid . "' AND ev_id='" . $eid . "' AND user_id='" . $uid . "' ";
    if (mysql_query($query) or mysql_error())
      {
        return true;
      }
    else
      {
        return false;
      }
  }

function deleteGroup($uid, $gid)
  {
    $query = " DELETE FROM `group` where id='" . $gid . "' AND user_id='" . $uid . "'";
    if (mysql_query($query) or mysql_error())
      {
        $query4 = "DELETE FROM groupdetails WHERE (user_id='" . $uid . "' AND group_id='" . $gid . "') "; // die;
        mysql_query($query4);
        return true;
      }
    if (mysql_affected_rows())
      {
        return true;
      }
    else
      {
        return false;
      }
  }
function imgexist($eid, $pid)
  {
    if ($pid == 0)
      {
        $query = " SELECT image FROM mange_event WHERE  ev_id='" . $eid . "' "; //die('0');			
      }
    else
      {
        $query = " SELECT id FROM event_images WHERE id='" . $pid . "' AND ev_id='" . $eid . "' "; // die('img');
      }
    if (mysql_query($query) or mysql_error())
      {
        $result = mysql_query($query) or mysql_error();
        if (mysql_num_rows($result) > 0)
          {
            $img = mysql_fetch_assoc($result);
            //return $img;
            return true;
          }
        else
          {
            return false;
          }
      }
  }
function alreadyreported($u, $e, $i)
  {
    if ($i == 0)
      {
        $query = " SELECT reportid FROM reportimage WHERE  eventid='" . $e . "' AND reportedbyid='" . $u . "' AND imageid=0 "; //die('0');			
      }
    else
      {
        $query = " SELECT reportid FROM reportimage WHERE  eventid='" . $e . "' AND reportedbyid='" . $u . "' AND imageid='" . $i . "' "; //die('1');			
      }
    $result = mysql_query($query) or mysql_error();
    if (mysql_num_rows($result) > 0)
      {
        return true;
      }
    else
      {
        return false;
      }
  }
function report_item($data)
  {
    //print_r($data); die;
    if ($pid == 0)
      {
        $i = insert($data, 'reportimage');
      }
    else
      {
        $i = insert($data, 'reportimage');
      }
    $result = mysql_query($query) or mysql_error();
    if (mysql_affected_rows())
      {
        return true;
      }
    else
      {
        return false;
      }
  }
function group_detail($u, $g, $IMG_URL)
  {
    //    $query = "SELECT id as group_id, user_id FROM `group` WHERE user_id ='".$u."' AND id ='".$g."' "; 
    $query = " SELECT g.id, g.name, g.icon, g.user_id, gd.friend_id FROM `group` as g INNER JOIN groupdetails as gd ON g.id = gd.group_id WHERE g.user_id='" . $u . "' AND g.id='" . $g . "' ";
    $result = mysql_query($query) or mysql_error();
    if (mysql_num_rows($result) > 0)
      {
        while ($y = mysql_fetch_assoc($result))
          {
            $y1[] = $y;
          }
        // echo "<pre>";  print_r($y1); die;
        if ($y1)
          {
            $e     = 0;
            $final = array();
            foreach ($y1 as $z)
              {
                if ($details = userProfileDetail($y1[$e]['friend_id'], $IMG_URL))
                  {
                    while ($y2 = mysql_fetch_assoc($details))
                      {
                        if (!empty($y2['profile_pic']))
                          {
                            $y2['profile_pic'] = $IMG_URL . "thumb_" . $y2['profile_pic'];
                          }
                        if (!empty($y2['uname']))
                          {
                            $y2['user_name'] = $y2['uname'];
                          }
                        $z1[] = $y2;
                      }
                  }
                $final['group_id']       = $y1[$e]['id'];
                $final['group_name']     = $y1[$e]['name'];
                $final['group_icon_url'] = $IMG_URL . "thumb_" . $y1[$e]['icon'];
                $final['friends']        = $z1;
                $e++;
              }
          }
      }
    //echo "<pre>"; print_r($final); die;
    return $final;
  }
function editgroup($data)
  {
    //echo "coming"; //die;	
    $uid              = $_REQUEST['user_id'];
    $gid              = $_REQUEST['group_id'];
    $ei               = $_FILES["icon"]["name"];
    $name             = $_REQUEST['name'];
    $updation_dat_tim = date('Y-m-d H:i:s');
    if (!empty($_REQUEST['user_id']) && !empty($_REQUEST['group_id']))
      {
        $fields = '';
        if (!empty($_REQUEST['name']))
          {
            $findme = '=';
            if (!$pos = strpos($fields, $findme))
              {
                $name = mysql_real_escape_string($_REQUEST['name']);
                $fields .= "name='" . $name . "' ";
              }
            else
              {
                $fields .= ",name='" . $name . "' ";
              }
          }
        if (!empty($_FILES["icon"]["name"]))
          {
            $findme = '=';
            if (!$pos = strpos($fields, $findme))
              {
                $fields .= "icon='" . $ei . "' ";
              }
            else
              {
                $fields .= ",icon='" . $ei . "' ";
              }
          }
        if (!empty($_REQUEST['datetime']))
          {
            $findme = '=';
            if (!$pos = strpos($fields, $findme))
              {
                $fields .= "datetime='" . $updation_dat_tim . "' ";
              }
            else
              {
                $fields .= ",datetime='" . $updation_dat_tim . "' ";
              }
          }
        //echo "<pre>"; echo $fields; 
      }
    $query = "UPDATE `group` SET $fields WHERE user_id = '" . $uid . "' AND id = '" . $gid . "' ";
    $result = mysql_query($query) or mysql_error();
   
        return true;
     
  }
function frndOrNOt($me, $ou)
  {
    $query = "SELECT user_id,friend_id,freq_status from friends 
	    where (user_id='" . $me . "' AND friend_id='" . $ou . "') OR (user_id='" . $ou . "' AND friend_id='" . $me . "')";
    $result = mysql_query($query) or mysql_error();
    if (mysql_num_rows($result) > 0)
      {
        if ($y = mysql_fetch_assoc($result))
          {
            $z[] = $y;
          }
        return $z;
      }
    else
      {
        return false;
      }
  }
function alrdy_acceptd_frndReq($h_id, $g_id, $resp)
  {
    $query = " SELECT req_id FROM friends WHERE user_id='" . $h_id . "' AND friend_id='" . $g_id . "' AND freq_status='" . $resp . "' "; //die;
    $result = mysql_query($query) or mysql_error();
    if (mysql_num_rows($result) > 0)
      {
        return true;
      }
    else
      {
        return false;
      }
  }
function ad_fReq($h_id, $g_id, $resp)
  {
if( $resp==2){$query = "DELETE from friends  WHERE user_id='" . $h_id . "' AND friend_id='" . $g_id . "'"; 
    $result = mysql_query($query) or mysql_error();
//print_r($query);die;
    if (mysql_affected_rows())
      {
        return true;
      }
    else
      {
        return false;
      }}else{
    $query = "UPDATE friends SET freq_status='" . $resp . "' WHERE user_id='" . $h_id . "' AND friend_id='" . $g_id . "'"; 
    $result = mysql_query($query) or mysql_error();
//print_r($query);die;
    if (mysql_affected_rows())
      {
        return true;
      }
    else
      {
        return false;
      }
}
  }
function last_freqAdded_u_dtails($u_id, $f_id, $identifier)
  {
    if ($identifier == 1)
      {
        $fd     = f_dtail($f_id);
        //echo "<pre>"; pr($fd); exit;
        $query  = " SELECT * FROM friends WHERE user_id=$u_id AND friend_id=$f_id AND   freq_status='1'";
        $result = mysql_query($query);
      }
    elseif ($identifier == 2)
      {
        $fd     = f_dtail($f_id);
        //echo "<pre>"; pr($fd); exit; 
        $query  = " SELECT * FROM friends WHERE user_id=$u_id AND friend_id=$f_id  AND  freq_status ='2' ";
        $result = mysql_query($query);
      }
    $row                = mysql_fetch_assoc($result);
    //echo $row; die;
    $row['friend_id']   = $fd['user_id'];
    $row['friend_name'] = $fd['uname'];
    unset($row['freq_status']);
    return $row;
  }
function check_email($e)
  {
    $query = " SELECT * FROM users WHERE email='" . $e . "' ";
    $result = mysql_query($query) or mysql_error();
    if (mysql_num_rows($result) > 0)
      {
        return $result;
      }
    else
      {
        return false;
      }
  }
function uniqueUserName($uUN)
  {
    $query = " SELECT uniqueusername FROM users WHERE uniqueusername='" . $uUN . "' ";
    $result = mysql_query($query) or mysql_error();
    if (mysql_num_rows($result) > 0)
      {
        return $result;
      }
    else
      {
        return false;
      }
  }
function event_detail($ev_id)
  {
    $query = "SELECT ev_id as event_id, user_id, event_name, date_from, date_to, locations, event_icon, image from mange_event WHERE ev_id=$ev_id ";
    $result = mysql_query($query) or mysql_error();
    if (mysql_num_rows($result) > 0)
      {
        $g                    = get_CountofComingGuest($ev_id);
        $z                    = mysql_fetch_assoc($g);
        $g1                   = get_total_EventImage($ev_id);
        $z1                   = mysql_fetch_assoc($g1);
        $z2                   = mysql_fetch_assoc($result);
        $z2['guest_count']    = $z['guest_count']+1;
        //$z2['total_no_pixos'] = $z1['image_count'] + 1;
		$z2['total_no_pixos'] = $z1['image_count'];
        return $z2;
      }
    else
      {
        return false;
      }
  }
/*=start=====get_Count of coming guest to an event======*/
function get_CountofComingGuest($ev_id)
  {
    $query = "SELECT Count(guest_id) as guest_count from invitation where accepted=1 AND event_id=$ev_id ";
    $result = mysql_query($query) or mysql_error();
    if (mysql_num_rows($result) > 0)
      {
        //$result2=mysql_fetch_assoc($result);
        return $result;
      }
    else
      {
        return false;
      }
  }
/*=end=====get_Count of coming guest to an event=+=====*/
/*=start=====get_Count_total_EventImage======*/
function get_total_EventImage($ev_id)
  {
    $query = "SELECT Count(image) as image_count from event_images WHERE ev_id=$ev_id ";
    $result = mysql_query($query) or mysql_error();
    if (mysql_num_rows($result) > 0)
      {
        //echo $result; die;
        return $result;
      }
    else
      {
        return false;
      }
  }
/*=end=====get_Count_total_EventImage========*/
function userProfileDetail($u)
  {
    $query = " SELECT uname, uniqueusername, profile_pic FROM users WHERE user_id='" . $u . "' "; // die;
    $result = mysql_query($query) or mysql_error();
    if (mysql_num_rows($result) > 0)
      {
        return $result;
      }
    else
      {
        return false;
      }
  }
function uProfDetail2($u)
  {
    $query = "SELECT count(image) as total_pixos from event_images where user_id='" . $u . "' ";
    $result = mysql_query($query) or mysql_error();
    if (mysql_num_rows($result) > 0)
      {
        return $result;
      }
    else
      {
        return false;
      }
  }
function uProfCountInvSent($u)
  {
    // echo "u3";
    $query = "SELECT COUNT(host_id) as inv_send_count FROM invitation WHERE host_id='" . $u . "' ";
    $result = mysql_query($query) or mysql_error();
    if (mysql_num_rows($result) > 0)
      {
        return $result;
      }
    else
      {
        return false;
      }
  }
function uProfCountInvRecvd($u)
  {
    //  echo "u4"; 
    $query = "SELECT COUNT(guest_id) as inv_recv_count FROM invitation WHERE guest_id='" . $u . "' ";
    $result = mysql_query($query) or mysql_error();
    if (mysql_num_rows($result) > 0)
      {
        return $result;
      }
    else
      {
        return false;
      }
  }
function comnEvntsMeMyFrnd($u, $f)
  {
    $query = "SELECT i.event_id FROM invitation as i INNER JOIN friends as f ON (i.host_id=f.user_id OR i.guest_id=f.user_id) WHERE  (i.host_id='" . $u . "' OR i.guest_id='" . $u . "') AND  (i.host_id='" . $f . "' OR i.guest_id='" . $f . "') AND i.accepted='1' GROUP BY i.event_id";
    $result = mysql_query($query) or mysql_error();
    if (mysql_num_rows($result) > 0)
      {
        while ($cemf = mysql_fetch_assoc($result))
          {
            $cemf1[] = $cemf;
          }
        return $cemf1;
      }
    else
      {
        return false;
      }
  }
function searchUniqueUserName($fname, $u)
  {
    $query = " SELECT * FROM users WHERE uniqueusername ='$fname'  and user_id!='" . $u . "'";
    $result = mysql_query($query) or mysql_error();
    if (mysql_num_rows($result) > 0)
      {
        return $result;
      }
    else
      {
        return false;
      }
  }
function search_f($fname, $u)
  {
    $query = " SELECT * FROM users WHERE uname LIKE '%$fname%'  and user_id!='" . $u . "'";
    $result = mysql_query($query) or mysql_error();
    if (mysql_num_rows($result) > 0)
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
    $query  = " SELECT * FROM users WHERE email='$email' ";
    $result = mysql_query($query);
    $row    = mysql_fetch_array($result);
    return $row;
  }
function last_ad_u_dtails($u_id, $f_id, $identifier)
  {
    if ($identifier == 1)
      {
        $fd     = f_dtail($f_id);
        //echo "<pre>"; echo $identifier; 
        $query  = " SELECT * FROM friends WHERE (user_id=$u_id AND friend_id=$f_id) AND (user_id=$f_id AND friend_id=$u_id)"; //die("asdasjdkahd");
        $result = mysql_query($query);
      }
    elseif ($identifier == 2)
      {
        $fd     = f_dtail($f_id);
        //echo "<pre>"; echo $identifier; 
        $query  = " SELECT * FROM friends WHERE user_id=$u_id AND friend_id=$f_id "; //die("ssssssssssss");
        $result = mysql_query($query);
      }
    $row                = mysql_fetch_assoc($result);
    $row['friend_name'] = $fd['uname'];
    return $row;
  }
function insert_or_delFreq($u_id, $f_id, $h1)
  {
    if ($h1 == 1)
      {
        $query = " INSERT into friends (req_id, user_id, friend_id) VALUES ('','" . $f_id . "','" . $u_id . "') ";
        $result = mysql_query($query) or mysql_error();
        return $result;
      }
    elseif ($h1 == 2)
      {
        $query = " DELETE FROM friends WHERE  user_id=$u_id and friend_id=$f_id ";
        $result = mysql_query($query) or mysql_error();
        if (mysql_affected_rows())
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
    $query = "select user_id, uname from users where user_id=" . $f_id;
    $res   = mysql_query($query);
    if (mysql_num_rows($res) > 0)
      {
        $row = mysql_fetch_assoc($res);
        return $row;
      }
    else
      {
        return false;
      }
  }
function del_Freq($u_id, $frnd, $resp)
  {
    $query = " DELETE FROM friends WHERE  user_id=$u_id and friend_id=$frnd "; //die;
    $result = mysql_query($query) or mysql_error();
    if (mysql_affected_rows())
      {
        return true;
      }
    else
      {
        return false;
      }
  }
function FrndReq_exist($usr_id, $frnd)
  {
    $query = "SELECT req_id FROM friends WHERE user_id='" . $usr_id . "' AND friend_id='" . $frnd . "' ";
    $result = mysql_query($query) or mysql_error();
    if (mysql_num_rows($result) > 0)
      {
        return true;
      }
    else
      {
        return false;
      }
  }
function alrdy_acceptd_frnd_req($usr_id, $frnd, $resp, $h1)
  {
    if ($resp == 1 && $h1 == 1)
      {
        echo $query = " SELECT req_id FROM friends WHERE (user_id=$usr_id AND friend_id=$frnd) AND (user_id=$frnd AND friend_id=$usr_id) ";
        $result = mysql_query($query) or mysql_error();
      }
    elseif ($resp == 2 && $h1 == 2)
      {
        echo $query = " SELECT req_id FROM friends WHERE user_id='" . $frnd . "' AND friend_id='" . $usr_id . "'  ";
        $result = mysql_query($query) or mysql_error();
      }
    if (mysql_num_rows($result) > 0)
      {
        return true;
      }
    else
      {
        return false;
      }
  }
function list_evnts($user_id, $IMG_URL)
  {
    $query = "SELECT ev_id, user_id, event_name, locations, date_from, date_to, event_icon, image FROM mange_event WHERE user_id ='" . $user_id . "' ORDER BY date_from, date_to DESC "; //die; 
    if ($res = mysql_query($query))
      {
        while ($row = mysql_fetch_assoc($res))
          {
            $events[] = $row;
          }
        return $events;
      }
    else
      {
        return false;
      }
  }
function list_evnts2($ev_id, $IMG_URL)
  {
    $query = "SELECT ev_id, user_id, event_name, locations, date_from, date_to, event_icon, image FROM mange_event WHERE ev_id='" . $ev_id . "' "; //die; 
    if ($res = mysql_query($query))
      {
        while ($row = mysql_fetch_assoc($res))
          {
            $events[] = $row;
          }
        return $events;
      }
    else
      {
        return false;
      }
  }
function list_evnts1($user_id)
  {
    $query = "SELECT event_id FROM invitation WHERE  guest_id='" . $user_id . "' AND accepted=1 ";
    $res   = mysql_query($query);
    if (mysql_num_rows($res) > 0)
      {
        while ($res1 = mysql_fetch_assoc($res))
          {
            $events[] = $res1;
          }
        foreach ($events as $r)
          {
            $ids      = $r['event_id'];
            $list_e   = list_evnts2($ids, $IMG_URL);
            $evList[] = $list_e;
          }
        return $evList;
      }
    else
      {
        return false;
      }
  }
function shw_gall($u_id, $ev_id, $IMG_URL)
  {
    //for getting inages
    $query = "SELECT image from mange_event where user_id=$u_id AND ev_id=$ev_id ";
    $res   = mysql_query($query);
    if (mysql_num_rows($res) > 0)
      {
        $result = mysql_fetch_assoc($res);
        $row[]  = $IMG_URL . 'thumb_' . $result['image'];
        $query1 = "SELECT image from event_images where user_id=$u_id AND ev_id=$ev_id and type='image'";
        $res1   = mysql_query($query1);
        if (mysql_num_rows($res1) > 0)
          {
            while ($result1 = mysql_fetch_array($res1))
              {
                $row[] = $IMG_URL . 'thumb_' . $result1['image'];
              }
          }
        //for get videos
        $query2 = "SELECT image,thumb_img from event_images where user_id=$u_id AND ev_id=$ev_id and type='video'";
        $res2   = mysql_query($query2);
        if (mysql_num_rows($res2) > 0)
          {
            while ($result2 = mysql_fetch_assoc($res2))
              {
                $result2['image'] = $IMG_URL . 'thumb_' . $result2['image'];
                if ($result2['thumb_img'])
                  {
                    $th = $IMG_URL . 'thumb_' . $result2['thumb_img'];
                  }
                else
                  {
                    $th = '';
                  }
                $result2['thumb_img'] = $th;
                $row1[]               = $result2;
              }
          }
        else
          {
            $row1 = "no video";
          }
        $query10 = "SELECT uname from users where user_id='$u_id'";
        $res10   = mysql_query($query10);
        $r       = mysql_fetch_assoc($res10);
        echo json_encode(array(
            'Status' => "true",
            'uploader_name' => $r['uname'],
            'uploader_id' => $user_id,
            'image_url' => $row,
            'video_url' => $row1
        ));
      }
    else
      {
        echo json_encode(array(
            'Status' => "false",
            'message' => 'user not created any event'
        ));
      }
  }
function friend_list($u_id)
  {
    $query = "SELECT u.user_id,u.uname as user_name, u.profile_pic, u.age, f.block_status from friends as f LEFT JOIN users as u ON u.user_id=f.friend_id where (f.user_id='" . $u_id . "') AND f.freq_status=1 
                union
                SELECT u.user_id,u.uname as user_name, u.profile_pic, u.age, f.block_status from friends as f LEFT JOIN users as u ON u.user_id=f.user_id where (f.friend_id='" . $u_id . "') AND f.freq_status=1";
    $result = mysql_query($query) or mysql_error();
    if (mysql_num_rows($result) > 0)
      {
        return $result;
      }
    else
      {
        return false;
      }
  }
function countInv($uid)
  {
    echo $query = "SELECT host_id, guest_id, event_id, count(guest_id) as countInvitations 
        FROM invitation where host_id='" . $uid . "' AND guest_id!='0' "; //die('gurjot');
    $res = mysql_query($query);
    $row = mysql_fetch_assoc($res);
    return $row;
  }
function frndsComng($ev_id, $imgPath, $fc)
  {
    if ($fc == 1)
      {
        $q1 = "SELECT guest_id as user_id from invitation where accepted=1 and event_id=" . $ev_id;
      }
    if ($fc == 2)
      {
        $q1 = "SELECT guest_id as user_id from invitation where accepted=2 and event_id=" . $ev_id;
      }

    if ($res1 = mysql_query($q1))
      {
        $count = mysql_num_rows($res1);
        if ($count > 0)
          {
            $k = 0;
            while ($w = mysql_fetch_assoc($res1))
              {
                $res_detail = "SELECT uname, profile_pic from users where user_id='" . $w['user_id'] . "'";
                $rdtail     = mysql_query($res_detail);
                while ($resDtail = mysql_fetch_assoc($rdtail))
                  {
                    //$gallery[$i]['friends_coming'][]=$resDtail;
                    $resDtail['profile_pic'] = $imgPath . $resDtail['profile_pic'];
                    $ntAccptd[]              = $resDtail;
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
//=======================Pixovent========================/
/*===========================================Pixovent===============================================*/
?>



























































































                            
                            
                            
                            
                            
                            
                            

                            
                            
                            
                            
