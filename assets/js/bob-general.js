/**
 * This script handles the functionality of the Start Bob AI and Stop Bob AI buttons on the Bob General Settings page.
 * It hides and shows the appropriate buttons based on user interaction.
 */
document.addEventListener('DOMContentLoaded', function() {
    var startBobAI = document.getElementById('start-bob-ai');
    var stopBobAI = document.getElementById('stop-bob-ai');
    var form = document.querySelector('form');
    var messageContainer = document.createElement('div');
    messageContainer.classList.add('bob-message');

    form.parentNode.insertBefore(messageContainer, form.nextSibling);

    function showMessage(message, type) {
        messageContainer.textContent = message;
        messageContainer.classList.remove('bob-message-success', 'bob-message-error');
        messageContainer.classList.add(type);
    
        if (type === 'bob-message-success') {
            startBobAI.removeAttribute('disabled');
        } else {
            startBobAI.setAttribute('disabled', 'disabled');
        }
    }   

    startBobAI.addEventListener('click', function(e) {
        e.preventDefault();

        var formData = new FormData(form);
        formData.append('action', 'start_bob_ai');

        fetch(ajaxurl, {
            method: 'POST',
            body: formData,
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            if (data.success) {
                startBobAI.style.display = 'none';
                stopBobAI.style.display = 'inline-block';
                showMessage('Bob AI has started updating your descroptions. Please check the Bob Stats section to see the progress. You can click the "Stop Bob AI" button to stop Bob AI.', 'bob-message-success');
            } else {
                showMessage('An error occurred while starting Bob AI. Please try again.', 'bob-message-error');
            }
        });
    });

    stopBobAI.addEventListener('click', function(e) {
        e.preventDefault();

        var formData = new FormData(form);
        formData.append('action', 'stop_bob_ai');

        fetch(ajaxurl, {
            method: 'POST',
            body: formData,
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            if (data.success) {
                stopBobAI.style.display = 'none';
                startBobAI.style.display = 'inline-block';
                showMessage('Bob AI has stopped. You can click the "Start Bob AI" button to start it again.', 'bob-message-success');
            } else {
                showMessage('An error occurred while stopping Bob AI. Please try again.', 'bob-message-error');
            }
        });
    });
});