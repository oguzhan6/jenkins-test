document.addEventListener('DOMContentLoaded', () => {
    const apiUrl = 'stats-api.php';
    const uptimeEl = document.querySelector('#uptime p');
    const memoryTextEl = document.querySelector('#memory p');
    const memoryBarEl = document.querySelector('#memory .progress-bar');
    const cpuTextEl = document.querySelector('#cpu p');
    const cpuBarEl = document.querySelector('#cpu .progress-bar');
    const errorEl = document.getElementById('error-message');

    const fetchAndRenderStats = async () => {
        try {
            const response = await fetch(apiUrl);
            if (!response.ok) {
                throw new Error(`API request failed with status ${response.status}`);
            }
            const stats = await response.json();
            errorEl.textContent = ''; // Clear previous errors

            // Uptime
            uptimeEl.textContent = stats.uptime || 'N/A';

            // Memory
            if (stats.memory) {
                memoryBarEl.style.width = `${stats.memory.percent}%`;
                memoryTextEl.textContent = `${stats.memory.used}MB / ${stats.memory.total}MB`;
            }

            // CPU
            if (stats.cpu) {
                cpuBarEl.style.width = `${stats.cpu.percent}%`;
                cpuTextEl.textContent = `${stats.cpu.percent}%`;
            }

        } catch (error) {
            errorEl.textContent = 'Could not load system stats. Please try again later.';
            console.error('Stats fetch error:', error);
        }
    };

    // Initial load
    fetchAndRenderStats();

    // Refresh every 5 seconds
    setInterval(fetchAndRenderStats, 5000);
});
