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

$farmer_id_penalty = intval($_SESSION["user_id"]);
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
                                } elseif ($_SESSION["user_type"] == "insp") {
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
            <a href="#"><i class="fa fa-newspaper-o" aria-hidden="true"></i>Articles</a>
            <a href="#"><i class="fa fa-user-o" aria-hidden="true"></i>Profile</a>
            <section class="userTool"><a href="contactUs.php"><i class="fa fa-address-book-o" aria-hidden="true"></i>Contact us</a><br><a href="#"><i class="fa fa-cog" aria-hidden="true"></i><span data-bs-toggle="modal" data-bs-target="#editModalProfile">Settings</span></a><br><a id="logout" href="login.php"><i class="fa fa-sign-out" aria-hidden="true"></i>Logout</a></section>
            <label id="userNameToShow">Hi,
                <?php echo $_SESSION["user_name"]; ?>
            </label>
        </div>
        <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Articles</li><br>
            </ol>
        </nav>
        <div class="container">
            <div class="card" style="width: 18rem;">
                <img src="images/article1.webp" class="card-img-topA" alt="Inspector Image" title="Inspector Image">
                <div class="card-body-up">
                    <h4 class="card-title">
                        "why blueberries are blue?"
                    </h4>
                    <p class="card-textA">
                        Nanostructures in the berries' waxy coating make them appear blue, despite dark red pigments in.
                        the skin
                    </p>
                </div>
                <div class="card-body">
                    <a href="https://www.sciencenews.org/article/blueberry-blue-color-nanostructure-wax-pigment" class="card-link">Read More</a>
                </div>
            </div>



            <div class="card" style="width: 18rem;">
                <img src="images/article2.webp" class="card-img-topA" alt="Inspector Image" title="Inspector Image">
                <div class="card-body-up">
                    <h4 class="card-title">
                        "This weird fern is the first known plant?"
                    </h4>
                    <p class="card-textA">
                        The tree fern Cyathea rojasiana gets thrifty to survive in Panamas Quebrada Chorro forest.
                    </p>
                </div>

                <div class="card-body">
                    <a href="https://www.sciencenews.org/article/fern-first-plant-dead-leaves-new-roots" class="card-link">Read More</a>
                </div>
            </div>


            <div class="card" style="width: 18rem;">
                <img src="images/article3.webp" class="card-img-topA" alt="Inspector Image" title="Inspector Image">
                <div class="card-body-up">
                    <h4 class="card-title">
                        "This first-of-its-kind palm plant"
                    </h4>
                    <p class="card-textA">
                        The behavior is rare among plants, and no other palm is known to do it.
                    </p>
                </div>
                <div class="card-body">
                    <a href="https://www.sciencenews.org/article/palm-plant-flowers-fruits-underground" class="card-link">Read More</a>
                </div>
            </div>



            <div class="card" style="width: 18rem;">
                <img src="images/article4.webp" class="card-img-topA" alt="Inspector Image" title="Inspector Image">
                <div class="card-body-up">
                    <h4 class="card-title">
                        "Most Delicious Poison"
                    </h4>
                    <p class="card-textA">
                        The book weeds through chemistry, evolution and world history to explore the origins of toxins and how humans have co-opted them for everything from medicines to spices to pesticides.
                    </p>
                </div>
                <div class="card-body">
                    <a href="https://www.sciencenews.org/article/poison-toxins-history-evolution-book" class="card-link">Read More</a>
                </div>
            </div>



            <div class="card" style="width: 18rem;">
                <img src="images/article5.webp" class="card-img-topA" alt="Inspector Image" title="Inspector Image">
                <div class="card-body-up">
                    <h4 class="card-title">
                        "Salty sweat helps one desert plant stay hydrated"
                    </h4>
                    <p class="card-textA">
                        A study provides a new look into the strategies plants have evolved to survive in harsh locales.
                    </p>
                </div>
                <div class="card-body">
                    <a href="https://www.sciencenews.org/article/salt-sweat-desert-plant-hydrated" class="card-link">Read More</a>
                </div>
            </div>


            <div class="card" style="width: 18rem;">
                <img src="images/article6.webp" class="card-img-topA" alt="Inspector Image" title="Inspector Image">
                <div class="card-body-up">
                    <h4 class="card-title">
                        "The first citrus fruits may have come from southern China"
                    </h4>
                    <p class="card-textA">
                        An in-depth genetic analysis of Citrus plants identifies where they probably originated.
                    </p>
                </div>
                <div class="card-body">
                    <a href="https://www.sciencenews.org/article/first-citrus-fruits-china-origin" class="card-link">Read More</a>
                </div>
            </div>

            <div class="card" style="width: 18rem;">
                <img src="images/article7.webp" class="card-img-topA" alt="Inspector Image" title="Inspector Image">
                <div class="card-body-up">
                    <h4 class="card-title">
                        "Flowers pollinated by honeybees make lower-quality seeds"
                    </h4>
                    <p class="card-textA">
                        A new study emphasizes the importance of conserving wild, native insects too.
                    </p>
                </div>
                <div class="card-body">
                    <a href="https://www.sciencenews.org/article/flowers-pollinate-honeybees-low-seeds" class="card-link">Read More</a>
                </div>
            </div>

            <div class="card" style="width: 18rem;">
                <img src="images/article8.webp" class="card-img-topA" alt="Inspector Image" title="Inspector Image">
                <div class="card-body-up">
                    <h4 class="card-title">
                        "A hunt for fungi might bring this orchid back from the brink"
                    </h4>
                    <p class="card-textA">
                        Successful completion of the work could lead to the potential restoration of the species in its natural habitat through scientific efforts.
                    </p>
                </div>
                <div class="card-body">
                    <a href="https://www.sciencenews.org/article/coopers-black-orchid-fungus-conservation" class="card-link">Read More</a>
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

    </main>
</body>

</html>