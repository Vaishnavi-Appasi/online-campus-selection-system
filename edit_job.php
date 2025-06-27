<?php
session_start();
  include('connection.php');
  $success = false;
  $error = "";
  if (!isset($_GET['job_id'])) 
  {
    die('invalid job id');
  }
  else
  {
      $job_id = $_GET['job_id'];

      $sql = "SELECT * FROM job WHERE id = ?";
      $stmt = $con->prepare($sql);
      $stmt->bind_param("i", $job_id);
      $stmt->execute();
      $result = $stmt->get_result();

      if ($result->num_rows === 0) {
          die("Job with ID $job_id not found.");
      }
      $job = $result->fetch_assoc();
      if ($_SERVER["REQUEST_METHOD"] == "POST") 
      {
          $title = mysqli_real_escape_string($con, trim($_POST['title']));
          $location = mysqli_real_escape_string($con, trim($_POST['location']));
          $salary = mysqli_real_escape_string($con, trim($_POST['salary']));
          $experience = mysqli_real_escape_string($con, trim($_POST['experience']));
          $qualification = mysqli_real_escape_string($con, trim($_POST['qualification']));
          $skills = mysqli_real_escape_string($con, trim($_POST['skills']));
          $description = mysqli_real_escape_string($con, trim($_POST['description']));

          if (empty($title) || empty($location) || empty($salary) || empty($experience) ||empty($qualification) || empty($skills) || empty($description)) 
          {
              $error = "All fields are required.";
          }

          if (empty($error)) 
          {
              $update_sql = "UPDATE job SET title = ?, location = ?, salary = ?, experience = ?, qualification = ?, skills = ?, description = ? WHERE id = ?";
              $stmt_update = $con->prepare($update_sql);
              $stmt_update->bind_param("sssssssi", $title, $location, $salary, $experience, $qualification, $skills, $description, $job_id);

              if ($stmt_update->execute())
              {
                  $cid = $job['cid'];
                  header("Location: postedjobs.php");
                  exit;
              } else 
              {
                  $error = "Database error: " . $stmt_update->error;
              }
          }
      }
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Edit Job</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    .container {
      background: #fff;
      max-width: 800px;
      margin: auto;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      color: #007bff;
    }
    .form-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
    }
    label {
      font-weight: bold;
    }
    input, select, textarea {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 5px;
      box-sizing: border-box;
    }
    textarea {
      min-height: 100px;
      resize: vertical;
    }
    .full-width {
      grid-column: span 2;
    }
    .btn-container {
      text-align: center;
      margin-top: 20px;
    }
    .btn {
      padding: 12px 30px;
      font-size: 16px;
      background-color: #007bff;
      color: #fff;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }
    .btn:hover {
      background-color: #0056b3;
    }
    .message {
      margin-top: 20px;
      padding: 12px;
      border-radius: 6px;
      text-align: center;
      font-weight: bold;
    }
    .error {
      background-color: #f8d7da;
      color: #721c24;
    }
    .success {
      background-color: #d4edda;
      color: #155724;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Edit Job</h2>
    <?php if ($error): ?>
      <div class="message error"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
      <div class="message success">Job updated successfully!</div>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="form-grid">
        <div>
          <label>Job Title</label>
          <input type="text" name="title" value="<?= htmlspecialchars($job['title']) ?>" required>
        </div>

        <div>
          <label>Location</label>
          <input type="text" name="location" value="<?= htmlspecialchars($job['location']) ?>" required>
        </div>

        <div>
          <label>Salary</label>
          <input type="text" name="salary" value="<?= htmlspecialchars($job['salary']) ?>" required>
        </div>

        <div>
          <label>Experience</label>
          <input type="text" name="experience" value="<?= htmlspecialchars($job['experience']) ?>" required>
        </div>

        <div class="full-width">
          <label>Qualification</label>
          <select name="qualification" required>
            <option value="">Select Qualification</option>
            <?php 
              $qualifications = ["Any Degree", "B.Tech CSE", "B.Tech ECE", "B.Tech EEE", "B.Tech Civil", "B.Tech Mechanical", "BCA", "MCA", "MBA"];
              foreach ($qualifications as $q) {
                $selected = ($q === $job['qualification']) ? "selected" : "";
                echo "<option value='" . htmlspecialchars($q) . "' $selected>" . htmlspecialchars($q) . "</option>";
              }
            ?>
          </select>
        </div>

        <div class="full-width">
          <label>Skills</label>
          <input type="text" name="skills" value="<?= htmlspecialchars($job['skills']) ?>" required>
        </div>

        <div class="full-width">
          <label>Job Description</label>
          <textarea name="description" required><?= htmlspecialchars($job['description']) ?></textarea>
        </div>
      </div>

      <div class="btn-container">
        <button type="submit" class="btn">Update Job</button>
      </div>
      <a class="back-link" href="javascript:history.back()">← Back</a>
    </form>
  </div>
</body>
</html>
