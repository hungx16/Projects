$(document).ready(function () {
  // Handle form submission
  $("#questionForm").submit(function (e) {
    e.preventDefault();

    $.ajax({
      url: "/exam_app_upgraded/controller/add_question.php",
      method: "POST",
      data: $(this).serialize(),
      dataType: "json",
      success: function (response) {
        console.log("Server Response:", response);
        if (response.status === "success") {
          alert(response.message);
          $("#addQuestionModal").modal("hide");
          location.reload();
          $("#questionForm")[0].reset();
          $("#choicesContainer").html("");
          $("#correctAnswerContainer").hide();
          let question = response.question;
          let newQuestion = `
                        <li class='list-group-item'>
                            <strong>Q: ${question.title} (${
            question.type
          })</strong>
                            <ul>
                                ${question.choices
                                  .map(
                                    (choice, i) =>
                                      `<li>${String.fromCharCode(
                                        65 + i
                                      )}: ${choice}</li>`
                                  )
                                  .join("")}
                            </ul>
                            <div class='text-success'><strong>Answer:</strong> ${
                              question.correctAnswer
                            }</div>
                        </li>
                    `;
          $("#questionList").prepend(newQuestion);
        } else {
          alert("Error: " + response.message);
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error:", status, error);
        console.error("Response Text:", xhr.responseText);
        alert("Error: " + error + "\n" + xhr.responseText);
      },
    });
  });

  // Handle dynamic choices
  $("#numberOfChoices").change(function () {
    let numChoices = parseInt($(this).val());
    let choicesContainer = $("#choicesContainer");
    let correctAnswerContainer = $("#correctAnswerContainer");

    choicesContainer.empty();
    if (!isNaN(numChoices)) {
      for (let i = 0; i < numChoices; i++) {
        let choiceLetter = String.fromCharCode(65 + i);
        choicesContainer.append(`
                    <div class="mb-3">
                        <label for="choice${choiceLetter}" class="form-label">Choice ${choiceLetter}</label>
                        <input type="text" class="form-control" id="choice${choiceLetter}" name="choices[]" required>
                    </div>
                `);
      }
      correctAnswerContainer.show();
    } else {
      correctAnswerContainer.hide();
    }
  });
});
