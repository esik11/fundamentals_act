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

// Handle form submission for adding or editing a department
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle nullable fields by assigning NULL if empty
    $dept_name = $_POST['dept_name'];
    $building = !empty($_POST['building']) ? $_POST['building'] : null;
    $budget = !empty($_POST['budget']) ? $_POST['budget'] : null;

    if (isset($_POST['original_dept_name']) && $_POST['original_dept_name'] != '') {
        // Update existing department
        $stmt = $pdo->prepare("UPDATE department SET dept_name=?, building=?, budget=? WHERE dept_name=?");
        $stmt->execute([$dept_name, $building, $budget, $_POST['original_dept_name']]);
    } else {
        // Add new department
        $stmt = $pdo->prepare("INSERT INTO department (dept_name, building, budget) VALUES (?, ?, ?)");
        $stmt->execute([$dept_name, $building, $budget]);
    }
    // Redirect back to the same page after form submission
    header("Location: department.php");
    exit();
}

// Handle deletion of a department
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM department WHERE dept_name = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: department.php");
    exit();
}

// Fetch all departments
$stmt = $pdo->query("SELECT * FROM department");
$departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all buildings from the classroom table (ensure your classroom table has a 'building' column)
$stmt = $pdo->query("SELECT DISTINCT building FROM classroom"); // Changed to classroom table
$buildings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if a department is being edited
$departmentToEdit = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM department WHERE dept_name = ?");
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
    <h1>Manage Department</h1>
    <form action="department.php" method="POST">
        <h2><?php echo $departmentToEdit ? 'Edit Department' : 'Add Department'; ?></h2>
        
        <?php if ($departmentToEdit): ?>
            <input type="hidden" name="original_dept_name" value="<?php echo htmlspecialchars($departmentToEdit['dept_name']); ?>">
        <?php endif; ?>

        <!-- Dropdown for building -->
        <label for="building">Building:</label>
        <select id="building" name="building" required>
            <option value="">Select Building</option>
            <?php foreach ($buildings as $buildingOption): ?>
                <option value="<?php echo htmlspecialchars($buildingOption['building']); ?>" 
                        <?php echo $departmentToEdit && $departmentToEdit['building'] == $buildingOption['building'] ? 'selected' : ''; ?> >
                    <?php echo htmlspecialchars($buildingOption['building']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="dept_name">Department Name:</label>
        <input type="text" id="dept_name" name="dept_name" value="<?php echo $departmentToEdit ? htmlspecialchars($departmentToEdit['dept_name']) : ''; ?>" required>

        <label for="budget">Budget:</label>
        <input type="number" id="budget" name="budget" value="<?php echo $departmentToEdit ? htmlspecialchars($departmentToEdit['budget']) : ''; ?>">

        <button type="submit"><?php echo $departmentToEdit ? 'Update Department' : 'Add Department'; ?></button>
    </form>

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
                    <a href="department.php?edit=<?php echo htmlspecialchars($department['dept_name']); ?>">Edit</a>
                    <a href="department.php?delete=<?php echo htmlspecialchars($department['dept_name']); ?>" 
                       onclick="return confirm('Are you sure you want to delete this department?');" class="delete">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
