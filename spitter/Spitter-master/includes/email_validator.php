<?php $email  = $_POST['email'];
$emailB = filter_var($email, FILTER_SANITIZE_EMAIL);

if (filter_var($emailB, FILTER_VALIDATE_EMAIL) === false ||
    $emailB != $email
) {
    echo "This email adress isn't valid, or you have not inputted one.";
    exit(0);
}
?>
