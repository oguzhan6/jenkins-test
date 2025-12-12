(async () => {
    const navPlaceholder = document.getElementById('nav-placeholder');
    if (navPlaceholder) {
        try {
            const response = await fetch('nav.html');
            if (!response.ok) throw new Error('Navigation not found');
            const navHtml = await response.text();
            navPlaceholder.innerHTML = navHtml;

            // Add active class to the current page's nav link
            const currentPage = window.location.pathname.split('/').pop();
            if (currentPage) {
                const activeLink = document.querySelector(`.nav-links a[href="${currentPage}"]`);
                if (activeLink) {
                    activeLink.classList.add('active');
                }
            } else {
                 const homeLink = document.querySelector(`.nav-links a[href="index.html"]`);
                 if(homeLink) homeLink.classList.add('active');
            }

        } catch (error) {
            console.error('Failed to load navigation:', error);
            navPlaceholder.innerHTML = '<p style="text-align:center; color:red;">Error: Navigation could not be loaded.</p>';
        }
    }
})();
