document
  .getElementById("delete-multiple-btn")
  .addEventListener("click", function () {
    document.getElementById("delete-options").style.display = "block";
    document.querySelectorAll(".delete-checkbox").forEach((checkbox) => {
      checkbox.style.display = "inline-block";
    });
  });

document.getElementById("cancel-delete").addEventListener("click", function () {
  document.getElementById("delete-options").style.display = "none";
  document.querySelectorAll(".delete-checkbox").forEach((checkbox) => {
    checkbox.style.display = "none";
    checkbox.checked = false;
  });
});

document
  .getElementById("confirm-delete")
  .addEventListener("click", function () {
    let selectedIds = [];
    document
      .querySelectorAll(".delete-checkbox:checked")
      .forEach((checkbox) => {
        selectedIds.push(checkbox.dataset.qid);
      });

    if (selectedIds.length === 0) {
      alert("No questions selected.");
      return;
    }

    if (!confirm("Are you sure you want to delete selected questions?")) {
      return;
    }

    fetch("/exam_app_upgraded/controller/delete_questions.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ qids: selectedIds }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          alert("Questions deleted successfully.");
          location.reload();
        } else {
          alert("Error deleting questions.");
        }
      });
  });
