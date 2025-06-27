<?php
session_start();
$email_error = $id_error = $success_message = "";
$name = $email = $contact = $qualification = $skills = "";

if (!isset($_SESSION['jid']) ) {
    die("Invalid Job ID.");
}

$jid = $_SESSION['jid'];
$sid=$_SESSION['sid'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'connection.php';

    $name = mysqli_real_escape_string($con, $_POST['name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $contact = mysqli_real_escape_string($con, $_POST['contact']);
    $qualification = mysqli_real_escape_string($con, $_POST['qualification']);
    $skills = mysqli_real_escape_string($con, $_POST['skills']);

    $exist_id_query = "SELECT * FROM css.student WHERE id = $sid";
    $exist_id_result = mysqli_query($con, $exist_id_query);

    if (mysqli_num_rows($exist_id_result) === 0) 
    {
        $id_error = "Incorrect Student ID.";
    } 
    else 
    {
        $check_email_query = "SELECT * FROM css.application WHERE email = '$email' AND jid = $jid";
        $check_id_query = "SELECT * FROM css.application WHERE sid = '$sid' AND jid = $jid";

        $email_exists = mysqli_query($con, $check_email_query);
        $id_exists = mysqli_query($con, $check_id_query);

        if (mysqli_num_rows($email_exists) > 0) 
        {
            $email_error = "This email is already registered for this job.";
        } elseif (mysqli_num_rows($id_exists) > 0)
        {
            $id_error = "This student ID has already applied for this job.";
        } 
        
        else {
            if (isset($_FILES['resume']) && $_FILES['resume']['error'] === 0) {
                $file_ext = pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION);
                $allowed_ext = ['pdf', 'doc', 'docx'];

                if (in_array(strtolower($file_ext), $allowed_ext)) {
                    $resume_name = "resume_" . $sid . "." . $file_ext;
                    $resume_tmp = $_FILES['resume']['tmp_name'];
                    $upload_dir = "resumes/";
                    $resume_path = $upload_dir . $resume_name;

                    if (move_uploaded_file($resume_tmp, $resume_path)) {
                        $sql = "INSERT INTO css.application (sid, name, email, contact, qualification, skills, resume, jid)
                                VALUES ('$sid', '$name', '$email', '$contact', '$qualification', '$skills', '$resume_name', $jid)";

                        if (mysqli_query($con, $sql)) {
                            $success_message = "Applied successfully!";
                        } else {
                            $id_error = "Error in application. Please try again.";
                        }
                    } else {
                        $id_error = "Failed to upload resume.";
                    }
                } else {
                    $id_error = "Invalid file type. Only PDF, DOC, DOCX allowed.";
                }
            } else {
                $id_error = "Resume upload error.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Apply for Job</title>
  <link rel="stylesheet" href="student.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"/>
  <style>
    .home { width: 100%; display: flex; justify-content: center; align-items: center; }
    section { padding: 20px; width: 100%; display: flex; justify-content: center; align-items: center; }
    section form {
      background-color: #fff;
      padding: 30px 40px;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 600px;
    }
    section form h2 { text-align: center; margin-bottom: 25px; font-size: 24px; color: #333; }
    section form label { display: block; margin-bottom: 6px; font-weight: 600; color: #444; }
    section form input[type="text"],
    section form input[type="email"],
    section form input[type="file"],
    section form input[type="submit"] {
      width: 100%;
      padding: 10px 12px;
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 14px;
    }
    section form input[type="submit"] {
      background-color: #007bff;
      color: white;
      font-size: 16px;
      font-weight: bold;
      cursor: pointer;
    }
    section form input[type="submit"]:hover {
      background-color: #0056b3;
    }
    .error { color: red; font-size: 13px; margin-top: -15px; margin-bottom: 10px; }
    .success { color: green; font-size: 15px; font-weight: bold; text-align: center; margin-bottom: 15px; }

  </style>
</head>
<body>
  <input type="checkbox" id="checkbox">
  <?php include "student.php"; ?>
  <div class="home">
    <section>
      <form action="apply.php?sid=<?= $sid ?>&jid=<?= $jid ?>" method="post" enctype="multipart/form-data">
        <h2>Student Job Application</h2>

        <?php if ($success_message): ?>
          <div class="success"><?= $success_message ?></div>
        <?php endif; ?>

        <label for="sid">ID:</label>
        <input type="text" id="sid_display" value="<?= htmlspecialchars($sid) ?>" readonly>
        <input type="hidden" name="sid" value="<?= htmlspecialchars($sid) ?>">
        <?php if ($id_error): ?><div class="error"><?= $id_error ?></div><?php endif; ?>

        <label for="name">Full Name:</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>" required>

        <label for="email">Email Address:</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
        <?php if ($email_error): ?><div class="error"><?= $email_error ?></div><?php endif; ?>

        <label for="contact">Contact Number:</label>
        <input type="text" id="contact" name="contact" value="<?= htmlspecialchars($contact) ?>" required>

        <label for="qualification">Qualification:</label>
        <input type="text" id="qualification" name="qualification" value="<?= htmlspecialchars($qualification) ?>" required>

        <label for="skills">Skills (comma separated):</label>
        <input type="text" id="skills" name="skills" value="<?= htmlspecialchars($skills) ?>" required>

        <label for="resume">Upload Resume:</label>
        <input type="file" id="resume" name="resume" accept=".pdf,.doc,.docx" required>

        <input type="submit" value="Apply Now">
        <a class="back-link" href="javascript:history.back()">← Back</a>
      </form>
    </section>
  </div>

  <script>
    const sidebarItems = document.querySelectorAll(".side-bar ul li");
    sidebarItems.forEach(item => {
      item.addEventListener("click", function () {
        sidebarItems.forEach(i => i.classList.remove("active"));
        this.classList.add("active");
      });
    });
  </script>
</body>
</html>
