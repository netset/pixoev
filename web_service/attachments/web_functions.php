                                <?php

/*-------general functions---------*/	
	function insert($userData,$table='')
	{
		$col ='';
		$val ='';
		$array_len=count($userData);
		$i=1;
                
		foreach( $userData as $key => $value )
		{

			$col .= "$key";
			if($i!=$array_len){$col .= ",";}
                        if($key=='event_name'){
                            $new_var=mysql_real_escape_string($value);
			    $val .= "'$new_var'";
                        }else{
                           $val .= "'$value'";
                        }
			if($i!=$array_len){$val .= ",";}
			$i++;
		}
         //echo $query = "INSERT INTO `$table` ($col) VALUES ($val)";die;
         $query = "INSERT INTO `$table` ($col) VALUES ($val)";
	 	  $result=mysql_query($query) or mysql_error();
		return $result;
	}	
	
	function delete($userData,$table='')
	{
		foreach( $userData as $key => $value )
		{
			$col_value .= "$key"."="."'$value'";
		}
		$query = "DELETE FROM `$table` WHERE $col_value";
		if(mysql_query($query) or mysql_error())
		{
			return true;
		}
		else
		{
			return false;
		}
		
	}
	
	function select($userData='*',$table='',$whr='1')
	{
		if(is_array($userData))
		{
			$array_len=count($userData);
			$i=1;
			foreach( $userData as $key => $value )
			{
				$col .= "$value";
				if($i!=$array_len)
				{
					$col .= ",";
				}
				$i++;
			}
		}
		else
		{
			$col='*';
		}
		
		if(is_array($whr))
		{
			foreach( $whr as $key => $value )
			{
				$whr .= "$key"."="."'$value'";
			}
		}
		else
		{
			$whr='1';
		}
		echo $query = "SELECT $col FROM `$table` WHERE $whr";
		if(mysql_query($query) or mysql_error())
		{
			return true;
		}
		else
		{
			return false;
		}
		
	}
	
	
	
function upload_file($image)
{		
	$image["name"]=str_replace(" ","",$image["name"]);
	$allowedExts = array("jpg", "jpeg", "gif", "png");
	$extension = end(explode(".", $image['name']));
	if ((($image["type"] == "image/gif") || ($image["type"] == "image/jpeg") || ($image["type"] == "image/png") || ($image["type"] == "image/pjpeg")) && ($image["size"] < 2097152) && in_array($extension, $allowedExts))
	{
		if ($image["error"] > 0)
		{
			//echo "Return Code: " . $image["error"] . "<br>";
			return false;
		}
		else
		{
			if (file_exists("images/thumb_" . $image["name"]))
			{
				//echo $image["name"] . " already exists. ";
				return false;
			}
			else
			{
			
				$images = $image["tmp_name"];
				$new_images = "thumb_".$image["name"];
				$width=500; //*** Fix Width & Heigh (Autu caculate) ***//
				$size=GetimageSize($images);
				$height=round($width*$size[1]/$size[0]);
				$images_orig = ImageCreateFromJPEG($images);
				$photoX = ImagesX($images_orig);
				$photoY = ImagesY($images_orig);
				$images_fin = ImageCreateTrueColor($width, $height);
				ImageCopyResampled($images_fin, $images_orig, 0, 0, 0, 0, $width+1, $height+1, $photoX, $photoY);
				if(ImageJPEG($images_fin,"images/".$new_images))
				{
					ImageDestroy($images_orig);
					ImageDestroy($images_fin);
					return true;
				}
				else
				{
					return false;
				}
			}
		}
	}
	else
	{
		//echo "Invalid file";
		return false;
	}
}
function getLocationInfoByIp(){
	$client  = @$_SERVER['HTTP_CLIENT_IP'];
	$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
	$remote  = @$_SERVER['REMOTE_ADDR'];
	$result  = array('country'=>'', 'city'=>'');
	if(filter_var($client, FILTER_VALIDATE_IP)){
		$ip = $client;
	}elseif(filter_var($forward, FILTER_VALIDATE_IP)){
		$ip = $forward;
	}else{
		$ip = $remote;
	}
	$ip_data = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$ip));   
	if($ip_data && $ip_data->geoplugin_countryName != null){
		$result['country'] = $ip_data->geoplugin_countryCode;
		$result['city'] = $ip_data->geoplugin_city;
	}
	return $ip_data->geoplugin_countryName;
}

function create_thumbnail($img_type,$file){
$name = pathinfo($file['name'], PATHINFO_FILENAME);
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);

$increment = ''; //start with no suffix

while(file_exists("images/thumb_".$name . $increment . '.' . $extension)) {
    $increment++;
}

$file['name'] = $name . $increment . '.' . $extension;
 if($img_type == 'thumb')
 {
 // $width = 140;
  //$height = 100;
  $width = 350;
  $height = 160;
  /* new file name */
 $path = "images/thumb_".$file['name'];
 }
 else if($img_type == 'medium')
 {
  $width = 350;
  $height = 250;
  /* new file name */
 $path = "images/thumb_".$file['name'];
 }
 /* Get original image x y*/
 list($w, $h) = getimagesize($file['tmp_name']);
 /* calculate new image size with ratio */
 $ratio = max($width/$w, $height/$h);
 $h = ceil($height / $ratio);
 $x = ($w - $width / $ratio) / 2;
 $w = ceil($width / $ratio);
 
 /* read binary data from image file */ 
 $imgString = file_get_contents($file['tmp_name']);
 
 /* create image from string */
 $image = imagecreatefromstring($imgString);
 $tmp = imagecreatetruecolor($width, $height);
 imagecopyresampled($tmp, $image,
   0, 0,
   $x, 0,
   $width, $height,
   $w, $h);
 /* Save image */
 switch ($file['type']) {
  case 'image/jpeg':
    if(imagejpeg($tmp, $path, 100))
    {
       $out=$file['name'];
    }
   break;
  case 'image/png':
   if(imagepng($tmp, $path, 0))
 {
       $out=$file['name'];
    }
   break;
  case 'image/gif':
   if(imagegif($tmp, $path))
 {
       $out=$file['name'];
    }
   break;
  default:
   exit;
   break;
 }
 
 
 if(!empty($out))
{
 return $out;
}
else
{
 return false;
}

 /* cleanup memory */
 //imagedestroy($image);
 //imagedestroy($tmp);
}

function resize_image($image,$upload)
{	

	$images = $image["tmp_name"];
	$new_images = "thumb_".$image["name"];
	$width=500; //*** Fix Width & Heigh (Autu caculate) ***//
	$size=GetimageSize($images);
	$height=round($width*$size[1]/$size[0]);
	$images_orig = ImageCreateFromJPEG($images);
	$photoX = ImagesX($images_orig);
	$photoY = ImagesY($images_orig);
	$images_fin = ImageCreateTrueColor($width, $height);
	ImageCopyResampled($images_fin, $images_orig, 0, 0, 0, 0, $width+1, $height+1, $photoX, $photoY);
	if(ImageJPEG($images_fin,"$upload/".$new_images))
	{
		ImageDestroy($images_orig);
		ImageDestroy($images_fin);
		return true;
	}
	else
	{
		return false;
	}
}
function get_country_from_city($city)
{
	if($city)
	{
		$address = urlencode($city);
		$geocode=file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address='.$address.'&sensor=false');
		$output= json_decode($geocode);
		// echo "<pre>";print_r($output);
	        for($j=0;$j<count($output->results[0]->address_components);$j++)
        	{
                	if($output->results[0]->address_components[$j]->types[0]=='country')
			{
				$country=$output->results[0]->address_components[$j]->long_name;		
			}			
	  	}
	  	return $country; 	
	}
	else
	{
		return false; 
	}
		
}

function pr($data)
{
	echo "<pre>";
	print_r($data);
	echo "</pre>";
}
function get_time_diff_in_str($db_time)
{
	$get_time=date('Y-m-d H:i:s');
	$btime= date('Y-m-d H:i:s',strtotime($db_time));
	$days = (strtotime($get_time) - strtotime($btime)) / (60 * 60 * 24); 
    $hour = (strtotime($get_time) - strtotime($btime)) / (60 * 60);
    $diff = strtotime($get_time) - strtotime($btime);
    $years   = floor($diff / (365*60*60*24)); 
    	$months  = floor(($diff - $years * 365*60*60*24) / (30*60*60*24)); 
	$days    = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
	$hours   = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24)/ (60*60)); 
	$minuts  = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60); 
	$seconds = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minuts*60));
  	if($years!=0)
 	{
   		$ago=$years." y";
 	}
	else
	{
	  	if($months!=0)
	 	{
			$start = strtotime($get_time);
			$end = strtotime($btime);
			$days_between = ceil(abs($end - $start) / 86400);
			$week=floor($days_between/7);
			$ago=$week."wk";
	 	}
		else
		{
		  if($days==0 && $hours!=0)
		  {
		   $ago=$hours."hr";
		  }
		  if($days==0 && $hours==0)
		  {
		   $ago=$minuts."m";
		  }
		  if($days!=0)
		  {
			  $week=floor($days/7);
			  if($week)
			  {
					$ago=$week."wk";
			  }
			  else
			  {
					$ago=$days."d";
			  }
		  }
		}
  	}
  return $ago;
}

function get_time_in_string($db_time)
{
	$get_time=date('Y-m-d H:i:s');
	$btime= date('Y-m-d H:i:s',strtotime($db_time));
	$days = (strtotime($get_time) - strtotime($btime)) / (60 * 60 * 24); 
    $hour = (strtotime($get_time) - strtotime($btime)) / (60 * 60);
    $diff = strtotime($get_time) - strtotime($btime);
    $years   = floor($diff / (365*60*60*24)); 
    	$months  = floor(($diff - $years * 365*60*60*24) / (30*60*60*24)); 
	$days    = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
	$hours   = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24)/ (60*60)); 
	$minuts  = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60); 
	$seconds = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minuts*60));
  	if($years!=0)
 	{
   		$ago=$years."y ago";
 	}
	else
	{
	  	if($months!=0)
	 	{
	   		$ago=$months."m ago";
	 	}
		else
		{
		  if($days==0 && $hours!=0)
		  {
		   $ago=$hours."h ago";
		  }
		  if($days==0 && $hours==0)
		  {
		   $ago=$minuts."min ago";
		  }
		  if($days!=0)
		  {
			  $week=floor($days/7);
			  if($week)
			  {
					$ago=$week."wk ago";
			  }
			  else
			  {
					$ago=$days."d ago";
			  }
		  }
		}
  	}
  return $ago;
}

function getIpAddress() {
    return (empty($_SERVER['HTTP_CLIENT_IP'])?(empty($_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER['REMOTE_ADDR']:$_SERVER['HTTP_X_FORWARDED_FOR']):$_SERVER['HTTP_CLIENT_IP']);
}
function get_timezone()
{
$ip= getIpAddress(); // the IP address to query
$query = @unserialize(file_get_contents('http://ip-api.com/php/'.$ip));
//pr($query);
if($query && $query['status'] == 'success') {
    return $query['timezone'];
}
else
{
    return 'Asia/calcutta';
}

}   

function get_date_acc_to_ip($date_time,$timezone)
{
	if(!empty($timezone))
	{
		$required_timezone=get_timezone();
		if($timezone == $required_timezone)
		{
			return $date_time;
exit;
		}
		else
		{
unset($date);
			$date = new DateTime($date_time, new DateTimeZone($timezone));
			$date->setTimezone(new DateTimeZone($required_timezone));
			return $date->format('Y-m-d H:i:s');
exit;
		}
	}
	else
	{
		return false;
exit;
	}
}

function get_server_date()
{
	$curr_timezone=get_timezone();
	$required_timezone='Australia/Adelaide';
	$date = new DateTime(date('Y-m-d H:i:s'), new DateTimeZone($curr_timezone));
	$date->setTimezone(new DateTimeZone($required_timezone));
	return $date->format('Y-m-d H:i:s');
}


function get_time_like_fb($db_time)
{

	$get_time=date('Y-m-d H:i:s');
	$btime= date('Y-m-d H:i:s',strtotime($db_time));
	$days = (strtotime($get_time) - strtotime($btime)) / (60 * 60 * 24); 
    $hour = (strtotime($get_time) - strtotime($btime)) / (60 * 60);
    $diff = strtotime($get_time) - strtotime($btime);
    $years   = floor($diff / (365*60*60*24)); 
    	$months  = floor(($diff - $years * 365*60*60*24) / (30*60*60*24)); 
	$days    = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
	$hours   = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24)/ (60*60)); 
	$minuts  = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60); 
	$seconds = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minuts*60));
  	if($years!=0)
 	{
 		$ago=date('M d, Y',strtotime($db_time));
   		$ago=$years." year";
 	}
	else
	{
	  	if($months!=0)
	 	{
	   		$ago=date('M d',strtotime($db_time));
	   		//$ago=$db_time;
	 	}
		else
		{
		  if($days==0 && $hours!=0)
		  {
			$ago=date('g:ma',strtotime($db_time));
		   	//$ago=$hours."hr";
		  }
		  if($days==0 && $hours==0)
		  {
		  	$ago=date('H:m',strtotime($db_time));
		   	//$ago=$minuts."m";
		  }
		  if($days!=0)
		  {
			  $week=floor($days/7);
			  if($week)
			  {
			  	$ago=date('M d',strtotime($db_time));
				//	$ago=$week."wk";
			  }
			  else
			  {
				$ago=date('D',strtotime($db_time));
				//	$ago=$days."d";
			  }
		  }
		}
  	}
  return $ago;
}

?>
                            
                            