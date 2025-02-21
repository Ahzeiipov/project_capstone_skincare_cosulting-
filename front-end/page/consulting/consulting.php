<?php
$servername = "localhost"; // or use 127.0.0.1
$username = "root";        // MySQL username
$password = "";            // No password
$dbname = "skin_care";    // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// If a recommendation_id is requested, fetch the corresponding product
if (isset($_GET['recommendation_id'])) {
    $recommendation_id = intval($_GET['recommendation_id']);
    $sql = "SELECT * FROM recommendations WHERE id = $recommendation_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(["error" => "No recommendation found"]);
    }
    $conn->close();
    exit; // Stop further execution
}

// Fetch questions and answers
$sql = "SELECT q.id as question_id, q.question_text, a.id as answer_id, a.answer_text, a.recommendation_id 
        FROM questions q 
        JOIN answers a ON q.id = a.question_id";
$result = $conn->query($sql);

$questions = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $questions[$row['question_id']]['text'] = $row['question_text'];
        $questions[$row['question_id']]['answers'][] = [
            'id' => $row['answer_id'],
            'text' => $row['answer_text'],
            'recommendation_id' => $row['recommendation_id']
        ];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../navigation/navigation-bar.css">
    <script src="../../navigation/navigation.js"></script>
    <link rel="stylesheet" href="consulting.css">
    <title>Skin Care Consultation</title>
</head>
<body>
    <section>
        <div id="navigation-bar"></div>
    </section>
    <section class="consulting">
        <div class="consulting-container">
            <div class="box" id="question-container">
                <h1 id="question-text">Question</h1>
                <div class="ans-options" id="options-container"></div>
                <div class="Thebutton">
                    <button class="Previous-btn" onclick="prevQuestion()"><i class="fa-solid fa-chevron-left" style="margin-right: 15px;"></i> Previous</button>
                    <button class="Previous-btn" onclick="nextQuestion()">Next <i class="fa-solid fa-chevron-right" style="margin-left: 15px;"></i></button>
                </div>
            </div>
            <div class="box" id="recommendation-container" style="display:none;">
                <h1>Recommended Product</h1>
                <div id="recommendation-content"></div>
            </div>
        </div>
    </section>

    <script>
        const questions = <?php echo json_encode($questions); ?>;
        let currentQuestionIndex = 0;
        let selectedAnswers = [];

        function loadQuestion() {
            const questionIds = Object.keys(questions);
            if (currentQuestionIndex >= questionIds.length) {
                showRecommendation();
                return;
            }

            let questionId = questionIds[currentQuestionIndex];
            let question = questions[questionId];

            document.getElementById('question-text').innerText = question.text;

            let optionsContainer = document.getElementById('options-container');
            optionsContainer.innerHTML = '';

            question.answers.forEach(answer => {
                let button = document.createElement('button');
                button.classList.add('option-btn');
                button.innerText = answer.text;
                button.onclick = () => selectAnswer(answer.recommendation_id);
                optionsContainer.appendChild(button);
            });
        }

        function selectAnswer(recommendationId) {
            selectedAnswers[currentQuestionIndex] = recommendationId;
            nextQuestion();
        }

        function nextQuestion() {
            currentQuestionIndex++;
            loadQuestion();
        }

        function prevQuestion() {
            if (currentQuestionIndex > 0) {
                currentQuestionIndex--;
                loadQuestion();
            }
        }

        function showRecommendation() {
            let countedRecommendations = {};

            selectedAnswers.forEach(id => {
                if (id) {
                    countedRecommendations[id] = (countedRecommendations[id] || 0) + 1;
                }
            });

            let bestRecommendationId = Object.keys(countedRecommendations).reduce((a, b) => 
                countedRecommendations[a] > countedRecommendations[b] ? a : b, null
            );

            if (bestRecommendationId) {
                fetch(`consulting.php?recommendation_id=${bestRecommendationId}`)
                    .then(response => response.json())
                    .then(data => {
                        let recommendationContent = document.getElementById('recommendation-content');
                        if (data.error) {
                            recommendationContent.innerHTML = `<h1>No recommendation found. Try again.</h1>`;
                        } else {
                            recommendationContent.innerHTML = `
                                <h1>${data.product_name}</h1>
                                <img src="${data.picture_url}" alt="${data.product_name}" style="max-width:300px;">
                                <p>${data.description}</p>
                            `;
                        }
                        document.getElementById('question-container').style.display = 'none';
                        document.getElementById('recommendation-container').style.display = 'block';
                    })
                    .catch(error => console.error('Error:', error));
            } else {
                document.getElementById('recommendation-content').innerHTML = `<h1>No recommendation found. Try again.</h1>`;
                document.getElementById('question-container').style.display = 'none';
                document.getElementById('recommendation-container').style.display = 'block';
            }
        }

        loadQuestion();
    </script>
</body>
</html>