<?php
session_start();
include('connection.php');

if (!isset($_SESSION['aid']) || empty($_SESSION['aid'])) {
    die('invalid application id');
}

$aid = intval($_SESSION['aid']);

$query = "SELECT * FROM application WHERE id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $aid);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("No application found.");
}

$row = $result->fetch_assoc();

$resumeData = $row['resume'];
$filePath = 'resumes/resume_' . $aid . '.pdf';
file_put_contents($filePath, $resumeData);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Application</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to right, #e3f2fd, #fff);
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 700px;
            margin: 50px auto;
            padding: 40px;
            background: white;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
        }

        h1 {
            text-align: center;
            color: #007bff;
            margin-bottom: 30px;
        }

        .field {
            display: flex;
            margin: 12px 0;
            font-size: 18px;
        }

        .field label {
            font-weight: bold;
            width: 160px;
            color: #333;
        }

        .field p {
            margin: 0;
            color: #555;
        }

        .download-link {
            display: inline-block;
            margin-top: 15px;
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            transition: background 0.3s ease;
        }

        .download-link:hover {
            background-color: #218838;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 30px;
            color: #007bff;
            font-weight: bold;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Application Details</h1>
    
    <div class="field">
        <label>Name:</label>
        <p><?php echo htmlspecialchars($row['name']); ?></p>
    </div>

    <div class="field">
        <label>Email:</label>
        <p><?php echo htmlspecialchars($row['email']); ?></p>
    </div>

    <div class="field">
        <label>Contact:</label>
        <p><?php echo htmlspecialchars($row['contact']); ?></p>
    </div>

    <div class="field">
        <label>Qualification:</label>
        <p><?php echo htmlspecialchars($row['qualification']); ?></p>
    </div>

    <div class="field">
        <label>Skills:</label>
        <p><?php echo nl2br(htmlspecialchars($row['skills'])); ?></p>
    </div>

    <div class="field">
        <label>Status:</label>
        <p><?php echo htmlspecialchars($row['status']); ?></p>
    </div>

    <div class="field">
        <label>Resume:</label>
        <p><a class="download-link" href="<?php echo $filePath; ?>" download>Download Resume</a></p>
    </div>

    <a class="back-link" href="javascript:history.back()">← Back</a>
</div>

</body>
</html>
