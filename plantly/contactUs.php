<?php
include "config.php";
$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

if (mysqli_connect_errno()) {
    header("Location: login.php");
    die("DB connection failed: " . mysqli_connect_error() . " (" . mysqli_connect_errno() . ")");
}
?>
<?php
session_start();

if (!isset($_SESSION["user_id"]) || empty($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
if (isset($_SESSION['success']) && $_SESSION['success'] == 2) {
    $dataUpdatedClass = 'data-updated-show';
} else {
    $dataUpdatedClass = 'data-updated-hide';
}

if (!empty($_GET["newUserName"])) {
    $new_userName = $_GET['newUserName'];
    $_SESSION["user_name"] = $_GET['newUserName'];
    $new_password = $_GET['newPassword'];
    $user_id = intval($_SESSION["user_id"]);
    $query = "UPDATE plantly_user
              SET name = '$new_userName', password = '$new_password'
              WHERE ID = $user_id;";

    $result = mysqli_query($connection, $query);
    if (!$result) {
        header("Location: login.php");
        die("DB query failed.");
    }
}

$farmer_id_penalty = $_SESSION["user_id"];
if ($_SESSION["user_type"] == "farmer") {
    $sql = "SELECT COUNT(*) AS penaltyCount FROM plantly_plant WHERE user_id = $farmer_id_penalty";
}
if ($_SESSION["user_type"] == "insp") {
    $sql = "SELECT COUNT(*) AS penaltyCount
FROM plantly_plant p
LEFT JOIN plantly_advices a ON p.case_id = a.case_id
WHERE a.advice IS NULL";
}
$result = mysqli_query($connection, $sql);
if (!$result) {
    header("Location: login.php");
    die("DB query failed.");
}

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $penaltyCount = $row["penaltyCount"];
} else {
    $penaltyCount = 0;
}


$sql = "SELECT plantly_advices.*, plantly_plant.plant_name 
        FROM plantly_advices 
        INNER JOIN plantly_plant ON plantly_advices.case_id = plantly_plant.case_id";

$result = mysqli_query($connection, $sql);

if (!$result) {
    header("Location: login.php");
    die("DB query failed.");
}

$advices = array();

while ($row = mysqli_fetch_assoc($result)) {
    $advice_date = $row["_data"];
    $advice = $row["advice"];
    $plant_name = $row["plant_name"];

    // Store each advice in the array
    $advices[] = array("date" => $advice_date, "advice" => $advice, "plant_name" => $plant_name);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["name"]) && isset($_POST["email"]) && isset($_POST["subject"]) && isset($_POST["message"])) {
        $name = $_POST["name"];
        $email = $_POST["email"];
        $subject = $_POST["subject"];
        $message = $_POST["message"];
        $user_id = $_SESSION["user_id"];

        // Prepare and execute the SQL query to insert the message into the contactUs table
        $query = "INSERT INTO plantly_contactUs (user_id, subject, messages) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($stmt, 'iss', $user_id, $subject, $message);
        $result = mysqli_stmt_execute($stmt);

        if ($result) {
            // Redirect to a success page or display a success message
            header("Location: index.php");
            exit;
        } else {
            // Handle the case where the insertion failed
            echo "Error: " . mysqli_error($connection);
        }
    } else {
        // Handle the case where form fields are missing
        echo "Error: Form fields are missing.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
    <link href="css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Assistant:wght@500&family=Passions+Conflict&display=swap');
    </style>
    <script defer="" src="js/scripts.js"></script>

    <title>Plantly</title>
</head>

<body class="wrapper">

    <header>
        <label id="userNameToShowSmall"> &nbsp; &nbsp;Hi,
            <?php echo $_SESSION["user_name"]; ?>
        </label>
        <div class="profilePic">
            <a href="#" id="editProfilePic">
                <img <?php echo 'src=' . $_SESSION['user_img'] . '' ?> alt="profile picture" title="profile picture">
            </a>
        </div>
        <div class="logo">
            <a href="index.php" class="logo-link" title="logo"></a>
        </div>
        <div class="navigatin">
            <nav class="navbar navbar-expand-lg bg-body-tertiary">
                <div class="container-fluid">
                    <!-- <a class="nav-link" href="#">Navbar</a> -->
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                            <li class="nav-item">
                                <a class="nav-link" aria-current="page" href="index.php">Home</a>
                            </li>
                            <li class="nav-item">
                                <?php if ($_SESSION["user_type"] == "farmer") {
                                    echo '<a class="nav-link" aria-current="page" href="newPlat.php">New Plant</a>';
                                } else {
                                    echo '<a class="nav-link" aria-current="page" href="#">Articles</a>';
                                }
                                ?>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="casesList.php">Cases
                                    <?php
                                    echo '<span class="penaltySum">(<span class="penaltySum" id="penalySumNum">' . $penaltyCount . '</span>)</span>';
                                    ?>
                                </a>
                            </li>
                            <li class="nav-item logOutToggle">
                                <a id="logout" href="login.php"><i class="fa fa-sign-out" aria-hidden="true"></i>Logout</a>
                            </li>
                        </ul>
                        <form class="d-flex " role="search">
                            <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                            <button class="btn btn-outline-success " type="submit">Search</button>
                        </form>
                    </div>
                </div>
            </nav>

        </div>

    </header>
    <main class="main">
        <div class="side-menu">
            <a href="#"><i class="fa fa-envelope-open-o" aria-hidden="true"></i></i><span data-bs-toggle="modal" data-bs-target="#messagesModal">Messages</span></a>
            <a href="article.php"><i class="fa fa-newspaper-o" aria-hidden="true"></i>Articles</a>
            <a href="#"><i class="fa fa-user-o" aria-hidden="true"></i>Profile</a>
            <section class="userTool"><a href="contactUs.php"><i class="fa fa-address-book-o" aria-hidden="true"></i>Contact us</a><br><a href="#"><i class="fa fa-cog" aria-hidden="true"></i><span data-bs-toggle="modal" data-bs-target="#editModalProfile">Settings</span></a><br><a id="logout" href="login.php"><i class="fa fa-sign-out" aria-hidden="true"></i>Logout</a></section>
            <label id="userNameToShow">Hi,
                <?php echo $_SESSION["user_name"]; ?>
            </label>
        </div>
        <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Contact Us</li><br>
            </ol>
        </nav>
        <!-- Contact Start -->
        <div class="container-fluid py-5 wow fadeInUp" data-wow-delay="0.1s">
            <div class="container">
                <div class="text-center mx-auto mb-5" style="max-width: 600px;">
                    <h5 class="text-primary text-uppercase" style="letter-spacing: 5px;">Contact Us</h5>
                    <h1 class="display-5 mb-0">Please Feel Free To Contact Us</h1>
                </div>
                <div class="row g-5">
                    <div class="col-lg-7 wow slideInUp" data-wow-delay="0.3s">
                        <div class="bg-light rounded p-5">
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                <div class="row g-3">
                                    <div class="col-6">
                                        <input type="text" class="form-control border-0 px-4" placeholder="Your Name" name="name" style="height: 55px;" required>
                                    </div>
                                    <div class="col-6">
                                        <input type="email" class="form-control border-0 px-4" placeholder="Your Email" name="email" style="height: 55px;" required>
                                    </div>
                                    <div class="col-12">
                                        <input type="text" class="form-control border-0 px-4" placeholder="Subject" name="subject" style="height: 55px;" required>
                                    </div>
                                    <div class="col-12">
                                        <textarea class="form-control border-0 px-4 py-3" rows="8" name="message" placeholder="Message"></textarea required>
                                    </div>
                                    <div class="col-12">
                                        <button class="btn btn-primary w-100 py-3" type="submit">Send Message</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-5 wow slideInUp" data-wow-delay="0.6s">
                        <div class="bg-light rounded p-5">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-geo-alt fs-1 text-primary me-3"></i>
                                <div class="text-start">
                                    <h5 class="mb-1">Our Office</h5>
                                    <span>Anna Frank Street 12, Ramat Gan, Israel</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-envelope-open fs-1 text-primary me-3"></i>
                                <div class="text-start">
                                    <h5 class="mb-1">Email Us</h5>
                                    <span>plantly@gmail.com.com</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-4">
                                <i class="bi bi-phone-vibrate fs-1 text-primary me-3"></i>
                                <div class="text-start">
                                    <h5 class="mb-1">Call Us</h5>
                                    <span>+975 52608 4795</span>
                                </div>
                            </div>
                            <div>
                                <iframe class="position-relative w-100" <iframe
                                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2528.4671802563675!2d34.81647341582557!3d32.08215342730244!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1502a5d999919cab%3A0x8d1efb36a5db07a2!2sAnna%20Frank%20St%2012%2C%20Ramat%20Gan%2C%20Israel!5e0!3m2!1sen!2sus!4v1646179307812!5m2!1sen!2sus"
                                    allowfullscreen="" loading="lazy"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editModalProfile" tabindex="-1" aria-labelledby="editModalProfile" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editModalProfile">Edit Profile</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form action="#" method="GET" id="frm">

              <div class="form-outline mb-4">
                <label class="form-label">User Name:</label>
                <input type="text" class="form-control" name="newUserName" placeholder="name" required>
              </div>

              <div class="form-outline mb-4">
                <label class="form-label">Password:</label>
                <input type="password" class="form-control" name="newPassword" placeholder="password" required>
              </div>
              <div class="modal-footer">
                <button type="button" id="ModalBtnN" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" id="ModalBtnY" class="btn btn-primary">Save changes</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="messagesModal" tabindex="-1" aria-labelledby="messagesModal" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="messagesModal">Expert Advices</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <?php
                if (!empty($advices)) {
                    foreach ($advices as $advice) {
                        echo "<p><span style='font-weight: bold; color: blue;'>Date: " . $advice['date'] . "</span> | <span style='color: green;'>Plant Name: " . $advice['plant_name'] . "</span> | " . $advice['advice'] . "</p>";
                    }
                } else {
                    echo "<p>No advices found for this case.</p>";
                }
                ?>
              </div>
            </div>
          </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="lib/wow/wow.min.js"></script>
        <script src="lib/easing/easing.min.js"></script>
        <script src="lib/waypoints/waypoints.min.js"></script>
        <script src="lib/owlcarousel/owl.carousel.min.js"></script>

        </main>
</body>

</html>