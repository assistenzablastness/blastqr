// Esegui la funzione al caricamento del DOM
document.addEventListener('DOMContentLoaded', function() {
    display_help();
});

function display_help() {
    // Seleziona tutti gli elementi con la classe 'question'
    var questions = document.querySelectorAll('.question');
    
    // Aggiungi un evento di click a ciascun elemento 'question'
    questions.forEach(function(question) {
        question.addEventListener('click', function() {
            // Trova l'elemento figlio con la classe 'question__text'
            var questionText = question.querySelector('.question__text');
            
            // Controlla lo stato di visualizzazione dell'elemento
            if (questionText.style.display === 'none' || questionText.style.display === '') {
                // Nascondi tutti gli altri elementi 'question__text'
                document.querySelectorAll('.question__text').forEach(function(text) {
                    text.style.display = 'none';
                });
                
                // Mostra l'elemento corrente
                questionText.style.display = 'block';
            } else {
                // Nascondi l'elemento corrente
                questionText.style.display = 'none';
            }
        });
    });
}

