<?php
// Database connection setup
$host = 'localhost';
$dbname = 'university_db3';
$username = 'root';
$password = '';
$pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Fetch instructors and their department names for the list
$stmt = $pdo->query("SELECT instructor.*, department.dept_name FROM instructor
                     LEFT JOIN department ON instructor.dept_name = department.dept_name");
$instructors = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch department names for the dropdown
$stmt = $pdo->query("SELECT dept_name FROM department");
$departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Initialize variables
$instructorToEdit = null;

// Handle deleting an instructor
if (isset($_GET['delete'])) {
    $idToDelete = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM instructor WHERE ID = ?");
        $stmt->execute([$idToDelete]);
        echo "Instructor deleted successfully!";
        header('Location: instructor.php');
        exit;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Handle editing an instructor
if (isset($_GET['edit'])) {
    $idToEdit = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM instructor WHERE ID = ?");
    $stmt->execute([$idToEdit]);
    $instructorToEdit = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Assuming this data comes from a form or somewhere else
$ID = $_POST['ID'] ?? null;
$first_name = $_POST['first_name'] ?? null;
$middle_name = $_POST['middle_name'] ?? null;
$last_name = $_POST['last_name'] ?? null;
$street_number = $_POST['street_number'] ?? null;
$street_name = $_POST['street_name'] ?? null;
$apt_number = $_POST['apt_number'] ?? null;
$city = $_POST['city'] ?? null;
$province = $_POST['province'] ?? null;
$postal_code = $_POST['postal_code'] ?? null;
$date_of_birth = $_POST['date_of_birth'] ?? null;
$salary = $_POST['salary'] ?? null;
$dept_name = $_POST['dept_name'] ?? null;

// INSERT Query
if (isset($_POST['insert'])) {
    try {
        $stmt = $pdo->prepare("INSERT INTO instructor (ID, first_name, middle_name, last_name, street_number, street_name, apt_number, city, province, postal_code, date_of_birth, salary, dept_name) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $ID,
            $first_name,
            $middle_name,
            $last_name,
            $street_number,
            $street_name,
            $apt_number,
            $city,
            $province,
            $postal_code,
            $date_of_birth,
            $salary ?: null,  // Use null if salary is not provided
            $dept_name ?: null  // Use null if dept_name is not selected
        ]);
        echo "Instructor added successfully!";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// UPDATE Query
if (isset($_POST['update'])) {
    $original_id = $_POST['original_id'];
    try {
        $stmt = $pdo->prepare("UPDATE instructor 
                               SET first_name=?, middle_name=?, last_name=?, street_number=?, street_name=?, apt_number=?, city=?, province=?, postal_code=?, date_of_birth=?, salary=?, dept_name=? 
                               WHERE ID=?");
        $stmt->execute([
            $first_name,
            $middle_name,
            $last_name,
            $street_number,
            $street_name,
            $apt_number,
            $city,
            $province,
            $postal_code,
            $date_of_birth,
            $salary ?: null,
            $dept_name ?: null,
            $original_id
        ]);
        echo "Instructor updated successfully!";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Instructor</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f7f7f7; margin: 0; padding: 0; }
        .container { max-width: 800px; margin: 20px auto; padding: 20px; background-color: #fff; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        h1 { text-align: center; margin-bottom: 20px; }
        form { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; }
        input, button, select { width: 100%; padding: 10px; margin-bottom: 10px; }
        button { background-color: #28a745; color: #fff; border: none; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 8px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #007bff; color: #fff; }
        .actions a { margin-right: 10px; color: #007bff; }
        .actions a.delete { color: #dc3545; }
    </style>
</head>
<body>
<div class="container">
    <h1>Manage Instructor</h1>
    <form action="instructor.php" method="POST">
        <h2><?php echo $instructorToEdit ? 'Edit Instructor' : 'Add Instructor'; ?></h2>

        <?php if ($instructorToEdit): ?>
            <input type="hidden" name="original_id" value="<?php echo htmlspecialchars($instructorToEdit['ID']); ?>">
        <?php endif; ?>

        <label for="ID">Instructor ID:</label>
        <input type="text" id="ID" name="ID" value="<?php echo $instructorToEdit ? htmlspecialchars($instructorToEdit['ID']) : ''; ?>" required>

        <label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name" value="<?php echo $instructorToEdit ? htmlspecialchars($instructorToEdit['first_name']) : ''; ?>" required>

        <label for="middle_name">Middle Name:</label>
        <input type="text" id="middle_name" name="middle_name" value="<?php echo $instructorToEdit ? htmlspecialchars($instructorToEdit['middle_name']) : ''; ?>">

        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" name="last_name" value="<?php echo $instructorToEdit ? htmlspecialchars($instructorToEdit['last_name']) : ''; ?>" required>

        <label for="street_number">Street Number:</label>
        <input type="text" id="street_number" name="street_number" value="<?php echo $instructorToEdit ? htmlspecialchars($instructorToEdit['street_number']) : ''; ?>" required>

        <label for="street_name">Street Name:</label>
        <input type="text" id="street_name" name="street_name" value="<?php echo $instructorToEdit ? htmlspecialchars($instructorToEdit['street_name']) : ''; ?>" required>

        <label for="apt_number">Apartment Number:</label>
        <input type="text" id="apt_number" name="apt_number" value="<?php echo $instructorToEdit ? htmlspecialchars($instructorToEdit['apt_number']) : ''; ?>">

        <label for="city">City:</label>
        <input type="text" id="city" name="city" value="<?php echo $instructorToEdit ? htmlspecialchars($instructorToEdit['city']) : ''; ?>" required>

        <label for="province">Province:</label>
        <input type="text" id="province" name="province" value="<?php echo $instructorToEdit ? htmlspecialchars($instructorToEdit['province']) : ''; ?>" required>

        <label for="postal_code">Postal Code:</label>
        <input type="text" id="postal_code" name="postal_code" value="<?php echo $instructorToEdit ? htmlspecialchars($instructorToEdit['postal_code']) : ''; ?>" required>

        <label for="date_of_birth">Date of Birth:</label>
        <input type="date" id="date_of_birth" name="date_of_birth" value="<?php echo $instructorToEdit ? htmlspecialchars($instructorToEdit['date_of_birth']) : ''; ?>" required>

        <label for="salary">Salary:</label>
        <input type="number" id="salary" name="salary" value="<?php echo $instructorToEdit ? htmlspecialchars($instructorToEdit['salary']) : ''; ?>">

        <label for="dept_name">Department:</label>
        <select id="dept_name" name="dept_name" >
            <option value="">Select Department</option>
            <?php foreach ($departments as $department): ?>
                <option value="<?php echo $department['dept_name']; ?>" <?php echo ($instructorToEdit && $instructorToEdit['dept_name'] == $department['dept_name']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($department['dept_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" name="<?php echo $instructorToEdit ? 'update' : 'insert'; ?>">
            <?php echo $instructorToEdit ? 'Update Instructor' : 'Add Instructor'; ?>
        </button>
    </form>

    <h2>Instructor List</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Middle Name</th>
                <th>Last Name</th>
                <th>Street Number</th>
                <th>Street Name</th>
                <th>Apartment Number</th>
                <th>City</th>
                <th>Province</th>
                <th>Postal Code</th>
                <th>Date of Birth</th>
                <th>Salary</th>
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
                    <td><?php echo htmlspecialchars($instructor['street_number']); ?></td>
                    <td><?php echo htmlspecialchars($instructor['street_name']); ?></td>
                    <td><?php echo htmlspecialchars($instructor['apt_number']); ?></td>
                    <td><?php echo htmlspecialchars($instructor['city']); ?></td>
                    <td><?php echo htmlspecialchars($instructor['province']); ?></td>
                    <td><?php echo htmlspecialchars($instructor['postal_code']); ?></td>
                    <td><?php echo htmlspecialchars($instructor['date_of_birth']); ?></td>
                    <td><?php echo htmlspecialchars($instructor['salary']); ?></td>
                    <td><?php echo htmlspecialchars($instructor['dept_name']); ?></td>
                    <td class="actions">
                        <a href="?edit=<?php echo $instructor['ID']; ?>">Edit</a> |
                        <a href="?delete=<?php echo $instructor['ID']; ?>" class="delete" onclick="return confirm('Are you sure you want to delete this instructor?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
