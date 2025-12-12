document.addEventListener('DOMContentLoaded', () => {
    const apiUrl = 'api.php';
    const form = document.getElementById('comment-form');
    const commentsList = document.getElementById('comments-list');
    const usernameInput = document.getElementById('username');
    const messageInput = document.getElementById('message');
    const errorMessage = document.getElementById('error-message');
    const submitBtn = document.getElementById('submit-btn');
    const btnText = submitBtn.querySelector('.btn-text');
    const spinner = submitBtn.querySelector('.spinner');

    /**
     * Fetches comments from the API and renders them to the page.
     */
    const fetchAndRenderComments = async () => {
        try {
            const response = await fetch(apiUrl);
            if (!response.ok) {
                throw new Error(`API request failed with status ${response.status}`);
            }
            const comments = await response.json();

            commentsList.innerHTML = ''; // Clear existing comments

            if (comments.length === 0) {
                commentsList.innerHTML = '<p>No comments yet. Be the first to post!</p>';
                return;
            }

            comments.forEach(comment => {
                const card = document.createElement('div');
                card.className = 'comment-card';

                const timestamp = new Date(comment.created_at).toLocaleString();

                card.innerHTML = `
                    <div class="timestamp">${timestamp}</div>
                    <div class="username">${escapeHTML(comment.username)}</div>
                    <p class="message">${escapeHTML(comment.message)}</p>
                `;
                commentsList.appendChild(card);
            });
        } catch (error) {
            commentsList.innerHTML = `<p class="error">Could not load comments. Please try again later.</p>`;
            console.error('Fetch error:', error);
        }
    };

    /**
     * Handles the form submission.
     */
    const handleFormSubmit = async (event) => {
        event.preventDefault(); // Prevent default page reload

        const username = usernameInput.value.trim();
        const message = messageInput.value.trim();

        if (!username || !message) {
            showError('Please fill out both your name and message.');
            return;
        }

        toggleSpinner(true);
        clearError();

        try {
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ username, message }),
            });

            if (!response.ok) {
                 const errorData = await response.json();
                throw new Error(errorData.message || 'Failed to submit comment.');
            }

            // Clear form and refresh comments
            form.reset();
            await fetchAndRenderComments();

        } catch (error) {
            showError(error.message);
            console.error('Submit error:', error);
        } finally {
            toggleSpinner(false);
        }
    };

    /**
     * Shows or hides the loading spinner on the submit button.
     */
    const toggleSpinner = (show) => {
        submitBtn.disabled = show;
        btnText.style.display = show ? 'none' : 'inline';
        spinner.style.display = show ? 'inline-block' : 'none';
    };

    /**
     * Displays an error message.
     */
    const showError = (message) => {
        errorMessage.textContent = message;
    };

    /**
     * Clears the error message.
     */
    const clearError = () => {
        errorMessage.textContent = '';
    };

    /**
     * Simple HTML escaping function to prevent XSS.
     */
    const escapeHTML = (str) => {
        const p = document.createElement('p');
        p.appendChild(document.createTextNode(str));
        return p.innerHTML;
    };


    // Add event listener for form submission
    form.addEventListener('submit', handleFormSubmit);

    // Initial load of comments
    fetchAndRenderComments();
});
