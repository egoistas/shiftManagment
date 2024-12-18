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
    <p>Επιτυχής εισαγωγή υπηρεσιών</p>
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

</script>

<footer>
    <p>&copy; Υπηρεσίες 165 ΜΠΕΠ(RM-70)</p>
</footer>
</body>
</html>
