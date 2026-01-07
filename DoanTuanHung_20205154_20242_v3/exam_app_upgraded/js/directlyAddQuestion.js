// Add event listener for "Add Question" buttons
document.querySelectorAll(".addExamQuestionBtn").forEach(function (button) {
  button.addEventListener("click", function () {
    // Get the eid from the clicked button
    var eid = this.getAttribute("data-eid");
    sessionStorage.setItem("currentEid", eid);
    // Set the eid value in the hidden input field of the modal
    document.getElementById("examId").value = eid;
    document.getElementById("bankExamId").value = eid;
  });
});
document
  .getElementById("directlyAddQuestionBtn")
  .addEventListener("click", function () {
    // Retrieve eid from session storage
    const eid = sessionStorage.getItem("currentEid");

    // Set the eid in the hidden input field for "Directly Add Question" modal
    document.getElementById("examId").value = eid;

    // Debugging: Log the eid
    console.log("Directly Add Question Button - Exam ID:", eid);
  });
