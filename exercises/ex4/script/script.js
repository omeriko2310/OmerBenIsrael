
document.addEventListener("DOMContentLoaded", function () {
    const imageUrls = {
        Red: "https://xcdn.next.co.uk/Common/Items/Default/Default/ItemImages/AltItemShot/315x472/U88491s.jpg",
        Blue: "https://xcdn.next.co.uk/Common/Items/Default/Default/ItemImages/AltItemShot/315x472/U88496s.jpg",
        Green: "https://xcdn.next.co.uk/Common/Items/Default/Default/ItemImages/AltItemShot/315x472/U88498s.jpg",
        Yellow: "https://xcdn.next.co.uk/Common/Items/Default/Default/ItemImages/AltItemShot/315x472/C09597s.jpg",
        Black: "https://xcdn.next.co.uk/common/Items/Default/Default/Publications/G88/shotview-315x472/4803/C90-184s.jpg",
        White: "https://xcdn.next.co.uk/common/Items/Default/Default/Publications/G88/shotview-315x472/4803/C83-639s.jpg",
    };
    const colorSelect = document.getElementById("color");
    const shirtImage = document.getElementById("shirt-image");
    function changeShirtColor() {
        const selectedColor = colorSelect.value;
        const imageUrl = imageUrls[selectedColor];
        shirtImage.src = imageUrl;
    }
    colorSelect.addEventListener("change", changeShirtColor);
});

function validate() {
    var checkboxes = document.getElementsByName('interests[]');
    var checked = 0;
    for (var i = 0; i < checkboxes.length; i++) {
        if (checkboxes[i].checked) {
            checked++;
        }
    }
    if (checked < 3) {
        alert("Please select at least 3 checkboxes.");
        return false;
    }
    return true;
}

const standardPrice = 30;
const premiumPrice = 40;
const shippingOptions = document.querySelectorAll('input[name="shipping"]');
const totalPriceInput = document.getElementById("total-price");
shippingOptions.forEach(option => {
    option.addEventListener("change", () => {
        if (option.value === "standard") {
            totalPriceInput.value = `${standardPrice}$`;
        } else if (option.value === "premium") {
            totalPriceInput.value = `${premiumPrice}$`;
        }
    });
});