<?php
require_once 'config.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login_faculty.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

$conn = getDBConnection();

$stmt = $conn->prepare("
    SELECT c.course_id, c.course_name, f.faculty_name
    FROM courses c
    JOIN faculty f ON c.faculty_id = f.faculty_id
");
$stmt->execute();
$result = $stmt->get_result();
$courses = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();


$stmt=$conn->prepare("
    SELECT r.course_id, c.course_name, r.student_id, r.approved
    FROM requests r
    JOIN courses c ON r.course_id = c.course_id
    WHERE r.student_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$requests = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();


$stmtmt = $conn->prepare("SELECT * FROM enrollment WHERE student_id = ?");
$stmtmt->bind_param("i", $user_id);
$stmtmt->execute();
$result = $stmtmt->get_result();
$enrollments = $result->fetch_all(MYSQLI_ASSOC);
$stmtmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <script src="student_dashboard.js" defer></script>
</head>
<body>
    <nav>
        <a href="student_dashboard.php">Dashboard</a>
        <a href="add_request.php">Join Course</a>
        <a href="student_mark_attendance.php">Mark Attendance</a>
        <a href="student_reports.php">My Reports</a>
        <a href="logout.php">Logout</a>
    </nav>
    <div class="container">
        <header>
            <h1>Welcome, <?php echo htmlspecialchars($user_name); ?>!</h1>
        </header>
        <main>
            <section id="enrollmentsSection">
                <h2>Your Courses</h2>
                <div id="enrollmentsContainer">
                    <?php if (empty($enrollments)): ?>
                        <p>No enrollments found.</p>
                    <?php else: ?>
                        <ul>
                            <?php foreach ($enrollments as $enrollment): ?>
                                <li>
                                    <strong><?php echo htmlspecialchars($enrollment['course_name']); ?></strong>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </section>
            
            <section id="requestsSection">
                <h2>Your Requests</h2>
                <div id="requestsContainer">
                    <?php if (empty($requests)): ?>
                        <p>No requests found. Please request to join a course.</p>
                    <?php else: ?>
                        <ul>
                            <?php foreach ($requests as $request): ?>
                                <li>
                                    <strong><?php echo htmlspecialchars($request['course_name']); ?></strong> - Status: <?php echo $request['approved'] ? 'Approved' : 'Pending'; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </section>

            <section id="coursesSection">
                <h2>All Available Courses</h2>
                <div id="coursesContainer">
                    <?php if (empty($courses)): ?>
                        <p>No courses found.</p>
                    <?php else: ?>
                        <ul>
                            <?php foreach ($courses as $course): ?>
                                <li>
                                    <strong><?php echo htmlspecialchars($course['course_name']); ?> - Faculty: <?php echo htmlspecialchars($course['faculty_name']); ?></strong>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>
</body>
</html>