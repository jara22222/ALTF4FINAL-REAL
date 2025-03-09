//Bar Chart
document.addEventListener("DOMContentLoaded", function () {
  const ctx = document.getElementById("barChart").getContext("2d");

  const myChart = new Chart(ctx, {
    type: "bar", // Change type if needed
    data: {
      labels: ["Coffee", "Pastries", "Rice Meals"],
      datasets: [
        {
          label: "Sales",
          data: [20, 16, 49],
          backgroundColor: "rgba(75, 192, 192, 0.2)",
          borderColor: "rgba(75, 192, 192, 1)",
          borderWidth: 1,
        },
      ],
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true,
        },
      },
    },
  });
});

//Line Chart
document.addEventListener("DOMContentLoaded", function () {
  const ctx = document.getElementById("lineChart").getContext("2d");

  // Get HTML elements for updating sales display
  const totalSalesElement = document.getElementById("totalSales");
  const yesterdaySalesElement = document.getElementById("yesterdaySales");

  // Format number to 2,000.00 style
  function formatCurrency(amount) {
    return new Intl.NumberFormat("en-PH", {
      style: "currency",
      currency: "PHP",
      minimumFractionDigits: 2,
    }).format(amount);
  }

  // Get formatted date for sales tracking
  function getFormattedDate(offset = 0) {
    let date = new Date();
    date.setDate(date.getDate() - offset);
    return date.toLocaleDateString("en-PH", {
      weekday: "long",
      month: "short",
      day: "numeric",
    });
  }

  // Store sales data for the last 7 days
  let salesData = {};
  for (let i = 6; i >= 0; i--) {
    salesData[getFormattedDate(i)] = 0; // Initialize past week sales with 0
  }

  let currentDate = getFormattedDate();
  let todaySales = 0;
  let yesterdaySales = 0;

  // Create the chart with dynamic colors
  const myLineChart = new Chart(ctx, {
    type: "line",
    data: {
      labels: Object.keys(salesData), // Last 7 days
      datasets: [
        {
          label: "Total Sales This Week",
          data: Object.values(salesData),
          borderColor: todaySales < yesterdaySales ? "red" : "blue",
          backgroundColor:
            todaySales < yesterdaySales
              ? "rgba(255, 0, 0, 0.2)"
              : "rgba(0, 123, 255, 0.2)",
          borderWidth: 2,
          tension: 0.3,
          pointRadius: 5,
          fill: true,
        },
      ],
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: function (value) {
              return formatCurrency(value);
            },
          },
        },
      },
    },
  });

  function updateSalesDisplay() {
    totalSalesElement.textContent = `Total Sales: ${formatCurrency(
      todaySales
    )}`;
    yesterdaySalesElement.textContent = `${formatCurrency(yesterdaySales)}`;
  }

  function realTime() {
    let now = new Date();
    let value = Math.floor(Math.random() * 2000); // Simulated sales value

    todaySales += value;

    // Update today's sales in the salesData object
    salesData[currentDate] = todaySales;

    // If new day starts, shift sales tracking
    let newDate = getFormattedDate();
    if (newDate !== currentDate) {
      console.log("Date changed! Resetting sales...");
      console.log(`Yesterday's Sales: ${todaySales}`);

      // Move today's sales to yesterday
      yesterdaySales = todaySales;
      todaySales = 0;
      currentDate = newDate;

      // Shift data, remove oldest day, and add the new day
      let keys = Object.keys(salesData);
      delete salesData[keys[0]]; // Remove oldest entry
      salesData[newDate] = 0; // Add new entry for today
    }

    // Update colors dynamically
    let newBorderColor = todaySales < yesterdaySales ? "red" : "blue";
    let newBackgroundColor =
      todaySales < yesterdaySales
        ? "rgba(255, 0, 0, 0.2)"
        : "rgba(0, 123, 255, 0.2)";

    myLineChart.data.datasets[0].borderColor = newBorderColor;
    myLineChart.data.datasets[0].backgroundColor = newBackgroundColor;

    // Update chart data
    myLineChart.data.labels = Object.keys(salesData);
    myLineChart.data.datasets[0].data = Object.values(salesData);

    updateSalesDisplay();
    myLineChart.update();
  }
  setInterval(realTime, 2000);
});

//Pie Chart

document.addEventListener("DOMContentLoaded", function () {
  const ctx = document.getElementById("pieChart").getContext("2d");

  const myChart = new Chart(ctx, {
    type: "pie", // Change type if needed
    data: {
      labels: ["Coffee", "Pastries", "Rice Meals"],
      datasets: [
        {
          label: "Sales",
          data: [20, 16, 49],
          backgroundColor: "rgba(75, 192, 192, 0.2)",
          borderColor: "rgba(75, 192, 192, 1)",
          borderWidth: 1,
        },
      ],
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true,
        },
      },
    },
  });
});
