document.addEventListener('DOMContentLoaded', () => {
    const apiKeyInput = document.querySelector('[name="openai_api_key"]');
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
});