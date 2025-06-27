<?php
    session_start();
    include('connection.php');

    if (isset($_SESSION['cid'])) {
        $cid = $_SESSION['cid'];

        $company_query = "SELECT * FROM company WHERE id = $cid";
        $company_result = mysqli_query($con, $company_query);

        if (!$company_result || mysqli_num_rows($company_result) === 0) {
            echo "Company not found.";
            exit();
        }

        $company = mysqli_fetch_assoc($company_result);

    } 
    else 
    {
        echo "Company ID not provided.";
        exit();
    }
    if (isset($_POST['delete_confirm']) && isset($cid)) 
    {
        $query = "DELETE FROM company WHERE id = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("i", $cid);

        if ($stmt->execute()) {
            session_destroy();
            header("Location: index.php");
            exit();
        } else {
            echo "Error deleting company: " . $stmt->error;
        }
        $stmt->close();
    }


?>

<!DOCTYPE html>
<html>
<head>
    <title>Company Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>

        .card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 700px;
            box-sizing: border-box;
        }


    .head {
        display: flex;
        align-items: center;
        margin-bottom: 25px;
    }

    .logo-image {
        width: 90px;
        height: 90px;
        border-radius: 10px;
        object-fit: cover;
        margin-right: 20px;
        border: 2px solid #ddd;
    }

    .company-name {
        font-size: 24px;
        font-weight: bold;
        color: #1a1a1a;
    }

    .info-row {
        display: flex;
        margin: 12px 0;
    }

    .info-label {
        width: 160px;
        font-weight: bold;
        color: #333;
    }

    .info-value a {
        color: #007bff;
        text-decoration: none;
    }

    .info-value a:hover {
        text-decoration: underline;
    }

    .profile-actions {
        text-align: center;
        margin-top: 25px;
    }

    .btn {
        padding: 10px 20px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        color: white;
        margin: 0 10px;
        display: inline-block;
        transition: background 0.3s;
    }

    .edit-btn {
        background-color: #28a745;
    }

    .edit-btn:hover {
        background-color: #218838;
    }

    .delete-btn {
        background-color: #dc3545;
    }

    .delete-btn:hover {
        background-color: #c82333;
    }

    @media screen and (max-width: 600px) {
        .info-row {
            flex-direction: column;
        }

        .info-label {
            width: auto;
            margin-bottom: 4px;
        }
    }
</style>

</head>
<body>

<?php include('company.php'); ?>

<div class="home">
    <div class="card">
        <div class="head">
            <img src="images/logos/<?php echo htmlspecialchars($company['logo']); ?>" alt="Company Logo" class="logo-image">
            <div class="company-name"><?php echo htmlspecialchars($company['name']); ?></div>
        </div>

        <div class="info-row">
            <div class="info-label">Company ID:</div>
            <div class="info-value"><?php echo htmlspecialchars($company['id']); ?></div>
        </div>

        <div class="info-row">
            <div class="info-label">Email:</div>
            <div class="info-value"><?php echo htmlspecialchars($company['email']); ?></div>
        </div>

        <div class="info-row">
            <div class="info-label">Contact:</div>
            <div class="info-value"><?php echo htmlspecialchars($company['contact']); ?></div>
        </div>

        <div class="info-row">
            <div class="info-label">Location:</div>
            <div class="info-value"><?php echo htmlspecialchars($company['location']); ?></div>
        </div>

        <div class="info-row">
            <div class="info-label">No. of Employees:</div>
            <div class="info-value"><?php echo htmlspecialchars($company['employes']); ?></div>
        </div>

        <div class="info-row">
            <div class="info-label">Website URL:</div>
            <div class="info-value">
                <a href="<?php echo htmlspecialchars($company['website']); ?>" target="_blank">
                    <?php echo htmlspecialchars($company['website']); ?>
                </a>
            </div>
        </div>

        <div class="info-row">
            <div class="info-label">Industry Type:</div>
            <div class="info-value"><?php echo htmlspecialchars($company['industry']); ?></div>
        </div>

        <div class="info-row">
            <div class="info-label">Description:</div>
            <div class="info-value"><?php echo nl2br(htmlspecialchars($company['description'])); ?></div>
        </div>

        <div class="profile-actions">
            <a href="edit_company.php" class="btn edit-btn">Edit</a>
            <a  class="btn delete-btn" onclick="showDeleteConfirm()">Delete</a>

        </div>
    </div>
</div>

    <div class="confirm-overlay" id="deleteConfirmBox">
        <div class="confirm-box">
            <h2>Do you want to delete?</h2>
            <form method="post">
                <button type="submit" name="delete_confirm" class="yes-btn">Yes</button>
                <button type="button" class="no-btn" onclick="hidedeleteConfirm()">No</button>
            </form>
        </div>
    </div>

    <script>
        function showDeleteConfirm() {
            document.getElementById('deleteConfirmBox').style.display = 'flex';
        }

        function hidedeleteConfirm() {
            document.getElementById('deleteConfirmBox').style.display = 'none';
        }
    </script>

</body>
</html>
