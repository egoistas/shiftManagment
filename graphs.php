<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Graphs - Υπηρεσίες 165 ΜΠΕΠ(RM-70)</title>
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

        .chart-container {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
        }

        .chart-card {
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin: 20px;
            padding: 20px;
            width: 90%;
        }

        .filter-container {
            margin-bottom: 20px;
        }

        .filter-container label {
            margin-right: 10px;
        }

        .filter-container input,
        .filter-container select {
            margin-right: 20px;
            padding: 5px;
        }
    </style>
</head>
<body>

<header>
    <h1>Graphs - Υπηρεσίες 165 ΜΠΕΠ(RM-70)</h1>
</header>

<nav>
    <a onclick="navigateToHome()">Home</a>
    <a onclick="navigateToReportPanel()">Report Panel</a>
    <a onclick="navigateToLogout()">Logout</a>
</nav>

<div class="container">
    <h2>Graphs</h2>
    <br>
    <h3>Στατιστικά στοιχειά στελεχών ανά υπηρεσία - καθημερινών/αργειών</h3>

    <div class="filter-container">
        <label for="startDate">Start Date:</label>
        <input type="date" id="startDate" name="startDate">
        <label for="endDate">End Date:</label>
        <input type="date" id="endDate" name="endDate">
        <label for="post">Post:</label>
        <select id="post" name="post">
            <option value="eas">ΕΕΑΣ</option>
            <option value="aydm">ΑΥΔΜ</option>
            <option value="baydm">ΒΑΥΔΜ</option>
            <option value="apil">ΑΞ.ΠΥΛΗΣ</option>
        </select>
        <button onclick="fetchGraphData()">Filter</button>
    </div>

    <div class="chart-container">
        <div class="chart-card">
            <canvas id="dailyRecordsChart"></canvas>
        </div>
        <div class="chart-card">
            <canvas id="holidayWeekendRecordsChart"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let dailyRecordsChart;
    let holidayWeekendRecordsChart;

    function navigateToLogout() {
        window.location.href = 'logout.php';
    }

    function navigateToHome() {
        window.location.href = 'welcome.php';
    }

    function navigateToReportPanel() {
        window.location.href = 'report_panel.php';
    }

    function fetchGraphData() {
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const post = document.getElementById('post').value;

        if (!startDate || !endDate || !post) {
            alert("Please select all filter options");
            return;
        }

        document.querySelector('.container').classList.add('loading');

        fetch(`fetch_graph_data.php?start_date=${startDate}&end_date=${endDate}&post=${post}`)
            .then(response => response.json())
            .then(data => {
                document.querySelector('.container').classList.remove('loading');
                if (dailyRecordsChart) {
                    dailyRecordsChart.destroy();
                }
                if (holidayWeekendRecordsChart) {
                    holidayWeekendRecordsChart.destroy();
                }
                renderDailyRecordsChart(data.dailyRecords);
                renderHolidayWeekendRecordsChart(data.holidayRecords);
            })
            .catch(error => {
                document.querySelector('.container').classList.remove('loading');
                console.error('Error fetching data:', error);
                alert('Failed to fetch data. Please try again later.');
            });
    }

    function renderDailyRecordsChart(data) {
        const ctx = document.getElementById('dailyRecordsChart').getContext('2d');
        const labels = data.map(record => `${record.rankName} ${record.surname} ${record.name}`);
        const values = data.map(record => record.count);

        dailyRecordsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Daily Records (Non-Holiday)',
                    data: values,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            maxRotation: 90,
                            minRotation: 45
                        }
                    },
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    function renderHolidayWeekendRecordsChart(data) {
        const ctx = document.getElementById('holidayWeekendRecordsChart').getContext('2d');
        const labels = data.map(record => `${record.rankName} ${record.surname} ${record.name}`);
        const holidayCounts = data.map(record => record.holidayCount);
        const weekendCounts = data.map(record => record.weekendCount);

        holidayWeekendRecordsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Holiday Records',
                        data: holidayCounts,
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Weekend Records',
                        data: weekendCounts,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            maxRotation: 90,
                            minRotation: 45
                        }
                    },
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
</script>
</body>
</html>
