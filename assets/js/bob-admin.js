function initBobAdmin() {
    const apiKeyInput = document.querySelector('[name="bob-openai-api-key"]');
    const apiKeyToggle = document.querySelector('#bob-api-key-toggle');

    if (apiKeyInput && apiKeyToggle) {
        apiKeyToggle.addEventListener('click', (event) => {
            event.preventDefault();

            if (apiKeyInput.type === 'password') {
                apiKeyInput.type = 'text';
                apiKeyToggle.textContent = 'Hide';
            } else {
                apiKeyInput.type = 'password';
                apiKeyToggle.textContent = 'Show';
            }
        });
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initBobAdmin);
} else {
    initBobAdmin();
}