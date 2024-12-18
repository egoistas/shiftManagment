<?php
// PHP Logic

// Initialize variables
$startDate = 'Not selected';
$endDate = 'Not selected';
$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $startDate = isset($_POST['start_date']) ? $_POST['start_date'] : $startDate;
    $endDate = isset($_POST['end_date']) ? $_POST['end_date'] : $endDate;
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    if ($action === 'select') {
        $message = "You selected start date: $startDate and end date: $endDate.";
    } elseif ($action === 'clear') {
        $startDate = 'Not selected';
        $endDate = 'Not selected';
        $message = "Selection cleared.";
    }
}

// Generate options for the date dropdowns
function generateDateOptions() {
    $options = '';
    for ($i = 1; $i <= 31; $i++) {
        $options .= "<option value=\"" . str_pad($i, 2, '0', STR_PAD_LEFT) . "\">" . str_pad($i, 2, '0', STR_PAD_LEFT) . "</option>";
    }
    return $options;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Date Selection Panel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .container {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .date-panel {
            margin-bottom: 20px;
        }
        .date-panel label {
            display: block;
            margin-bottom: 10px;
        }
        .date-panel select {
            width: 100%;
            padding: 10px;
            font-size: 16px;
        }
        .button-group {
            display: flex;
            justify-content: space-between;
        }
        .button-group button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Date Selection Panel</h1>
        <form method="post" action="">
            <div class="date-panel">
                <label for="start_date">Start Date:</label>
                <select id="start_date" name="start_date">
                    <?php echo generateDateOptions(); ?>
                </select>
                <label for="end_date">End Date:</label>
                <select id="end_date" name="end_date">
                    <?php echo generateDateOptions(); ?>
                </select>
            </div>
            <div class="button-group">
                <button type="submit" name="action" value="select">Select</button>
                <button type="submit" name="action" value="clear">Clear</button>
            </div>
        </form>

        <?php if ($message): ?>
            <p><?php echo $message; ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
