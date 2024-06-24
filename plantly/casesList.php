<?php
include "config.php";
session_start();

$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
if (mysqli_connect_errno()) {
    header("Location: login.php");
    die("DB connection failed: " . mysqli_connect_error());
}

if (!isset($_SESSION["user_id"]) || empty($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

function executeQuery($connection, $query, $params, $types)
{
    $stmt = mysqli_prepare($connection, $query);
    if ($stmt === false) {
        header("Location: login.php");
        die("DB query preparation failed: " . mysqli_error($connection));
    }
    if (!empty($types) && !empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    return $stmt;
}

// Handle plant deletion
if (!empty($_GET["penaltyId"]) && !empty($_GET["selectYes"])) {
    $penalty_id = intval($_GET['penaltyId']);
    $queries = [
        "DELETE FROM plantly_plant WHERE case_id = ?" => 'i',
        "DELETE FROM plantly_advices WHERE case_id = ?" => 'i'
    ];

    foreach ($queries as $query => $types) {
        $stmt = executeQuery($connection, $query, [$penalty_id], $types);
        if (!$stmt) {
            header("Location: login.php");
            die("DB query failed.");
        }
    }

    $_SESSION['success'] = 3;
    header("Location: casesList.php");
    exit;
}

// Handle plant update
if (!empty($_GET["penaltyId"]) && !empty($_GET["newDescription"]) && !empty($_GET["paneltyAmount"])) {
    $penalty_id = intval($_GET['penaltyId']);
    $new_description = $_GET['newDescription'];
    $new_amount = $_GET['paneltyAmount'];

    $query = "UPDATE plantly_plant SET summary = ?, Amount = ? WHERE case_id = ?";
    $stmt = executeQuery($connection, $query, [$new_description, $new_amount, $penalty_id], 'sii');

    if (!$stmt) {
        die("Update query failed.");
    }

    $_SESSION['success'] = 3;
    header("Location: casesList.php");
    exit;
}

// Handle user update
if (!empty($_GET["newUserName"])) {
    $new_userName = $_GET['newUserName'];
    $_SESSION["user_name"] = $new_userName;
    $new_password = $_GET['newPassword'];
    $user_id = intval($_SESSION["user_id"]);

    $query = "UPDATE plantly_user SET name = ?, password = ? WHERE ID = ?";
    $stmt = executeQuery($connection, $query, [$new_userName, $new_password, $user_id], 'ss');

    if (!$stmt) {
        header("Location: login.php");
        die("DB query failed.");
    }
}

// Fetch user type and adjust the query based on user type
$user_type = $_SESSION["user_type"];
$user_id = $_SESSION["user_id"];
if ($user_type == 'farmer') {
    $query = "SELECT p.case_id, p.plant_name, p.pic_path, p.summary, p._type AS plant_type, p._date AS plant_date, a.advice, a._data AS advice_date
              FROM plantly_plant p
              LEFT JOIN plantly_advices a ON p.case_id = a.case_id
              WHERE p.user_id = ?
              ORDER BY p.case_id, a._data DESC";
    $stmt = executeQuery($connection, $query, [$user_id], 'i');
} else if ($user_type == 'insp') {
    $query = "SELECT p.case_id, p.plant_name, p.pic_path, p.summary, p._type AS plant_type, p._date AS plant_date, a.advice, a._data AS advice_date
              FROM plantly_plant p
              LEFT JOIN plantly_advices a ON p.case_id = a.case_id
              ORDER BY p.case_id, a._data DESC";
    $stmt = executeQuery($connection, $query, [], '');
}

if (!$stmt) {
    die("Query failed.");
}

$result = mysqli_stmt_get_result($stmt);
$plants = [];
while ($row = mysqli_fetch_assoc($result)) {
    $case_id = $row['case_id'];
    if (!isset($plants[$case_id])) {
        $plants[$case_id] = [
            'plant_name' => $row['plant_name'],
            'pic_path' => $row['pic_path'],
            'summary' => $row['summary'],
            'plant_type' => $row['plant_type'],
            'plant_date' => $row['plant_date'],
            'advices' => []
        ];
    }
    if ($row['advice'] !== null) {
        $plants[$case_id]['advices'][] = [
            'advice' => $row['advice'],
            'advice_date' => $row['advice_date']
        ];
    }
}

mysqli_free_result($result);
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

$dataUpdatedClass = isset($_SESSION['success']) && $_SESSION['success'] == 3 ? 'data-updated-show' : 'data-updated-hide';
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
    <script defer="" src="js/scriptsPanelty.js"></script>
    <title>Plantly - Cases</title>
</head>

<body class="wrapper">
    <header>
        <label id="userNameToShowSmall"> &nbsp; &nbsp;Hi, <?php echo htmlspecialchars($_SESSION["user_name"]); ?></label>
        <div class="profilePic">
            <a href="#" id="editProfilePic"><img src="<?php echo htmlspecialchars($_SESSION['user_img']); ?>" alt="profile picture" title="profile picture"></a>
        </div>
        <div class="logo"><a href="index.php" class="logo-link" title="logo"></a></div>
        <div class="navigatin">
            <nav class="navbar navbar-expand-lg bg-body-tertiary">
                <div class="container-fluid">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                            <li class="nav-item"><a class="nav-link" aria-current="page" href="index.php">Home</a></li>
                            <li class="nav-item"><?php if ($_SESSION["user_type"] == "farmer") {
                                                        echo '<a class="nav-link" aria-current="page" href="newPlat.php">New Plant</a>';
                                                    } ?></li>
                            <a class="nav-link" href="casesList.php">Cases
                                <?php
                                echo '<span class="penaltySum">(<span class="penaltySum" id="penalySumNum">' . $penaltyCount . '</span>)</span>';
                                ?>
                            </a>
                            <li class="nav-item logOutToggle"><a id="logout" href="login.php"><i class="fa fa-sign-out" aria-hidden="true"></i>Logout</a></li>
                        </ul>
                        <form class="d-flex" role="search">
                            <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                            <button class="btn btn-outline-success" type="submit">Search</button>
                        </form>
                    </div>
                </div>
            </nav>
        </div>
    </header>
    <main>
        <div class="side-menu">
            <a href="#"><i class="fa fa-envelope-open-o" aria-hidden="true"></i><span data-bs-toggle="modal" data-bs-target="#messagesModal">Messages</span></a>
            <a href="article.php"><i class="fa fa-newspaper-o" aria-hidden="true"></i>Articles</a>
            <a href="#"><i class="fa fa-user-o" aria-hidden="true"></i>Profile</a>
            <section class="userTool"><a href="contactUs.php"><i class="fa fa-address-book-o" aria-hidden="true"></i>Contact us</a><br><a href="#"><i class="fa fa-cog" aria-hidden="true"></i><span data-bs-toggle="modal" data-bs-target="#editModalProfile">Settings</span></a><br><a id="logout" href="login.php"><i class="fa fa-sign-out" aria-hidden="true"></i>Logout</a></section>
            <label id="userNameToShow">Hi, <?php echo htmlspecialchars($_SESSION["user_name"]); ?></label>
        </div>
        <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Open Cases</li>
            </ol>
        </nav>
        <section id="penaltyUpdatedP" class="<?php echo $dataUpdatedClass; ?>">Plant updated successfully!!</section>
        <div class="editPenalty">
            <div class="modal fade" id="removeModalPenalty" tabindex="-1" aria-labelledby="removeModalPenalty" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="removeModalPenalty">Remove Plant</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="#" method="GET" id="frm">
                                <div class="form-outline mb-4">
                                    <label class="form-label">Are You Sure You Want To Delete This Plant?</label>
                                </div>
                                <input type="hidden" name="penaltyId" value="">
                                <input type="hidden" name="selectYes" value="1">
                                <div class="modal-footer">
                                    <button type="button" id="ModalBtnN" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" id="ModalBtnY" class="btn btn-primary">Save changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="editModalPenalty" tabindex="-1" aria-labelledby="editModalPenalty" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalPenalty">Edit Plant</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="#" method="GET" id="frm">
                                <div class="form-outline mb-4">
                                    <label class="form-label">Edit Description:</label>
                                    <textarea class="form-control" id="description" name="newDescription" placeholder="More Details..." required></textarea>
                                </div>
                                <input type="hidden" name="penaltyId" value="">
                                <div class="modal-footer">
                                    <button type="button" id="ModalBtnN" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" id="ModalBtnY" class="btn btn-primary">Save changes</button>
                                </div>
                            </form>
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
                        if (!empty($plants)) {
                            foreach ($plants as $plant) {
                                foreach ($plant['advices'] as $advice) {
                                    echo "<p><span style='font-weight: bold; color: blue;'>Date: " . htmlspecialchars($advice['advice_date'] ?? '') . "</span> | <span style='color: green;'>Plant Name: " . htmlspecialchars($plant['plant_name'] ?? '') . "</span> | " . htmlspecialchars($advice['advice'] ?? '') . "</p>";
                                }
                            }
                        } else {
                            echo "<p>No advices found for this case.</p>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <?php
            foreach ($plants as $case_id => $plant) {
                echo '<div class="card" style="width: 18rem;">';
                echo '<img src="' . htmlspecialchars($plant['pic_path'] ?? '') . '" class="card-img-top" alt="Plant Image" title="Plant Image">';
                echo '<div class="card-body-up">';
                echo '<h5 class="card-title">' . htmlspecialchars($plant["plant_name"] ?? '') . ($_SESSION["user_type"] == "insp" ? '<button type="button" class="btn modalBtn2 labelEdit btn-primary" data-bs-toggle="modal" data-bs-target="#removeModalPenalty" data-penalty="' . htmlspecialchars($case_id) . '"><i class="fa fa-trash-o" aria-hidden="true"></i></button><button type="button" class="btn modalBtn2 labelEdit btn-primary" data-bs-toggle="modal" data-bs-target="#editModalPenalty" data-penalty="' . htmlspecialchars($case_id) . '"><i class="fa fa-pencil" aria-hidden="true"></i></button>' : '') . '</h5>';
                echo '<p class="card-text">' . htmlspecialchars($plant["summary"] ?? '') . '</p>';
                echo '</div>';
                echo '<ul class="list-group list-group-flush">';
                echo '<li class="list-group-item">Plant Type: <span>' . htmlspecialchars($plant["plant_type"] ?? '') . '</span></li>';
                echo '<li class="list-group-item">Case ID: <span>' . htmlspecialchars($case_id) . '</span></li>';
                echo '<li class="list-group-item card-date">Date Created: <span>' . htmlspecialchars($plant["plant_date"] ?? '') . '</span></li>';
                foreach ($plant['advices'] as $advice) {
                    echo '<li class="list-group-item">Advice given on ' . htmlspecialchars($advice["advice_date"] ?? '') . ': ' . htmlspecialchars($advice["advice"] ?? '') . '</li>';
                }
                echo '</ul>';
                echo '<div class="card-body">';
                echo '<a href="ExpertPlant.php?caseId=' . htmlspecialchars($case_id) . '" class="card-link">See plant page</a>';
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>
    </main>
</body>

</html>