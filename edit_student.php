<?php
session_start();
include('connection.php');

$id_error = "";
$email_error = "";
$message = "";

if (isset($_SESSION['sid'])) {
    $old_id = $_SESSION['sid'];

    $sql = "SELECT * FROM student WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param('i', $old_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        die("Student not found.");
    }

    $student = $result->fetch_assoc();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $new_id = intval($_POST['id']);
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $dob = $_POST['dob'];
        $gender = $_POST['gender'];
        $mobile = trim($_POST['mobile']);
        $qualification = trim($_POST['qualification']);
        $branch = $_POST['branch'];
        $batch = trim($_POST['batch']);
        $cgpa = floatval($_POST['cgpa']);
        $skills = trim($_POST['skills']);
        $description = trim($_POST['description']);

        // Validations
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email_error = "Invalid email format.";
        } else {
            $check_sql = "SELECT email FROM student WHERE email = ? AND id != ?";
            $check_stmt = $con->prepare($check_sql);
            $check_stmt->bind_param('si', $email, $old_id);
            $check_stmt->execute();
            $check_res = $check_stmt->get_result();
            if ($check_res->num_rows > 0) {
                $email_error = "Email already exists for another student.";
            }
        }

        if (!preg_match('/^\d{10}$/', $mobile)) {
            $message = "Mobile number must be exactly 10 digits.";
        }

        if ($cgpa < 0 || $cgpa > 10) {
            $message = "CGPA must be between 0 and 10.";
        }

        // ID check
        if ($new_id != $old_id) {
            $id_check = $con->prepare("SELECT id FROM student WHERE id = ?");
            $id_check->bind_param('i', $new_id);
            $id_check->execute();
            $id_res = $id_check->get_result();
            if ($id_res->num_rows > 0) {
                $id_error = "New ID already exists. Please choose a different one.";
            }
        }

        if (empty($email_error) && empty($message) && empty($id_error)) {
            $target_dir = "images/logos/";
            $photo_to_save = $student['photo'];

            if (!empty($_FILES["photo"]["name"])) {
                $ext = strtolower(pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'gif'];

                if (!in_array($ext, $allowed)) {
                    $message = "Invalid photo file type. Allowed: jpg, jpeg, png, gif.";
                } else {
                    $photo_name = "student_" . $new_id . "." . $ext;
                    $target_file = $target_dir . $photo_name;

                    if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                        if (!empty($student['photo']) && $student['photo'] != $photo_name && file_exists($target_dir . $student['photo'])) {
                            unlink($target_dir . $student['photo']);
                        }
                        $photo_to_save = $photo_name;
                    } else {
                        $message = "Photo upload failed.";
                    }
                }
            }

            if (empty($message)) {
                $update_sql = "UPDATE student SET
                    id = ?, photo = ?, name = ?, email = ?, dob = ?, gender = ?, mobile = ?, qualification = ?, branch = ?, batch = ?, cgpa = ?, skills = ?, description = ?
                    WHERE id = ?";

                $stmt_update = $con->prepare($update_sql);
                $stmt_update->bind_param(
                    'isssssssssdsis',
                    $new_id,
                    $photo_to_save,
                    $name,
                    $email,
                    $dob,
                    $gender,
                    $mobile,
                    $qualification,
                    $branch,
                    $batch,
                    $cgpa,
                    $skills,
                    $description,
                    $old_id
                );

                if ($stmt_update->execute()) {
                    $message = "Profile updated successfully.";
                    $old_id = $new_id;
                    $_SESSION['sid'] = $new_id;
                    $stmt = $con->prepare($sql);
                    $stmt->bind_param('i', $old_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $student = $result->fetch_assoc();
                    header("Location: student_profile.php");
                } else {
                    $message = "Database error: " . $stmt_update->error;
                }
            }
        }
    }
} else {
    die("No ID provided.");
}
?>

<!-- HTML CODE SAME EXCEPT ID FIELD IS NOW EDITABLE -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Edit Student Profile</title>
  <style>
    .container {
    max-width: 500px;
    margin: 20px auto;
    padding: 25px;
    background-color: #f8f9fa;
    border-radius: 10px;
    box-shadow: 0 0 15px rgba(0,0,0,0.1);
    font-family: Arial, sans-serif;
  }
  .title {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 20px;
    text-align: center;
  }
  .profile-photo-wrapper {
    position: relative;
    width: 120px;
    height: 120px;
    margin: 0 auto 10px;
  }
  .profile-photo-wrapper label {
    display: block;
    cursor: pointer;
    width: 100%;
    height: 100%;
  }
  .profile-photo {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
    border: 3px solid #007BFF;
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
  }
  .upload-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 35%;
    background: rgba(0, 0, 0, 0.4);
    border-radius: 0 0 50% 50%;
    color: white;
    font-size: 14px;
    text-align: center;
    line-height: 40px;
    opacity: 0;
    transition: opacity 0.3s;
  }
  .profile-photo-wrapper:hover .upload-overlay {
    opacity: 1;
  }
  .profile-photo-wrapper input[type="file"] {
    display: none;
  }

  .form-group {
    margin-bottom: 15px;
  }
  input[type="text"], input[type="email"], input[type="tel"], input[type="date"],
  input[type="number"], select, textarea {
    width: 100%;
    padding: 10px 12px;
    border: 1.5px solid #ccc;
    border-radius: 6px;
    font-size: 16px;
    box-sizing: border-box;
    transition: border-color 0.3s ease;
  }
  input:focus, select:focus, textarea:focus {
    border-color: #007BFF;
    outline: none;
  }
  .btn-container {
    text-align: center;
  }
  .btn {
    background-color: #007BFF;
    color: white;
    padding: 12px 25px;
    border: none;
    border-radius: 6px;
    font-size: 18px;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }
  .btn:hover {
    background-color: #0056b3;
  }
  .message {
    text-align: center;
    margin-bottom: 15px;
    font-weight: 600;
    color: green;
  }
  .message.error, .error {
    color: red;
    font-weight: 600;
    margin-top: 5px;
    font-size: 14px;
  }
  .gender-container {
    display: flex;
    gap: 30px;
    margin-left: 5px;
    font-weight: 600;
    margin-top: 10px;
    align-items: center;
  }
  .gender-container label {
    display: flex;
    align-items: center;
    gap: 5px;
    font-weight: normal;
  }
  </style>
</head>
<body>
<div class="container">
  <div class="title">Edit Student Profile (ID: <?php echo htmlspecialchars($student['id']); ?>)</div>

  <?php if (!empty($message)) : ?>
    <p class="message <?php echo (stripos($message, 'error') !== false || stripos($message, 'failed') !== false) ? 'error' : ''; ?>">
      <?php echo htmlspecialchars($message); ?>
    </p>
  <?php endif; ?>
  <?php if (!empty($id_error)) : ?>
    <p class="message error"><?php echo htmlspecialchars($id_error); ?></p>
  <?php endif; ?>

  <form method="POST" action="" enctype="multipart/form-data" novalidate>
    <div class="profile-photo-wrapper">
      <label>
        <input type="file" name="photo" accept="image/*" />
        <img src="<?php
          $photo_path = "images/logos/";
          $photo_file = $student['photo'] && file_exists($photo_path . $student['photo']) ? $student['photo'] : 'default.png';
          echo htmlspecialchars($photo_path . $photo_file);
        ?>" class="profile-photo" alt="Profile Photo" />
        <div class="upload-overlay">Change</div>
      </label>
    </div>

    <div class="form-group">
      <input type="text" name="id" value="<?php echo htmlspecialchars($student['id']); ?>" required />
    </div>

    <div class="form-group">
      <input type="text" name="name" placeholder="Full Name" value="<?php echo htmlspecialchars($student['name']); ?>" required />
    </div>

    <div class="form-group">
      <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($student['email']); ?>" required />
      <?php if ($email_error): ?><div class="error"><?php echo $email_error; ?></div><?php endif; ?>
    </div>

    <div class="form-group">
      <label>Date of Birth:&nbsp;&nbsp;
        <input type="date" name="dob" value="<?php echo htmlspecialchars($student['dob']); ?>" required>
      </label>
    </div>

    <div class="form-group gender-container">
      <label>Gender:</label>
      <label><input type="radio" name="gender" value="Male" <?php if ($student['gender'] == 'Male') echo 'checked'; ?> /> Male</label>
      <label><input type="radio" name="gender" value="Female" <?php if ($student['gender'] == 'Female') echo 'checked'; ?> /> Female</label>
      <label><input type="radio" name="gender" value="Other" <?php if ($student['gender'] == 'Other') echo 'checked'; ?> /> Other</label>
    </div>

    <div class="form-group">
      <input type="tel" name="mobile" placeholder="Mobile Number" value="<?php echo htmlspecialchars($student['mobile']); ?>" pattern="[0-9]{10}" required />
    </div>

    <div class="form-group">
      <input type="text" name="qualification" placeholder="Qualification" value="<?php echo htmlspecialchars($student['qualification']); ?>" />
    </div>

    <div class="form-group">
      <select name="branch" required>
        <option value="">Select Branch</option>
        <option value="CSE" <?php if ($student['branch'] == 'CSE') echo 'selected'; ?>>CSE</option>
        <option value="ECE" <?php if ($student['branch'] == 'ECE') echo 'selected'; ?>>ECE</option>
        <option value="MECH" <?php if ($student['branch'] == 'MECH') echo 'selected'; ?>>MECH</option>
        <option value="CIVIL" <?php if ($student['branch'] == 'CIVIL') echo 'selected'; ?>>CIVIL</option>
        <option value="IT" <?php if ($student['branch'] == 'IT') echo 'selected'; ?>>IT</option>
      </select>
    </div>

    <div class="form-group">
      <input type="text" name="batch" placeholder="Batch (e.g. 2021-2025)" value="<?php echo htmlspecialchars($student['batch']); ?>" />
    </div>

    <div class="form-group">
      <input type="number" name="cgpa" step="0.01" min="0" max="10" placeholder="CGPA" value="<?php echo htmlspecialchars($student['cgpa']); ?>" />
    </div>

    <div class="form-group">
      <textarea name="skills" rows="3" placeholder="Skills (comma separated)"><?php echo htmlspecialchars($student['skills']); ?></textarea>
    </div>

    <div class="form-group">
      <textarea name="description" rows="4" placeholder="Description"><?php echo htmlspecialchars($student['description']); ?></textarea>
    </div>

    <div class="btn-container">
      <button type="submit" class="btn">Update Profile</button>
    </div>
    <a class="back-link" href="javascript:history.back()">← Back</a>
  </form>
</div>
</body>
</html>
