document.addEventListener('DOMContentLoaded', function() {
    const announcementsContainer = document.getElementById('announcements-container');
    const loadingState = document.getElementById('announcements-loading');
    const emptyState = document.getElementById('announcements-empty');

    async function loadAnnouncements() {
        loadingState.style.display = 'flex';
        announcementsContainer.style.display = 'none';
        emptyState.style.display = 'none';

        try {
            const response = await fetch('../BackEnd/api/getPublicAnnouncements.php?limit=6');
            const result = await response.json();

            if (result.success && result.data && result.data.length > 0) {
                displayAnnouncements(result.data);
            } else {
                emptyState.style.display = 'block';
                announcementsContainer.style.display = 'none';
            }
        } catch (error) {
            console.error('Error loading announcements:', error);
            emptyState.style.display = 'block';
            announcementsContainer.style.display = 'none';
        } finally {
            loadingState.style.display = 'none';
        }
    }

    function displayAnnouncements(announcements) {
        announcementsContainer.style.display = 'grid';
        emptyState.style.display = 'none';

        announcementsContainer.innerHTML = announcements.map(announcement => {
            const date = new Date(announcement.Date_Publication);
            const formattedDate = date.toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });
            
            const imageHtml = announcement.Image_Path 
                ? `<div class="announcement-image-wrapper">
                     <img src="../../${announcement.Image_Path}" alt="${escapeHtml(announcement.Title)}" class="announcement-image">
                   </div>`
                : '';

            return `
                <div class="announcement-card">
                    ${imageHtml}
                    <div class="announcement-content">
                        <h3 class="announcement-title">${escapeHtml(announcement.Title)}</h3>
                        <p class="announcement-text">${escapeHtml(announcement.Text)}</p>
                        <div class="announcement-date">
                            <i class="fas fa-calendar-alt"></i>
                            <span>${formattedDate}</span>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Initial load
    loadAnnouncements();
});

