<?php
session_start();

if (!isset($_SESSION['username']) || !in_array($_SESSION['role'], ['commander', 'admin'])) {
    header("Location: login.php"); 
    exit();
}
ob_start();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Υπηρεσίες 165 - Καλώς ήρθατε</title>
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
        }

        h2 {
            color: #333;
        }

        .calendar {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
            margin-top: 20px;
        }

        .calendar button {
            width: 100%;
            padding: 10px;
            background-color: #eee;
            border: 1px solid #ccc;
            cursor: not-allowed;
        }

        .calendar button.enabled {
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }

        #details {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ccc;
            background-color: #fff;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }

        .post-column {
            border: 1px solid #ccc;
            padding: 10px;
            background-color: #fafafa;
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

        .controls {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .controls select {
            margin: 0 10px;
            padding: 5px;
        }
    </style>
</head>
<body>

<header>
    <h1>Υπηρεσίες 165 ΜΠΕΠ(RM-70)</h1>
</header>

<nav>
    <a onclick="navigateToHome()">Home</a>
    <a onclick="navigateToReportPanel()">Report Panel</a>
    <a onclick="navigateToLogout()">Logout</a>
</nav>



<div class="container">
    <h2>Υπηρεσίες 165 ΜΠΕΠ(RM-70)</h2>
    <div class="controls">
        <select id="month-select">
            <option value="0">January</option>
            <option value="1">February</option>
            <option value="2">March</option>
            <option value="3">April</option>
            <option value="4">May</option>
            <option value="5">June</option>
            <option value="6" selected>July</option>
            <option value="7">August</option>
            <option value="8">September</option>
            <option value="9">October</option>
            <option value="10">November</option>
            <option value="11">December</option>
        </select>
        <select id="year-select">
            <script>
                const currentYear = new Date().getFullYear();
                for (let i = currentYear - 5; i <= currentYear + 5; i++) {
                    document.write(`<option value="${i}" ${i === 2024 ? 'selected' : ''}>${i}</option>`);
                }
            </script>
        </select>
        <button onclick="loadCalendar()">Load Calendar</button>
    </div>
    <div id="calendar" class="calendar">
        <!-- Calendar buttons will be generated here -->
    </div>
    <div id="details">
        <div id="eas-column" class="post-column">
            <h3>ΕΕΑΣ</h3>
            <!-- EAS post details will be shown here -->
        </div>
        <div id="aydm-column" class="post-column">
            <h3>ΑΥΔΜ</h3>
            <!-- AYDM post details will be shown here -->
        </div>
        <div id="baydm-column" class="post-column">
            <h3>ΒΑΥΔΜ</h3>
            <!-- BAYDM post details will be shown here -->
        </div>
        <div id="apil-column" class="post-column">
            <h3>ΑΞ.ΠΥΛΗΣ</h3>
            <!-- APIL post details will be shown here -->
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.0/anime.min.js"></script>


<script>

    // REDIRECTIONS!!!
function navigateToLogout() {
    window.location.href = 'logout.php';
}

function navigateToHome() {
    window.location.href = 'welcome.php';
}

function navigateToReportPanel(){
    window.location.href = 'report_panel.php';
}


// loadTheCalendaer

function loadCalendar() {
    const month = document.getElementById('month-select').value;
    const year = document.getElementById('year-select').value;

    fetch(`get_calendar.php?month=${month}&year=${year}`)
        .then(response => response.json())
        .then(data => {
            const calendarDiv = document.getElementById('calendar');
            const easColumn = document.getElementById('eas-column');
            const aydmColumn = document.getElementById('aydm-column');
            const baydmColumn = document.getElementById('baydm-column');
            const apilColumn = document.getElementById('apil-column');
            const daysInMonth = new Date(year, parseInt(month) + 1, 0).getDate();
            calendarDiv.innerHTML = '';
            easColumn.innerHTML = '<h3>ΕΑΑΣ</h3>';
            aydmColumn.innerHTML = '<h3>ΑΥΔΜ</h3>';
            baydmColumn.innerHTML = '<h3>ΒΑΥΔΜ</h3>';
            apilColumn.innerHTML = '<h3>ΑΞ.ΠΥΛΗΣ</h3>';
            
            for (let day = 1; day <= daysInMonth; day++) {
                const dateStr = `${year}-${String(parseInt(month) + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                const dateData = data.filter(d => d.dateid === dateStr);

                const dayButton = document.createElement('button');
                dayButton.textContent = day;
                if (dateData.length > 0) {
                    dayButton.classList.add('enabled');
                    dayButton.onclick = () => {
                        easColumn.innerHTML = '<h3>ΕΑΑΣ</h3>';
                        aydmColumn.innerHTML = '<h3>ΑΥΔΜ</h3>';
                        baydmColumn.innerHTML = '<h3>ΒΑΥΔΜ</h3>';
                        apilColumn.innerHTML = '<h3>ΑΞ.ΠΥΛΗΣ</h3>';
                        
                        dateData.forEach(d => {
                            const detail = `<p>Date: ${d.dateid}</p>
                                            <p>Βαθμος : ${d.rankName} </p>
                                            <p>Όνομα: ${d.name}</p>
                                            <p>Επώνυμο: ${d.surname}</p>`;
                            if (d.post === 'eas') {
                                easColumn.innerHTML += detail;
                            } else if (d.post === 'aydm') {
                                aydmColumn.innerHTML += detail;
                            } else if (d.post === 'baydm') {
                                baydmColumn.innerHTML += detail;
                            } else if (d.post === 'apil') {
                                apilColumn.innerHTML += detail;
                            }
                        });
                    };
                }

                calendarDiv.appendChild(dayButton);
            }
        })
        .catch(error => console.error('Error:', error));
}
</script>

<footer>
    <p>&copy; Υπηρεσίες 165 ΜΠΕΠ(RM-70)</p>
</footer>
</body>
</html>
