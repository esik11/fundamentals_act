<?php
// Database connection
$host = 'localhost'; // Database host
$db = 'university_db3'; // Database name
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
    // Handle nullable fields by assigning NULL if empty
    $course_code = $_POST['course_code'];
    $title = $_POST['title'];
    $credits = !empty($_POST['credits']) ? $_POST['credits'] : NULL; // Credits can be NULL if empty
    $dept_name = !empty($_POST['dept_name']) ? $_POST['dept_name'] : NULL; // Department can be NULL if empty

    if (isset($_POST['original_course_code']) && $_POST['original_course_code'] != '') {
        // Update existing course
        $stmt = $pdo->prepare("UPDATE course SET course_code=?, title=?, credits=?, dept_name=? WHERE course_code=?");
        $stmt->execute([$course_code, $title, $credits, $dept_name, $_POST['original_course_code']]);
    } else {
        // Add new course
        $stmt = $pdo->prepare("INSERT INTO course (course_code, title, credits, dept_name) VALUES (?, ?, ?, ?)");
        $stmt->execute([$course_code, $title, $credits, $dept_name]);
    }
    // Redirect back to the same page after form submission
    header("Location: course.php");
    exit();
}

// Handle deletion of a course
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM course WHERE course_code = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: course.php");
    exit();
}

// Fetch all courses
$stmt = $pdo->query("SELECT * FROM course");
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all departments for selection in form
$stmt = $pdo->query("SELECT dept_name FROM department");
$departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if a course is being edited
$courseToEdit = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM course WHERE course_code = ?");
    $stmt->execute([$_GET['edit']]);
    $courseToEdit = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Course</title>
    <style>
        /* Add your styles here */
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        form {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input, select, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
        }
        button {
            background-color: #28a745;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: #fff;
        }
        .actions a {
            margin-right: 10px;
            color: #007bff;
        }
        .actions a.delete {
            color: #dc3545;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Manage Course</h1>
    <form action="course.php" method="POST">
        <h2><?php echo $courseToEdit ? 'Edit Course' : 'Add Course'; ?></h2>
        
        <?php if ($courseToEdit): ?>
            <input type="hidden" name="original_course_code" value="<?php echo htmlspecialchars($courseToEdit['course_code']); ?>">
        <?php endif; ?>

        <label for="course_code">Course Code:</label>
        <input type="text" id="course_code" name="course_code" value="<?php echo $courseToEdit ? htmlspecialchars($courseToEdit['course_code']) : ''; ?>" required>

        <label for="title">Title:</label>
        <input type="text" id="title" name="title" value="<?php echo $courseToEdit ? htmlspecialchars($courseToEdit['title']) : ''; ?>" required>

        <label for="credits">Credits:</label>
        <input type="number" id="credits" name="credits" value="<?php echo $courseToEdit ? htmlspecialchars($courseToEdit['credits']) : ''; ?>" >

        <label for="dept_name">Department:</label>
        <select id="dept_name" name="dept_name" >
            <option value="">-- Select Department --</option> <!-- Option for NULL value -->
            <?php foreach ($departments as $department): ?>
                <option value="<?php echo htmlspecialchars($department['dept_name']); ?>" 
                    <?php echo $courseToEdit && $courseToEdit['dept_name'] == $department['dept_name'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($department['dept_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit"><?php echo $courseToEdit ? 'Update Course' : 'Add Course'; ?></button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Course Code</th>
                <th>Title</th>
                <th>Credits</th>
                <th>Department</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($courses as $course): ?>
            <tr>
                <td><?php echo htmlspecialchars($course['course_code']); ?></td>
                <td><?php echo htmlspecialchars($course['title']); ?></td>
                <td><?php echo htmlspecialchars($course['credits']); ?></td>
                <td><?php echo htmlspecialchars($course['dept_name']); ?></td>
                <td class="actions">
                    <a href="course.php?edit=<?php echo htmlspecialchars($course['course_code']); ?>">Edit</a>
                    <a href="course.php?delete=<?php echo htmlspecialchars($course['course_code']); ?>" 
                       onclick="return confirm('Are you sure you want to delete this course?');" class="delete">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
