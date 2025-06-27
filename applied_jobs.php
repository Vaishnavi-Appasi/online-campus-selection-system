<?php 
session_start();
include('connection.php');

$success = false;
$errorMsg = "";
$result = null;

if (isset($_SESSION['sid']) && is_numeric($_SESSION['sid'])) {
    $sid = intval($_SESSION['sid']);
} else {
    die("Invalid student ID.");
}

$query = "SELECT * FROM job WHERE id IN (SELECT jid FROM application WHERE sid = ?)";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $sid);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Applied Jobs</title>
    <style>
        .home h1.page-title {
          margin-top: 20px;
          font-size: 36px;
          color: #23242b;
        }
        .welcome {
          text-align: center;
          margin-bottom: 20px;
        }
        .welcome h1 {
          font-size: 50px;
        }
        .welcome h2 {
          font-size: 15px;
        }
        .no-jobs {
          text-align: center;
          margin: 100px 110px 100px 100px;
        }
        .no-jobs h1 {
          margin-top:100px;
          font-size: 30px;
        }
        .container {
          display: flex;
          flex-wrap: wrap;
          gap: 20px;
          padding: 20px;
          justify-content: center;
        }
        .box {
          width: 450px;
          background: #fff;
          border: 2px solid #ccc;
          border-radius: 10px;
          padding: 20px;
          display: flex;
          flex-direction: column;
          justify-content: space-between;
        }
        .box h1 {
          font-size: 22px;
          margin-bottom: 10px;
        }
        .box h2 {
          font-size: 16px;
          margin: 5px 0;
          color: #555;
        }
        .box p {
          margin-top: 10px;
          color: #444;
        }
     .edit {
    display: flex-end;
    margin-top: 15px;}
    </style>
</head>
<body>
    
<?php include('student.php')?>
<?php include('connection.php') ?>

<div class="home" id="home">
    <?php
        if (!$result || mysqli_num_rows($result) == 0) {
            echo "<div class='no-jobs'>
                    <h1>You have not applied to any jobs yet...</h1>
                  </div>";
            exit();
        }
    ?>

    <div class="welcome">
        <h1>WELCOME BACK!</h1>
        <h2>Here are the jobs you have applied...</h2>
    </div>

    <div class="container">
        <?php
           while ($row = mysqli_fetch_assoc($result)) {
            $jid = $row['id'];
                 $aidQuery = "SELECT id FROM css.application WHERE jid = ? AND sid = ?";
                  $aidStmt = $con->prepare($aidQuery);
                  $aidStmt->bind_param("ii", $jid, $sid);
                  $aidStmt->execute();
                  $aidResult = $aidStmt->get_result();
                  if ($application = $aidResult->fetch_assoc()) {
                      $_SESSION['aid'] = $application['id'];
                  }
                  $aidStmt->close();

                echo '
                <div class="box">
                    <h1>' . htmlspecialchars($row['title']) . '</h1>
                    <h2><i class="fa fa-map-marker"></i> ' . htmlspecialchars($row['location']) . '</h2>
                    <h2><i class="fa fa-money"></i> ' . htmlspecialchars($row['salary']) . '</h2>
                    <h2><i class="fa fa-briefcase"></i> ' . htmlspecialchars($row['experience']) . '</h2>
                    <h2><i class="fa fa-calendar"></i> ' . htmlspecialchars($row['date_posted']) . '</h2>
                    
                    <div class="edit">
                        <a href="view_application.php">view application</a>
                    </div>
                </div>';
            }
        ?>
    </div>
</div>

</body>
</html>