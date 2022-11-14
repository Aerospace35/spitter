<?php 

include 'core/init.php';
  
$user_id = $_SESSION['user_id'];

$user = User::getData($user_id);
$who_users = Follow::whoToFollow($user_id);
$notify_count = User::CountNotification($user_id);

if (User::checkLogIn() === false) 
header('location: index.php');

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings | Spitter</title>
    <link rel="stylesheet" href="assets/css/<?php echo $theme; ?>/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/<?php echo $theme; ?>/all.min.css">

    <!-- time function to force css file to reload -->
    
    <link rel="stylesheet" href="assets/css/<?php echo $theme; ?>/profile_style.css?v=<?php echo time(); ?>">
    <link rel="shortcut icon" type="image/png" href="assets/images/twitter.svg"> 
   
</head>
<body>
     
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div  class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
           <?php include "includes/tweetcontext.php"; ?>
    </div>
  </div>
</div>

    <div id="mine">
    <?php include "includes/sidebar.php"; ?>
          
  

      <div class="grid-posts">
        <div class="border-right">
          <div class="grid-toolbar-center">
            <div class="center-input-search">
              
            </div>
           
          </div>

          <div class="box-fixed" id="box-fixed"></div>
  
          <div class="box-home feed">
               <div class="container">
                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                  <a style="color:white !important;border:white;" class="nav-link active text-center" id="v-pills-home-tab" data-toggle="pill" href="#v-pills-home" role="tab" aria-controls="v-pills-home" aria-selected="true">Change Email or Username</a>
                  <a style="color:white !important;" class="nav-link text-center" id="v-pills-profile-tab" data-toggle="pill" href="#v-pills-profile" role="tab" aria-controls="v-pills-profile" aria-selected="false">Change Password</a>
				  <button onclick="window.location.href='./handle/handleDeleteAccount.php';">Delete Account</button>
                </div>
                <div class="tab-content" id="v-pills-tabContent">
                  <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel" aria-labelledby="v-pills-home-tab">
                        <!-- Change EMAIL and USAERNAME Form -->

                    <form method="POST" action="handle/handleAccountSetting.php" class="py-4" >
                      
                    <?php  if (isset($_SESSION['errors_account'] )) {
                        
                        ?>
                              
                        <?php foreach ($_SESSION['errors_account'] as $error) { ?>

                            <div  class="alert alert-danger" role="alert">
                                <p style="font-size: 15px;" class="text-center"> <?php echo $error ; ?> </p>  
                            </div> 
                                    <?php }   ?> 

                        <?php }  unset($_SESSION['errors_account'])  ?>
                      <div class="form-group">
                        <label for="exampleInputEmail1">Email address</label>
                        <input type="email" name="email" value="<?php echo $user->email; ?>" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email">
                    
                      </div>
                      <div class="form-group">
                        <label for="exampleInputPassword1">Username</label>
                        <input type="text" name="username" value="<?php echo $user->username; ?>" class="form-control" id="exampleInputPassword1" placeholder="Username">
                      </div>
                      
                      <div class="text-center">

                        <button type="submit" name="submit" class="btn btn-primary">Save Changes</button>
                      </div>

                    </form>

                  </div>
                  <div class="tab-pane fade" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
                    

                    <!-- Change Password Form -->

                    <form method="POST" action="handle/handleChangePassword.php" class="py-4" >
                    <script src="assets/js/jquery-3.5.1.min.js"></script>
                    <?php  if (isset($_SESSION['errors_password'] )) {
                        
                        ?>
                        

                         <script>  
                                $(document).ready(function(){
                            // Open modal on page load
                            $("#v-pills-profile-tab").click();
                    
                          });
                          </script>

                        <?php foreach ($_SESSION['errors_password'] as $error) { ?>

                            <div  class="alert alert-danger" role="alert">
                                <p style="font-size: 15px;" class="text-center"> <?php echo $error ; ?> </p>  
                            </div> 
                                    <?php }   ?> 

                        <?php }  unset($_SESSION['errors_password'])  ?>

                      <div class="form-group">
                        <label for="exampleInputEmail1">Old Password</label>
                        <input type="password" name="old_password" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Old Password">
                    
                      </div>
                      <div class="form-group">
                        <label for="exampleInputPassword1">New Password</label>
                        <input type="password" name="new_password" class="form-control" id="exampleInputPassword1" placeholder="New Password">
                      </div>

                      <div class="form-group">
                        <label for="exampleInputPassword1">Verify Password</label>
                        <input type="password" name="ver_password" class="form-control" id="exampleInputPassword1" placeholder="New Password">
                      </div>
                      
                      <div class="text-center">

                        <button type="submit" name="submit" class="btn btn-primary">Save Changes</button>
                        
                      </div>

                    </form>

                  </div>
     
                </div>
                   
               </div>
           
          </div>
        </div>
        <div> 
            
        <div style="width: 90%;" class="container">

            <div class="input-group py-2 m-auto pr-5 position-relative">

            <i id="icon-search" class="fas fa-search tryy"></i>
            <input type="text" class="form-control search-input"  placeholder="Search Spitter">
            <div class="search-result">

        
            </div>
            </div>
       </div>


      
            

                
       <div class="box-share">
            <p class="txt-share"><strong>Who to follow</strong></p>
            <?php 
            foreach($who_users as $user) { 
              //  $u = User::getData($user->user_id);
               $user_follow = Follow::isUserFollow($user_id , $user->id) ;
               ?>
          <div class="grid-share">
          <a style="position: relative; z-index:5; color:black" href="<?php echo $user->username;  ?>">
                      <img
                        src="assets/images/users/<?php echo $user->img; ?>"
                        alt=""
                        class="img-share"
                      />
                    </a>
                    <div>
                      <p>
                      <a style="position: relative; z-index:5; color:black" href="<?php echo $user->username;  ?>">  
                      <strong><?php echo $user->name; ?></strong>
                      </a>
                    </p>
                      <p class="username">@<?php echo $user->username; ?>
                      <?php if (Follow::FollowsYou($user->id , $user_id)) { ?>
                  <span class="ml-1 follows-you">Follows You</span></p>
                  <?php } ?></p></p>
                    </div>
                    <div>
                      <button class="follow-btn follow-btn-m 
                      <?= $user_follow ? 'following' : 'follow' ?>"
                      data-follow="<?php echo $user->id; ?>"
                      data-user="<?php echo $user_id; ?>"
                      data-profile="<?php echo $u_id; ?>"
                      style="font-weight: 700;">
                      <?php if($user_follow) { ?>
                        Following 
                      <?php } else {  ?>  
                          Follow
                        <?php }  ?> 
                      </button>
                    </div>
                  </div>

                  <?php }?>
         
          
          </div>
  
  
  
        </div>
      </div> </div>
      <script src="assets/js/search.js"></script>    
       <script src="assets/js/follow.js"></script>
      <script src="https://kit.fontawesome.com/38e12cc51b.js" crossorigin="anonymous"></script>
      <!-- <script src="assets/js/jquery-3.4.1.slim.min.js"></script> -->
        <script src="assets/js/popper.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
</body>
</html>
