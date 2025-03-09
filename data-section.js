document.addEventListener("DOMContentLoaded", function () {
  console.log("data-section.js loaded successfully!");

  let buttons = document.querySelectorAll(".toggle-btn");
  let sections = document.querySelectorAll(".chart-1, .chart-2");

  if (buttons.length === 0) {
    console.error("No toggle buttons found!");
    return;
  }

  if (sections.length === 0) {
    console.error("No chart sections found!");
    return;
  }

  buttons.forEach((button) => {
    button.addEventListener("click", function () {
      // Remove active class from all buttons
      buttons.forEach((btn) => btn.classList.remove("active"));

      // Add active class to the clicked button
      this.classList.add("active");

      // Hide all chart sections
      sections.forEach((section) => {
        section.classList.add("d-none");
        section.style.display = "none"; // Ensures it hides
      });

      // Show the selected section
      let targetSections = document.querySelectorAll(
        "." + this.dataset.section
      );
      if (targetSections.length === 0) {
        console.error("No elements found for:", this.dataset.section);
      } else {
        targetSections.forEach((section) => {
          section.classList.remove("d-none");
          section.style.display = "block"; // Forces visibility
        });
      }

      // Debugging
      console.log("Clicked section:", this.dataset.section);
      console.log("Elements found:", targetSections);
    });
  });
});
