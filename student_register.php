<?php
include('connection.php');

$id_error = "";
$email_error = "";
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password_plain = $_POST['password'];
    $dob = $_POST['dob'];
    $gender = mysqli_real_escape_string($con, $_POST['gender']);
    $mobile = mysqli_real_escape_string($con, $_POST['mobile']);
    $qualification = mysqli_real_escape_string($con, $_POST['qualification']);
    $branch = mysqli_real_escape_string($con, $_POST['branch']);
    $batch = mysqli_real_escape_string($con, $_POST['batch']);
    $cgpa = (float)$_POST['cgpa'];
    $skills = mysqli_real_escape_string($con, $_POST['skills']);
    $description = mysqli_real_escape_string($con, $_POST['description']);

    $check_id = mysqli_query($con, "SELECT id FROM student WHERE id = '$id'");
    if (mysqli_num_rows($check_id) > 0) {
        $id_error = "Student ID already exists.";
    }
     if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password_plain)) {
    $message = "Password must contain at least 1 uppercase letter, 1 lowercase letter, 1 digit, 1 special character, and be at least 8 characters long.";
    }
    $check_email = mysqli_query($con, "SELECT email FROM student WHERE email = '$email'");
    if (mysqli_num_rows($check_email) > 0) {
        $email_error = "Email already exists.";
    }

    if (empty($id_error) && empty($email_error)) {
        $target_dir = "images/logos/";
        $ext = strtolower(pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($ext, $allowed)) {
            $message = "Invalid photo file type.";
        } else {
            $photo_name = "student_" . $id . "." . $ext;
            $target_file = $target_dir . $photo_name;

            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                $password = password_hash($password_plain, PASSWORD_DEFAULT);

                $sql = "INSERT INTO student (photo, id, name, email, password, dob, gender, mobile, qualification, branch, batch, cgpa, skills, description)
                        VALUES ('$photo_name', '$id', '$name', '$email', '$password', '$dob', '$gender', '$mobile', '$qualification', '$branch', '$batch', '$cgpa', '$skills', '$description')";
                $_SESSION['sid']=$id;
                if (mysqli_query($con, $sql)) {
                    header("Location: all_jobs.php");
                    exit();
                } else {
                    $message = "Database error: " . mysqli_error($con);
                }
            } else {
                $message = "Photo upload failed.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Student Registration</title>
  <style>
    * {
      padding: 0; margin: 0; box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    body {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }
    .container {
      background: #fff;
      width: 90%;
      max-width: 900px;
      border-radius: 10px;
      padding: 25px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
    .title {
      text-align: center;
      font-size: 26px;
      font-weight: bold;
      margin-bottom: 20px;
      color: #333;
    }
    form {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
    }
    .form-group {
      flex: 1 1 45%;
      display: flex;
      flex-direction: column;
      position: relative;
    }
    .form-group.full-width {
      flex: 1 1 100%;
    }
    .form-group input,
    .form-group select,
    .form-group textarea {
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 14px;
    }
    .form-group textarea {
      resize: vertical;
    }
    .btn-container {
      width: 100%;
      display: flex;
      justify-content: center;
    }
    .btn {
      width: 200px;
      background: #fda085;
      color: #fff;
      font-size: 15px;
      font-weight: bold;
      border: none;
      padding: 10px;
      border-radius: 6px;
      cursor: pointer;
      transition: 0.3s ease;
    }
    .btn:hover {
      background: #fc7e45;
    }
    .message {
      text-align: center;
      margin-bottom: 15px;
      font-weight: 600;
      color: green;
    }
    .error {
      color: red;
      font-size: 12px;
      position: absolute;
      bottom: -18px;
    }
    .password-wrapper {
      position: relative;
    }
    .toggle-password {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      font-size: 18px;
      cursor: pointer;
      color: #666;
      font-family: 'Segoe UI Emoji', 'Apple Color Emoji', 'Noto Color Emoji', 'Segoe UI Symbol', sans-serif;
      user-select: none;
    }
    #dob {
      width: 100%;
      max-width: 300px; 
    }
    input[type="file"] {
      width: 300px;
    }
    @media (max-width: 768px) {
      form {
        flex-direction: column;
      }
      .form-group {
        flex: 1 1 100%;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="title">Student Registration</div>

    <?php if (!empty($message)) : ?>
      <p class="message <?php echo (str_contains($message, 'error') || str_contains($message, 'failed')) ? 'error' : ''; ?>">
        <?php echo htmlspecialchars($message); ?>
      </p>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data" id="regForm" novalidate>

      <div class="form-group">
        <input type="text" name="id" placeholder="Student ID" value="<?php echo $_POST['id'] ?? ''; ?>" required />
        <?php if ($id_error): ?><div class="error"><?php echo $id_error; ?></div><?php endif; ?>
      </div>

      <div class="form-group">
        <input type="text" name="name" placeholder="Full Name" value="<?php echo $_POST['name'] ?? ''; ?>" required />
      </div>

      <div class="form-group full-width">
        <input type="email" name="email" placeholder="Email" value="<?php echo $_POST['email'] ?? ''; ?>" required />
        <?php if ($email_error): ?><div class="error"><?php echo $email_error; ?></div><?php endif; ?>
      </div>

      <div class="form-group password-wrapper">
        <input type="password" name="password" id="password" placeholder="Password" required />
        <span class="toggle-password" onclick="togglePassword('password', this)">👁️</span>
      </div>
      <div class="form-group password-wrapper">
        <input type="password" id="confirmPassword" placeholder="Confirm Password" required />
        <span class="toggle-password" onclick="togglePassword('confirmPassword', this)">👁️</span>
      </div>

      <div class="form-group">
        <label>Date of Birth:&nbsp;&nbsp;&nbsp;&nbsp;
          <input type="date" name="dob" id="dob" value="<?php echo $_POST['dob'] ?? ''; ?>" required>
        </label>
      </div>

      <div class="form-group">
        <div style="display: flex; font-weight: bold; gap: 50px; margin-left: 5px; margin-top: 5px;">
          <label>Gender:</label>
          <label style="display: flex; align-items: center; gap: 5px;">
            <input type="radio" name="gender" value="Male" required <?php if (($_POST['gender'] ?? '') == 'Male') echo 'checked'; ?>> Male
          </label>
          <label style="display: flex; align-items: center; gap: 5px;">
            <input type="radio" name="gender" value="Female" <?php if (($_POST['gender'] ?? '') == 'Female') echo 'checked'; ?>> Female
          </label>
          <label style="display: flex; align-items: center; gap: 5px;">
            <input type="radio" name="gender" value="Other" <?php if (($_POST['gender'] ?? '') == 'Other') echo 'checked'; ?>> Other
          </label>
        </div>
      </div>

      <div class="form-group">
        <input type="tel" name="mobile" placeholder="Mobile Number" value="<?php echo $_POST['mobile'] ?? ''; ?>"/>
      </div>
      <div class="form-group">
        <label>Profile photo:&nbsp;&nbsp;
          <input type="file" name="photo" accept=".jpg,.jpeg,.png,.gif"/>
        </label>
      </div>

      <div class="form-group">
        <select name="qualification" required>
          <option value="">Select Qualification</option>
          <option value="BTech" <?php if (($_POST['qualification'] ?? '') == 'BTech') echo 'selected'; ?>>BTech</option>
          <option value="MTech" <?php if (($_POST['qualification'] ?? '') == 'MTech') echo 'selected'; ?>>MTech</option>
        </select>
      </div>
      <div class="form-group">
               <select name="branch" required>
          <option value="">Select Branch</option>
          <option value="CSE" <?php if (($_POST['branch'] ?? '') == 'CSE') echo 'selected'; ?>>CSE</option>
          <option value="ECE" <?php if (($_POST['branch'] ?? '') == 'ECE') echo 'selected'; ?>>ECE</option>
          <option value="civil" <?php if (($_POST['branch'] ?? '') == 'Civil') echo 'selected'; ?>>civil</option>
          <option value="Mechanical" <?php if (($_POST['branch'] ?? '') == 'Mechanical') echo 'selected'; ?>>Mechanical</option>
        </select>
      </div>

      <div class="form-group">
               <select name="batch" required>
          <option value="">Select Batch</option>
          <option value="2022-2026" <?php if (($_POST['batch'] ?? '') == '2022-2026') echo 'selected'; ?>>2022-2026</option>
          <option value="2023-2027" <?php if (($_POST['batch'] ?? '') == '2023-2027') echo 'selected'; ?>>2023-2027</option>
          <option value="2022-2026" <?php if (($_POST['batch'] ?? '') == '2022-2026') echo 'selected'; ?>>2024-2028</option>
          <option value="2023-2027" <?php if (($_POST['batch'] ?? '') == '2023-2027') echo 'selected'; ?>>2025-2029</option>
        </select>
      </div>
      <div class="form-group">
        <input type="number" step="0.01" name="cgpa" placeholder="CGPA" value="<?php echo $_POST['cgpa'] ?? ''; ?>" min="0" max="10" />
      </div>

      <div class="form-group full-width">
        <input type="text" name="skills" placeholder="Skills (comma separated)" value="<?php echo $_POST['skills'] ?? ''; ?>"/>
      </div>
      <div class="form-group full-width">
        <textarea name="description" rows="3" placeholder="Description about yourself" required><?php echo $_POST['description'] ?? ''; ?></textarea>
      </div>

      <div class="btn-container">
        <button type="submit" class="btn">Register</button>
      </div>
    </form>
  </div>

  <script>
    function togglePassword(fieldId, icon) {
      const input = document.getElementById(fieldId);
      if (input.type === "password") {
        input.type = "text";
        icon.textContent = "👁️"; 
      } else {
        input.type = "password";
        icon.textContent = "👁️";  
      }
    }
    document.getElementById('regForm').addEventListener('submit', function(e) {
    const pass = document.getElementById('password').value;
  const confirm = document.getElementById('confirmPassword').value;

  const pattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;

  if (pass !== confirm) {
    alert("Passwords do not match!");
    e.preventDefault();
  } else if (!pattern.test(pass)) {
    alert("Password must be at least 8 characters long and include:\n- One uppercase letter\n- One lowercase letter\n- One number\n- One special character");
    e.preventDefault();
  }
    });
  </script>
</body>
</html>