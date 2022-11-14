<div class="wrapper-left">
        <div class="sidebar-left">
          <div class="grid-sidebar" style="margin-top: 12px">
            <div class="icon-sidebar-align">
              <img src="assets/images/twitter-logo.png" alt="" height="60px" width="60px" />
            </div>
          </div>

          <a href="home.php">
          <div class="grid-sidebar bg-active" style="margin-top: 12px">
            <div class="icon-sidebar-align">
              <i style="font-size: 26px; color: #CB7944;" class="fas fa-home"></i>
            </div>
            <div class="wrapper-left-elements">
              <a class="" href="home.php" style="margin-top: 4px;"><strong>Home</strong></a>
            </div>
          </div>
          </a>
  
           <a href="notification.php">
          <div class="grid-sidebar">
            <div class="icon-sidebar-align position-relative">
                <?php if ($notify_count > 0) { ?>
              <i class="notify-count"><?php echo $notify_count; ?></i> 
              <?php } ?>
              <i style="font-size: 26px; color: #CB7944;" class="fas fa-bell"></i>
            </div>
  
  
  
            <div class="wrapper-left-elements">
              <a href="notification.php" style="margin-top: 4px"><strong>Notification</strong></a>
            </div>
          </div>
          </a>
        <a href="<?php echo BASE_URL . "discover.php"; ?>">
          <div class="grid-sidebar ">
            <div class="icon-sidebar-align">
              <i style="font-size: 26px; color: #CB7944;" class="fas fa-compass"></i>
            </div>
            <div class="wrapper-left-elements">
              <a href="<?php echo BASE_URL . "discover.php"; ?>" style="margin-top: 4px"><strong>Discover</strong></a>
            </div>
            </div>
        
        
            <a href="<?php echo BASE_URL . $user->username; ?>">
          <div class="grid-sidebar">
            <div class="icon-sidebar-align">
              <i style="font-size: 26px; color: #CB7944;" class="fas fa-user"></i>
            </div>
  
            <div class="wrapper-left-elements">
              <!-- <a href="/twitter/<?php echo $user->username; ?>"  style="margin-top: 4px"><strong>Profile</strong></a> -->
              <a  href="<?php echo BASE_URL . $user->username; ?>"  style="margin-top: 4px"><strong>Profile</strong></a>
            
            </div>
          </div>
          </a>
          <a href="<?php echo BASE_URL . "account.php"; ?>">
          <div class="grid-sidebar ">
            <div class="icon-sidebar-align">
              <i style="font-size: 26px; color: #CB7944;" class="fas fa-cog"></i>
            </div>
            <div class="wrapper-left-elements">
              <a href="<?php echo BASE_URL . "account.php"; ?>" style="margin-top: 4px"><strong>Settings</strong></a>
            </div>
            </div>
            <a href="<?php echo BASE_URL . "/core/userlist/"; ?>">
          <div class="grid-sidebar ">
            <div class="icon-sidebar-align">
              <i style="font-size: 26px; color: #CB7944;" class="fas fa-list"></i>
            </div>
            <div class="wrapper-left-elements">
              <a href="<?php echo BASE_URL . "/core/userlist/"; ?>" style="margin-top: 4px"><strong>User List</strong></a>
            </div>
           
            
          </div>
          </a>
          
          <a href="<?php echo "/" . "?theme=dark"; ?>">
          <div class="grid-sidebar ">
            <div class="icon-sidebar-align">
              <i style="font-size: 26px; color: #CB7944;" class="fas fa-moon"></i>
            </div>
            <div class="wrapper-left-elements">
              <a href="<?php echo "/index.php" . "?theme=dark"; ?>" style="margin-top: 4px"><strong>Dark Mode</strong></a>
            </div>
           
            
          </div>
          </a>
          <a href="<?php echo "/" . "?theme=light"; ?>">
          <div class="grid-sidebar ">
            <div class="icon-sidebar-align">
              <i style="font-size: 26px; color: #CB7944;" class="fas fa-sun"></i>
            </div>
            <div class="wrapper-left-elements">
              <a href="<?php echo "/index.php" . "?theme=light"; ?>" style="margin-top: 4px"><strong>Light Mode</strong></a>
            </div>
           
            
          </div>
          </a>
          <a href="includes/logout.php">
          <div class="grid-sidebar">
            <div class="icon-sidebar-align">
            <i style="font-size: 26px; color:red" class="fas fa-sign-out-alt"></i>
            </div>
  
            <div class="wrapper-left-elements">
              <a style="color:red" href="includes/logout.php" style="margin-top: 4px"><strong>Logout</strong></a>
            </div>
          </div>
          </a>
          <button class="button-twittear" id="auto" onclick="" data-toggle="modal" data-target="#exampleModalCenter">
            <strong>Spit</strong>
          </button>
  
          <div class="box-user">
            <div class="grid-user">
              <div>
                <img
                  src="assets/images/users/<?php echo $user->img ?>"
                  alt="user"
                  class="img-user"
                />
              </div>
              <div>
                <p class="name"><strong><?php if($user->name !== null) {
                echo $user->name; } ?></strong></p>
                <p class="username">@<?php echo $user->username; ?></p>
              </div>
              <div class="mt-arrow">
                <img
                  src="https://i.ibb.co/mRLLwdW/arrow-down.png"
                  alt=""
                  height="18.75px"
                  width="18.75px"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
