<?php
session_start();
include('connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['app_id'])) {
    $app_id = intval($_POST['app_id']);
    $action = $_POST['action'];

    if ($action === 'accept') {
        $status = 'Accepted';
    } elseif ($action === 'reject') {
        $status = 'Rejected';
    } else {
        $status = '';
    }

    if ($status !== '') {
        $update_sql = "UPDATE css.application SET status = ? WHERE id = ?";
        $stmt = $con->prepare($update_sql);
        $stmt->bind_param('si', $status, $app_id);
        $stmt->execute();
    }
}
if (!isset($_GET['job_id'])) {
    die("Company ID not specified.");
} else {
    $jid = $_GET['job_id'];
    $sql = "SELECT * FROM css.application WHERE jid = $jid";
    $result = $con->query($sql);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Applicants</title>
    <style>
        body {
            font-family: Arial;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            max-width: 1100px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 10px #ccc;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: center;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        a {
            color: #007bff;
        }
        .btn {
            padding: 6px 12px;
            margin: 2px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        .accept {
            background-color: #28a745;
            color: white;
        }
        .reject {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Applicants List</h1>
    <?php
    if ($result->num_rows > 0) {
        echo "<table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Qualification</th>
                    <th>Skills</th>
                    <th>Resume</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['name']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['qualification']}</td>
                    <td>{$row['skills']}</td>
                    <td><a href='{$row['resume']}' target='_blank'>View</a></td>
                    <td>{$row['status']}</td>
                    <td>
                        <form method='POST' style='display:inline;'>
                            <input type='hidden' name='app_id' value='{$row['id']}'>
                            <input type='hidden' name='action' value='accept'>
                            <button type='submit' class='btn accept'>Accept</button>
                        </form>
                        <form method='POST' style='display:inline;'>
                            <input type='hidden' name='app_id' value='{$row['id']}'>
                            <input type='hidden' name='action' value='reject'>
                            <button type='submit' class='btn reject'>Reject</button>
                        </form>
                    </td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No applicants found.</p>";
    }

    $con->close();
    ?>
</div>

</body>
</html>
