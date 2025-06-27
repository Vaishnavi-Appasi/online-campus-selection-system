<?php 
  include('connection.php');
  session_start();

  if (!isset($_SESSION['cid'])) 
  {
      die("Error: Company ID (cid) not provided in the URL.");
  }
  $cid = $_SESSION['cid'];

  $success = false;
  $errorMsg = "";

  if ($_SERVER["REQUEST_METHOD"] == "POST") 
  {
      $title         = mysqli_real_escape_string($con, $_POST['title']);
      $location      = mysqli_real_escape_string($con, $_POST['location']);
      $salary        = mysqli_real_escape_string($con, $_POST['salary']);
      $experience    = mysqli_real_escape_string($con, $_POST['experience']);
      $qualification = mysqli_real_escape_string($con, $_POST['qualification']);
      $skills        = mysqli_real_escape_string($con, $_POST['skills']);
      $description   = mysqli_real_escape_string($con, $_POST['description']);
      $sql = "INSERT INTO job (cid, title, location, experience, salary, qualification, skills, description)
          VALUES ('$cid', '$title', '$location', '$experience', '$salary', '$qualification', '$skills', '$description')";

      $result = mysqli_query($con, "SELECT id FROM company WHERE id = '$cid'");
      if (mysqli_num_rows($result) == 0)
      {
          die("$cid Error: Invalid Company ID.");
      }

      if (mysqli_query($con, $sql)) 
      {
          $success = true;
      } 
      else 
      {
          $errorMsg = mysqli_error($con);
      }
  }
?>
<!DOCTYPE html>
<html>
  <head>
      <title>Post a Job</title>
      <style>
      .container {
          background-color: #ffffff;
          padding: 30px 40px;
          border-radius: 10px;
          box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
          width: 100%;
          max-width: 700px;
      }
      .container h1 {
          text-align: center;
          margin-bottom: 30px;
          font-family: 'Georgia', serif;
          font-size: 28px;
          color: #333;
      }
      .form-grid {
          display: grid;
          grid-template-columns: 1fr 1fr;
          gap: 20px;
      }
      .full-width {
          grid-column: span 2;
      }
      input[type="text"],
      textarea {
          width: 100%;
          padding: 10px 12px;
          border: 1px solid #ccc;
          border-radius: 6px;
          font-size: 14px;
      }
      .qualification {
        display: flex;
        align-items: center;
        gap: 15px; 
      }

      .qualification label {
        min-width: 180px;
      }

      .qualification select {
        flex: 1;
        padding: 10px 12px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 14px;
      }

      .submit-btn {
          grid-column: span 2;
          text-align: center;
          margin-top: 20px;
      }
      button[type="submit"] {
          padding: 10px 30px;
          background-color: #008cba;
          color: white;
          font-size: 16px;
          border: none;
          border-radius: 6px;
          cursor: pointer;
          transition: background-color 0.3s ease;
      }
      button[type="submit"]:hover {
          background-color: #0072a1;
      }

      .success-message,
      .error-message {
          margin-top: 20px;
          text-align: center;
          font-weight: bold;
          padding: 10px;
          border-radius: 5px;
      }

      .success-message {
          color: green;
          background-color: #e6ffea;
          border: 1px solid green;
      }

      .error-message {
          color: red;
          background-color: #ffe6e6;
          border: 1px solid red;
      }
      </style>
  </head>
  <body>
      
    <?php include('company.php')?>
      <div class="home" id="home">
        <div class="container">
          <h1>Post a Job</h1>
          <form method="POST" action="">
            <div class="form-grid">
              <input type="text" name="title" placeholder="Job Title" required>
              <input type="text" name="location" placeholder="Location" required>
              <input type="text" name="salary" placeholder="Salary" required>
              <input type="text" name="experience" placeholder="Experience" required>
              
              <div class="full-width">
                <div class="qualification">
                <label for="qualification">Required Qualification : </label>
                <select name="qualification" required>
                  <option value="">Select Qualification</option>
                  <option value="Any Degree">Any Degree</option>
                  <option value="B.Tech CSE">B.Tech CSE</option>
                  <option value="B.Tech ECE">B.Tech ECE</option>
                  <option value="B.Tech EEE">B.Tech EEE</option>
                  <option value="B.Tech Civil">B.Tech Civil</option>
                  <option value="B.Tech Mechanical">B.Tech Mechanical</option>
                  <option value="BCA">BCA</option>
                  <option value="MCA">MCA</option>
                  <option value="MBA">MBA</option>
                </select>
              </div>
            </div>

              <div class="full-width">
                <input type="text" name="skills" placeholder="skills required" required>
              </div>

              <div class="full-width">
                <textarea name="description" placeholder="Job description" required></textarea>
              </div>
            </div>

            <div class="submit-btn">
              <button type="submit">Post Job</button>
            </div>

            <?php if ($success): ?>
              <div class="success-message">Job posted successfully!</div>
            <?php elseif (!empty($errorMsg)): ?>
              <div class="error-message">Error: <?= htmlspecialchars($errorMsg) ?></div>
            <?php endif; ?>
          </form>
        </div>
      </div>

  </body>
</html>  