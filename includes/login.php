<?php

    
?>
    <form action="./handle/handlelogin.php" class="login-box" method="POST">
            <input class="" name="email" size="16" type="email"  placeholder="Email">
            
            <input class="" name="password" size="16" type="password" placeholder="Password">
            
            <input type="submit" name="login" value="Log in">
            <a class="login-link" href="forgotpassword.php">Forgot password?</a>
            <div class="con">
				
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
    </div>
    </form>
 
	
	
