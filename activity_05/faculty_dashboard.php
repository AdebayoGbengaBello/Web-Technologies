<?php
require_once 'config.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login_faculty.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve_request_id'])) {
    $request_id_to_approve = $_POST['approve_request_id'];
    
    $conn = getDBConnection();
    $update_stmt = $conn->prepare("
        UPDATE requests r
        JOIN courses c ON r.course_id = c.course_id
        SET r.approved = 1
        WHERE r.request_id = ? AND c.faculty_id = ?
    ");
    
    $update_stmt->bind_param("ii", $request_id_to_approve, $user_id);
    
    if ($update_stmt->execute()) {
        $update_stmt->close();
        $enrolstmt = $conn->prepare("
            INSERT INTO enrollment (student_id, course_id, course_name, student_name)
            SELECT r.student_id, r.course_id, c.course_name, s.student_name
            FROM requests r
            JOIN courses c ON r.course_id = c.course_id
            JOIN students s ON r.student_id = s.student_id
            WHERE r.request_id = ? AND c.faculty_id = ?");
        $enrolstmt->bind_param("ii", $request_id_to_approve, $user_id);
        $enrolstmt->execute();
        $conn->close();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
    $update_stmt->close();
}

$conn = getDBConnection();
$stmt = $conn->prepare("
    SELECT c.course_id, c.course_name
    FROM courses c
    WHERE c.faculty_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();  
$courses = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();

$stmt=$conn->prepare("
    SELECT r.request_id, r.course_id, c.course_name, s.student_name, r.approved
    FROM requests r
    JOIN courses c ON r.course_id = c.course_id
    JOIN students s ON r.student_id = s.student_id
    WHERE c.faculty_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$requests = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <a href="add_course.php">Add New Course</a>
        <a href="logout.php">Logout</a>
        <a href="faculty_sessions.php">View Sessions</a>
    </nav>
    <div class="container">
        <header>
            <h1>Welcome, <?php echo htmlspecialchars($user_name); ?>!</h1>
        </header>
        <main>
            <section id="coursesSection">
                <h2>Your Courses</h2>
                <div id="coursesContainer">
                    <?php if (empty($courses)): ?>
                        <p>No courses found. Please add a new course.</p>
                    <?php else: ?>
                        <ul>
                            <?php foreach ($courses as $course): ?>
                                <li>
                                    <strong><?php echo htmlspecialchars($course['course_name']); ?></strong>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </section>
            <section id="requestsSection">
                <h2>Student Requests</h2>
                <div id="requestsContainer">
                    <?php if (empty($requests)): ?>
                        <p>No student requests at the moment.</p>
                    <?php else: ?>
                        <ul>
                            <?php foreach ($requests as $request): ?>
                                <li>
                                    <strong><?php echo htmlspecialchars($request['student_name']); ?></strong> requested to join <em><?php echo htmlspecialchars($request['course_name']); ?></em> - 
                                    Status: <?php echo $request['approved'] ? 'Approved' : 'Pending'; ?>
                                    <?php if (!$request['approved']): ?>
                                       <form method="POST" action="" style="display:inline;">
                                            <input type="hidden" name="approve_request_id" value="<?php echo $request['request_id']; ?>">
                                            <button type="submit" class="approveBtn">Approve</button>
                                        </form>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
        </main>
    </div>
</body>
</html>