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

if (!empty($_GET['category'])) {
  $cat = $_GET['category'];
} else {
  $cat = 'All';
}
if ($_SESSION["user_type"] == 'insp') {
  $query = "SELECT * FROM plantly_plant";
}
if ($_SESSION["user_type"] == 'farmer') {
  $query = "SELECT * FROM plantly_plant WHERE user_id = '" . $_SESSION["user_id"] . "'";
}
if ($cat != 'All') {
  if ($cat == 'avg') {
    $query .= " ORDER BY CAST($cat AS DECIMAL)";
  } else {
    $query .= " ORDER by $cat";
  }
}
$result = mysqli_query($connection, $query);
if (!$result) {
  header("Location: login.php");
  die("DB query failed.");
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
                <a class="nav-link selectedNav" aria-current="page" href="#">Home</a>
              </li>
              <li class="nav-item">
                <?php if ($_SESSION["user_type"] == "farmer") {
                  echo '<a class="nav-link" aria-current="page" href="newPlat.php">New Plant</a>';
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
      <a href="#"><i class="fa fa-envelope-open-o" aria-hidden="true"></i><span data-bs-toggle="modal" data-bs-target="#messagesModal">Messages</span></a>
      <a href="article.php"><i class="fa fa-newspaper-o" aria-hidden="true"></i>Articles</a>
      <a href="#"><i class="fa fa-user-o" aria-hidden="true"></i>Profile</a>
      <section class="userTool"><a href="contactUs.php"><i class="fa fa-address-book-o" aria-hidden="true"></i>Contact
          us</a><br><a href="#"><i class="fa fa-cog" aria-hidden="true"></i><span data-bs-toggle="modal" data-bs-target="#editModalProfile">Settings</span></a><br><a id="logout" href="login.php"><i class="fa fa-sign-out" aria-hidden="true"></i>Logout</a></section>
      <label id="userNameToShow">Hi,
        <?php echo $_SESSION["user_name"]; ?>
      </label>
    </div>
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
      </ol>
    </nav>
    <section id="dataRemoveP" class="<?php echo $dataUpdatedClass; ?>">Data removed successfully!!</section>
    <table class="table" id="myTable">
      <thead>
        <tr>
          <th scope="col"></th>
          <th scope="col">Date <button class="sort-button btn" data-column="_date"><i class="fa fa-sort-amount-asc" aria-hidden="true"></i></button></th>
          <th scope="col" id="colName">Name <button class="sort-button btn" data-column="plant_name"><i class="fa fa-sort-amount-asc" aria-hidden="true"></i></button></th>
          <!-- <th class="responsive-cols" scope="col" id="colAvg">AVG <button class="sort-button btn" data-column="avg" title="This column represents the percentage of water consumption in relation to plots with similar characteristics"><i class="fa fa-sort-amount-asc " aria-hidden="true"></i></button></th> -->
          <th class="responsive-cols" scope="col" id="colLevel">Level <button class="sort-button btn" data-column="urgency"><i class="fa fa-sort-amount-asc" aria-hidden="true"></i></button></th>
          <th class="responsive-cols" scope="col" id="colType">Plant Type<button class="sort-button btn" data-column="_type"><i class="fa fa-sort-amount-asc" aria-hidden="true"></i></button> </th>
          <th scope="col">Summary</th>
          <th scope="col">
          </th>
        </tr>
      </thead>
      <tbody>
        <?php
        while ($row = mysqli_fetch_assoc($result)) {
          echo '<tr class="' . $row['urgency'] . '">';
          echo '<td>&nbsp;     &nbsp;</td>';
          echo '<td>' . $row['_date'] . '</td>';
          echo '<td>' . $row['plant_name'] . '</td>';
          // echo '<td class="responsive-cols">' . $row['AVG'] . '</td>';
          echo '<td class="responsive-cols">' . $row['urgency'] . '</td>';
          echo '<td class="responsive-cols">' . $row['_type'] . '</td>';
          echo '<td>' . $row['summary'] . '</td>';
          if ($_SESSION["user_type"] == "farmer") {
            echo '<td><a href="plant.php?caseId=' . $row['case_id'] . '" class="btn btn-link Details">Details</a></td>';
          } else if ($_SESSION["user_type"] == "insp") {
            echo '<td><a href="ExpertPlant.php?caseId=' . $row['case_id'] . '" class="btn btn-link Details">Details</a></td>';
          }

          echo '</tr>';
        }
        ?>
      </tbody>
    </table>
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
    <?php $_SESSION['success'] = 0; ?>
  </main>
</body>

</html>