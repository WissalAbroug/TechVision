<?php $pageTitle = $quiz['titre']; require __DIR__ . '/../layout/header.php'; ?>

<style>
.quiz-step { display: none; animation: slideIn .3s ease; }
.quiz-step.active { display: block; }
@keyframes slideIn { from { opacity: 0; transform: translateX(20px); } to { opacity: 1; transform: translateX(0); } }
.option-label { display: block; padding: 1rem; border: 2px solid var(--border); border-radius: 8px; cursor: pointer; transition: all .2s ease; margin-bottom: 0.5rem; }
.option-label:hover { background: var(--bg-hover); border-color: var(--primary); }
input[type="radio"]:checked + .option-label { background: rgba(37,99,235,0.05); border-color: var(--primary); font-weight: bold; }
input[type="radio"] { display: none; }
</style>

<div class="container" style="max-width: 800px; margin: 3rem auto;">
    
    <div class="card" style="padding: 2.5rem;">
        <div class="flex-between mb-4">
            <h2 style="margin:0; font-size: 1.3rem;"><?= htmlspecialchars($quiz['titre']) ?></h2>
            <div class="badge badge-primary" id="timerBadge">⏱ 30s</div>
        </div>
        
        <div class="progress mb-4" style="height: 8px; background: var(--border); border-radius: 4px; overflow: hidden;">
            <div id="quizProgressBar" style="height: 100%; width: 0%; background: var(--primary); transition: width .3s ease;"></div>
        </div>

        <form method="POST" action="<?= APP_URL ?>/?ctrl=quiz&action=submit" id="quizForm">
            <?= csrf_field() ?>
            <input type="hidden" name="quiz_id" value="<?= $quiz['id'] ?>">

            <?php foreach ($questions as $i => $q): 
                $options = is_array($q['options']) ? $q['options'] : json_decode($q['options'], true);
            ?>
            <div class="quiz-step <?= $i === 0 ? 'active' : '' ?>" data-index="<?= $i ?>">
                <h3 class="mb-4">Question <?= $i + 1 ?> / <?= count($questions) ?></h3>
                <p style="font-size: 1.1rem; margin-bottom: 1.5rem; font-weight: 500;"><?= htmlspecialchars($q['question']) ?></p>
                
                <div class="options-container">
                    <?php foreach ($options as $optIndex => $optText): ?>
                    <label>
                        <input type="radio" name="answers[<?= $q['id'] ?>]" value="<?= $optIndex ?>">
                        <span class="option-label"><?= htmlspecialchars($optText) ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>

            <div class="mt-4 flex-between">
                <div id="questionCounter" class="text-muted" style="font-weight: bold;">1 / <?= count($questions) ?></div>
                <button type="button" class="btn btn-primary" id="nextBtn" onclick="nextQuestion()"><?= $lang==='fr'?'Suivant &rarr;':'Next &rarr;' ?></button>
                <button type="submit" class="btn btn-success" id="submitBtn" style="display:none;">💾 <?= $lang==='fr'?'Terminer le Test':'Finish Test' ?></button>
            </div>
        </form>
    </div>

</div>

<script>
let currentIndex = 0;
const total = <?= count($questions) ?>;
const steps = document.querySelectorAll('.quiz-step');
const progressBar = document.getElementById('quizProgressBar');
const counter = document.getElementById('questionCounter');
const nextBtn = document.getElementById('nextBtn');
const submitBtn = document.getElementById('submitBtn');
const timerBadge = document.getElementById('timerBadge');

let timeLeft = 30; // 30 seconds per question
let timerInterval;

function startTimer() {
    clearInterval(timerInterval);
    timeLeft = 30;
    timerBadge.textContent = `⏱ ${timeLeft}s`;
    timerInterval = setInterval(() => {
        timeLeft--;
        timerBadge.textContent = `⏱ ${timeLeft}s`;
        if (timeLeft <= 5) timerBadge.style.color = 'var(--danger)';
        else timerBadge.style.color = 'inherit';
        
        if (timeLeft <= 0) {
            clearInterval(timerInterval);
            nextQuestion(true); // auto next on timeout
        }
    }, 1000);
}

function updateUI() {
    steps.forEach((el, i) => {
        el.classList.remove('active');
        if (i === currentIndex) el.classList.add('active');
    });
    
    progressBar.style.width = ((currentIndex) / total) * 100 + '%';
    counter.textContent = `${currentIndex + 1} / ${total}`;
    
    if (currentIndex === total - 1) {
        nextBtn.style.display = 'none';
        submitBtn.style.display = 'block';
    } else {
        nextBtn.style.display = 'block';
        submitBtn.style.display = 'none';
    }
    startTimer();
}

function nextQuestion(auto = false) {
    const currentStep = steps[currentIndex];
    const checked = currentStep.querySelector('input[type="radio"]:checked');
    
    if (!auto && !checked) {
        alert("<?= $lang==='fr'?'Veuillez sélectionner une réponse.':'Please select an answer.' ?>");
        return;
    }
    
    if (currentIndex < total - 1) {
        currentIndex++;
        updateUI();
    } else if (auto) {
        // If last question timeouts, auto submit
        document.getElementById('quizForm').submit();
    }
}

// Start first timer
startTimer();
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
