<?php

session_start();

if (!isset($_SESSION['username']) || !in_array($_SESSION['role'], ['aydm'])) {
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Προγραμματισμός ΑΥΔΜ</title>
    <style>
       body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        header {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 1em 0;
            animation: fadeInDown 1s ease-in-out;
        }

        nav {
            background-color: #444;
            overflow: hidden;
        }

        nav a {
            float: left;
            display: block;
            color: #f2f2f2;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
            cursor: pointer;
        }

        nav a:hover {
            background-color: #ddd;
            color: black;
        }

        .container {
            padding: 20px;
            margin: 0 10%;
        }

        h2 {
            color: #333;
        }

        .button-container {
            text-align: center;
            margin-bottom: 20px;
        }

        .button-container button {
            padding: 10px 20px;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 0;
            position: relative;
        }

        th {
            background-color: #f2f2f2;
        }

        .calendar-checkbox {
            width: 100%;
            height: 100%;
            padding: 10px;
            background-color: #eee;
            border: none;
            cursor: pointer;
        }

        .calendar-checkbox:checked {
            background-color: blue;
            color: white;
        }

        .weekend {
            background-color: #d3d3d3; /* Light gray color for weekends */
        }

        .summary-mark {
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            font-size: 24px;
            display: none; /* Initially hide the mark */
        }

        .summary-mark.active {
            display: block; /* Display mark if active */
        }

        .invalid {
            background-color: #ffcccc !important; /* Light red for invalid rows */
        }

        @keyframes fadeInDown {
            0% {
                opacity: 0;
                transform: translateY(-20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 0.1em 0;
        }
    </style>
</head>
<body>

<header>
    <h1>Υπηρεσίες 165 ΜΠΕΠ(RM-70)</h1>
</header>

<nav>
    
    <a onclick="navigateToLogout()">Logout</a>
</nav>

<div class="container">
    <h2>Προγραμματισμός ΑΥΔΜ</h2>
    <div class="button-container">
        <button type="button" onclick="loadNames()">Load Schedule</button>
    </div>
    <form id="schedule-form" method="POST" action="submit_schedule.php" onsubmit="return submitSchedule()">
        <table id="schedule-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <!-- Day headers will be dynamically generated -->
                </tr>
            </thead>
            <tbody>
                <!-- Table content will be dynamically populated -->
            </tbody>
        </table>
        <div class="button-container">
            <button type="submit">Submit Schedule</button>
        </div>
    </form>
</div>

<script>
    let currentYear, currentMonth, formattedMonth, formattedYear;

    function loadNames() {
        const today = new Date();
        currentYear = today.getFullYear();
        currentMonth = today.getMonth(); // 0-based index

        let nextMonth = currentMonth + 1;
        let nextYear = currentYear;

        if (nextMonth > 11) {
            nextMonth = 0;
            nextYear += 1;
        }

        formattedMonth = String(nextMonth + 1).padStart(2, '0');
        formattedYear = nextYear;

        fetch(`get_names.php?postid=2&month=${formattedMonth}&year=${formattedYear}`)
            .then(response => response.json())
            .then(data => {
                const tbody = document.querySelector('#schedule-table tbody');
                tbody.innerHTML = '';

                const daysInMonth = new Date(nextYear, nextMonth + 1, 0).getDate();

                const thead = document.querySelector('#schedule-table thead');
                const thElements = thead.querySelectorAll('th');
                thElements.forEach((th, index) => {
                    if (index > 0) th.remove();
                });

                for (let day = 1; day <= daysInMonth; day++) {
                    const th = document.createElement('th');
                    th.textContent = day;
                    thead.querySelector('tr').appendChild(th);
                }

                data.forEach(person => {
                    const row = document.createElement('tr');
                    const nameCell = document.createElement('td');
                    nameCell.textContent = `${person.rankName} ${person.surname} ${person.name}`;
                    row.appendChild(nameCell);

                    row.dataset.num_of_duty = person.num_of_duty;

                    for (let day = 1; day <= daysInMonth; day++) {
                        const dayCell = document.createElement('td');
                        const checkbox = document.createElement('input');
                        checkbox.type = 'checkbox';
                        checkbox.classList.add('calendar-checkbox');
                        checkbox.name = `duty[${person.nameid}][${formattedYear}-${formattedMonth}-${String(day).padStart(2, '0')}]`;
                        checkbox.value = '1';

                        const timestamp = new Date(nextYear, nextMonth, day);
                        const dayOfWeek = timestamp.getDay();
                        if (dayOfWeek === 0 || dayOfWeek === 6) {
                            dayCell.classList.add('weekend');
                        }

                        dayCell.appendChild(checkbox);
                        row.appendChild(dayCell);
                    }

                    tbody.appendChild(row);
                });
            })
            .catch(error => console.error('Error:', error));
    }

    function submitSchedule() {
        const dailyCheckErrors = validateDailyCheck();
        const personCheckErrors = validatePersonCheck();

        if (dailyCheckErrors.length > 0 || personCheckErrors.length > 0) {
            const allErrors = [...dailyCheckErrors, ...personCheckErrors];
            alert(allErrors.join('\n'));
            return false;
        }
        return true;
    }

    function validateDailyCheck() {
        const tbody = document.querySelector('#schedule-table tbody');
        const errors = [];
        const daysInMonth = new Date(formattedYear, formattedMonth - 1 + 1, 0).getDate(); // Days in current month

        for (let day = 1; day <= daysInMonth; day++) {
            let checkedCount = 0;
            const dayString = `${formattedYear}-${formattedMonth}-${String(day).padStart(2, '0')}`;

            tbody.querySelectorAll('tr').forEach(row => {
                const checkbox = row.querySelector(`input[name*='[${dayString}']`);
                if (checkbox && checkbox.checked) {
                    checkedCount++;
                }
            });

            if (checkedCount !== 1) {
                errors.push(`Each day must have exactly one duty assigned. Day ${day} has ${checkedCount} duties assigned.`);
            }
        }

        return errors;
    }

    function validatePersonCheck() {
        const rows = document.querySelectorAll('#schedule-table tbody tr');
        const errors = [];

        rows.forEach(row => {
            const checkboxes = row.querySelectorAll('.calendar-checkbox:checked');
            const num_of_duty = parseInt(row.dataset.num_of_duty);

            if (checkboxes.length > num_of_duty) {
                row.classList.add('invalid');
                errors.push(`Person ${row.querySelector('td').textContent} has more duties assigned (${checkboxes.length}) than allowed (${num_of_duty}).`);
            } else {
                row.classList.remove('invalid');
            }
        });

        return errors;
    }

    loadNames(); // Load names when the page loads

        // REDIRECTIONS!!!
function navigateToLogout() {
    window.location.href = 'logout.php';
}

</script>

<footer>
    <p>&copy; Υπηρεσίες 165 ΜΠΕΠ(RM-70)</p>
</footer>

</body>
</html>
