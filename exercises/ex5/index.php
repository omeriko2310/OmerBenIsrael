<!DOCTYPE html>
<html>
<head>
  <title>Omer's Boutique Bookstore</title>
  <link rel="stylesheet" href="style/style.css">
  <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
  <script src="js/script.js"></script> 
</head>
<body>
  <h1>Welcome to Omer's Boutique Bookstore</h1>
  <div class="book-list">
    <?php
    include "db.php";
    include "config.php";
    $query = "SELECT * FROM tbl_21_books";
    $result = mysqli_query($connection, $query);

    if (!empty($_SERVER['QUERY_STRING'])) {
      $cat = $_GET['category'];
    } else {
      $cat = 'all';
    }
    $query = "SELECT * FROM tbl_21_books";
    if ($cat != 'all') {
      $query .= " WHERE category = '$cat'";
    }
    $query .=  " ORDER BY book_id";
    $result = mysqli_query($connection, $query);
    if (!$result) {
        die("DB query failed.");
    }

    while ($row = mysqli_fetch_assoc($result)) {
        $bookId = $row['book_id'];
        $bookName = $row['book_name'];
        $imagePath = $row['image_path_1'];
        $description = $row['description'];
        $price = $row['price'];
        $rating = $row['rating'];
        $authorName = $row['author_name'];
        $category = $row['category'];
        $bookPageURL = "book.php?book_id=" . urlencode($bookId);
        echo "<a href='$bookPageURL' class='book'>";
        echo "<div>";
        echo "<h2>$bookName</h2>";
        echo "<img src='$imagePath' alt='$bookName'/>";
        echo "<p>$description</p>";
        echo "<p>Price: $price$</p>";
        echo "<p>Rating: $rating/5</p>";
        echo "<p>Author: $authorName</p>";
        echo "<p>Category: $category</p>";
        echo "</div>";
        echo "</a>";
    }

    mysqli_close($connection);
    ?>
  </div>
  <div class="selection">
    <script src="js/dispCat.js"></script> 
    <select id="category-list" aria-label="category-list">
      <option value="0" selected>Select category</option>
    </select>
  </div>
</body>
</html>