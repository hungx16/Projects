document.addEventListener("DOMContentLoaded", () => {
  // Bootstrap 5 modal instance
  const addTypeModal = new bootstrap.Modal(
    document.getElementById("addTypeModal")
  );

  // Open modal and load types
  document.querySelectorAll(".add-type-btn").forEach((btn) => {
    btn.addEventListener("click", async function () {
      const qid = this.getAttribute("data-qid");
      document.getElementById("modalQid").value = qid;

      // Fetch types from the server
      const response = await fetch(
        "/exam_app_upgraded/controller/add_type_to_question.php"
      );
      const types = await response.json();

      // Populate the type dropdown
      const typeSelect = document.getElementById("typeSelect");
      typeSelect.innerHTML = ""; // Clear existing options
      types.forEach((type) => {
        const option = document.createElement("option");
        option.value = type.type_id;
        option.textContent = type.type_name;
        typeSelect.appendChild(option);
      });

      // Show Bootstrap 5 modal
      addTypeModal.show();
    });
  });

  // Handle form submission
  document
    .getElementById("addTypeForm")
    .addEventListener("submit", async function (e) {
      e.preventDefault();

      const formData = new FormData(this);
      const response = await fetch(
        "/exam_app_upgraded/controller/add_type_to_question.php",
        {
          method: "POST",
          body: formData,
        }
      );

      if (response.ok) {
        alert("Type added successfully!");
        location.reload(); // Hide modal
      } else {
        alert("Failed to add type. Please try again.");
      }
    });
});
