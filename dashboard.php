<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f4f4f4;
        }

        .container {
            display: flex;
            width: 100%;
            height: 100vh;
        }

        .sidebar {
            width: 250px;
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        .sidebar ul {
            list-style-type: none;
            padding: 0;
        }

        .sidebar ul li {
            margin: 15px 0;
        }

        .sidebar ul li a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            display: block;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .sidebar ul li a:hover {
            background-color: #34495e;
        }

        .main-content {
            flex-grow: 1;
            padding: 20px;
        }

        .main-content h1 {
            margin-bottom: 20px;
        }

        .table-list {
            list-style-type: none;
            padding: 0;
        }

        .table-list li {
            margin: 10px 0;
        }

        .table-list li a {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 18px;
            transition: background-color 0.3s;
        }

        .table-list li a:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="sidebar">
        <h2> Dashboard</h2>
        <ul>
            <li><a href="tables/instructor.php">Manage Instructors</a></li>
            <li><a href="tables/department.php">Manage Departments</a></li>
            <li><a href="tables/course.php">Manage Courses</a></li>
            <li><a href="tables/classroom.php">Manage Classrooms</a></li>
            <li><a href="tables/section.php">Manage Sections</a></li>
            <li><a href="timeslot.php">Manage Time Slots</a></li>
            <li><a href="tables/student.php">Manage Students</a></li>
        </ul>
    </div>

    <div class="main-content">
        <h1>Welcome to the University  Dashboard</h1>
        <p>Select an option from the sidebar to manage university data.</p>
    </div>
</div>

</body>
</html>