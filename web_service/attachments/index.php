<h2>(1)<span style="color:blue">Custom SIGNUp/REGISTRATION</span></h2>
<form action="web_service.php" method="post" enctype="multipart/form-data">service_type="<span style="color:red">register</span>"<br/>
<input type="hidden" name="service_type" value="register">
<table>
<tr><td>User Name :</td><td><input type="text" name="uname" ></input></td><td>uname</td></tr>
<tr><td>Email :</td><td><input type="text" name="email" ></input></td><td>   email</td></tr>
<tr><td>Age :</td><td><input type="text" name="age" ></input></td><td>   age</td></tr>
<tr><td>Password:</td><td><input type="password" name="password" ></input></td><td>   password</td></tr>
<tr><td>Profile Picture :</td><td><input type="file" name="profile_pic" ></input></td><td>   profile_pic</td></tr>
<tr><td><input type="submit" ></input></td></tr>
</table>
</form>


<h2>(2)<span style="color:blue">Custom LOGIN</span></h2>
<form action="web_service.php" method="post" >service_type="<span style="color:red">login</span>"<br/>
<input type="hidden" name="service_type" value="login">
<table>
<tr><td>Email :</td><td><input type="text" name="email" ></input></td><td>   email</td></tr>
<tr><td>Password:</td><td><input type="password" name="password" ></input></td><td>   password</td></tr>
<tr><td><input type="submit" ></input></td></tr>
</table>
</form>



<h2>(3)<span style="color:blue">CREATE EVENT</span></h2>
<form action="add_item.php" method="post" enctype="multipart/form-data">service_type="<span style="color:red">create_event</span>"<br/>
<input type="hidden" name="service_type" value="create_event">
<table>
<tr><td>**User Id :    </td><td><input type="text" name="user_id" >             </input></td><td>   user_id </td></tr>
<tr><td>**Event name : </td><td><input type="text" name="event_name" >          </input></td><td>   event_name </td></tr>
<tr><td>**date from:   </td><td><input type="text" name="date_from" id="" >     </input></td><td>   date_from &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; format:yyyy-mm-dd(space)hh:mm:ss </td></tr>
<tr><td>**date to:     </td><td><input type="text" name="date_to" >             </input></td><td>   date_to &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; format:yyyy-mm-dd(space)hh:mm:ss </td></tr>
<tr><td>**Event icon:  </td><td><input type="file" name="event_icon" >          </input></td><td>   event_icon </td></tr>
<!-- <tr><td>**Description: </td><td><input type="text" name="description" >         </input></td><td>   description </td></tr>  -->
<tr><td>**Location:   </td><td><input type="text" name="location" >           </input></td><td>   location </td></tr>
<!-- <tr><td>**Auto delete(1 or 2):</td><td><input type="text" name="auto_delete" >  </input></td><td>    auto_delete </td></tr>
<tr><td>** Timer(1-24):</td><td><input type="text" name="timer" >               </input></td><td>          timer </td></tr> -->
<!-- <tr><td>** Image:      </td><td><input type="file" name="image" >               </input></td><td>    image </td></tr> -->
<tr><td>** inavitations:      </td><td><input type="text" name="invite" >       </input></td><td>invite </td></tr>
<tr><td><input type="submit" >   </input></td></tr>
</table>
</form> 

<h2>(4)<span style="color:blue" >Edit EVENT</span></h2>
<form action="add_item.php" method="post" enctype="multipart/form-data">service_type="<span style="color:red">edit_event</span>"<br/>
<input type="hidden" name="service_type" value="edit_event">
<table>
<tr><td>**User Id :</td><td><input type="text" name="user_id" ></input></td><td>   user_id</td></tr>
<tr><td>**Event Id :</td><td><input type="text" name="event_id" ></input></td><td>   event_id</td></tr>
<tr><td>Event name :</td><td><input type="text" name="event_name" ></input></td><td>   event_name</td></tr>
<tr><td>date from:</td><td><input type="text" name="date_from" id="" ></input></td><td> date_from &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; format:yyyy-mm-dd(space)hh:mm:ss </td></tr>
<tr><td>date to:</td><td><input type="text" name="date_to" ></input></td><td>   date_to &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; format:yyyy-mm-dd(space)hh:mm:ss </td></tr>
<tr><td>Event_icon:</td><td><input type="file" name="event_icon" ></input></td><td>   event_icon</td></tr>
<!-- <tr><td>Description:</td><td><input type="text" name="description" ></input></td><td>   description</td></tr> -->
<tr><td>Locations:</td><td><input type="text" name="locations" ></input></td><td>   locations</td></tr>
<!--<tr><td>Auto_delete(1 or 2):</td><td><input type="text" name="auto_delete" ></input></td><td>   auto_delete</td></tr>
<tr><td>Timer(1-48):</td><td><input type="text" name="timer" ></input></td><td>   timer</td></tr>-->
<!-- <tr><td>Image:</td><td><input type="file" name="image" ></input></td><td>   image</td></tr> -->
<tr><td>** inavitations:      </td><td><input type="text" name="invite" >       </input></td><td>invite </td></tr>
<tr><td><input type="submit" ></input></td></tr>
</table></form> 

<h2>(5)<span style="color:blue">Delete EVENT</span></h2>
<form action="add_item.php" method="post" >service_type="<span style="color:red">delete_event</span>"<br/>
<input type="hidden" name="service_type" value="delete_event">
<table>
<tr><td>**User Id :</td><td><input type="text" name="user_id" ></input></td><td>   user_id</td></tr>
<tr><td>**Event_id :</td><td><input type="text" name="event_id" ></input></td><td>  event_id</td></tr>
<tr><td><input type="submit" ></input></td></tr>
</table>
</form>


<h2>(6)<span style="color:blue">List All Events</span></h2>
<form action="web_service.php" method="post" >service_type="<span style="color:red">list_of_events</span>"<br/>
<input type="hidden" name="service_type" value="list_of_events">
<table>
<tr><td>**User Id :</td><td><input type="text" name="user_id" ></input></td><td>   user_id</td></tr>
<tr><td><input type="submit" ></input></td></tr>
</table>
</form> 

 
<h2>(7)<span style="color:blue">Add(pixos) videos</span></h2>
<form action="add_item.php" method="post" enctype="multipart/form-data">service_type="<span style="color:red">add_images_videos</span>"<br/>
<input type="hidden" name="service_type" value="add_images_videos">
<table>
<tr><td>**User Id :</td><td><input type="text" name="user_id" ></input></td><td>   user_id</td></tr>
<tr><td>**Event_id :</td><td><input type="text" name="event_id" ></input></td><td>  event_id</td></tr>
<tr><td>event files:</td><td><input type="file" name="file1" ></input></td><td>   file1</td></tr>
<tr><td>event thumb files:</td><td><input type="file" name="t_file1" ></input></td><td>   t_file1</td></tr>
<tr><td>type:</td><td><input type="text" name="type" ></input></td><td>   type</td></tr>
<tr><td><input type="submit" ></input></td></tr>
</table>
</form> 

<h2>(8)<span style="color:blue">Show Gallery</span></h2>
<form action="web_service.php" method="post" >service_type="<span style="color:red">show_gallery</span>"<br/>
<input type="hidden" name="service_type" value="show_gallery">
<table>
<tr><td>**User Id :</td><td><input type="text" name="user_id" ></input></td><td>   user_id</td></tr>
<tr><td>**Event_id :</td><td><input type="text" name="event_id" ></input></td><td>  event_id</td></tr>                        
<tr><td><input type="submit" ></input></td></tr>
</table>
</form> 

<h2>(9)<span style="color:blue">Event Detail</span>(<span style="color:orange">need_further_modification</span>)</h2>
<form action="event.php" method="post" >service_type="<span style="color:red">event_detail</span>"<br/>
<input type="hidden" name="service_type" value="event_detail">
<table>
<tr><td>User Id :</td><td><input type="text" name="user_id" ></input></td><td>   user_id</td></tr>
<tr><td>**Event_id :</td><td><input type="text" name="event_id" ></input></td><td>   event_id</td></tr>
<!-- <tr><td>Status :</td><td><input type="text" name="status" ></input></td><td>   status</td></tr> -->
<tr><td><input type="submit" ></input></td></tr>
</table>
</form>

<h2>(10-A)<span style="color:blue">Friend Request i recieved</span></h2>
<form action="event.php" method="post" >service_type="<span style="color:red">fReqIRecvd</span>"<br/>
<input type="hidden" name="service_type" value="fReqIRecvd">
<table>
<tr><td>**User Id :<input type="text" name="user_id" ></td><td>user_id</td></tr>
<tr><td><input type="submit" ></input></td></tr>
</table>
</form>    


<h2>(10-B)<span style="color:blue">Friend request</span></h2>
<form action="invitation.php" method="post" >service_type="<span style="color:red">send_friend_req</span>"<br/>
<input type="hidden" name="service_type" value="send_friend_req">
<table>
<tr><td>User Id :</td><td><input type="text" name="user_id" ></input></td><td>   user_id</td></tr>
<tr><td>Friend id :</td><td><input type="text" name="friend_id" ></input></td><td>   friend_id</td></tr>
<tr><td><input type="submit" ></input></td></tr>
</table>
</form>




<h2>(11)<span style="color:blue">Accept/Deny Friend request</span></h2>
<form action="web_service.php" method="post" >service_type="<span style="color:red">ad_friend_req</span>"<br/>
<input type="hidden" name="service_type" value="ad_friend_req">
<table>
<tr><td>Friend Id(To whom request was Sent):</td><td><input type="text" name="friend_id" ></input></td><td>   friend_id</td></tr>
<tr><td>User Id(who Sent this request):</td><td><input type="text" name="user_id" ></input></td><td>   user_id</td></tr>
<tr><td>Response(Accepted=1, Denied=2):</td><td><input type="text" name="response" ></input></td><td>response</td></tr>
<tr><td><input type="submit" ></input></td></tr>
</table>
</form>



<h2>(12)<span style="color:blue">Friend List</span></h2>
<form action="web_service.php" method="post" >service_type="<span style="color:red">f_list</span>"<br/>
<input type="hidden" name="service_type" value="f_list">
<table>
<tr><td>**User Id :</td><td><input type="text" name="user_id" ></input></td><td>   user_id</td></tr>
<tr><td><input type="submit" ></input></td></tr>
</table></form>

<h2>(13)<span style="color:blue">Search Friend/Friends</span></h2>
<form action="web_service.php" method="post" >service_type="<span style="color:red">search_friends</span>"<br/>
<input type="hidden" name="service_type" value="search_friends">
<table>
<tr><td>**User Id :</td><td><input type="text" name="user_id" ></input></td><td>user_id</td></tr>
<tr><td>**Friend name :</td><td><input type="text" name="fname" ></input></td><td>fname</td></tr>
<tr><td><input type="submit" ></input></td></tr>
</table>
</form>


<h2>(14)<span style="color:blue">Invite Friends to an Event</span></h2>
<form action="event.php" method="post" >service_type="<span style="color:red">event_invitation</span>"<br/>
<input type="hidden" name="service_type" value="event_invitation">
<table>
<tr><td>HOST(User Id) :</td><td><input type="text" name="host_id" ></input></td><td>   host_id</td></tr>
<tr><td>GUEST(Friend id) :</td><td><input type="text" name="guest_id" ></input></td><td>   guest_id</td></tr>
<tr><td>Event id :</td><td><input type="text" name="event_id" ></input></td><td>   event_id</td></tr>
<tr><td><input type="submit" ></input></td></tr>
</table>
</form>

<h2>(15)<span style="color:blue">Accept/Deny Invitation</span></h2>
<form action="event.php" method="post" >service_type="<span style="color:red">response_of_invitation</span>"<br/>
<input type="hidden" name="service_type" value="response_of_invitation">
<table>
<tr><td>Guest Id :</td><td><input type="text" name="guest_id" ></input></td><td>   guest_id</td></tr>
<tr><td>Host Id :</td><td><input type="text" name="host_id" ></input></td><td>   host_id</td></tr>
<tr><td>Event Id :</td><td><input type="text" name="event_id" ></input></td><td>   event_id</td></tr>
<tr><td>Response(Accepted=1, Denied=2):</td><td><input type="text" name="response" ></input></td><td>response</td></tr>
<tr><td><input type="submit" ></input></td></tr>
</table>
</form>




<h2>(16)<span style="color:blue">Invitation I received</span></h2>
<form action="event.php" method="post" >service_type="<span style="color:red">inv_i_recvd</span>"<br/>
<input type="hidden" name="service_type" value="inv_i_recvd">
<table>
<tr><td>**User Id :<input type="text" name="user_id" ></td><td>user_id</td></tr>
<tr><td><input type="submit" ></input></td></tr>
</table>
</form>    



<h2>(17)<span style="color:blue">List of Invited Users</span></h2>
<form action="event.php" method="post" >service_type="<span style="color:red">coming</span>"<br/>
<input type="hidden" name="service_type" value="coming">
<table>
<tr><td>**Event Id :</td><td><input type="text" name="event_id" ></input></td><td>event_id</td></tr>
<tr><td>**Guest(Guest-Coming=1 OR Guest-not-Coming=2)  :</td><td><input type="text" name="guest_type"></input></td><td>guest_type</td></tr>
<tr><td><input type="submit" ></input></td></tr>
</table>
</form>   


<h2>(18)<span style="color:blue">Forgot Password</span></h2>
<form action="web_service.php" method="post" >sevice_type="<span style='color:red'>forgot_password</span>"<br/>
<input type="hidden" name="service_type" value="forgot_password">
<table>
<tr><td>**Email :<input type="text" name="email" >email</input></td><td><br/>
<tr><td><input type="submit" ></input></td></tr>
</table>
</form> 

<h2>(19)<span style="color:blue">USER Profile</span>(<span style="color:orange">user_profile</span>)</h2>
<form action="web_service.php" method="post" >service_type="<span style="color:red">user_profile</span>"<br/>
<input type="hidden" name="service_type" value="user_profile">
<table>
<tr><td>**User Id :</td><td><input type="text" name="user_id" ></input></td><td>   user_id</td></tr>
<tr><td>**Friend Id :</td><td><input type="text" name="friend_id" ></input></td><td>   friend_id</td></tr>
<tr><td><input type="submit" ></input></td></tr>
</table>
</form>

 
<h2>(20)<span style="color:blue">Create Group</span>(<span style="color:orange">create_group</span>)</h2>
<form action="web_service.php" method="post" enctype="multipart/form-data" >
service_type="<span style="color:red">create_group</span>"<br/>
<input type="hidden" name="service_type" value="create_group">
<table>
<tr><td>**User Id :</td><td><input type="text" name="user_id" ></input></td><td>   user_id</td></tr>
<tr><td>**Group Name :</td><td><input type="text" name="name" ></input></td><td>   name</td></tr> 
<tr><td>**Group Icon :</td><td><input type="file" name="icon" ></input></td><td>   icon</td></tr> 
<tr><td>Array of Friend Id</td><td><input type="text" name="f_id" ></input></td><td>   f_id</td></tr>
<tr><td><input type="submit" ></input></td></tr>
</table>
</form>

<h2>(20=>B)<span style="color:blue">Edit Group</span>(<span style="color:orange">editgroup</span>)</h2>
<form action="web_service.php" method="post" enctype="multipart/form-data" >
service_type="<span style="color:red">editgroup</span>"<br/>
<input type="hidden" name="service_type" value="editgroup">
<table>
<tr><td>**User Id :</td><td><input  type="text"   name="user_id" ></input></td><td>    user_id</td></tr>
<tr><td>**Group Id :</td><td><input type="text"   name="group_id"></input></td><td>  group_id</td></tr>
<tr><td>**Group Name :</td><td><input type="text" name="name" ></input></td><td>    name</td></tr> 
<tr><td>**Group Icon :</td><td><input type="file" name="icon" ></input></td><td>    icon</td></tr> 
<tr><td>Array of Friend Id</td><td><input type="text" name="friend_id" ></input></td><td>friend_id</td></tr>
<tr><td><input type="submit" ></input></td></tr>
</table>
</form>
 
<h2>(21)<span style="color:blue">List of All Groups</span>(<span style="color:orange">group_list</span>)</h2>
<form action="web_service.php" method="post" enctype="multipart/form-data" >
service_type="<span style="color:red">group_list</span>"<br/>
<input type="hidden" name="service_type" value="group_list">
<table>
<tr><td>**User Id :</td><td><input type="text" name="user_id" ></input></td><td>   user_id</td></tr>

<tr><td><input type="submit" ></input></td></tr>
</table>
</form>

<h2>(22)<span style="color:blue">Group Detail</span>(<span style="color:orange">group_detail</span>)</h2>
<form action="group.php" method="post" enctype="multipart/form-data" >
service_type="<span style="color:red">group_detail</span>"<br/>
<input type="hidden" name="service_type" value="group_detail">
<table>
<tr><td>**User Id :</td><td><input type="text" name="user_id" ></input></td><td>   user_id</td></tr>
<tr><td>**Group Id :</td><td><input type="text" name="group_id" ></input></td><td>   group_id</td></tr>
<tr><td><input type="submit" ></input></td></tr>
</table>
</form>

  
<h2>(23)<span style="color:blue">Delete Image</span>(<span style="color:orange">deleteImg</span>)</h2>
<form action="web_service.php" method="post" enctype="multipart/form-data" >
service_type="<span style="color:red">deleteImg</span>"<br/>
<input type="hidden" name="service_type" value="deleteImg">
<table>
<tr><td>**User Id :</td><td><input type="text" name="user_id" ></input></td><td>   user_id</td></tr>
<tr><td>**Event Id :</td><td><input type="text" name="event_id" ></input></td><td>   event_id</td></tr>
<tr><td>**Image id:</td><td><input type="text" name="pic_id" ></input></td><td>pic_id</td></tr>
<tr><td><input type="submit" ></input></td></tr>
</table>
</form>
  
<h2>(24)<span style="color:blue">Download Image</span>(<span style="color:orange">dwmloadImg</span>)</h2>
<form action="web_service.php" method="post" enctype="multipart/form-data" >
service_type="<span style="color:red">dwmloadImg</span>"<br/>
<input type="hidden" name="service_type" value="dwmloadImg">
<table>
<tr><td>**User Id :</td><td><input type="text" name="user_id" ></input></td><td>   user_id</td></tr>
<tr><td>**Event Id :</td><td><input type="text" name="event_id" ></input></td><td>   event_id</td></tr>
<tr><td>**Image id:</td><td><input type="text" name="pic_id" ></input></td><td>pic_id</td></tr>
<tr><td><input type="submit" ></input></td></tr>
</table>
</form>

<h2>(25)<span style="color:blue">Report Image</span>(<span style="color:orange">reportImg</span>)</h2>
<form action="web_service.php" method="post" enctype="multipart/form-data" >
service_type="<span style="color:red">reportImg</span>"<br/>
<input type="hidden" name="service_type" value="reportImg">
<table>
<tr><td>**User Id :</td><td><input type="text" name="user_id" ></input></td><td>   user_id</td></tr>
<tr><td>**Event Id :</td><td><input type="text" name="event_id" ></input></td><td>   event_id</td></tr>
<tr><td>**Image id:</td><td><input type="text" name="pic_id" ></input></td><td>pic_id</td></tr>
<tr><td>**Report Image :</td><td><input type="text" name="report" ></input></td><td>report(<span style="color:orange">report=</span>1 <span style="color:orange"> unreport=</span>0</span>)</td></tr>
<tr><td><input type="submit" ></input></td></tr>
</table>
</form>
                      
                            
<h2>(26)<span style="color:blue">List all Report Images</span>(<span style="color:orange">allreportImg</span>)</h2>
<form action="web_service.php" method="post" enctype="multipart/form-data" >
service_type="<span style="color:red">allreportImg</span>"<br/>
<input type="hidden" name="service_type" value="allreportImg">
<table>
<!-- <tr><td>**User Id :</td><td><input type="text" name="user_id" ></input></td><td>   user_id</td></tr> -->
<tr><td>**Event Id :</td><td><input type="text" name="event_id" ></input></td><td>   event_id</td></tr>
<!-- <tr><td>**Image id:</td><td><input type="text" name="pic_id" ></input></td><td>pic_id</td></tr> -->
<tr><td><input type="submit" ></input></td></tr>
</table>
</form>
                            
<h2>(27)<span style="color:blue">Delete Friend</span>(<span style="color:orange">deleteFriend</span>)</h2>
<form action="web_service.php" method="post" enctype="multipart/form-data" >
service_type="<span style="color:red">deleteFriend</span>"<br/>
<input type="hidden" name="service_type" value="deleteFriend">
<table>
<tr><td>**User Id :</td><td><input type="text" name="user_id" ></input></td><td> user_id</td></tr>
<tr><td>**Friend Id :</td><td><input type="text" name="friend_id" ></input></td><td> friend_id</td></tr>
<tr><td><input type="submit" ></input></td></tr>
</table>
</form>
        
<h2>(28)<span style="color:blue">Edit Profile Picture</span>(<span style="color:orange">editProfilePic</span>)</h2>
<form action="web_service.php" method="post" enctype="multipart/form-data" >
service_type="<span style="color:red">editProfilePic</span>"<br/>
<input type="hidden" name="service_type" value="editProfilePic">
<table>
<tr><td>**User Id :</td><td><input type="text" name="user_id" ></input></td><td> user_id</td></tr>
<tr><td>**Profile Pic :</td><td><input type="file" name="profile_pic" ></input></td><td> profile_pic</td></tr>
<tr><td><input type="submit" ></input></td></tr>
</table>
</form>

<h2>(29)<span style="color:blue">Block user</span>(<span style="color:orange">blockUser</span>)</h2>
<form action="web_service.php" method="post" enctype="multipart/form-data" >
service_type="<span style="color:red">blockUser</span>"<br/>
<input type="hidden" name="service_type" value="blockUser">
<table>
<tr><td>**User Id :</td><td><input type="text" name="user_id" ></input></td><td> user_id</td></tr>
<tr><td>**Friend Id :</td><td><input type="text" name="friend_id" ></input></td><td> friend_id</td></tr>
<tr><td><input type="submit" ></input></td></tr>
</table>
</form>


                   
<h2>(30)<span style="color:blue">Delete User</span>(<span style="color:orange">deleteUser</span>)</h2>
<form action="web_service.php" method="post" enctype="multipart/form-data" >
service_type="<span style="color:red">deleteUser</span>"<br/>
<input type="hidden" name="service_type" value="deleteUser">
<table>
<tr><td>**User Id :</td><td><input type="text" name="user_id" ></input></td><td> user_id</td></tr>
<tr><td><input type="submit" ></input></td></tr>
</table>
</form>
                            
<h2>(31)<span style="color:blue">Delete Event from User-FEED list</span>(<span style="color:orange">deleteUserfrmfeed</span>)</h2>
<form action="web_service.php" method="post" enctype="multipart/form-data" >
service_type="<span style="color:red">deleteUserfrmfeed</span>"<br/>
<input type="hidden" name="service_type" value="deleteUserfrmfeed">
<table>
<tr><td>**User Id :</td><td><input type="text" name="user_id" ></input></td><td> user_id</td></tr>
<!-- <tr><td>**Friend Id :</td><td><input type="text" name="friend_id" ></input></td><td> friend_id</td></tr><tr><td> -->
<tr><td>**Event Id :</td><td><input type="text" name="event_id" ></input></td><td>   event_id</td></tr>
<tr><td><input type="submit" ></input></td></tr>
</table>
</form>
                            
                            

                            
                            
                            
                           
                            

                            
                            

                            
                            
                            
                            


                            
                            
                            

                            
                            
                            