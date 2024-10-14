<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students Derogatory Records</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            color: #343a40;
            margin: 0;
            padding: 20px;
        }

        h2 {
            color: #000; /* Changed to black */
        }

        /* Table Styles */
        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }

        th, td {
            text-align: left;
            padding: 12px;
            border: 1px solid #ced4da;
        }

        th {
            background-color: #000; /* Changed to black */
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #e9ecef;
        }

        /* Button Styles */
        button {
            background-color: #000; /* Changed to black */
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #333; /* Darker shade of black */
        }

        /* Search Input */
        input[type="text"], select {
            border-radius: 5px;
            border: 1px solid #ced4da;
            padding: 10px;
            width: 100%;
            margin-bottom: 12px; /* Added margin for spacing */
        }

        input[type="text"]:focus, select:focus {
            border-color: #000; /* Changed to black */
            outline: none;
        }

        /* Card Styles */
        .border {
            border-radius: 8px;
            background-color: white;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Shadow effect */
        }

        .rounded {
            border-radius: 8px;
        }

        /* Hide Records Section by Default */
        .records-section {
            display: none;
        }
    </style>
</head>
<body>

<main class="p-4">
    <h2 class="text-2xl font-bold mb-4">Students Derogatory Records</h2>
    <p class="mb-4">This module contains records of students with derogatory notes. Below is the list of students with their details.</p>
    
    <!-- Search Filter -->
    <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Search by student's initials/Student Id..." title="Type student initials">

    <h3 class="text-xl font-semibold mt-6">Student Information</h3>
    
    <table id="studentsTable" class="min-w-full bg-white border border-gray-300 mb-4">
        <thead>
            <tr>
                <th>Student Number</th>
                <th>Last Name</th>
                <th>First Name</th>
                <th>Middle Name</th>
                <th>Year Graduated</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>202210001</td>
                <td>Doe</td>
                <td>John</td>
                <td>A.</td>
                <td>2022</td>
                <td>
                    <button onclick="toggleRecords('records1')">Check Records</button>
                </td>
            </tr>
            <tr>
                <td>202310002</td>
                <td>Smith</td>
                <td>Jane</td>
                <td>B.</td>
                <td>2023</td>
                <td>
                    <button onclick="toggleRecords('records2')">Check Records</button>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Records Section for John Doe -->
    <div id="records1" class="records-section border rounded">
        <h3 class="text-xl font-semibold mb-4">Student Previous Records for John Doe</h3>

        <label for="violation1" class="block mb-2 font-medium">Violation:</label>
        <input type="text" id="violation1" placeholder="Enter violation if applicable">

        <label for="actionTaken1" class="block mb-2 font-medium">Action Taken:</label>
        <input type="text" id="actionTaken1" placeholder="Enter action taken if applicable">

        <label for="settled1" class="block mb-2 font-medium">Settled:</label>
        <select id="settled1">
            <option value="">Select...</option>
            <option value="yes">Yes</option>
            <option value="no">No</option>
        </select>

        <label for="sanction1" class="block mb-2 font-medium">Sanction:</label>
        <select id="sanction1">
            <option value="">Select Sanction</option>
            <option value="suspension">Suspension</option>
            <option value="expulsion">Expulsion</option>
            <option value="verbal_warning">Verbal Warning</option>
            <option value="written_warning">Written Warning</option>
            <option value="others">Others</option>
        </select>

        <!-- Submit Button -->
        <button type="submit" class="mt-4">Submit Record</button>
    </div>

    <!-- Records Section for Jane Smith -->
    <div id="records2" class="records-section border rounded">
        <h3 class="text-xl font-semibold mb-4">Student Previous Records for Jane Smith</h3>

        <label for="violation2" class="block mb-2 font-medium">Violation:</label>
        <input type="text" id="violation2" placeholder="Enter violation if applicable">

        <label for="actionTaken2" class="block mb-2 font-medium">Action Taken:</label>
        <input type="text" id="actionTaken2" placeholder="Enter action taken if applicable">

        <label for="settled2" class="block mb-2 font-medium">Settled:</label>
        <select id="settled2">
            <option value="">Select...</option>
            <option value="yes">Yes</option>
            <option value="no">No</option>
        </select>

        <label for="sanction2" class="block mb-2 font-medium">Sanction:</label>
        <select id="sanction2">
            <option value="">Select Sanction</option>
            <option value="suspension">Suspension</option>
            <option value="expulsion">Expulsion</option>
            <option value="verbal_warning">Verbal Warning</option>
            <option value="written_warning">Written Warning</option>
            <option value="others">Others</option>
        </select>

        <!-- Submit Button -->
        <button type="submit" class="mt-4">Submit Record</button>
    </div>
</main>

<script>
    // Function to toggle the visibility of the records section
    function toggleRecords(recordId) {
        const recordSection = document.getElementById(recordId);
        recordSection.style.display = (recordSection.style.display === "block") ? "none" : "block";
    }

    // Function to filter the student table based on input in the search field
    function filterTable() {
        const input = document.getElementById('searchInput');
        const filter = input.value.toLowerCase();
        const table = document.getElementById('studentsTable');
        const trs = table.getElementsByTagName('tr');

        for (let i = 1; i < trs.length; i++) { // Start from 1 to skip the table header
            const tds = trs[i].getElementsByTagName('td');
            let found = false;

            for (let j = 0; j < tds.length; j++) {
                const td = tds[j];
                if (td && td.textContent.toLowerCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }

            trs[i].style.display = found ? '' : 'none';
        }
    }
</script>

</body>
</html>
