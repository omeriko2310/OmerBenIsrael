function showCategory(data) {
    const selectElement = document.getElementById('category-list');
    for (const category of data.categories) {
        const optionElement = document.createElement('option');
        optionElement.value = category;
        optionElement.textContent = category;
        selectElement.appendChild(optionElement);
    }
  }
  
  fetch("data/categories.json")
    .then(response => response.json())
    .then(data => showCategory(data));