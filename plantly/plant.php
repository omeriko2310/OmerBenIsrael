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
if (empty($_GET["caseId"])) {
  header("Location: index.php");
}
$_SESSION['success'] = 0;
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
if (!empty($_GET["plantName"])) {
  $plant_name = $_GET['plantName'];
  // $plot_size = $_GET['plotSize'];
  $case_id = $_GET['caseId'];
  $query = "UPDATE plantly_plant
              SET plant_name = '$plant_name'
              WHERE case_id = $case_id;";

  $result = mysqli_query($connection, $query);
  if (!$result) {
    header("Location: login.php");
    die("DB query failed.");
  }
  $_SESSION['success'] = 1;
}
if (!empty($_GET["selectYes"])) {
  $case_id = $_GET['caseId'];
  $query = "DELETE FROM plantly_plant
              WHERE case_id = $case_id;";

  $result = mysqli_query($connection, $query);
  if (!$result) {
    header("Location: login.php");
    die("DB query failed.");
  }
  $_SESSION['success'] = 2;
  header("Location: index.php");
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
if (!empty($_GET["caseId"])) {
  $caseId = $_GET["caseId"];
  $sql = "SELECT * FROM plantly_advices WHERE case_id = $caseId";
  $result = mysqli_query($connection, $sql);
}
if (!$result) {
  header("Location: login.php");
  die("DB query failed.");
}
if ($result) {
  $advices = array(); 
  while ($row = mysqli_fetch_assoc($result)) {
    $advice_date = $row["_data"]; 
    $advice = $row["advice"];
    // Store each advice in the array
    $advices[] = array("date" => $advice_date, "advice" => $advice);
  }
}

$sql = "SELECT plantly_advices.*, plantly_plant.plant_name 
        FROM plantly_advices 
        INNER JOIN plantly_plant ON plantly_advices.case_id = plantly_plant.case_id";

$result = mysqli_query($connection, $sql);

if (!$result) {
    header("Location: login.php");
    die("DB query failed.");
}

$_advices = array();

while ($row = mysqli_fetch_assoc($result)) {
    $_advice_date = $row["_data"];
    $_advice = $row["advice"];
    $_plant_name = $row["plant_name"];

    // Store each advice in the array
    $_advices[] = array("date" => $_advice_date, "advice" => $_advice, "plant_name" => $_plant_name);
}

if (!empty($_GET["caseId"])) {
  $case_id = $_GET["caseId"];
  $query = "SELECT * FROM plantly_plant WHERE case_id = '$case_id'";
  $result = mysqli_query($connection, $query);
  $row = mysqli_fetch_assoc($result);
}
if (isset($_SESSION['success']) && $_SESSION['success'] == 1) {
  $dataUpdatedClass = 'data-updated-show';
} else {
  $dataUpdatedClass = 'data-updated-hide';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
    integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n"
    crossorigin="anonymous"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
    crossorigin="anonymous"></script>
  <link href="css/style.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Assistant:wght@500&family=Passions+Conflict&display=swap');
  </style>
  <script defer="" src="js/scriptsC.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4"></script>
  <title>Agritech</title>
</head>

<body class="wrapper">
  <header>
    <label id="userNameToShowSmall"> &nbsp; &nbsp;Hi,
      <?php echo $_SESSION["user_name"]; ?>
    </label>
    <div class="logo">
      <a href="index.php" class="logo-link" title="logo"></a>
    </div>
    <div class="navigatin">
      <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
            aria-label="Toggle navigation">
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
                  echo '<a class="nav-link" aria-current="page" href="#">History</a>';
                }
                ?>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="casesList.php">Cases
                  <?php if ($_SESSION["user_type"] == "farmer") {
                    echo '<span class="penaltySum">(<span class="penaltySum" id="penalySumNum">' . $penaltyCount . '</span>)</span>';
                  } ?>
                </a>
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
    <div class="side-menu" id="side-meneP">
      <a href="#"><i class="fa fa-envelope-open-o" aria-hidden="true"></i><span data-bs-toggle="modal" data-bs-target="#messagesModalAll">Messages</span</a>
      <a href="article.php"><i class="fa fa-newspaper-o" aria-hidden="true"></i>Articles</a>
      <a href="#"><i class="fa fa-user-o" aria-hidden="true"></i>Profile</a>
      <section class="userTool"><a href="contactUs.php"><i class="fa fa-address-book-o" aria-hidden="true"></i>Contact us</a><br><a
          href="#"><i class="fa fa-cog" aria-hidden="true"></i><span data-bs-toggle="modal" data-bs-target="#editModalProfile">Settings</span></a><br><a id="logout" href="login.php"><i
            class="fa fa-sign-out" aria-hidden="true"></i>Logout</a></section>
      <label id="userNameToShow">Hi,
        <?php echo $_SESSION["user_name"]; ?>
      </label>
    </div>
    <nav
      style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);"
      aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Plant</li><br>
      </ol>
    </nav>
    <div class="upper-main row ">
      <div class="col-12 col-sm-6 col-md-3">
        <a id="newPicBtn" href="#" class="btn btnCrop btn-primary btn-lg active upperGraphButtons" role="button"
          aria-pressed="true">NEW PICTURE</a>
      </div>
      <div class="col-12 col-sm-6 col-md-3">
        <a id="newMeasue" href="#" class="btn btnCrop btn-primary btn-lg active upperGraphButtons" role="button"
          aria-pressed="true">NEW MEASURE</a>
      </div>
      <div class="col-12 col-sm-6 col-md-3">
        <?php if ($_SESSION["user_type"] == "farmer") {
          echo '<a id="btn3" href="newPlat.php? caseId= ' . $row["case_id"] . ' " class="btn btnCrop btn-primary btn-lg active upperGraphButtons" role="button" aria-pressed="true" >NEW CASE</a>';
        } else {
          echo '<a id="btn3" href="newPanelty.php?caseId= ' . $row["case_id"] . '" class="btn btnCrop btn-primary btn-lg active upperGraphButtons" role="button" aria-pressed="true" >NEW PENALTY</a>';
        } ?>
      </div>
      <div class="col-12 col-sm-6 col-md-3">
        <a id="btn4" href="#" class="btn btnCrop btn-primary btn-lg active upperGraphButtons" role="button"
          aria-pressed="true" data-bs-toggle="modal" data-bs-target="#messagesModal">ADVICES</a>
      </div>
    </div>


    <div class="my-chart">
      <h2>Total Use</h2>
      <h2 class="responsive">Plot Name:<span>
          <?php echo $row["plant_name"] ?>
        </span></h2>
      <!-- <h2 class="responsive">Pest Size: <span><?php echo $row["plot_size"] ?></span></h2> -->
      <section>
        <!-- Modal -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModal" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="editModal">Edit Plant</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <form action="#" method="GET" id="frm1">

                  <div class="form-outline mb-4">
                    <label class="form-label">Plant Name:</label>
                    <input type="text" class="form-control" name="plantName" placeholder="name" required>
                  </div>
                  <input type="text" name="caseId" <?php echo 'value="' . $row["case_id"] . '"'; ?> hidden>
                  <div class="modal-footer">
                    <button type="button" id="ModalBtnN" class="btn btn-secondary"
                      data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="ModalBtnY" class="btn btn-primary">Save changes</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>

        <div class="modal fade" id="removeModal" tabindex="-1" aria-labelledby="removeModal" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="removeModal">Remove Plot</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <form method="GET" id="frm2">
                  <div class="form-outline mb-4">
                    <label class="form-label">Are You Sure You Want To Delete This Plot?</label>
                  </div>
                  <input type="text" name="caseId" <?php echo 'value="' . $row["case_id"] . '"'; ?> hidden>
                  <input type="text" name="selectYes" value="1" hidden>
                  <div class="modal-footer">
                    <button type="button" id="ModalBtnN" class="btn btn-secondary"
                      data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="ModalBtnY" class="btn btn-primary">Save changes</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>

        <div class="modal fade" id="editModalProfile" tabindex="-1" aria-labelledby="editModalProfile"
          aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="editModalProfile">Edit Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <form action="#" method="GET" id="frm3">

                  <div class="form-outline mb-4">
                    <label class="form-label">User Name:</label>
                    <input type="text" class="form-control" name="newUserName" placeholder="name" required>
                  </div>

                  <div class="form-outline mb-4">
                    <label class="form-label">Password:</label>
                    <input type="password" class="form-control" name="newPassword" placeholder="password" required>
                  </div>
                  <input type="text" name="caseId" <?php echo 'value="' . $row["case_id"] . '"'; ?> hidden>
                  <div class="modal-footer">
                    <button type="button" id="ModalBtnN" class="btn btn-secondary"
                      data-bs-dismiss="modal">Close</button>
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
                    echo "<p><span style='font-weight: bold; color: blue;'>Date: " . $advice['date'] . "</span> | " . $advice['advice'] . "</p>";
                  }
                } else {
                  echo "<p>No advices found for this case.</p>";
                }
                ?>
              </div>
            </div>
          </div>
        </div>

        <div class="modal fade" id="messagesModalAll" tabindex="-1" aria-labelledby="messagesModalAll" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="messagesModalAll">Expert Advices</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <?php
                if (!empty($_advices)) {
                  foreach ($_advices as $_advice) {
                    echo "<p><span style='font-weight: bold; color: blue;'>Date: " . $_advice['date'] . "</span> | <span style='color: green;'>Plant Name: " . $_advice['plant_name'] . "</span> | " . $_advice['advice'] . "</p>";
                  }
                } else {
                  echo "<p>No advices found for this case.</p>";
                }
                ?>
              </div>
            </div>
          </div>
        </div>
      </section>
      <section id="dataUpdatedP" class="<?php echo $dataUpdatedClass; ?>">Data updated successfully!!</section>
      <div class="dropdown">
        <?php if ($_SESSION["user_type"] == "farmer") {
          echo '
                <button type="button" class="btn modalBtn btn-primary" data-bs-toggle="modal" data-bs-target="#removeModal">
                <i class="fa fa-trash-o" aria-hidden="true"></i>
                </button>
                <button type="button" class="btn modalBtn btn-primary" data-bs-toggle="modal" data-bs-target="#editModal">
                <i class="fa fa-pencil" aria-hidden="true"></i>
                </button>';
        }
        ?>
        <i id="downloadBtn" class="fa fa-download btn" aria-hidden="true"></i>
      </div>
      <input type="hidden" id="plantCaseId" <?php echo 'value="' . $case_id . '"'; ?>>
      <canvas id="myChart" class=""></canvas>
      <canvas id="myChart2" class="hide-chart2"></canvas>
      <canvas id="myChart3" class="hide-chart"></canvas>
      <section>
        <?php echo '<img src="' . $row['pic_path'] . '" class="card-img-top" id="plantPicM" alt="Inspector Image" title="Inspector Image">'; ?>
      </section>
  </main>
</body>

</html>