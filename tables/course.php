<?php
// Database connection
$host = 'localhost'; // Database host
$db = 'university_db'; // Database name
$user = 'root'; // Database username
$pass = ''; // Database password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Handle form submission for adding or editing a course
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['course_id']) && $_POST['course_id'] != '') {
        // Update existing course
        $stmt = $pdo->prepare("UPDATE course SET title=?, credits=?, dept_name=? WHERE course_id=?");
        $stmt->execute([
            $_POST['title'],
            $_POST['credits'],
            $_POST['dept_name'],
            $_POST['course_id']
        ]);
    } else {
        // Add new course
        $stmt = $pdo->prepare("INSERT INTO course (title, credits, dept_name) VALUES (?, ?, ?)");
        $stmt->execute([
            $_POST['title'],
            $_POST['credits'],
            $_POST['dept_name']
        ]);
    }
    // Redirect back to the same page after form submission
    header("Location: course.php");
    exit();
}

// Handle deletion of a course
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM course WHERE course_id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: course.php");
    exit();
}

// Fetch all courses
$stmt = $pdo->query("SELECT * FROM course");
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if a course is being edited
$courseToEdit = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM course WHERE course_id = ?");
    $stmt->execute([$_GET['edit']]);
    $courseToEdit = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch all departments from the department table for the dropdown
$deptStmt = $pdo->query("SELECT dept_name FROM department");
$departments = $deptStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Course</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 2rem;
            color: #333;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        form h2 {
            margin-bottom: 20px;
            font-size: 1.5rem;
            color: #444;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
            color: #555;
        }

        input, select, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        button {
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #218838;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
        }

        table thead {
            background-color: #007bff;
            color: white;
        }

        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table tbody tr:hover {
            background-color: #f1f1f1;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .actions {
            display: flex;
            gap: 10px;
        }

        .actions a {
            background-color: #007bff;
            padding: 8px 10px;
            border-radius: 4px;
            color: white;
            text-align: center;
        }

        .actions a.delete {
            background-color: #dc3545;
        }

        .actions a:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Manage Course</h1>
    <a href="/fundamentals_lab/fundamentals_act/dashboard.php">Back to dashboard</a>
    <!-- Form for adding or editing courses -->
    <form action="course.php" method="POST">
        <h2><?php echo $courseToEdit ? 'Edit Course' : 'Add Course'; ?></h2>
        
        <?php if ($courseToEdit): ?>
            <input type="hidden" name="course_id" value="<?php echo htmlspecialchars($courseToEdit['course_id']); ?>">
        <?php endif; ?>

        <label for="title">Title:</label>
        <input type="text" id="title" name="title" value="<?php echo $courseToEdit ? htmlspecialchars($courseToEdit['title']) : ''; ?>" required>

        <label for="credits">Credits:</label>
        <input type="number" id="credits" name="credits" value="<?php echo $courseToEdit ? htmlspecialchars($courseToEdit['credits']) : ''; ?>" required>

        <label for="dept_name">Department Name:</label>
        <select id="dept_name" name="dept_name">
            <option value="">Select Department</option>
            <?php foreach ($departments as $department): ?>
                <option value="<?php echo htmlspecialchars($department['dept_name']); ?>" <?php echo $courseToEdit && $courseToEdit['dept_name'] == $department['dept_name'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($department['dept_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit"><?php echo $courseToEdit ? 'Update Course' : 'Add Course'; ?></button>
    </form>

    <!-- Table to display courses -->
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Credits</th>
                <th>Department</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($courses as $course): ?>
        <tr>
            <td><?php echo htmlspecialchars($course['title']); ?></td>
            <td><?php echo htmlspecialchars($course['credits']); ?></td>
            <td><?php echo htmlspecialchars($course['dept_name']); ?></td>
            <td class="actions">
                <a href="course.php?edit=<?php echo htmlspecialchars($course['course_id']); ?>">Edit</a>
                <a href="course.php?delete=<?php echo htmlspecialchars($course['course_id']); ?>" 
                   onclick="return confirm('Are you sure you want to delete this course?');" class="delete">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
