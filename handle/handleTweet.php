<?php 
include '../core/init.php';
require_once '../core/classes/validation/Validator.php';
require_once '../core/classes/image.php';
$rand = substr(str_shuffle("0123456789"), 0, 9);
use validation\Validator;

if (User::checkLogIn() === false) 
header('location: index.php'); 

if (isset($_POST['tweet'])) {

    $status =  User::checkInput($_POST['status']) ;

    $img = $_FILES['tweet_img'];
    
    if ($_POST['status'] == '' && $img['name'] == '' ) {
    $_SESSION['errors_tweet'] = ['status or image are required'];
    header('location: ../home.php'); 
    die();
    }

    $v = new Validator;
    $v->rules('status' , $status , ['string' , 'max:14']);
    if ($img['name'] != '') 
     $v->rules('image' , $img , ['image']);

    $errors = $v->errors;
    
    if ($errors == []) { 
        
        if ($img['name'] != '') {
        $image = new Image($img , "tweet"); 
        $tweetImg = $image->new_name ;
       
        } else $tweetImg = null;
        
       
        
        date_default_timezone_set("America/Edmonton");
        //posts database is what post ID is initially created off of
        $data = [
            'user_id' => $_SESSION['user_id'] , 
            'post_on' => date("Y-m-d H:i:s") ,
        ];
        // create function can handle with all tables and return last inserted id
        $post_id = User::create('posts' , $data);
       //experimental
$servername = "localhost";
$username = "";
$password = "";
$dbname = "";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "UPDATE posts SET id=$rand WHERE id=$post_id";

if ($conn->query($sql) === TRUE) {
  echo "Record updated successfully";
} else {
  echo "Error updating record: " . $conn->error;
}

$conn->close();
            //end experiment//
        $data_tweet = [
            'post_id' => $rand  ,
            'status' => $status , 
            'img' => $tweetImg
        ];
        User::create('tweets' , $data_tweet);
        if ($img['name'] != '') {
        $image->upload(); }
         
        // notify users if mention!

        preg_match_all("/@+([a-zA-Z0-9_]+)/i", $status, $mention);
        $user_id = $_SESSION['user_id'];
        foreach($mention[1] as $men) {
            $id = User::getIdByUsername($men);
            if($id != $user_id ) {
                $data_notify = [
                    'notify_for' => $id ,
                    'notify_from' => $user_id ,
                    'target' => $post_id , 
                    'type' => 'mention' ,
                     'time' => date("Y-m-d H:i:s") ,
                     'count' => '0' , 
                     'status' => '0'
                  ];
          
                  Tweet::create('notifications' , $data_notify);
                
            } 
            
        }
        // end notification
        //  add trends to database
        preg_match_all("/#+([a-zA-Z0-9_]+)/i", $status, $hashtag);
       
        if(!empty($hashtag) ){ 

            Tweet::addTrend($status);
        }
        // end add trend
       
        header('location: ../home.php');
    } else {
        $_SESSION['errors_tweet'] = $errors;
        header('location: ../home.php');
    }   
} else header('location: ../home.php');
$conn->close();
?>
