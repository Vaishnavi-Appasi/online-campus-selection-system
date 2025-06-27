<?php
    session_start();
    include('connection.php');

    if (isset($_SESSION['sid'])) {
        $sid = $_SESSION['sid'];

        $query = "SELECT * FROM student WHERE id = $sid";
        $result = mysqli_query($con, $query);

        if (!$result || mysqli_num_rows($result) === 0) {
            echo "ID not found.";
            exit();
        }

        $student = mysqli_fetch_assoc($result);

    } 
    else 
    {
        echo "ID not provided.";
        exit();
    }
    if (isset($_POST['delete_confirm']) && isset($_GET['id'])) {
        $Sid = intval($_SESSION['id']);

        $query = "DELETE FROM student WHERE id = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("i", $sid);

        if ($stmt->execute()) {
            session_destroy();
            header("Location: index.php");
            exit();
        } else {
            echo "Error deleting company: " . $stmt->error;
        }
        $stmt->close();
    }


?>

<!DOCTYPE html>
<html>
<head>
    <title>student Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>

        .card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 700px;
            box-sizing: border-box;
        }


    .head {
        display: flex;
        align-items: center;
        margin-bottom: 25px;
    }

    .logo-image {
        width: 90px;
        height: 90px;
        border-radius: 10px;
        object-fit: cover;
        margin-right: 20px;
        border: 2px solid #ddd;
    }

    .student-name {
        font-family:georgia;
        font-size: 24px;
        font-weight: bold;
        color: #1a1a1a;
    }

    .info-row {
        display: flex;
        margin: 12px 0;
    }

    .info-label {
        width: 160px;
        font-weight: bold;
        color: #333;
    }

    .info-value a {
        color: #007bff;
        text-decoration: none;
    }

    .info-value a:hover {
        text-decoration: underline;
    }

    .profile-actions {
        text-align: center;
        margin-top: 25px;
    }

    .btn {
        padding: 10px 20px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        color: white;
        margin: 0 10px;
        display: inline-block;
        transition: background 0.3s;
    }

    .edit-btn {
        background-color: #28a745;
    }

    .edit-btn:hover {
        background-color: #218838;
    }

    .delete-btn {
        background-color: #dc3545;
    }

    .delete-btn:hover {
        background-color: #c82333;
    }

    @media screen and (max-width: 600px) {
        .info-row {
            flex-direction: column;
        }

        .info-label {
            width: auto;
            margin-bottom: 4px;
        }
    }
</style>

</head>
<body>

<?php include('student.php'); ?>

<div class="home">
    <div class="card">
        <div class="head">
            <img src="images/logos/student_<?php echo htmlspecialchars($id) . '.png'; ?>" alt="images/logos/default.png" class="logo-image">

            <div class="student-name"><?php echo htmlspecialchars($student['name']); ?></div>
        </div>

        <div class="info-row">
            <div class="info-label">Student ID:</div>
            <div class="info-value"><?php echo htmlspecialchars($student['id']); ?></div>
        </div>

        <div class="info-row">
            <div class="info-label">Email:</div>
            <div class="info-value"><?php echo htmlspecialchars($student['email']); ?></div>
        </div>

        <div class="info-row">
            <div class="info-label">Contact:</div>
            <div class="info-value"><?php echo htmlspecialchars($student['mobile']); ?></div>
        </div>
        
        <div class="info-row">
            <div class="info-label">Date-of-Birth:</div>
            <div class="info-value"><?php echo htmlspecialchars($student['dob']); ?></div>
        </div>
        
        <div class="info-row">
            <div class="info-label">Industry Type:</div>
            <div class="info-value"><?php echo htmlspecialchars($student['gender']); ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Qualification:</div>
            <div class="info-value"><?php echo htmlspecialchars($student['qualification']); ?></div>
        </div>

        <div class="info-row">
            <div class="info-label">Branch:</div>
            <div class="info-value"><?php echo htmlspecialchars($student['branch']); ?></div>
        </div>
         <div class="info-row">
            <div class="info-label">Batch:</div>
            <div class="info-value"><?php echo htmlspecialchars($student['batch']); ?></div>
        </div>
         <div class="info-row">
            <div class="info-label">CGPA:</div>
            <div class="info-value"><?php echo htmlspecialchars($student['cgpa']); ?></div>
        </div>


        <div class="info-row">
            <div class="info-label">Description:</div>
            <div class="info-value"><?php echo nl2br(htmlspecialchars($student['description'])); ?></div>
        </div>

        <div class="profile-actions">
            <a href="edit_student.php" class="btn edit-btn">Edit</a>
            <a  class="btn delete-btn" onclick="showDeleteConfirm()">Delete</a>

        </div>
    </div>
</div>

    <div class="confirm-overlay" id="deleteConfirmBox">
        <div class="confirm-box">
            <h2>Do you want to delete?</h2>
            <form method="post">
                <button type="submit" name="delete_confirm" class="yes-btn">Yes</button>
                <button type="button" class="no-btn" onclick="hidedeleteConfirm()">No</button>
            </form>
        </div>
    </div>

    <script>
        function showDeleteConfirm() {
            document.getElementById('deleteConfirmBox').style.display = 'flex';
        }

        function hidedeleteConfirm() {
            document.getElementById('deleteConfirmBox').style.display = 'none';
        }
    </script>

</body>
</html>
