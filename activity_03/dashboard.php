<?php
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Not logged in, redirect to login page
    header("Location: login.php");
    exit();
}

// Get user info from session
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        /* Your existing dashboard CSS here */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logout-btn {
            background: white;
            color: #667eea;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
        }    
        
        .error {
            color: #d32f2f;
            background-color: #ffebee;
            padding: 10px;
            border-radius: 4px;
            border-left: 4px solid #d32f2f;
        }   
        
    </style>
</head>
<body>
    <div class="header">
        <h1>Welcome, <?php echo htmlspecialchars($user_name); ?>!</h1>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
    
    <!-- Your existing dashboard HTML here -->
    <div style="padding: 20px;">
        <p>User ID: <?php echo $user_id; ?></p>
        <p>You are successfully logged in!</p>
        <p>// for studnts access</p>
        

    <!DOCTYPE html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>User Dashboard</title> 
        <style>


            /* Reset default browser styles */
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box; /* Makes width/height include padding and border */
            }

            body {
                font-family: Arial, sans-serif;
                background-color: #f0f0f0;
            }

            /* FLEXBOX container - holds sidebar and main content side by side */
            .container {
                display: flex; /* Children will be placed horizontally */
            }

            /* SIDEBAR NAVIGATION - Uses <aside> semantic tag for supplementary content */
            aside {
                width: 200px; /* Fixed width for sidebar */
                background-color: #5050d8;
                min-height: 100vh; /* Full viewport height */
                padding: 20px;
            }

            /* Navigation using <nav> semantic tag - indicates navigation section */
            nav {
                display: flex;
                flex-direction: column; /* Stack links vertically */
                gap: 10px; /* Space between navigation links */
            }

            /* Style for navigation links */
            nav a {
                color: white;
                text-decoration: none; /* Remove underline */
                padding: 10px;
                border-radius: 5px;
                transition: background-color 0.3s; /* Smooth color change on hover */
            }

            /* Hover effect - changes background when mouse is over link */
            nav a:hover {
                background-color: rgba(255, 255, 255, 0.2); /* Semi-transparent white */
            }

            /* MAIN CONTENT - Uses <main> semantic tag for primary content */
            main {
                flex: 1; /* Takes up remaining space after sidebar */
                padding: 30px;
            }

            /* Header styling */
            h1 {
                color: #333;
                margin-bottom: 30px;
            }

            h2 {
                color: #333;
                margin-bottom: 15px;
                margin-top: 30px;
            }

            /* GRID for top summary cards */
            .card-grid {
                display: grid; /* Creates a grid layout */
                grid-template-columns: repeat(3, 1fr); /* 3 equal columns */
                gap: 20px; /* Space between grid items */
                margin-bottom: 30px;
            }

            /* Individual card styling */
            .card {
                background-color: white;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1); /* Subtle shadow for depth */
            }

           
            section {
                background-color: white;
                padding: 20px;
                border-radius: 8px;
                margin-bottom: 20px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }

          
            .course-item {
                border: 1px solid #ddd;
                padding: 15px;
                margin-bottom: 10px;
                border-radius: 5px;
            }

            .course-item h3 {
                color: #5050d8;
                margin-bottom: 5px;
            }

            .course-item p {
                color: #666;
                font-size: 14px;
            }

            /* Progress bar container */
            .progress-bar {
                width: 100%;
                height: 10px;
                background-color: #e0e0e0;
                border-radius: 5px;
                margin-top: 10px;
                overflow: hidden; 
            }

           
            .progress-fill {
                height: 100%;
                background-color: #5050d8;
                transition: width 0.5s; 
            }

            /* Session item styling */
            .session-item {
                border-left: 4px solid #5050d8;
                padding: 10px;
                margin-bottom: 10px;
                background-color: #f9f9f9;
            }

            .session-item h3 {
                color: #333;
                margin-bottom: 5px;
            }

           
            @media (max-width: 768px) {
                .container {
                    flex-direction: column; /* Stack sidebar and main vertically */
                }
                
                aside {
                    width: 100%; /* Sidebar takes full width on mobile */
                    min-height: auto;
                }
                
                nav {
                    flex-direction: row; /* Links side by side on mobile */
                }
                
                .card-grid {
                    grid-template-columns: 1fr; /* Single column on mobile */
                }
            }
        </style>
    </head>
    <body>
        <!-- Main container using flexbox -->
        <div class="container">
            
            <!-- ASIDE: Semantic tag for sidebar content -->
            <aside>
                <!-- NAV: Semantic tag specifically for navigation links -->
                <nav>
                    <!-- Anchor tags with href="#id" for smooth scrolling to sections -->
                    <a href="#home">Home</a>
                    <a href="courses.php">Courses</a>
                    <a href="sessions.php">Sessions</a>
                    <a href="#reports.php">Reports</a>
                </nav>
            </aside>

            <!-- MAIN: Semantic tag for the primary content of the page -->
            <main>
                <!-- Page header -->
                <h1>Welcome to User Dashboard</h1>

                <!-- Grid layout for summary cards -->
                <div class="card-grid">
                    <div class="card">
                        <h2>Courses</h2>
                        <!-- ID attribute allows JavaScript to find and update this element -->
                        <p id="courseCount">Loading...</p>
                    </div>
                    <div class="card">
                        <h2>Sessions</h2>
                        <p id="sessionCount">Loading...</p>
                    </div>
                    <div class="card">
                        <h2>Report</h2>
                        <p id="reportSummary">Loading...</p>
                    </div>
                </div>

             
                <section id="courses">
                    <h2>Course List</h2>
                    
                    <div id="courseList">
                        <p>Loading courses...</p>
                    </div>
                </section>

                <section id="sessions">
                    <h2>Upcoming Sessions</h2>
                    <div id="sessionList">
                        <p>Loading sessions...</p>
                    </div>
                </section>

                <section id="reports">
                    <h2>Performance Report</h2>
                    <div id="reportContent">
                        <p>Loading report...</p>
                    </div>
                </section>
            </main>
        </div>

        <script>
   
    const API_BASE = '';

    // FUNCTION: Fetch data from API with authentication
    async function fetchFromAPI(endpoint) {
        try {
            const response = await fetch(endpoint);
            if (!response.ok) {
                throw new Error(`API error: ${response.status}`);
            }
            return await response.json();
        } catch (error) {
            console.error('Fetch error:', error);
            throw error;
        }
    }

    // ASYNC FUNCTION: Load all dashboard data
    async function loadDashboardData() {
        try {
            const data = await fetchFromAPI('dashboard_api.php');
            
            // Update summary cards
            document.getElementById('courseCount').textContent = 
                data.summary.courseCount + ' Active Courses';
            document.getElementById('sessionCount').textContent = 
                data.sessions.length + ' Upcoming Sessions';
            document.getElementById('reportSummary').textContent = 
                data.summary.averageProgress + '% Average Progress';
            
            // Load courses
            loadCourses(data.courses);
            
            // Load sessions
            loadSessions(data.sessions);
            
            // Load report
            loadReport(data.summary);
            
        } catch (error) {
            // Handle errors gracefully
            document.getElementById('courseList').innerHTML = 
                '<p class="error">Error loading courses. Please try again.</p>';
            document.getElementById('sessionList').innerHTML = 
                '<p class="error">Error loading sessions. Please try again.</p>';
            document.getElementById('reportContent').innerHTML = 
                '<p class="error">Error loading report. Please try again.</p>';
            
            // Set default values for cards
            document.getElementById('courseCount').textContent = 'Error';
            document.getElementById('sessionCount').textContent = 'Error';
            document.getElementById('reportSummary').textContent = 'Error';
        }
    }

    function loadCourses(courses) {
        const courseList = document.getElementById('courseList');
        
        if (courses.length === 0) {
            courseList.innerHTML = '<p>No courses enrolled yet.</p>';
            return;
        }
        
        let html = '';
        courses.forEach(course => {
            html += `
                <div class="course-item">
                    <h3>${course.courseName}</h3>
                    <p>Instructor: ${course.instructorName}</p>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: ${course.progress_percentage}%"></div>
                    </div>
                    <p>${course.progress_percentage}% Complete</p>
                </div>
            `;
        });
        
        courseList.innerHTML = html;
    }

    function loadSessions(sessions) {
        const sessionList = document.getElementById('sessionList');
        
        if (sessions.length === 0) {
            sessionList.innerHTML = '<p>No upcoming sessions.</p>';
            return;
        }
        
        let html = '';
        sessions.forEach(session => {
            // Format date for better display
            const sessionDate = new Date(session.sessionDate).toLocaleDateString();
            html += `
                <div class="session-item">
                    <h3>${session.sessionTitle}</h3>
                    <p><strong>Course:</strong> ${session.courseName}</p>
                    <p><strong>Date:</strong> ${sessionDate} at ${session.sessionTime}</p>
                </div>
            `;
        });
        
        sessionList.innerHTML = html;
    }

    function loadReport(summary) {
        const reportContent = document.getElementById('reportContent');
        
        reportContent.innerHTML = `
            <div class="course-item">
                <h3>Learning Summary</h3>
                <p><strong>Total Courses:</strong> ${summary.courseCount}</p>
                <p><strong>Average Progress:</strong> ${summary.averageProgress}%</p>
                <p><strong>Total Learning Hours:</strong> ${summary.totalHours} hours</p>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: ${summary.averageProgress}%"></div>
                </div>
            </div>
        `;
    }

    // EVENT LISTENER: Load data when page is ready
    document.addEventListener('DOMContentLoaded', function() {
        loadDashboardData();
        
        // Add smooth scrolling for navigation
        document.querySelectorAll('nav a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href').substring(1);
                const targetSection = document.getElementById(targetId);
                if (targetSection) {
                    targetSection.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    });

    // Add error styling to CSS
    const style = document.createElement('style');
    style.textContent = `
        .error {
            color: #d32f2f;
            background-color: #ffebee;
            padding: 10px;
            border-radius: 4px;
            border-left: 4px solid #d32f2f;
        }
    `;
    document.head.appendChild(style);
</script>
    </body>
    </html>
        </div>
    </body>
    </html>