<?php
session_start();
include "connection.php";

if (!isset($_SESSION['jid']) || empty($_SESSION['jid'])) {
    die("Invalid Student ID or Job ID.");
} 
$jid= intval($_SESSION['jid']);
$sql = "SELECT * FROM company WHERE id = (SELECT cid from css.job where id =$jid)";
$result = $con->query($sql);

if ($result->num_rows === 0) {
  echo "Company not found.";
  exit;
}

$company = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?php echo htmlspecialchars($company['name']); ?>  Details</title>
  <style>
    .home {
      display: flex;
      min-height: 100vh;
      align-items:center;
      justify-content:center;
    }

    .main-content {
      flex: 1;
      padding: 30px;
    }

    .company-details {
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
      max-width: 750px;
      margin: auto;
    }

    .company-details h2 {
      margin-top: 0;
      font-size: 28px;
      color: #333;
    }

    .company-details p {
      font-size: 16px;
      line-height: 1.6;
      color: #444;
      margin: 10px 0;
    }
  </style>
</head>
<body>

<input type="checkbox" id="checkbox" />
<?php include "student.php"; ?>

<div class="home">

  <div class="main-content">
    <div class="company-details">
      <h2><?php echo htmlspecialchars($company['name']); ?></h2>
      <p><strong>📧 Email:</strong> <?php echo htmlspecialchars($company['email']); ?></p>
      <p><strong>📞 Contact:</strong> <?php echo htmlspecialchars($company['contact']); ?></p>
      <p><strong>📍 Location:</strong> <?php echo htmlspecialchars($company['location']); ?></p>
      <p><strong>👥 Employees:</strong> <?php echo htmlspecialchars($company['employes']); ?></p>
      <p><strong>🌐 Website:</strong> <a href="<?php echo htmlspecialchars($company['website']); ?>" target="_blank"><?php echo htmlspecialchars($company['website']); ?></a></p>
      <p><strong>🏭 Industry:</strong> <?php echo htmlspecialchars($company['industry']); ?></p>
      <p><strong>🗓 Registered On:</strong> <?php echo htmlspecialchars($company['date']); ?></p>
      <p><strong>📝 About:</strong><br><?php echo nl2br(htmlspecialchars($company['description'])); ?></p>

    </div>
    <a class="back-link" href="javascript:history.back()">← Back</a>
  </div>
</div>
</body>
</html>