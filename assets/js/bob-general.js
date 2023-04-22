/**
 * This script handles the functionality of the Start Bob AI and Stop Bob AI buttons on the Bob General Settings page.
 * It hides and shows the appropriate buttons based on user interaction.
 */
document.addEventListener('DOMContentLoaded', function() {
    // Get elements from the DOM
    var startBobAI = document.getElementById('start-bob-ai');
    var stopBobAI = document.getElementById('stop-bob-ai');
    var form = document.querySelector('form');
    var messageContainer = document.createElement('div');
    messageContainer.classList.add('bob-message');

    // Set initial visibility of the buttons based on Bob AI status
    if (bobData.bobAiStatus === 'running') {
        startBobAI.style.display = 'none';
        stopBobAI.style.display = 'inline-block';
    } else {
        startBobAI.style.display = 'inline-block';
        stopBobAI.style.display = 'none';
    }

    // Add the message container to the DOM
    form.parentNode.insertBefore(messageContainer, form.nextSibling);

    // Helper function to display messages to the user
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

    // Event handler for the "Start Bob AI" button
    startBobAI.addEventListener('click', function(e) {
        e.preventDefault();

        var formData = new FormData(form);
        formData.append('action', 'start_bob_ai');
        formData.append('_ajax_nonce', bobData.startNonce);

        // Send AJAX request to start Bob AI
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
                showMessage('Bob AI has started updating your descriptions. Please check the Bob Stats section in a few hours to see the progress. You can click the "Stop Bob AI" button to stop Bob AI.', 'bob-message-success');
            } else {
                showMessage('An error occurred while starting Bob AI. Please try again.', 'bob-message-error');
            }
        });
    });

    // Helper function to enable the "Start Bob AI" button if Bob AI is not running
    function enableStartButton() {
        if (bobData.bobAiStatus !== 'running') {
            startBobAI.removeAttribute('disabled');
        }
    }

    // Event handler for the "Stop Bob AI" button
    stopBobAI.addEventListener('click', function(e) {
        e.preventDefault();

        // Prepare form data for AJAX request
        var formData = new FormData(form);
        formData.append('action', 'stop_bob_ai');
        formData.append('_ajax_nonce', bobData.stopNonce);

        // Send AJAX request to stop Bob AI
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
    document.addEventListener('settingsSaved', enableStartButton);
});