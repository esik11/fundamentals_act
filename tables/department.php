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

// Handle form submission for adding or editing a department
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['dep_id']) && $_POST['dep_id'] != '') {
        // Update existing department
        $stmt = $pdo->prepare("UPDATE department SET dept_name=?, building=?, budget=? WHERE dep_id=?");
        $stmt->execute([
            $_POST['dept_name'],
            $_POST['building'],
            $_POST['budget'],
            $_POST['dep_id']
        ]);
    } else {
        // Add new department
        $stmt = $pdo->prepare("INSERT INTO department (dept_name, building, budget) VALUES (?, ?, ?)");
        $stmt->execute([
            $_POST['dept_name'],
            $_POST['building'],
            $_POST['budget']
        ]);
    }
    // Redirect back to the same page after form submission
    header("Location: department.php");
    exit();
}

// Handle deletion of a department
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM department WHERE dep_id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: department.php");
    exit();
}

// Fetch all departments
$stmt = $pdo->query("SELECT * FROM department");
$departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if a department is being edited
$departmentToEdit = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM department WHERE dep_id = ?");
    $stmt->execute([$_GET['edit']]);
    $departmentToEdit = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Department</title>
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

        input, button {
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
    <h1>Manage Department</h1>
    <a href="/fundamentals_lab/fundamentals_act/dashboard.php">Back to dashboard</a>
    <!-- Form for adding or editing departments -->
    <form action="department.php" method="POST">
        <h2><?php echo $departmentToEdit ? 'Edit Department' : 'Add Department'; ?></h2>
        
        <?php if ($departmentToEdit): ?>
            <input type="hidden" name="dep_id" value="<?php echo htmlspecialchars($departmentToEdit['dep_id']); ?>">
        <?php endif; ?>

        <label for="dept_name">Department Name:</label>
        <input type="text" id="dept_name" name="dept_name" value="<?php echo $departmentToEdit ? htmlspecialchars($departmentToEdit['dept_name']) : ''; ?>" required>

        <label for="building">Building:</label>
        <input type="text" id="building" name="building" value="<?php echo $departmentToEdit ? htmlspecialchars($departmentToEdit['building']) : ''; ?>" required>

        <label for="budget">Budget:</label>
        <input type="number" id="budget" name="budget" value="<?php echo $departmentToEdit ? htmlspecialchars($departmentToEdit['budget']) : ''; ?>" required>

        <button type="submit"><?php echo $departmentToEdit ? 'Update Department' : 'Add Department'; ?></button>
    </form>

    <!-- Table to display departments -->
    <table>
        <thead>
            <tr>
                <th>Department Name</th>
                <th>Building</th>
                <th>Budget</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($departments as $department): ?>
        <tr>
            <td><?php echo htmlspecialchars($department['dept_name']); ?></td>
            <td><?php echo htmlspecialchars($department['building']); ?></td>
            <td><?php echo htmlspecialchars($department['budget']); ?></td>
            <td class="actions">
                <a href="department.php?edit=<?php echo htmlspecialchars($department['dep_id']); ?>">Edit</a>
                <a href="department.php?delete=<?php echo htmlspecialchars($department['dep_id']); ?>" 
                   onclick="return confirm('Are you sure you want to delete this department?');" class="delete">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
