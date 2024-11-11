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

// Handle form submission for adding or editing a time slot
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['time_slot_id'])) {
        // Update existing time slot based on time_slot_id
        $stmt = $pdo->prepare("UPDATE time_slot SET day=?, start_time=?, end_time=? WHERE time_slot_id=?");
        $stmt->execute([
            $_POST['day'],
            $_POST['start_time'],
            $_POST['end_time'],
            $_POST['time_slot_id']
        ]);
    } else {
        // Add new time slot
        $stmt = $pdo->prepare("INSERT INTO time_slot (day, start_time, end_time) VALUES (?, ?, ?)");
        $stmt->execute([
            $_POST['day'],
            $_POST['start_time'],
            $_POST['end_time']
        ]);
    }
    // Redirect back to the same page after form submission
    header("Location: timeslot.php");
    exit();
}

// Handle deletion of a time slot based on time_slot_id
if (isset($_GET['delete_time_slot_id'])) {
    $stmt = $pdo->prepare("DELETE FROM time_slot WHERE time_slot_id = ?");
    $stmt->execute([$_GET['delete_time_slot_id']]);
    header("Location: timeslot.php");
    exit();
}

// Fetch all time slots
$stmt = $pdo->query("SELECT * FROM time_slot");
$time_slots = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if a time slot is being edited based on time_slot_id
$timeSlotToEdit = null;
if (isset($_GET['edit_time_slot_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM time_slot WHERE time_slot_id = ?");
    $stmt->execute([$_GET['edit_time_slot_id']]);
    $timeSlotToEdit = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Time Slots</title>
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
    <h1>Manage Time Slots</h1>
    <a href="/fundamentals_lab/fundamentals_act/dashboard.php">Back to dashboard</a>
    <!-- Form for adding or editing time slots -->
    <form action="timeslot.php" method="POST">
        <h2><?php echo $timeSlotToEdit ? 'Edit Time Slot' : 'Add Time Slot'; ?></h2>
        
        <?php if ($timeSlotToEdit): ?>
            <!-- Hidden fields to pass time_slot_id for editing -->
            <input type="hidden" name="time_slot_id" value="<?php echo htmlspecialchars($timeSlotToEdit['time_slot_id']); ?>">
        <?php endif; ?>

        <label for="day">Day:</label>
        <select id="day" name="day" required>
            <option value="">Select Day</option>
            <option value="Monday" <?php echo $timeSlotToEdit && $timeSlotToEdit['day'] === 'Monday' ? 'selected' : ''; ?>>Monday</option>
            <option value="Tuesday" <?php echo $timeSlotToEdit && $timeSlotToEdit['day'] === 'Tuesday' ? 'selected' : ''; ?>>Tuesday</option>
            <option value="Wednesday" <?php echo $timeSlotToEdit && $timeSlotToEdit['day'] === 'Wednesday' ? 'selected' : ''; ?>>Wednesday</option>
            <option value="Thursday" <?php echo $timeSlotToEdit && $timeSlotToEdit['day'] === 'Thursday' ? 'selected' : ''; ?>>Thursday</option>
            <option value="Friday" <?php echo $timeSlotToEdit && $timeSlotToEdit['day'] === 'Friday' ? 'selected' : ''; ?>>Friday</option>
        </select>

        <label for="start_time">Start Time:</label>
        <input type="time" id="start_time" name="start_time" value="<?php echo $timeSlotToEdit ? htmlspecialchars($timeSlotToEdit['start_time']) : ''; ?>" required>

        <label for="end_time">End Time:</label>
        <input type="time" id="end_time" name="end_time" value="<?php echo $timeSlotToEdit ? htmlspecialchars($timeSlotToEdit['end_time']) : ''; ?>" required>

        <button type="submit"><?php echo $timeSlotToEdit ? 'Update Time Slot' : 'Add Time Slot'; ?></button>
    </form>

    <!-- Table to display time slots -->
    <table>
        <thead>
            <tr>
                <th>Day</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
    <?php foreach ($time_slots as $time_slot): ?>
    <tr>
        <td><?php echo htmlspecialchars($time_slot['day']); ?></td>
        <td><?php echo htmlspecialchars($time_slot['start_time']); ?></td>
        <td><?php echo htmlspecialchars($time_slot['end_time']); ?></td>
        <td class="actions">
            <!-- Edit and Delete links based on time_slot_id -->
            <a href="timeslot.php?edit_time_slot_id=<?php echo htmlspecialchars($time_slot['time_slot_id']); ?>">Edit</a>
            <a href="timeslot.php?delete_time_slot_id=<?php echo htmlspecialchars($time_slot['time_slot_id']); ?>" class="delete" onclick="return confirm('Are you sure you want to delete this time slot?')">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
