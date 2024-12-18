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

// Handle form submission for adding or editing a section
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['sec_id']) && $_POST['sec_id'] != '') {
        // Update existing section
        $stmt = $pdo->prepare("UPDATE section SET course_id=?, semester=?, year=? WHERE sec_id=?");
        $stmt->execute([
            $_POST['course_id'],
            $_POST['semester'],
            $_POST['year'],
            $_POST['sec_id']
        ]);
    } else {
        // Add new section
        $stmt = $pdo->prepare("INSERT INTO section (course_id, semester, year) VALUES (?, ?, ?)");
        $stmt->execute([
            $_POST['course_id'],
            $_POST['semester'],
            $_POST['year']
        ]);
    }
    // Redirect back to the same page after form submission
    header("Location: section.php");
    exit();
}

// Handle deletion of a section
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM section WHERE sec_id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: section.php");
    exit();
}

// Fetch all sections
$stmt = $pdo->query("SELECT * FROM section");
$sections = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Function to get course title by course_id
function getCourseTitle($pdo, $course_id) {
    $stmt = $pdo->prepare("SELECT title FROM course WHERE course_id = ?");
    $stmt->execute([$course_id]);
    $course = $stmt->fetch(PDO::FETCH_ASSOC);
    return $course ? $course['title'] : 'Unknown Course';
}

// Check if a section is being edited
$sectionToEdit = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM section WHERE sec_id = ?");
    $stmt->execute([$_GET['edit']]);
    $sectionToEdit = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch all courses for the dropdown
$courseStmt = $pdo->query("SELECT course_id, title FROM course");
$courses = $courseStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Sections</title>
    <style>
        /* Add your styles here */
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
    <h1>Manage Sections</h1>
    <a href="/fundamentals_lab/fundamentals_act/dashboard.php">Back to dashboard</a>
    <!-- Form for adding or editing sections -->
    <form action="section.php" method="POST">
        <h2><?php echo $sectionToEdit ? 'Edit Section' : 'Add Section'; ?></h2>
        
        <?php if ($sectionToEdit): ?>
            <input type="hidden" name="sec_id" value="<?php echo htmlspecialchars($sectionToEdit['sec_id']); ?>">
        <?php endif; ?>

        <label for="course_id">Course:</label>
        <select id="course_id" name="course_id" required>
            <option value="">Select Course</option>
            <?php foreach ($courses as $course): ?>
                <option value="<?php echo htmlspecialchars($course['course_id']); ?>" <?php echo $sectionToEdit && $sectionToEdit['course_id'] == $course['course_id'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($course['title']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="semester">Semester:</label>
        <input type="text" id="semester" name="semester" value="<?php echo $sectionToEdit ? htmlspecialchars($sectionToEdit['semester']) : ''; ?>" required>

        <label for="year">Year:</label>
        <input type="number" id="year" name="year" value="<?php echo $sectionToEdit ? htmlspecialchars($sectionToEdit['year']) : ''; ?>" required>

        <button type="submit"><?php echo $sectionToEdit ? 'Update Section' : 'Add Section'; ?></button>
    </form>

    <!-- Table to display sections -->
    <table>
        <thead>
            <tr>
                <th>Course Title</th> <!-- Updated header to reflect course title -->
                <th>Semester</th>
                <th>Year</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($sections as $section): ?>
        <tr>
            <td><?php echo htmlspecialchars(getCourseTitle($pdo, $section['course_id'])); ?></td> <!-- Display course title -->
            <td><?php echo htmlspecialchars($section['semester']); ?></td>
            <td><?php echo htmlspecialchars($section['year']); ?></td>
            <td class="actions">
                <a href="section.php?edit=<?php echo htmlspecialchars($section['sec_id']); ?>">Edit</a>
                <a href="section.php?delete=<?php echo htmlspecialchars($section['sec_id']); ?>" 
                   onclick="return confirm('Are you sure you want to delete this section?');" class="delete">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
