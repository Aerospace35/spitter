<?php 
 
  
    include 'core/init.php' ;
    $theme = $_GET["theme"];
   $_SESSION["theme"] = $theme;
    if (isset($_SESSION['user_id'])) {
      header('location: home.php');
    }
   
?>

<html>
	<head>
		
<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<title>Spitter</title>
		<meta charset="UTF-8" />
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.css"/>
        <!-- <link rel="stylesheet" href="assets/css/style-complete.css"/> -->
        <link rel="stylesheet" href="assets/css/<?php echo $theme; ?>/index_style.css?v=<?php echo time(); ?>">
        <link rel="stylesheet" href="assets/css/<?php echo $theme; ?>/bootstrap.min.css">
        <link rel="stylesheet" href="assets/css/<?php echo $theme; ?>/all.min.css">
        <link rel="stylesheet" href="assets/css/<?php echo $theme; ?>/home_style.css?v=<?php echo time(); ?>">

		<link rel="shortcut icon" type="image/png" href="assets/images/twitter-logo.png"> 
		<style>
	.header-card {
    position: fixed; //fixed is absolute to a browser
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(0, 0, 0, 0.1);
    color: white;
}
.banner {
	background: rgba(0, 0, 0, 0.1);
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
 }

</style>
	</head>

<center>
                    <div class="slow-login">
                        
                        <button class="login-small-display signin-btn pri-btn">Log in</button>
                        <br><br>
                        <a href="?theme=dark">Dark Mode</a>
                        <a href="?theme=light">Light Mode</a>
                        <span class="front-para">See what’s happening in the world right now</span>
                        <span class="join">Spitter: a Kinder Bird App</span>
                        <br>
                        <img class="login-bird" src="assets/images/twitter-logo.png" alt=""/><br>
                        
                        
                        
                        <?php 
    
    if(isset($_SESSION['errors'])) {
          foreach ($_SESSION['errors'] as $error) {
          echo ' <li class="error-li">
          <div class="span-fp-error">'.$error.'</div>
          </li>';
          
          }
          unset($_SESSION['errors']);  

         
          
    } 
        
      ?>
      
      
      <br>
                        <img width="370px" height="62px" src="assets/images/spitterad.png" alt=""/><br>
      <br>
      <span class="join">Features</span>
     <div class="features"> <br>
      --Spitter allows anyone to have a blue checkmark for free!
      <br>
      <br>
      --Say big things- you now have 420 characters per Spit!
      <br><br>
      --Impersonate billionaires freely
      <br><br>
      --Connect with others easily
      <br><br>
      --Don't pay 8 dollars a month for anything
      <br><br><br>
      </div>
      <a href="rules.php"><span class="join">Rules</span></a><br>
      <a href="privacy.php"><span class="join">Privacy & Data</span></a><br>
      <span class="join">Sign Up Today!</span>
      <br><br><br><br><br><br><br><br><br><br>
      
      
                            <div class="banner">
    <center><button type="button" id="auto" onclick="" class="signup-btn pri-btn" data-toggle="modal" data-target="#exampleModalCenter">Sign Up</button></center>
    <center><button type="button" id="auto" onclick="" class="signup-btn pri-btn" data-toggle="modal" data-target="#exampleModalCenter2">Log In</button></center>
</div>

</center>                             
                            <!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 style="font-weight: 700;" class="modal-title" id="exampleModalLongTitle">Sign Up</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
                    
         <?php include "includes/signup-form.php"; ?>
      </div>
      
    </div>
  </div>
</div>

<!--modal login-->
<div class="modal fade" id="exampleModalCenter2" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle2" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 style="font-weight: 700;" class="modal-title" id="exampleModalLongTitle2">Log In</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
<?php  include 'includes/login.php' ?>
</div>
      
    </div>
  </div>
</div>


                    </div>
            </section>
            
           
        </main>
        
        <script src="assets/js/jquery-3.4.1.slim.min.js"></script>
        <script src="assets/js/popper.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
        <script src="assets/js/mine.js"></script>
</body>
</html>
