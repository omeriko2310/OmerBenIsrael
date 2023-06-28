<!DOCTYPE html>
<html>
<head>
  <title>Book Details</title>
  <link rel="stylesheet" href="style/style.css">
</head>
<body>
    <div class="book-list">
        <?php
        include "db.php";
        include "config.php";
        $bookId = $_GET['book_id'];
        $query = "SELECT * FROM tbl_21_books WHERE book_id = '$bookId'";
        $result = mysqli_query($connection, $query);
        if ($row = mysqli_fetch_assoc($result)) {
            $bookId = $row['book_id'];
            $bookName = $row['book_name'];
            $imagePath1 = $row['image_path_1'];
            $imagePath2 = $row['image_path_2'];
            $description = $row['description'];
            $price = $row['price'];
            $rating = $row['rating'];
            $authorName = $row['author_name'];
            $category = $row['category'];

            echo "<div class='book'>";
            echo "<h2>$bookName</h2>";
            echo "<img src='$imagePath1' alt='$bookName'/>";
            echo "<p>$description</p>";
            echo "<p>Category: $category</p>";
            echo "</div>";

            echo "<div class='book'>";
            echo "<img src='$imagePath2' alt='$bookName'/>";
            echo "<p>$authorName</p>";
            echo "</div>";
        } 
        else {
            echo "<p>Book not found.</p>";
        }

        mysqli_close($connection);
        ?>
    </div>
</body>
</html>
