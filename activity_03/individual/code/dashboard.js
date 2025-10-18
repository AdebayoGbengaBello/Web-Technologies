async function loadDashboardData() {
    try {
        const response = await fetch('dashboard_api.php');
        if (!response.ok) {
            throw new Error(`API error: ${response.status}`);
        }
        const data = await response.json();
        document.getElementById('courseCount').textContent = data.summary.courseCount + ' Active';
        document.getElementById('averageProgress').textContent = data.summary.averageProgress + '%';
        document.getElementById('totalHours').textContent = data.summary.totalHours + ' Hours';
        loadSessions(data.sessions);
    } catch (error) {
        console.error('Failed to load dashboard data:', error);
        document.getElementById('sessionList').innerHTML = '<p class="error">Error loading sessions.</p>';
    }
}

function loadSessions(sessions) {
    const sessionList = document.getElementById('sessionList');
    if (sessions.length === 0) {
        sessionList.innerHTML = '<p>No upcoming sessions.</p>';
        return;
    }
    let html = '';
    sessions.forEach(session => {
        const sessionDate = new Date(session.sessionDate).toLocaleDateString();
        html += `
            <div class="session-item">
                <h3>${session.sessionTitle} (${session.courseName})</h3>
                <p><strong>Type:</strong> ${session.sessionType}</p>
                <p><strong>Date:</strong> ${sessionDate} at ${session.sessionTime}</p>
                ${session.notes ? `<p><strong>Notes:</strong> ${session.notes}</p>` : ''}
            </div>
        `;
    });
    sessionList.innerHTML = html;
}

document.addEventListener('DOMContentLoaded', loadDashboardData);