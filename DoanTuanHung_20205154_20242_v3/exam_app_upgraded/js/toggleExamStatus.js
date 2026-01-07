document.addEventListener("DOMContentLoaded", function () {
  // Show/hide date fields
  document.querySelectorAll('input[name="mode"]').forEach((radio) => {
    radio.addEventListener("change", () => {
      const show =
        document.querySelector('input[name="mode"]:checked').value === "range";
      document.getElementById("timeRangeFields").style.display = show
        ? "block"
        : "none";
    });
  });

  // Set EID on modal open
  document.querySelectorAll(".toggleExamStatusBtn").forEach((button) => {
    button.addEventListener("click", () => {
      const eid = button.getAttribute("data-eid");
      document.getElementById("toggleExamEid").value = eid;
    });
  });

  // Submit form
  document
    .getElementById("toggleExamStatusForm")
    .addEventListener("submit", function (e) {
      e.preventDefault();
      const formData = new FormData(this);

      fetch("/exam_app_upgraded/controller/toggle_exam_status.php", {
        method: "POST",
        body: formData,
      })
        .then((res) => res.text())
        .then((response) => {
          alert(response);
          location.reload(); // Refresh to see status change
        })
        .catch((err) => alert("Error: " + err));
    });
});
