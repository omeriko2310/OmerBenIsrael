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
    header("Location: index.php");
    exit;
}
if ($_SESSION["user_type"] != 'farmer') {
    header("Location: index.php");
}
$_SESSION['success'] =0;
if (!empty($_GET["newUserName"])) {
    $new_userName = $_GET['newUserName'];
    $_SESSION["user_name"] = $_GET['newUserName'];
    $new_password = $_GET['newPassword'];
    $user_id = intval($_SESSION["user_id"]);
    $query = "UPDATE tbl_229_users
                SET name = '$new_userName', password = '$new_password'
                WHERE ID = $user_id;";
    
    $result = mysqli_query($connection, $query);
    if (!$result) {
        header("Location: login.php");
        die("DB query failed.");
    }
}
$farmer_id_penalty = $_SESSION["user_id"];
$sql = "SELECT COUNT(*) AS penaltyCount FROM plantly_plant WHERE user_id = $farmer_id_penalty";
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
if (!empty($_POST["plantName"])) {
    $plant_name = $_POST["plantName"];
    $Urgency = $_POST["Urgency"];
    $_Type= $_POST["Type"];
    $summary = $_POST["description"];
    $user_id = intval($_SESSION["user_id"]);

    if (isset($_FILES['userImage'])) {
        $errors = array();
        $file_name = $_FILES['userImage']['name'];
        $file_size = $_FILES['userImage']['size'];
        $file_tmp = $_FILES['userImage']['tmp_name'];
        $file_type = $_FILES['userImage']['type'];
        $capturedImagePath = $_POST["capturedImagePath"];

        $file_parts = explode('.', $_FILES['userImage']['name']);
        $file_ext = strtolower(end($file_parts));
        $extensions = array("jpeg", "jpg", "png","webp");

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

    $flag = $_POST["flag"];
    if( $flag=="1"){
        $file_destination = $capturedImagePath;
    }

    $query = "INSERT INTO plantly_plant (user_id, urgency, summary , _type, _date , plant_name,pic_path)
          VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP(), ?, ?)";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, 'isssss', $user_id, $Urgency, $summary, $_Type, $plant_name, $file_destination);

    // Execute the statement
    $result = mysqli_stmt_execute($stmt);
    // $result = mysqli_query($connection, $query);
    if (!$result) {
        header("Location: login.php");
        die("DB query failed.");
    }
    header("Location: index.php");
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

if ($_SESSION["user_type"] == 'farmer') {
    $query = "SELECT MAX(case_id) AS lastCaseId FROM plantly_plant WHERE user_id = '" . $_SESSION["user_id"] . "'";
    $result = mysqli_query($connection, $query);
    if (!$result) {
        header("Location: login.php");
        die("DB query failed.");
    }
    $row = mysqli_fetch_assoc($result);
    $lastCaseId = intval($row['lastCaseId']);
    $defaultCaseNumber = $lastCaseId + 1;
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4"></script>
    <title>Plantly- New Plant</title>
</head>

<body class="wrapper">
    <header>
         <label id="userNameToShowSmall">  &nbsp; &nbsp;Hi, <?php echo $_SESSION["user_name"]; ?></label>
        <div class="logo">
            <a href="index.php" class="logo-link" alt="logo" title="logo"></a>
        </div>
        <div class="navigatin">
            <nav class="navbar navbar-expand-lg bg-body-tertiary">
                <div class="container-fluid">
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
                                    echo '<a class="nav-link selectedNav" aria-current="page" href="#">New Plant</a>';
                                } else {
                                    echo '<a class="nav-link" aria-current="page" href="#">History</a>';
                                }
                                ?>
                            </li>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="casesList.php">Cases<?php if ($_SESSION["user_type"] == "farmer") {
                                                                                        echo '<span class="penaltySum">(<span class="penaltySum" id="penalySumNum">' . $penaltyCount . '</span>)</span>';
                                                                                    } ?></a>
                            </li>
                            <li class="nav-item">
                                <?php if ($_SESSION["user_type"] == "insp") {
                                    echo '<a class="nav-link" aria-current="page" href="allAdvices.php">All Advices</a>';
                                } ?>
                            </li>
                            <li class="nav-item logOutToggle">
                                <a id="logout" href="login.php"><i class="fa fa-sign-out" aria-hidden="true"></i>Logout</a>
                            </li>
                        </ul>
                        <form class="d-flex" role="search">
                            <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                            <button class="btn btn-outline-success" type="submit">Search</button>
                        </form>
                    </div>
                </div>
            </nav>
        </div>
        <div class="profilePic">
            <a href="#" id="editProfilePic">
                <img <?php echo 'src=' . $_SESSION['user_img'] . '' ?> alt="profile picture" title="profile picture">
            </a>
        </div>
    </header>
    <main>
        <div class="side-menu">
            <a href="#"><i class="fa fa-envelope-open-o" aria-hidden="true"></i></i><span data-bs-toggle="modal" data-bs-target="#messagesModal">Messages</span></a>
            <a href="article.php"><i class="fa fa-newspaper-o" aria-hidden="true"></i>Articles</a>
            <a href="#"><i class="fa fa-user-o" aria-hidden="true"></i>Profile</a>
            <section class="userTool"><a href="contactUs.php"><i class="fa fa-address-book-o" aria-hidden="true"></i>Contact us</a><br><a href="#"><i class="fa fa-cog" aria-hidden="true"></i><span data-bs-toggle="modal" data-bs-target="#editModalProfile">Settings</span></a><br><a id="logout" href="login.php"><i class="fa fa-sign-out" aria-hidden="true"></i>Logout</a></section>
            <label id="userNameToShow">Hi, <?php echo $_SESSION["user_name"]; ?></label>
        </div>
        <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">New Case</li>
            </ol>
        </nav>
        <div class="newPenalty">
            <div class="newPenaltyH2">New Plant
                <p class="newPlotP">Please fill out the form below to register a new Plant</p>
            </div>
            <form action="#" method="post" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-sm-4">
                        <label class="form-label">Plant Name</label>
                        <input type="text" class="form-control input-field" id="plotName" name="plantName" placeholder="name">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Urgency</label>
                        <select class="form-control input-field" id="Urgency" name="Urgency" required>
                            <option value="" disabled selected>Select Urgency</option>
                            <option value="High">High</option>
                            <option value="Medium">Medium</option>
                            <option value="Low">Low</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <label class="form-label">Irrigation frequency- Per week</label>
                        <input type="number" class="form-control input-field" min="1" id="Irrigation" name="Irrigation" placeholder="0" required>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Plant Type</label>
                        <input type="text" class="form-control input-field" id="Type" name="Type"  placeholder="type" required>
                    </div>
                </div>
                <div class="row picture">
                    <div class="col-sm-6">
                        <label class="form-label" >Plant Image:</label>
                        <input type="file" class="form-control" name="userImage" id="userImage">
                    </div>
                    <div class="col-sm-6 take_pic">
                    <button type="button" class="btn btn-primary" id="take-picture">Take Picture</button>
                    <input type="hidden" name="capturedImagePath" id="capturedImagePath" value="">
                    <input type="hidden" name="flag" id="flag" value="0">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Describe</label>
                    <textarea class="form-control" id="description" name="description" placeholder="More details..."></textarea>
                </div>
                <div class="newPenaltyButton">
                    <div id="BACK" class="col-md-6">
                        <button type="button" class="btn btn-primary" onclick="window.location.href='crop.php'">BACK</button>
                    </div>
                    <div id="SUBMIT" class="col-6 ms-auto">
                        <input type="hidden" class="form-control" id="caseNumberInput" name="caseNumber" placeholder="Case Number" value="<?php echo $defaultCaseNumber; ?>">
                        <button type="submit" id="SUBMITbtn" class="btn btn-primary">SUBMIT</button>
                    </div>
                </div>
            </form>
        </div>
    </main>
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
    <script>
        $(document).ready(function() {
            $('#editProfilePic').on('click', function(event) {
                event.preventDefault(); 
                $('#editModalProfile').modal('show'); 
            });
        });
    </script>
</body>

</html>