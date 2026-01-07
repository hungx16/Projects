// Attach event listener to pass eid dynamically
document.querySelectorAll(".addExamQuestionBtn").forEach((button) => {
  button.addEventListener("click", function () {
    const eid = this.getAttribute("data-eid"); // Retrieve `eid`
    sessionStorage.setItem("currentEid", eid); // Store `eid`

    // Assign eid to hidden input fields
    document.getElementById("examId").value = eid;

    // Update redirect button to add from question bank
    const redirectBtn = document.getElementById("redirectBtn");
    redirectBtn.onclick = function () {
      window.location.href = "add_from_question_bank.php?eid=" + eid;
    };
  });
});
