document.addEventListener("DOMContentLoaded", function () {
  const modifyBtns = document.querySelectorAll(".modify-btn");
  const modifyForm = document.getElementById("modifyQuestionForm");
  const numChoicesSelect = document.getElementById("modifyNumberOfChoices");
  const choicesContainer = document.getElementById("modifyChoicesContainer");

  let currentOptions = []; // Temporarily store old options for reuse if needed

  modifyBtns.forEach((btn) => {
    btn.addEventListener("click", function () {
      // Clear previous state
      modifyForm.reset();
      choicesContainer.innerHTML = "";
      currentOptions = [];

      // Get attributes
      const qid = this.getAttribute("data-qid");
      const title = this.getAttribute("data-title");
      const numChoices = parseInt(this.getAttribute("data-num-choices"), 10);
      const correct = this.getAttribute("data-correct");
      const options = JSON.parse(this.getAttribute("data-options"));

      // Save options for reuse
      currentOptions = options;

      // Populate modal fields
      document.getElementById("modifyQid").value = qid;
      document.getElementById("modifyQuestionTitle").value = title;
      document.getElementById("modifyNumberOfChoices").value = numChoices;
      document.getElementById("modifyCorrectAnswer").value = correct;

      // Trigger generation of inputs
      generateChoiceInputs(numChoices);
    });
  });

  // Regenerate input fields when number changes
  numChoicesSelect.addEventListener("change", function () {
    const count = parseInt(this.value, 10);
    if (!isNaN(count)) {
      generateChoiceInputs(count);
    }
  });

  // Function to generate choices
  function generateChoiceInputs(count) {
    choicesContainer.innerHTML = "";
    for (let i = 0; i < count; i++) {
      const val = currentOptions[i] || ""; // Pre-fill if available
      choicesContainer.innerHTML += `
          <div class="mb-2">
            <input type="text" class="form-control" name="choices[]" value="${val}" placeholder="Option ${String.fromCharCode(
        65 + i
      )}" required>
          </div>`;
    }
  }

  // Submit modified question
  modifyForm.addEventListener("submit", function (e) {
    e.preventDefault();

    const formData = new FormData(this);
    fetch("/exam_app_upgraded/controller/modify_question.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.text()) // <-- Read as plain text first
      .then((text) => {
        console.log("Raw response:", text);
        const data = JSON.parse(text); // Now try parsing manually
        if (data.success) {
          alert("Question modified successfully!");
          location.reload();
        } else {
          alert("Error updating question!");
        }
      })
      .catch((error) => {
        console.error("Fetch Error:", error);
        alert("Network or server error.");
      });
  });
});
