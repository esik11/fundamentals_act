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

// Handle form submission for adding or editing an instructor
// Handle form submission for adding or editing an instructor
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'] ?? ''; // Handle optional middle name
    $last_name = $_POST['last_name'];
    $street_number = $_POST['street_number'] ?? '';
    $street_name = $_POST['street_name'] ?? '';
    $apt_number = $_POST['apt_number'] ?? '';
    $city = $_POST['city'] ?? '';
    $state = $_POST['state'] ?? '';
    $postal_code = $_POST['postal_code'] ?? '';
    $date_of_birth = $_POST['date_of_birth'];
    $salary = $_POST['salary'] !== '' ? $_POST['salary'] : null; // Set to NULL if empty
    $dept_name = $_POST['dept_name'] !== '' ? $_POST['dept_name'] : null; // Set to NULL if empty

    if (isset($_POST['id']) && $_POST['id'] != '') {
        // Update existing instructor
        $stmt = $pdo->prepare("UPDATE instructor SET first_name=?, middle_name=?, last_name=?, street_number=?, street_name=?, apt_number=?, city=?, state=?, postal_code=?, date_of_birth=?, salary=?, dept_name=? WHERE id=?");
        $stmt->execute([
            $first_name,
            $middle_name,
            $last_name,
            $street_number,
            $street_name,
            $apt_number,
            $city,
            $state,
            $postal_code,
            $date_of_birth,
            $salary,
            $dept_name,
            $_POST['id']
        ]);
    } else {
        // Add new instructor
        $stmt = $pdo->prepare("INSERT INTO instructor (first_name, middle_name, last_name, street_number, street_name, apt_number, city, state, postal_code, date_of_birth, salary, dept_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $first_name,
            $middle_name,
            $last_name,
            $street_number,
            $street_name,
            $apt_number,
            $city,
            $state,
            $postal_code,
            $date_of_birth,
            $salary,
            $dept_name
        ]);
    }
    // Redirect back to the same page after form submission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}


// Handle deletion of an instructor
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM instructor WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch all instructors
$stmt = $pdo->query("SELECT * FROM instructor");
$instructors = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if an instructor is being edited
$instructorToEdit = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM instructor WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $instructorToEdit = $stmt->fetch(PDO::FETCH_ASSOC);
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
    <title>Manage Instructor</title>
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
    <h1>Manage Instructor</h1>
    <a href="/fundamentals_lab/fundamentals_act/dashboard.php">Back to dashboard</a>
    <!-- Form for adding or editing instructors -->
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <h2><?php echo $instructorToEdit ? 'Edit Instructor' : 'Add Instructor'; ?></h2>

        <?php if ($instructorToEdit): ?>
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($instructorToEdit['ID']); ?>">
        <?php endif; ?>

        <label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name" value="<?php echo $instructorToEdit ? htmlspecialchars($instructorToEdit['first_name']) : ''; ?>" required>

        <label for="middle_name">Middle Name:</label>
        <input type="text" id="middle_name" name="middle_name" value="<?php echo $instructorToEdit ? htmlspecialchars($instructorToEdit['middle_name']) : ''; ?>">

        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" name="last_name" value="<?php echo $instructorToEdit ? htmlspecialchars($instructorToEdit['last_name']) : ''; ?>" required>

        <label for="street_number">Street Number:</label>
        <input type="text" id="street_number" name="street_number" value="<?php echo $instructorToEdit ? htmlspecialchars($instructorToEdit['street_number']) : ''; ?>">

        <label for="street_name">Street Name:</label>
        <input type="text" id="street_name" name="street_name" value="<?php echo $instructorToEdit ? htmlspecialchars($instructorToEdit['street_name']) : ''; ?>">

        <label for="apt_number">Apartment Number:</label>
        <input type="text" id="apt_number" name="apt_number" value="<?php echo $instructorToEdit ? htmlspecialchars($instructorToEdit['apt_number']) : ''; ?>">

        <label for="city">City:</label>
        <input type="text" id="city" name="city" value="<?php echo $instructorToEdit ? htmlspecialchars($instructorToEdit['city']) : ''; ?>">

        <label for="state">State:</label>
        <input type="text" id="state" name="state" value="<?php echo $instructorToEdit ? htmlspecialchars($instructorToEdit['state']) : ''; ?>">

        <label for="postal_code">Postal Code:</label>
        <input type="text" id="postal_code" name="postal_code" value="<?php echo $instructorToEdit ? htmlspecialchars($instructorToEdit['postal_code']) : ''; ?>">

        <label for="date_of_birth">Date of Birth:</label>
        <input type="date" id="date_of_birth" name="date_of_birth" value="<?php echo $instructorToEdit ? htmlspecialchars($instructorToEdit['date_of_birth']) : ''; ?>" required>

        <label for="salary">Salary:</label>
        <input type="number" id="salary" name="salary" value="<?php echo $instructorToEdit ? htmlspecialchars($instructorToEdit['salary']) : ''; ?>" >

        <label for="dept_name">Department:</label>
        <select id="dept_name" name="dept_name" >
            <option value="">--Select Department--</option>
            <?php foreach ($departments as $department): ?>
                <option value="<?php echo htmlspecialchars($department['dept_name']); ?>" <?php echo $instructorToEdit && $instructorToEdit['dept_name'] === $department['dept_name'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($department['dept_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit"><?php echo $instructorToEdit ? 'Update' : 'Add'; ?> Instructor</button>
    </form>

    <!-- Display list of instructors -->
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>First Name</th>
            <th>Middle Name</th>
            <th>Last Name</th>
            <th>Department</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($instructors as $instructor): ?>
            <tr>
                <td><?php echo htmlspecialchars($instructor['ID']); ?></td>
                <td><?php echo htmlspecialchars($instructor['first_name']); ?></td>
                <td><?php echo htmlspecialchars($instructor['middle_name']); ?></td>
                <td><?php echo htmlspecialchars($instructor['last_name']); ?></td>
                <td><?php echo htmlspecialchars($instructor['dept_name']); ?></td>
                <td class="actions">
                    <a href="?edit=<?php echo htmlspecialchars($instructor['ID']); ?>">Edit</a>
                    <a href="?delete=<?php echo htmlspecialchars($instructor['ID']); ?>" class="delete" onclick="return confirm('Are you sure you want to delete this instructor?');">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
