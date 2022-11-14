

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<?php
include '../init.php';

    $result = User::search("_");
         
    if(!empty($result)) {

        echo ' <div class="nav-right-down-wrap">
        <ul> ';
    foreach ($result as $user) {
        echo ' <li >
        <div class="nav-right-down-inner">
        <div class="nav-right-down-left">
            <a class="" href="'.BASE_URL.$user->username.'"><img class="mt-2 ml-1" height="64px" width="64px" src="/assets/images/users/'.$user->img.'"></a>
        </div>
        <div class="nav-right-down-right">
            <div class="nav-right-down-right-headline">
            <a class="" href="'.BASE_URL.$user->username.'">'. $user->name.'</a>
            <span class="d-block">@'. $user->username.'</span>
            </div>
            
        </div>
        </div> 
        </li> ';
    }

    echo ' </ul>
    </div> ';
}

?>
