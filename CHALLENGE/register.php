<?php
session_start();

if (isset($_SESSION['user'])) {
    header("Location: home.php");
    exit;
}

if (isset($_SESSION['adm'])) {
    header("Location: dashboard.php");
    exit;
}
require_once 'components/db_connect.php';
require_once 'components/file_upload.php';

$error = false;

$firstname = $lastname = $email = $pass;
$fnameError = $lnameError = $emailError = $passError;


if (isset($_POST['btn-signup'])) {
    $firstname = trim($_POST['firstname']);
    $firstname = strip_tags($firstname);
    $firstname = htmlspecialchars($firstname);

    $lastname = trim($_POST['lastname']);
    $lastname = strip_tags($lastname);
    $lastname = htmlspecialchars($lastname);

    $email = trim($_POST['email']);
    $email = strip_tags($email);
    $email = htmlspecialchars($email);

    $pass = trim($_POST['pass']);
    $pass = strip_tags($pass);
    $pass = htmlspecialchars($pass);

    $uploadError = "";


    if (empty($firstname) || empty($lastname)) {
        $error = true;
        $fnameError = "Please enter your name and surname";
    } elseif (strlen($firstname) < 3 || strlen($lastname) < 3) {
        $error = true;
        $fnameError = "Name and surname must have at least 3 characters";
    } elseif (!preg_match("/^[a-zA-z]+$/", $firstname) || !preg_match("/^[a-zA-z]+$/", $lastname)) {
        $error = true;
        $fname = "Name and surname must contain only letters and no spaces";
    }

    if (empty($phone)) {
        $error = true;
        $phoneError = "Please enter your phone number";
    } elseif (strlen($phone) < 6) {
        $error = true;
        $phoneError = "Please enter a valid phone number";
    }

    if (empty($email)) {
        $error = true;
        $emailError = "Please enter your email";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = true;
        $emailError = "Please enter valid email address";
    } else {
        $query = "SELECT email FROM users WHERE email='$email'";
        $result = mysqli_query($connect, $query);
        $count = mysqli_num_rows($result);
        if ($count != 0) {
            $error = true;
            $emailError = "Provided email already exists";
        }
    }

    if (empty($pass)) {
        $error = true;
        $passError = "Please enter the password";
    } elseif (strlen($pass) < 6) {
        $error = true;
        $passError = "Password must have at least 6 characters";
    }

    $password = hash('sha256', $pass);

    if (!$error) {
        $query = "INSERT INTO users(firstname, lastname, password, email, phone, picture) 
        VALUES ('$firstname', '$lastname','$password','$email', '$phone', '$picture->fileName')";

        $res = mysqli_query($connect, $query);

        if ($res) {
            $errTyp = "success";
            $errMSG = "Successfully registered, you may login now";
            $uploadError = ($picture->error != 0) ? $picture->ErrorMessage : '';
        } else {
            $errTyp = "danger";
            $errMSG = "Something went wrong, try again later...";
            $uploadError = ($picture->error != 0) ? $picture->ErrorMessage : '';
        }
    }
}
mysqli_close($connect);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login and Registration System</title>
    <?php require_once 'components/boot.php' ?>
</head>

<body>
    <div class="container py-5 h-100">
        <div class="row justify-content-center align-items-center h-100">
            <div class="col-12 col-lg-9 col-xl-7">
                <div class="card shadow-2-strong card-registration" style="border-radius: 15px;">
                    <div class="card-body p-4 p-md-5">
                        <h3 class="mb-4 pb-2 pb-md-0 mb-md-5">Registration Form</h3>
                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" autocomplete="off" enctype="multipart/form-data">
                            <?php
                            if (isset($errMSG)) {
                            ?>
                                <div class="alert alert-<?php echo $errTyp ?>">
                                    <p><?php echo $errMSG; ?></p>
                                    <p><?php echo $uploadError; ?></p>
                                </div>
                            <?php
                            }
                            ?>
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div class="form-outline">
                                        <input type="text" name="firstname" class="form-control" placeholder="First name" maxlength="50" value="<?php echo $firstname ?>" />
                                        <span class="text-danger"> <?php echo $fnameError; ?> </span>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="form-outline">
                                        <input type="text" name="lastname" class="form-control" placeholder="Surname" maxlength="50" value="<?php echo $lastname ?>" />
                                        <span class="text-danger"> <?php echo $fnameError; ?> </span>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div class="form-outline">
                                        <input type="email" name="email" class="form-control" placeholder="Enter Your Email" maxlength="40" value="<?php echo $email ?>" />
                                        <span class="text-danger"> <?php echo $emailError; ?> </span>
                                    </div> 
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="form-outline">
                                        <input type="password" name="pass" class="form-control" placeholder="Enter Password" maxlength="15" />
                                        <span class="text-danger"> <?php echo $passError; ?> </span>
                                    </div>
                                </div>
                            </div>

                            </div>
                            </div>
                            <div class="d-flex justify-content-center">
                                <button type="submit" class="btn btn-primary btn-lg btn-block" style="width:100%" name="btn-signup">Register</button>
                            </div>
                            <p class="text-center text-muted mt-5 mb-0">Have already an account? <a href="index.php" class="fw-bold text-body"><u>Login here</u></a></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>