document.addEventListener('DOMContentLoaded', function() {
    $('.modalBtn2').on('click', function() {
        let penaltyId = $(this).data('penalty');
        let modalTarget = $(this).data('bs-target');
        
        if (modalTarget === '#removeModalPenalty') {
            $('#removeModalPenalty input[name="penaltyId"]').val(penaltyId);
            console.log($('#removeModalPenalty input[name="penaltyId"]').val());
        } else if (modalTarget === '#editModalPenalty') {
            $('#editModalPenalty input[name="penaltyId"]').val(penaltyId);
            console.log($('#editModalPenalty input[name="penaltyId"]').val());
        }
        $(modalTarget).modal('show');
    });
});

$(document).ready(function() {
    $('#editProfilePic').on('click', function(event) {
        event.preventDefault(); // Prevent the link's default action
        $('#editModalProfile').modal('show'); // Show the modal with the specified ID
    });
});
