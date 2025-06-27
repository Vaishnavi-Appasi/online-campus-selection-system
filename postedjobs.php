<?php 
  include('connection.php');
  session_start();
  $success = false;
  $errorMsg = "";
  $result = null; 

  if (isset($_SESSION['cid'])) {
    $cid =$_SESSION['cid'];

    $query = "SELECT * FROM job WHERE cid = '$cid'";
    $result = mysqli_query($con, $query);

    if (!$result) 
    {
      die("Connection error.");
    }
  }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Posted Jobs</title>
    
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
          margin: 100px 0;
        }
        .no-jobs h1 {
          font-size: 70px;
        }
        .no-jobs p {
          margin: 20px 0;
          font-size: 18px;
        }
        .no-jobs .post {
          padding: 10px 20px;
          font-size: 20px;
          border: none;
          border-radius: 5px;
          background-color: skyblue;
          cursor: pointer;
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
          display: flex;
          justify-content: space-between;
          align-items: center;
          margin-top: 15px;
        }
        .button-group {
            display: flex;
            gap: 10px;
        }
        .edit-btn,
        .delete-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            color: white;
            cursor: pointer;
            font-weight: bold;
        }

        .edit-btn { background: #007bff; }
        .delete-btn { background: #dc3545; }
    </style>
</head>
<body>
    
    <?php include('company.php')?>

    <div class="home" id="home">
        <?php
            if (mysqli_num_rows($result) == 0) {
                echo "<div class='no-jobs'>
                        <h1>Welcome back!</h1>
                        <p>You haven't posted anything yet</p>
                        <a href='post_job.php?cid=" . $cid . "'><button class='post'>Post Job</button></a>
                      </div>";
                exit();
            }
        ?>

        <div class="welcome">
            <h1>WELCOME BACK!</h1>
            <h2>Here are the jobs you have posted...</h2>
        </div>

        <div class="container">
     <?php
          while ($row = mysqli_fetch_assoc($result)) {
              $job_id = urlencode($row['id']);
              echo '
              <div class="box">
                  <h1>' . htmlspecialchars($row['title']) . '</h1>
                  <h2><i class="fa fa-map-marker"></i> ' . htmlspecialchars($row['location']) . '</h2>
                  <h2><i class="fa fa-money"></i> ' . htmlspecialchars($row['salary']) . '</h2>
                  <h2><i class="fa fa-briefcase"></i> ' . htmlspecialchars($row['experience']) . '</h2>
                  <h2><i class="fa fa-calendar"></i> ' . htmlspecialchars($row['date_posted']) . '</h2>
                  <div class="edit">
                      <div>
                          <a href="view_applicants.php?job_id=' . $job_id . '">View Applicants</a>
                      </div>
                      <a href="edit_job.php?job_id=' . $job_id . '"><button class="edit-btn">Edit</button></a>
                      <a href="delete_job.php?job_id=' . $job_id . '"><button class="delete-btn">Delete</button></a>
                  </div>
              </div>';
          }
      ?>

        </div>
    </div>

</body>
</html>