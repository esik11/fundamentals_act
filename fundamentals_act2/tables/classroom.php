<?php
// Database connection
$host = 'localhost';
$db = 'university_db3';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Fetch all buildings from the department table
$stmt = $pdo->query("SELECT building FROM department");
$buildings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission for adding or editing a classroom
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['building']) && isset($_POST['room_number']) && isset($_POST['capacity'])) {
        if ($_POST['building'] && $_POST['room_number'] && $_POST['capacity']) {
            try {
                // Check if we are updating or inserting
                if (isset($_POST['room_number_old'])) {
                    // Update existing classroom
                    $stmt = $pdo->prepare("UPDATE classroom SET capacity=? WHERE building=? AND room_number=?");
                    $stmt->execute([$_POST['capacity'], $_POST['building'], $_POST['room_number_old']]);
                } else {
                    // Add new classroom
                    $stmt = $pdo->prepare("INSERT INTO classroom (building, room_number, capacity) VALUES (?, ?, ?)");
                    $stmt->execute([$_POST['building'], $_POST['room_number'], $_POST['capacity']]);
                }
                header("Location: classroom.php"); // Redirect to refresh the page and show changes
                exit();
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage(); // Display error if query fails
            }
        } else {
            echo "Please fill in all fields.";
        }
    }
}

// Handle deletion of a classroom
if (isset($_GET['delete_building']) && isset($_GET['delete_room_number'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM classroom WHERE building = ? AND room_number = ?");
        $stmt->execute([$_GET['delete_building'], $_GET['delete_room_number']]);
        header("Location: classroom.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Fetch all classrooms
$stmt = $pdo->query("SELECT * FROM classroom");
$classrooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if a classroom is being edited
$classroomToEdit = null;
if (isset($_GET['edit_building']) && isset($_GET['edit_room_number'])) {
    $stmt = $pdo->prepare("SELECT * FROM classroom WHERE building = ? AND room_number = ?");
    $stmt->execute([$_GET['edit_building'], $_GET['edit_room_number']]);
    $classroomToEdit = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Classrooms</title>
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

        input, button, select {
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

        select {
            background-color: #fff;
            color: #333;
            padding: 10px;
            font-size: 1rem;
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

<<div class="container">
    <h1>Manage Classrooms</h1>
    <a href="/fundamentals_lab/fundamentals_act/dashboard.php">Back to dashboard</a>

    <!-- Form for adding or editing classrooms -->
    <form action="classroom.php" method="POST">
        <h2><?php echo $classroomToEdit ? 'Edit Classroom' : 'Add Classroom'; ?></h2>
        
        <?php if ($classroomToEdit): ?>
            <!-- Hidden fields to pass building and room number for editing -->
            <input type="hidden" name="building" value="<?php echo htmlspecialchars($classroomToEdit['building']); ?>">
            <input type="hidden" name="room_number_old" value="<?php echo htmlspecialchars($classroomToEdit['room_number']); ?>">
        <?php endif; ?>

        <label for="building">Building:</label>
        <input type="text" id="building" name="building" value="<?php echo $classroomToEdit ? htmlspecialchars($classroomToEdit['building']) : ''; ?>" required>

        <label for="room_number">Room Number:</label>
        <input type="text" id="room_number" name="room_number" value="<?php echo $classroomToEdit ? htmlspecialchars($classroomToEdit['room_number']) : ''; ?>" required>

        <label for="capacity">Capacity:</label>
        <input type="number" id="capacity" name="capacity" value="<?php echo $classroomToEdit ? htmlspecialchars($classroomToEdit['capacity']) : ''; ?>" required>

        <button type="submit"><?php echo $classroomToEdit ? 'Update Classroom' : 'Add Classroom'; ?></button>
    </form>

    <!-- Table to display classrooms -->
    <table>
        <thead>
            <tr>
                <th>Building</th>
                <th>Room Number</th>
                <th>Capacity</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($classrooms as $classroom): ?>
        <tr>
            <td><?php echo htmlspecialchars($classroom['building']); ?></td>
            <td><?php echo htmlspecialchars($classroom['room_number']); ?></td>
            <td><?php echo htmlspecialchars($classroom['capacity']); ?></td>
            <td class="actions">
                <!-- Edit and Delete links -->
                <a href="classroom.php?edit_building=<?php echo htmlspecialchars($classroom['building']); ?>&edit_room_number=<?php echo htmlspecialchars($classroom['room_number']); ?>">Edit</a>
                <a href="classroom.php?delete_building=<?php echo htmlspecialchars($classroom['building']); ?>&delete_room_number=<?php echo htmlspecialchars($classroom['room_number']); ?>" class="delete" onclick="return confirm('Are you sure you want to delete this classroom?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>