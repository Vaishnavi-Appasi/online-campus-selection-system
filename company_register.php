<?php
session_start();
  include('connection.php');

  $id_error = "";
  $email_error = "";
  $message = "";

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password_plain = $_POST['password'];
    $contact = mysqli_real_escape_string($con, $_POST['contact']);
    $location = mysqli_real_escape_string($con, $_POST['location']);
    $employees = mysqli_real_escape_string($con, $_POST['employees']);
    $website = mysqli_real_escape_string($con, $_POST['website']);
    $industry = mysqli_real_escape_string($con, $_POST['industry']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $date = date('Y-m-d');

    if (!ctype_digit($id) || (int)$id <= 0) 
    {
        $id_error = "ID must be a positive integer.";
    } 
    else 
    {
        $check_id_sql = "SELECT id FROM company WHERE id = ?";
        $check_id_stmt = $con->prepare($check_id_sql);
        $check_id_stmt->bind_param('i', $id);
        $check_id_stmt->execute();
        $check_id_res = $check_id_stmt->get_result();
        if ($check_id_res->num_rows > 0) 
        {
          $id_error = "This ID is already taken by another company.";
        }
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
    {
        $email_error = "Invalid email format.";
    } 
    else 
    {
        $check_sql = "SELECT id FROM company WHERE email = ? AND id != ?";
        $check_stmt = $con->prepare($check_sql);
        $check_stmt->bind_param('si', $email, $id);
        $check_stmt->execute();
        $check_res = $check_stmt->get_result();
        if ($check_res->num_rows > 0) 
        {
            $email_error = "Email already exists";
        }
    }
      if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password_plain)) {
    $message = "Password must contain at least 1 uppercase letter, 1 lowercase letter, 1 digit, 1 special character, and be at least 8 characters long.";
    }

    if ($employees !== "" && !is_numeric($employees)) 
    {
        $message = "Number of employees must be numeric.";
    }

    if ($website !== "" && !filter_var($website, FILTER_VALIDATE_URL))
    {
        $message = "Invalid website URL format.";
    }

    if (empty($id_error) && empty($email_error)&&empty($message)) 
    {
        $target_dir = "images/logos/";
        $ext = strtolower(pathinfo($_FILES["logo"]["name"], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($ext, $allowed))
        {
            $message = "Invalid logo file type.";
        } 
        else 
        {
            $logo_name = "company_" . $id . "." . $ext;
            $target_file = $target_dir . $logo_name;

            if (move_uploaded_file($_FILES["logo"]["tmp_name"], $target_file)) 
            {
                $password_hashed = password_hash($password_plain, PASSWORD_DEFAULT);

                $sql = "INSERT INTO company (id, name, email, password, contact, location, employes, website, industry, date, description, logo)
                        VALUES ('$id', '$name', '$email', '$password_hashed', '$contact', '$location', '$employees', '$website', '$industry', '$date', '$description', '$logo_name')";

                if (mysqli_query($con, $sql)) 
                {
                  $_SESSION['cid']=$id;
                  header("Location: postedjobs.php");
                  exit();
                }

                else 
                {
                  $message = "Error: " . mysqli_error($con);
                }
            } 
            else 
            {
                $message = "Logo upload failed.";
            }
        }
    }
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Company Registration</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    * {
      box-sizing: border-box;
      margin: 0; padding: 0;
      font-family: Arial, sans-serif;
    }
    body {
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 20px;
    }
    .container {
      background: #fff;
      padding: 25px;
      border-radius: 10px;
      max-width: 800px;
      width: 100%;
      box-shadow: 0 0 15px rgba(0,0,0,0.2);
    }
    h2 {
      text-align: center;
      margin-bottom: 20px;
    }
    form {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
    }
    .form-group {
      flex: 1 1 45%;
      display: flex;
      flex-direction: row;
      position: relative;
      align-items: center;
      gap: 10px;
    }
    .form-group.full-width {
      flex: 1 1 100%;
    }
    input, select, textarea {
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 14px;
      width: 100%;
    }

    .password-wrapper {
      position: relative;
    }
    .password-wrapper input[type="password"],
    .password-wrapper input[type="text"] {
      padding-right: 35px;
    }
    .toggle-visibility {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      user-select: none;
      font-size: 18px;
      color: #666;
    }
    .btn {
      background: #9face6;
      color: white;
      padding: 10px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-weight: bold;
    }
    .btn:hover {
      background: #6c83d3;
    }
    .error {
      color: red;
      font-size: 12px;
      margin-top: 5px;
    }
    .message {
      text-align: center;
      margin-bottom: 15px;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Signup</h2>

    <?php if (!empty($message)) : ?>
      <p class="message error"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" id="regForm" novalidate>
      <div class="form-group">
        <input type="text" name="id" placeholder="Company ID" required value="<?php echo $_POST['id'] ?? ''; ?>">
        <?php if ($id_error): ?><div class="error"><?php echo $id_error; ?></div><?php endif; ?>
      </div>

      <div class="form-group">
        <input type="text" name="name" placeholder="Company Name" required value="<?php echo $_POST['name'] ?? ''; ?>">
      </div>

      <div class="form-group">
        <input type="email" name="email" placeholder="Email" required value="<?php echo $_POST['email'] ?? ''; ?>">
        <?php if ($email_error): ?><div class="error"><?php echo $email_error; ?></div><?php endif; ?>
      </div>

      <div class="form-group">
        <input type="text" name="contact" placeholder="Contact Number" required value="<?php echo $_POST['contact'] ?? ''; ?>">
      </div>

      <div class="form-group password-wrapper">
        <input type="password" name="password" id="password" placeholder="Password" required>
        <span class="toggle-visibility" onclick="togglePassword('password', this)">👁️</span>
      </div>

      <div class="form-group password-wrapper">
        <input type="password" id="confirmPassword" placeholder="Confirm Password" required>
        <span class="toggle-visibility" onclick="togglePassword('confirmPassword', this)">👁️</span>
      </div>

      <div class="form-group">
        <input type="text" name="location" placeholder="Location" required value="<?php echo $_POST['location'] ?? ''; ?>">
      </div>

      <div class="form-group">
        <input type="text" name="employees" placeholder="No. of Employees" required value="<?php echo $_POST['employees'] ?? ''; ?>">
      </div>

      <div class="form-group">
        <select name="industry" required>
          <option value="">Select Industry</option>
          <option value="Technology" <?php if (($_POST['industry'] ?? '') === 'Technology') echo 'selected'; ?>>Technology</option>
          <option value="Finance" <?php if (($_POST['industry'] ?? '') === 'Finance') echo 'selected'; ?>>Finance</option>
          <option value="Healthcare" <?php if (($_POST['industry'] ?? '') === 'Healthcare') echo 'selected'; ?>>Healthcare</option>
          <option value="Others" <?php if (($_POST['industry'] ?? '') === 'Others') echo 'selected'; ?>>Others</option>
        </select>
      </div>

  <div class=" form-group">
    <label for="logo"><b>&nbspLogo:</b></label>
    <input type="file" name="logo" id="logo" accept=".jpg,.jpeg,.png,.gif" required>
  </div>


      <div class="form-group">
         <input type="text" name="website" placeholder="Website URL" required value="<?php echo $_POST['website'] ?? ''; ?>">
      </div>

      <div class="form-group full-width">
        <textarea name="description" rows="4" placeholder="Company Description" required><?php echo $_POST['description'] ?? ''; ?></textarea>
      </div>

      <div class="form-group full-width">
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