<?php
session_start();
include('connection.php'); 

$id_error = "";
$email_error = "";
$message = "";

if(!isset($_SESSION['cid']) && ctype_digit($_SESSION['cid']))
{
  die("invalid ID");
} 

$old_id=$_SESSION['cid'];

$sql = "SELECT * FROM company WHERE id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $old_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Company with ID $old_id not found.");
}

$company = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_id = trim($_POST['id']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $contact = trim($_POST['contact']);
    $location = trim($_POST['location']);
    $employees = trim($_POST['employees']);
    $website = trim($_POST['website']);
    $industry = trim($_POST['industry']);
    $description = trim($_POST['description']);

    if (!ctype_digit($new_id) || (int)$new_id <= 0) 
    {
        $id_error = "ID must be a positive integer.";
    } else {
        $new_id = (int)$new_id;
        if ($new_id !== $old_id) {
            $check_id_sql = "SELECT id FROM company WHERE id = ?";
            $check_id_stmt = $con->prepare($check_id_sql);
            $check_id_stmt->bind_param('i', $new_id);
            $check_id_stmt->execute();
            $check_id_res = $check_id_stmt->get_result();
            if ($check_id_res->num_rows > 0) {
                $id_error = "This ID is already taken by another company.";
            }
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
        $check_stmt->bind_param('si', $email, $old_id);
        $check_stmt->execute();
        $check_res = $check_stmt->get_result();
        if ($check_res->num_rows > 0) 
        {
            $email_error = "Email already exists for another company.";
        }
    }

    if ($employees !== "" && !is_numeric($employees)) 
    {
        $message = "Number of employees must be numeric.";
    }

    if ($website !== "" && !filter_var($website, FILTER_VALIDATE_URL)) 
    {
        $message = "Invalid website URL format.";
    }

    if (empty($email_error) && empty($message) && empty($id_error)) 
    {
        $target_dir = "images/logos/";
        $logo_to_save = $company['logo'];

        if (isset($_FILES["logo"]) && $_FILES["logo"]["error"] === UPLOAD_ERR_OK) 
        {
            $ext = strtolower(pathinfo($_FILES["logo"]["name"], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];

            if (!in_array($ext, $allowed)) 
            {
                $message = "Invalid logo file type. Allowed: jpg, jpeg, png, gif.";
            } 
            else 
            {
                $logo_name = "company_" . $new_id . "." . $ext;
                $target_file = $target_dir . $logo_name;

                if (move_uploaded_file($_FILES["logo"]["tmp_name"], $target_file)) 
                {
                    if (!empty($company['logo']) && $company['logo'] != $logo_name && file_exists($target_dir . $company['logo'])) 
                    {
                        unlink($target_dir . $company['logo']);
                    }
                    $logo_to_save = $logo_name;
                }
                else 
                {
                    $message = "Logo upload failed.";
                }
            }
        }

        if (empty($message)) {
          $_SESSION['cid']=$new_id;
            $update_sql = "UPDATE company SET
                id = ?, name = ?, email = ?, contact = ?, location = ?,
                employes = ?, website = ?, industry = ?, description = ?, logo = ?
                WHERE id = ?";

            $stmt_update = $con->prepare($update_sql);
            $stmt_update->bind_param(
                "isssssssssi",
                $new_id, $name, $email, $contact, $location,
                $employees, $website, $industry, $description, $logo_to_save, $old_id
            );

            if ($stmt_update->execute()) 
            {
                $message = "Company profile updated successfully.";
                $stmt = $con->prepare("SELECT * FROM company WHERE id = ?");
                $stmt->bind_param('i', $new_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $company = $result->fetch_assoc();

                header("Location: company_profile.php?id=" . $new_id);
                exit;
            } 
            else 
            {
                $message = "Database error: " . $stmt_update->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Edit Company Profile</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<style>
  * {
    box-sizing: border-box;
    font-family: Arial, sans-serif;
  }
  body {
    background: linear-gradient(135deg, #74ebd5, #9face6);
    padding: 20px;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    min-height: 100vh;
  }
  .container {
    background: #fff;
    padding: 30px 40px;
    border-radius: 10px;
    max-width: 600px;
    width: 100%;
    box-shadow: 0 0 15px rgba(0,0,0,0.2);
  }
  h2 {
    text-align: center;
    margin-bottom: 30px;
    font-size: 28px;
  }
  .logo-wrapper {
    width: 140px;
    height: 140px;
    margin: 0 auto 20px;
    position: relative;
  }
  .logo-wrapper label {
    width: 100%;
    height: 100%;
    cursor: pointer;
    display: block;
  }
  .logo-photo {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
    border: 3px solid #007BFF;
  }
  .upload-overlay {
    position: absolute;
    bottom: 0;
    width: 100%;
    height: 35%;
    background: rgba(0,0,0,0.4);
    border-radius: 0 0 50% 50%;
    color: white;
    text-align: center;
    line-height: 40px;
    opacity: 0;
    transition: opacity 0.3s;
  }
  .logo-wrapper:hover .upload-overlay {
    opacity: 1;
  }
  input[type="file"] {
    display: none;
  }
  .row {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
  }
  .form-group {
    flex: 1;
    display: flex;
    flex-direction: column;
    margin-bottom: 20px;
  }
  label {
    font-weight: bold;
    margin-bottom: 5px;
  }
  input, textarea {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 15px;
    width: 100%;
  }
  textarea {
    resize: vertical;
  }
  .btn-container {
    text-align: center;
  }
  .btn {
    background: #007BFF;
    color: white;
    padding: 12px 28px;
    border: none;
    border-radius: 6px;
    font-size: 18px;
    cursor: pointer;
  }
  .btn:hover {
    background: #0056b3;
  }
  .message {
    text-align: center;
    color: green;
    font-weight: bold;
    margin-bottom: 15px;
  }
  .error {
    color: red;
    font-size: 14px;
    margin-top: 4px;
  }
</style>
</head>
<body>
  <div class="container">
    <h2>Edit Profile</h2>

    <?php if ($message): ?>
      <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form action="?id=<?= htmlspecialchars($old_id) ?>" method="POST" enctype="multipart/form-data">

      <div class="logo-wrapper">
        <label for="logo">
          <?php if (!empty($company['logo']) && file_exists("images/logos/".$company['logo'])): ?>
            <img src="<?= "images/logos/".htmlspecialchars($company['logo']) ?>" alt="Logo" class="logo-photo" />
            <div class="upload-overlay">Change Logo</div>
          <?php else: ?>
            <div style="border: 2px dashed #007BFF; border-radius: 50%; height: 100%; display:flex; align-items:center; justify-content:center; color:#007BFF;">Upload Logo</div>
          <?php endif; ?>
        </label>
        <input type="file" name="logo" id="logo" accept="image/*">
      </div>

      <div class="row">
        <div class="form-group">
          <label for="id">Company ID *</label>
          <input type="text" name="id" id="id" value="<?= htmlspecialchars($company['id']) ?>" required />
          <?php if ($id_error): ?><span class="error"><?= $id_error ?></span><?php endif; ?>
        </div>
        <div class="form-group">
          <label for="name">Company Name *</label>
          <input type="text" name="name" id="name" value="<?= htmlspecialchars($company['name']) ?>" required />
        </div>
      </div>

      <div class="row">
        <div class="form-group">
          <label for="email">Email *</label>
          <input type="email" name="email" id="email" value="<?= htmlspecialchars($company['email']) ?>" required />
          <?php if ($email_error): ?><span class="error"><?= $email_error ?></span><?php endif; ?>
        </div>
        <div class="form-group">
          <label for="contact">Contact Number</label>
          <input type="text" name="contact" id="contact" value="<?= htmlspecialchars($company['contact']) ?>" />
        </div>
      </div>

      <div class="row">
        <div class="form-group">
          <label for="location">Location</label>
          <input type="text" name="location" id="location" value="<?= htmlspecialchars($company['location']) ?>" />
        </div>
        <div class="form-group">
          <label for="employees">Number of Employees</label>
          <input type="text" name="employees" id="employees" value="<?= htmlspecialchars($company['employes']) ?>" />
        </div>
      </div>

      <div class="row">
        <div class="form-group">
          <label for="website">Website</label>
          <input type="url" name="website" id="website" value="<?= htmlspecialchars($company['website']) ?>" />
        </div>
        <div class="form-group">
          <label for="industry">Industry</label>
          <input type="text" name="industry" id="industry" value="<?= htmlspecialchars($company['industry']) ?>" />
        </div>
      </div>

      <div class="form-group">
        <label for="description">Description</label>
        <textarea name="description" id="description"><?= htmlspecialchars($company['description']) ?></textarea>
          </div>

      <div class="btn-container">
        <button class="btn" type="submit">Update Profile</button>
      </div>
      <a class="back-link" href="javascript:history.back()">← Back</a>
    </form>
  </div>
</body>
</html>
