$(document).ready(function() {
    let sortItems = $('.sort-button');
    sortItems.on('click', function() {
      let column = $(this).data('column');
      let url = 'index.php?category=' + column;
      window.location.href = url;
    });
  

    $('#editProfilePic').on('click', function(event) {
      event.preventDefault(); 
      $('#editModalProfile').modal('show'); // Show the modal with the specified ID
    });

    document.getElementById('take-picture').addEventListener('click', function() {
      fetch('takePic.php', {
          method: 'POST',
          headers: {
              'Content-Type': 'application/json'
          }
      })
      .then(response => {
          if (!response.ok) {
              throw new Error('Network response was not ok');
          }
          return response.text();
      })
      .then(data => {
          console.log('Picture taken successfully.');
          // Create img tag and set src attribute to the captured picture path
          const img = document.createElement('img');
          img.src = data;
          img.className = 'picImg';
 
          document.querySelector('.picture').appendChild(img);
          document.getElementById('capturedImagePath').value = data;
          document.getElementById('flag').value = '1';
          
      })
      .catch(error => {
          console.error('Error taking picture:', error);
      });
    });

    document.getElementById('SUBMITbtn').addEventListener('click', function() {
        
        let caseNumber = document.getElementById('caseNumberInput').value.trim();
        // Check if the case number is empty
        if (!caseNumber) {
            console.error('Case number is required.');
            return;
        }

        fetch('createJson.php?caseNumber=' + caseNumber, {
            method: 'GET', 
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(data => {
            console.log('JSON file created successfully.');
        })
        .catch(error => {
            console.error('Error taking picture:', error);
        });
    });



});
