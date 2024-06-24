<?php
include 'config.php';
session_start();
if (!empty($_SESSION["user_id"])) {
    session_destroy();
}

if (!empty($_POST["loginMail"])) {
    $query = "SELECT * FROM plantly_user 
        WHERE email='" . $_POST["loginMail"]
        . "'and password = '"
        . $_POST["loginPass"] . "'";

    $result = mysqli_query($connection, $query);
    $row = mysqli_fetch_array($result);

    if (is_array($row)) {
        $_SESSION["user_id"] = $row['ID'];
        $_SESSION["user_type"] = $row['user_type'];
        $_SESSION["user_img"] = $row['profile_url'];
        $_SESSION["user_name"] = $row['name'];
        $_SESSION["login"] = true;
        header('Location: index.php');
    } else {
        $message = 'invail username or password';
    }
}
?>
<?php
if (!empty($_POST["userEmail"])) {
    $user_name = $_POST["userName"];
    $user_email = $_POST["userEmail"];
    $user_password = $_POST["userPassword"];
    $user_type = $_POST["userType"];
    echo $user_email;
    if (isset($_FILES['userImage'])) {
        $errors = array();
        $file_name = $_FILES['userImage']['name'];
        $file_size = $_FILES['userImage']['size'];
        $file_tmp = $_FILES['userImage']['tmp_name'];
        $file_type = $_FILES['userImage']['type'];

        $file_parts = explode('.', $_FILES['userImage']['name']);
        $file_ext = strtolower(end($file_parts));
        $extensions = array("jpeg", "jpg", "png","");

        if (in_array($file_ext, $extensions) === false) {
            $errors[] = "extension not allowed, please choose a JPEG or PNG file.";
        }

        if ($file_size > 2097152) {
            $errors[] = 'File size must be less than 2 MB';
        }

        if (empty($errors) == true) {
            $file_name_new = uniqid('', true) . '.' . $file_ext;
            $file_destination = 'images/' . $file_name_new;

            move_uploaded_file($file_tmp, $file_destination);
        } else {
            $file_destination = 'images/defaultProfilePic.jpg';
            move_uploaded_file($file_tmp,'images/defaultProfilePic.jpg');
            print_r($errors);
        }
    }
    $query = "SELECT * FROM plantly_user WHERE email = '$user_email'";
    $result = mysqli_query($connection, $query);
    if (!$result) {
        header("Location: login.php");
        die("DB query failed.");
    }
    if (mysqli_num_rows($result) > 0) {
        $error_message = "*Email already taken.";
    }
    else{
        $query = "INSERT INTO plantly_user (`name`, email, `password`, user_type, profile_url)
        VALUES ('$user_name','$user_email', '$user_password', '$user_type', '$file_destination')";

        $result = mysqli_query($connection, $query);
        if (!$result) {
            header("Location: login.php");
            die("DB query failed.");
        }
        header("Location: index.php");
    }
}

if (!empty($_GET["cropId"])) {
    $plot_id = $_GET["cropId"];
    $query = "SELECT * FROM tbl_229 WHERE plot_id = '$plot_id'";
    $result = mysqli_query($connection, $query);
    $row = mysqli_fetch_assoc($result);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <title>Plantly- Login Page</title>
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
        integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
        crossorigin="anonymous"></script>
    <link href="css/style.css" rel="stylesheet">
</head>

<body class="logIn_page">
    <div class="modal fade" id="removeModal" tabindex="-1" aria-labelledby="removeModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registerModalLabel">Sign Up</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="#" method="POST" id="signup_form" enctype="multipart/form-data">
                        <div class="form-outline mb-4">
                            <label class="form-label" >Name:</label>
                            <input type="text" class="form-control input-field" name="userName" id="userName"
                                placeholder="enter your name" required>
                        </div>

                        <div class="form-outline mb-4">
                            <label class="form-label" >Email address:</label>
                            <input type="email" class="form-control input-field" name="userEmail" id="userEmail"
                                placeholder="enter your email" required>
                        </div>

                        <div class="form-outline mb-4">
                            <label class="form-label" >Password:</label>
                            <input type="password" class="form-control input-field" name="userPassword"
                                id="userPassword" placeholder="enter your password" required>
                        </div>

                        <div class="form-outline mb-4">
                            <label class="form-label" >User Type:</label>
                            <select class="form-control input-field" name="userType" >
                                <option value="insp">Inspector</option>
                                <option value="farmer">Farmer</option>
                            </select>
                        </div>

                        <div class="form-outline mb-4">
                            <label class="form-label" >Profile Image:</label>
                            <input type="file" class="form-control" name="userImage" id="userImage">
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Register</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="logoLogIn">
        <a href="login.php" id="loginLogo" class="logo-link"  title="logo"></a>
    </div>
    <div class="logIn_form">
        <h1 class="mb-4">Login</h1>
        <form action="#" method="post" id="frm">
            <div class="form-outline mb-4">
            <label class="form-label" >Email address: <span id="emailError" style="display: <?php echo isset($error_message) ? 'block' : 'none'; ?>;"><?php echo isset($error_message) ? $error_message : ''; ?></span></label>                <input type="email" class="form-control" name="loginMail" id="loginMail" placeholder="email" required>
            </div>
            <div class="form-outline mb-4">
                <label class="form-label" >Password:</label>
                <input type="password" class="form-control" name="loginPass" id="loginPass" placeholder="password"
                    required>
            </div>

            <button type="submit" id="loginBtn1" class="btn btn-primary btn-block mb-4">Sign in</button>

            <div class="text-center">
                <p>Not a member? <a href="#" data-bs-toggle="modal" data-bs-target="#removeModal">Register</a></p>
            </div>
        </form>
    </div>
</body>

</html>