
    document.addEventListener('DOMContentLoaded', function() {
    const queryString = window.location.pathname.split('/')[2];
    startProgressStream(queryString);

    function startProgressStream(tirageId) {
    const eventSource = new EventSource(`/tirages/progress/${tirageId}`);

    eventSource.addEventListener('progress', function(event) {
    const data = JSON.parse(event.data);
    updateUI(data);
});

    eventSource.addEventListener('complete', function() {
    eventSource.close();
    addMessage("Le tirage au sort est terminé avec succès !", "success");
});

    eventSource.addEventListener('quotas', function(event) {
    const data = JSON.parse(event.data);
    addMessage(data.message, "info");
});

    eventSource.onerror = function(error) {
    eventSource.close();
    addMessage("Une erreur s'est produite lors de la connexion au serveur", "error");
};
}

    function updateUI(data) {
    // Remove empty state if it exists
    const emptyState = document.getElementById('emptyState');
    if (emptyState) emptyState.remove();

    // Update progress
    document.getElementById('progressText').textContent = `${data.progress}/${data.total}`;

    const percent = Math.round((data.progress / data.total) * 100);
    document.getElementById('progressPercent').textContent = `${percent}%`;
    document.getElementById('progressBar').style.width = `${percent}%`;

    // Add new candidate
    const candidat = data.candidat;
    const card = document.createElement('div');
    card.className = 'candidate-card bg-white p-4 rounded-lg shadow-sm';
    card.innerHTML = `
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center mr-3
                        ${candidat.sexe === 'M' ? 'bg-blue-100 text-blue-600' : 'bg-pink-100 text-pink-600'}">
                        <i class="fas ${candidat.sexe === 'M' ? 'fa-male' : 'fa-female'}"></i>
                    </div>
                    <div class="flex-1">
                        <div class="font-bold truncate">${candidat.name}</div>
                        <div class="text-xs text-gray-600 truncate">${candidat.group}</div>
                    </div>
                </div>
            `;

    document.getElementById('resultsContainer').prepend(card);
}

    function addMessage(text, type) {
    const messageDiv = document.getElementById('message');
    const message = document.createElement('div');
    message.className = `text-sm ${type === 'error' ? 'text-red-800' : type === 'success' ? 'text-green-800' : 'text-blue-800'}`;

    const icon = type === 'error' ? 'exclamation-circle' :
    type === 'success' ? 'check-circle' : 'info-circle';

    message.innerHTML = `<i class="fas fa-${icon} mr-2"></i> ${text}`;
    messageDiv.appendChild(message);
    messageDiv.scrollTop = messageDiv.scrollHeight;
}
});
