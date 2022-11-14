<?php

   include 'core/init.php';
  
   $user_id = $_SESSION['user_id'];

   $user = User::getData($user_id);
   
   if (User::checkLogIn() === false) 
   header('location: index.php');


   $tweets = Tweet::tweets($user_id);
   $who_users = Follow::whoToFollow($user_id);
   $notify_count = User::CountNotification($user_id);
 
?>
    

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | Spitter</title>
    
    <link rel="shortcut icon" type="image/png" href="assets/images/twitter.svg"> 
    <link rel="stylesheet" href="assets/css/<?php echo $theme; ?>/bootstrap.min.css">
        <link rel="stylesheet" href="assets/css/<?php echo $theme; ?>/all.min.css">
        <link rel="stylesheet" href="assets/css/<?php echo $theme; ?>/home_style.css?v=<?php echo time(); ?>">
    
   
</head>
<body>

  <script src="assets/js/jquery-3.5.1.min.js"></script>

      <!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div  class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
           <?php include "includes/tweetcontext.php"; ?>
    </div>
  </div>
</div>

      

      <!-- End welcome -->

    <div id="mine">
 
    <?php include "includes/sidebar.php"; ?>
          

      <div class="grid-posts">
        <div class="border-right">
          <div class="grid-toolbar-center">
            <div class="center-input-search">
              <div class="input-group-login" id="whathappen">
                
                <div class="container">
                  <div class="part-1">
                    <div class="header">
                      <div class="home">
                        <h2>Home</h2>
                      </div>
                      <!-- <div class="icon">
                        <button type="button" name="button">+</button>
                      </div> -->
                    </div>
            
                    <div class="text">
                      <form class="" action="handle/handleTweet.php" method="post" enctype="multipart/form-data">
                        <div class="inner">
            
                            <img src="assets/images/users/<?php echo $user->img ?>" alt="profile photo">
                        
                          <label>
            
                            <textarea class="text-whathappen" name="status" rows="8" cols="80" placeholder="What's happening?"></textarea>
                        
                        </label>
                        </div> 
                            
                         <!-- tmp image upload place -->
                        <div class="position-relative upload-photo"> 
                          <img class="img-upload-tmp" src="assets/images/tweets/tweet-60666d6b426a1.jpg" alt="">
                          <div class="icon-bg">
                          <i id="#upload-delete-tmp" style="color: #CB7944" class="fas fa-times position-absolute upload-delete"></i>  

                          </div>
                        </div>


                        <div class="bottom"> 
                          
                          <div class="bottom-container">
                              
                            
                              
                           
                            <label for="tweet_img" class="ml-3 mb-2 uni">

                              <i class="fa fa-image item1-pair"></i>
                            </label>
                            <input class="tweet_img" id="tweet_img" type="file" name="tweet_img">    
                                
                          </div>
                          <div class="hash-box">
                        
                              <ul style="margin-bottom: 0;">


                              </ul>
                          
                          </div>
                          <?php if (isset($_SESSION['errors_tweet'])) { 
                            
                            foreach($_SESSION['errors_tweet'] as $t) {?>
                            
                          <div class="alert alert-danger">
                          <span class="item2-pair"> <?php echo $t; ?> </span>
                          </div>
                         
                         <?php } } unset($_SESSION['errors_tweet']); ?>
                          <div>
                         
                            <span class="bioCount" id="count">420</span>
                            <input id="tweet-input" type="submit" name="tweet" value="Spit" class="submit"
                            >
                          </div>
                      </div>
                      </form>
                    </div>
                  </div>
                  <div class="part-2">
            
                  </div>
            
                </div>
                
                
              </div>
            </div>
            <!-- <div class="mt-icon-settings">
              <img src="https://i.ibb.co/W5T9ycN/settings.png" alt="" />
            </div> -->
          </div>
          <div class="box-fixed" id="box-fixed"></div>
            
          <?php  include 'includes/tweets.php'; ?>

        </div>


        <div class="wrapper-right">
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
                  <?php } ?></p>
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
      </div>
      </div> 
      <script src="assets/js/search.js"></script>
          <script src="assets/js/photo.js?v=<?php echo time(); ?>"></script>
          <script type="text/javascript" src="assets/js/hashtag.js"></script>
          <script type="text/javascript" src="assets/js/like.js"></script>
          <script type="text/javascript" src="assets/js/comment.js?v=<?php echo time(); ?>"></script>
          <script type="text/javascript" src="assets/js/retweet.js?v=<?php echo time(); ?>"></script>
          <script type="text/javascript" src="assets/js/follow.js?v=<?php echo time(); ?>"></script>
      <script src="https://kit.fontawesome.com/38e12cc51b.js" crossorigin="anonymous"></script>
      <!-- <script src="assets/js/jquery-3.4.1.slim.min.js"></script> -->
      <script src="assets/js/jquery-3.5.1.min.js"></script>

        <script src="assets/js/popper.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
</body>
</html> 
