<?php
// Database connection
$host = 'localhost';
$db = 'university_db';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Handle form submission for adding or editing a time slot
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['time_slot_id']) && $_POST['time_slot_id'] != '') {
        // Update existing time slot
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

// Handle deletion of a time slot
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM time_slot WHERE time_slot_id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: timeslot.php");
    exit();
}

// Fetch all time slots
$stmt = $pdo->query("SELECT * FROM time_slot");
$timeSlots = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if a time slot is being edited
$timeSlotToEdit = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM time_slot WHERE time_slot_id = ?");
    $stmt->execute([$_GET['edit']]);
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

        input, select, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        select {
            appearance: none;
            background-color: #fff;
            color: #555;
            font-size: 1rem;
            cursor: pointer;
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
    </style>
</head>
<body>

<div class="container">
    <h1>Manage Time Slots</h1>
    <a href="/fundamentals_lab/fundamentals_act/dashboard.php">Back to dashboard</a>
    
    <!-- Form for adding or editing time slots -->
    <!-- Form for adding or editing time slots -->
<form action="timeslot.php" method="POST">
    <h2><?php echo $timeSlotToEdit ? 'Edit Time Slot' : 'Add Time Slot'; ?></h2>
    
    <?php if ($timeSlotToEdit): ?>
        <input type="hidden" name="time_slot_id" value="<?php echo htmlspecialchars($timeSlotToEdit['time_slot_id']); ?>">
    <?php endif; ?>

    <label for="day">Day:</label>
    <select id="day" name="day" required>
        <option value="Monday" <?php echo ($timeSlotToEdit && $timeSlotToEdit['day'] === 'Monday') ? 'selected' : ''; ?>>Monday</option>
        <option value="Tuesday" <?php echo ($timeSlotToEdit && $timeSlotToEdit['day'] === 'Tuesday') ? 'selected' : ''; ?>>Tuesday</option>
        <option value="Wednesday" <?php echo ($timeSlotToEdit && $timeSlotToEdit['day'] === 'Wednesday') ? 'selected' : ''; ?>>Wednesday</option>
        <option value="Thursday" <?php echo ($timeSlotToEdit && $timeSlotToEdit['day'] === 'Thursday') ? 'selected' : ''; ?>>Thursday</option>
        <option value="Friday" <?php echo ($timeSlotToEdit && $timeSlotToEdit['day'] === 'Friday') ? 'selected' : ''; ?>>Friday</option>
        <option value="Saturday" <?php echo ($timeSlotToEdit && $timeSlotToEdit['day'] === 'Saturday') ? 'selected' : ''; ?>>Saturday</option>
        <option value="Sunday" <?php echo ($timeSlotToEdit && $timeSlotToEdit['day'] === 'Sunday') ? 'selected' : ''; ?>>Sunday</option>
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
        <?php foreach ($timeSlots as $timeSlot): ?>
        <tr>
            <td><?php echo htmlspecialchars($timeSlot['day']); ?></td>
            <td><?php echo htmlspecialchars($timeSlot['start_time']); ?></td>
            <td><?php echo htmlspecialchars($timeSlot['end_time']); ?></td>
            <td class="actions">
                <a href="timeslot.php?edit=<?php echo htmlspecialchars($timeSlot['time_slot_id']); ?>">Edit</a>
                <a href="timeslot.php?delete=<?php echo htmlspecialchars($timeSlot['time_slot_id']); ?>" 
                   onclick="return confirm('Are you sure you want to delete this time slot?');" class="delete">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
