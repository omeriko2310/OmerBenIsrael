let timeRangeDropdown = document.getElementById("timeRangeDropdown");
let dropdownItems = document.querySelectorAll(".dropdown-item");
let myChart;
let myChart2;
let myChart3;

function updateCharts() {
  let caseId = document.getElementById('plantCaseId').value.trim();
  // Fetch data from a single JSON file that contains all case IDs
  let dataURL = 'chartData.json';

  console.log(caseId);

  // Fetch the entire JSON file
  fetch(dataURL)
    .then((response) => response.json())
    .then((data) => {
      // Check if the data for the given caseId exists
      if (data.hasOwnProperty(caseId)) {
        let caseData = data[caseId];
        let chart1Data = caseData.chart1;
        let chart2Data = caseData.chart2;
        let chart3Data = caseData.chart3;

        // Update chart1 data
        myChart.data.datasets = chart1Data.datasets;
        myChart.data.labels = chart1Data.labels;

        // Update chart2 data
        myChart2.data.datasets = chart2Data.datasets;
        myChart2.data.labels = chart2Data.labels;

        // Update chart3 data
        myChart3.data.datasets = chart3Data.datasets;
        myChart3.data.labels = chart3Data.labels;

        // Update the charts
        myChart.update();
        myChart2.update();
        myChart3.update();
      } else {
        console.error('No data found for case ID:', caseId);
      }
    })
    .catch((error) => console.error('Error fetching data:', error));
}


let ctx = document.getElementById("myChart").getContext("2d");
let data = {
  labels: [
    "2024-02-13",
    "2024-02-13",
    "2024-02-14",
    "2024-02-14",
    "2024-02-15",
    "2024-02-15",
    "2024-02-16",
    "2024-02-16",
  ],
  datasets: [
    {
      label: "Moisture",
      data: [354.05, 354.09, 354.7, 354.07, 354.06, 354.3, 354.05, 354.07],
      fill: false,
      borderColor: "rgb(75, 192, 192)",
      tension: 0.1,
    },
  ],
};

let options = {
  responsive: true,
  scales: {
    y: {
      beginAtZero: true,
    },
  },
};

myChart = new Chart(ctx, {
  type: "line",
  data: data,
  options: options,
});

let ctr = document.getElementById("myChart2").getContext("2d");
let data2 = {
  labels: [
    "2024-02-13",
    "2024-02-13",
    "2024-02-14",
    "2024-02-14",
    "2024-02-15",
    "2024-02-15",
    "2024-02-16",
    "2024-02-16",
  ],
  datasets: [
    {
      label: "Temp",
      data: [26.277, 26.577, 25.257, 26.277, 26.577, 25.257, 26.277, 26.577],
      backgroundColor: [
        "rgba(255, 99, 132, 0.2)",
        "rgba(54, 162, 235, 0.2)",
        "rgba(255, 206, 86, 0.2)",
        "rgba(75, 192, 192, 0.2)",
        "rgba(153, 102, 255, 0.2)",
        "rgba(255, 159, 64, 0.2)",
        "rgba(255, 159, 64, 0.2)",
      ],
      borderColor: [
        "rgba(255, 99, 132, 1)",
        "rgba(54, 162, 235, 1)",
        "rgba(255, 206, 86, 1)",
        "rgba(75, 192, 192, 1)",
        "rgba(153, 102, 255, 1)",
        "rgba(255, 159, 64, 1)",
        "rgba(255, 159, 64, 0.2)",
      ],
      borderWidth: 1,
    },
  ],
};

let options2 = {
  responsive: true,
  scales: {
    y: {
      beginAtZero: true,
    },
  },
};

myChart2 = new Chart(ctr, {
  type: "bar",
  data: data2,
  options: options2,
});

let ctz = document.getElementById("myChart3").getContext("2d");
let data3 = {
  labels: [
    "2024-02-13",
    "2024-02-13",
    "2024-02-14",
    "2024-02-14",
    "2024-02-15",
    "2024-02-15",
    "2024-02-16",
    "2024-02-16",
  ],
  datasets: [
    {
      label: "Temp",
      data: [26.277, 26.577, 25.257, 25.477, 24.577, 24.257, 26.277, 26.577],
      backgroundColor: [
        "rgba(255, 99, 132, 0.2)",
        "rgba(54, 162, 235, 0.2)",
        "rgba(255, 206, 86, 0.2)",
        "rgba(75, 192, 192, 0.2)",
        "rgba(153, 102, 255, 0.2)",
        "rgba(255, 159, 64, 0.2)",
        "rgba(255, 159, 64, 0.2)",
      ],
      borderColor: [
        "rgba(255, 99, 132, 1)",
        "rgba(54, 162, 235, 1)",
        "rgba(255, 206, 86, 1)",
        "rgba(75, 192, 192, 1)",
        "rgba(153, 102, 255, 1)",
        "rgba(255, 159, 64, 1)",
        "rgba(255, 159, 64, 0.2)",
      ],
      borderWidth: 1,
    },
  ],
};

let options3 = {
  responsive: true,
  scales: {
    y: {
      beginAtZero: true,
    },
  },
};

myChart3 = new Chart(ctz, {
  type: "line",
  data: data3,
  options: options3,
});

$(document).ready(function () {
  $("#editProfilePic").on("click", function (event) {
    event.preventDefault(); // Prevent the link's default action
    $("#editModalProfile").modal("show"); // Show the modal with the specified ID
  });
});



document.getElementById('newPicBtn').addEventListener('click', function() {
  let caseId = document.getElementById('plantCaseId').value.trim();

  fetch('takeNewPic.php?caseNumber=' + caseId, {
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
      document.getElementById('plantPicM').value = data;

      setTimeout(() => {
          window.location.reload(); 
      }, 2000); 

  })
  .catch(error => {
      console.error('Error taking picture:', error);
  });
});

$(document).ready(function () {
document.getElementById('newMeasue').addEventListener('click', async function (event) {
  const spinner = document.getElementById("spinner");
  spinner.style.display = 'inline-block'; // Display the spinner

  event.preventDefault(); // Prevent the default action (page refresh)

  // Retrieve the case ID value
  let caseId = document.getElementById('plantCaseId').value.trim();

  // Simulate asynchronous operation (replace with your actual logic)
  try {
    await new Promise(resolve => setTimeout(resolve, 6000)); // Simulating a 6-second delay
    generateRandomData(); // Your actual function that generates data
    console.log('New measurement was taken');
  } catch (error) {
    console.error('Error generating data:', error);
  } finally {
    spinner.style.display = 'none'; // Hide the spinner after operation completes
  }
});
});
function generateRandomData() {

  let caseId = document.getElementById('plantCaseId').value.trim();
  let labels = ["2024-02-13", "2024-02-14", "2024-02-15", "2024-02-16"];

  // Function to generate random data close to the last data point
  function generateRandomCloseData(lastData, range) {
    return lastData.map(value => {
      let variation = (Math.random() - 0.5) * range;
      return (parseFloat(value) + variation).toFixed(2);
    });
  }

  // Get the last data points from the existing chart data
  let lastMoistureData = myChart.data.datasets[0].data.length > 0 ? myChart.data.datasets[0].data : [354.0];
  let lastTempData = myChart2.data.datasets[0].data.length > 0 ? myChart2.data.datasets[0].data : [26.0];
  let lastSavingsData = myChart3.data.datasets[0].data.length > 0 ? myChart3.data.datasets[0].data : [26.0];

  // Generate new random data close to the last data points
  let randomMoistureData = generateRandomCloseData(lastMoistureData, 0.1);
  let randomTempData = generateRandomCloseData(lastTempData, 0.5);
  let randomSavingsData = generateRandomCloseData(lastSavingsData, 0.5);

  myChart.data.labels = labels;
  myChart.data.datasets[0].data = randomMoistureData;

  myChart2.data.labels = labels;
  myChart2.data.datasets[0].data = randomTempData;

  myChart3.data.labels = labels;
  myChart3.data.datasets[0].data = randomSavingsData;

; 

  myChart.update();
  myChart2.update();
  myChart3.update();
}

window.onload = updateCharts;


