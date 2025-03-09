document.addEventListener("DOMContentLoaded", function () {
  const ctx = document.getElementById("barChart").getContext("2d");

  let products, orders;

  try {
    products = JSON.parse(document.getElementById("products").value || "[]");
    orders = JSON.parse(document.getElementById("orders").value || "[]");
  } catch (error) {
    console.error("Error parsing JSON:", error);
    products = [];
    orders = [];
  }

  console.log("Products:", products);
  console.log("Orders:", orders);

  const myChart = new Chart(ctx, {
    type: "bar",
    data: {
      labels: products,
      datasets: [
        {
          label: "Sales",
          data: orders,
          backgroundColor: "rgba(75, 192, 192, 0.5)",
          borderColor: "rgba(75, 192, 192, 1)",
          borderWidth: 1,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        x: {
          ticks: {
            autoSkip: false,
            maxRotation: 45,
            minRotation: 45,
          },
        },
        y: {
          beginAtZero: true,
        },
      },
      plugins: {
        legend: {
          display: false,
        },
      },
    },
  });
});

document.addEventListener("DOMContentLoaded", function () {
  const ctx = document.getElementById("lineChart").getContext("2d");

  // Get HTML elements for updating sales display
  const totalSalesElement = document.getElementById("totalSales");
  const yesterdaySalesElement = document.getElementById("yesterdaySales");
  const valueInput = document.getElementById("value");

  // Convert PHP value to a number
  let todaySales = parseFloat(valueInput.value) || 0;
  let yesterdaySales = 0;

  // Format number to PHP currency format
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
  salesData[currentDate] = todaySales; // Set today's sales from PHP

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

  // Parse data from hidden inputs
  const products = JSON.parse(
    document.getElementById("products").value || "[]"
  );
  const values = JSON.parse(
    document.getElementById("values").value || "[]"
  ).map(Number); // Ensure values are numbers

  console.log("Products:", products); // Debug: Check parsed products
  console.log("Values:", values); // Debug: Check parsed values

  // Calculate total sum of values
  let totalValues = values.reduce((acc, num) => acc + num, 0);
  console.log("Total Values:", totalValues); // Debug: Check total sum

  // Calculate percentages
  let percentages =
    totalValues > 0
      ? values.map((num) => ((num / totalValues) * 100).toFixed(2) + "%")
      : values.map(() => "0%");

  console.log("Percentages:", percentages);

  // Create the pie chart
  const myChart = new Chart(ctx, {
    type: "pie",
    data: {
      labels: products,
      datasets: [
        {
          label: "Sales",
          data: values,
          backgroundColor: [
            "rgba(255, 99, 132, 0.6)", // Red
            "rgba(54, 162, 235, 0.6)", // Blue
            "rgba(75, 192, 192, 0.6)", // Green
          ],
          borderColor: [
            "rgba(255, 99, 132, 1)", // Red
            "rgba(54, 162, 235, 1)", // Blue
            "rgba(75, 192, 192, 1)", // Green
          ],
          borderWidth: 1,
        },
      ],
    },
    options: {
      responsive: true,
      plugins: {
        datalabels: {
          color: "#000",
          font: {
            weight: "bold",
            size: 14,
          },
          formatter: (value, context) => {
            return percentages[context.dataIndex]; // Display percentage on the chart
          },
        },
      },
    },
    plugins: [ChartDataLabels], // Enable the datalabels plugin
  });
});
