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

// Handle form submission for adding or editing a student
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['ID']) && $_POST['ID'] != '') {
        // Update existing student
        $stmt = $pdo->prepare("UPDATE student SET first_name=?, middle_name=?, last_name=?, tot_cred=?, dept_name=? WHERE ID=?");
        $stmt->execute([
            $_POST['first_name'],
            $_POST['middle_name'],
            $_POST['last_name'],
            $_POST['tot_cred'],
            $_POST['dept_name'],
            $_POST['ID']
        ]);
    } else {
        // Add new student
        $stmt = $pdo->prepare("INSERT INTO student (first_name, middle_name, last_name, tot_cred, dept_name) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['first_name'],
            $_POST['middle_name'],
            $_POST['last_name'],
            $_POST['tot_cred'],
            $_POST['dept_name']
        ]);
    }
    // Redirect back to the same page after form submission
    header("Location: student.php");
    exit();
}

// Handle deletion of a student
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM student WHERE ID = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: student.php");
    exit();
}

// Fetch all students
$stmt = $pdo->query("SELECT * FROM student");
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if a student is being edited
$studentToEdit = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM student WHERE ID = ?");
    $stmt->execute([$_GET['edit']]);
    $studentToEdit = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch all departments for the dropdown
$deptStmt = $pdo->query("SELECT dept_name FROM department");
$departments = $deptStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students</title>
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
    <h1>Manage Students</h1>
    <a href="/fundamentals_lab/fundamentals_act/dashboard.php">Back to dashboard</a>
    <!-- Form for adding or editing students -->
    <form action="student.php" method="POST">
        <h2><?php echo $studentToEdit ? 'Edit Student' : 'Add Student'; ?></h2>
        
        <?php if ($studentToEdit): ?>
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($studentToEdit['ID']); ?>">
        <?php endif; ?>

        <label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name" value="<?php echo $studentToEdit ? htmlspecialchars($studentToEdit['first_name']) : ''; ?>" required>

        <label for="middle_name">Middle Name:</label>
        <input type="text" id="middle_name" name="middle_name" value="<?php echo $studentToEdit ? htmlspecialchars($studentToEdit['middle_name']) : ''; ?>">

        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" name="last_name" value="<?php echo $studentToEdit ? htmlspecialchars($studentToEdit['last_name']) : ''; ?>" required>

        <label for="tot_cred">Total Credits:</label>
        <input type="number" id="tot_cred" name="tot_cred" value="<?php echo $studentToEdit ? htmlspecialchars($studentToEdit['tot_cred']) : ''; ?>" required>

        <label for="dept_name">Department Name:</label>
        <select id="dept_name" name="dept_name">
            <option value="">Select Department</option>
            <?php foreach ($departments as $department): ?>
                <option value="<?php echo htmlspecialchars($department['dept_name']); ?>" <?php echo $studentToEdit && $studentToEdit['dept_name'] == $department['dept_name'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($department['dept_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit"><?php echo $studentToEdit ? 'Update Student' : 'Add Student'; ?></button>
    </form>

    <!-- Table to display students -->
    <table>
        <thead>
            <tr>
                <th>First Name</th>
                <th>Middle Name</th>
                <th>Last Name</th>
                <th>Total Credits</th>
                <th>Department</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
    <?php foreach ($students as $student): ?>
    <tr>
        <td><?php echo htmlspecialchars($student['first_name']); ?></td>
        <td><?php echo htmlspecialchars($student['middle_name']); ?></td>
        <td><?php echo htmlspecialchars($student['last_name']); ?></td>
        <td><?php echo htmlspecialchars($student['tot_cred']); ?></td>
        <td><?php echo htmlspecialchars($student['dept_name']); ?></td>
        <td class="actions">
            <a href="student.php?edit=<?php echo htmlspecialchars($student['ID']); ?>">Edit</a>
            <a href="student.php?delete=<?php echo htmlspecialchars($student['ID']); ?>" 
               onclick="return confirm('Are you sure you want to delete this student?');" class="delete">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</tbody>

    </table>
</div>

</body>
</html>
