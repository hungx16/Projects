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
    const selectedIds = [];
    const eid = parseInt(
      new URLSearchParams(window.location.search).get("eid")
    );

    document
      .querySelectorAll(".delete-checkbox:checked")
      .forEach((checkbox) => {
        selectedIds.push(checkbox.dataset.qid);
      });

    if (selectedIds.length === 0) {
      alert("No questions selected.");
      return;
    }

    if (
      !confirm(
        "Are you sure you want to delete selected questions from this exam?"
      )
    ) {
      return;
    }

    fetch("/exam_app_upgraded/controller/delete_exam_questions.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ eid: eid, qids: selectedIds }),
    })
      .then((response) => response.text()) // ⬅️ Notice .text() instead of .json()
      .then((text) => {
        console.log("Raw response:", text);
        const data = JSON.parse(text); // now try parsing manually
        if (data.success) {
          alert("Questions removed from exam successfully.");
          location.reload();
        } else {
          alert("Server error: " + (data.error || "Unknown error"));
        }
      })
      .catch((err) => {
        console.error("Parse error:", err);
        alert("Unexpected server response. See console for details.");
      });
  });
