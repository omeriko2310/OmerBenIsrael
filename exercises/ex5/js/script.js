$(document).ready(function () {
  $('#category-list').on('change', function () {
      var selectedCategory = $(this).val();
      var url = 'index.php?category=' + selectedCategory;
      window.location.href = url;
  });
});