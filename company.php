<?php
    $name='';
    $sql = "SELECT name FROM company WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $cid);
    $stmt->execute();
    $stmt->bind_result($name);
    $stmt->fetch();
    $stmt->close();
    $con->close();
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout_confirm'])) {
        session_unset();
        session_destroy();
        header('Location: index.php');
        exit();
    }
?>

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
  }
  html, body {
    width: 100%;
    background: #f0f2f5;
    color: #333;
  }

  .home {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding-top: 20px;
  }
  h1, h2 {
    font-family: Georgia, serif;
    color: #383636;
  }
  .header {
    display: flex;
    flex-direction:row;
    justify-content: space-between;
    align-items: center;
    padding: 10px 20px;
    background: #23242b;
    color: #fff;
  }
  .header .u-name {
    font-size: 20px;
  }
  .header .u-name b {
    color: #127b8e;
  }
  .header img {
    width: 30px;
    border-radius: 50%;
  }
  .header i {
    font-size: 20px;
    cursor: pointer;
  }
  .header i:hover {
    color: #127b8e;
  }

  .body {
    display: flex;
  }
  .side-bar {
    width: 200px;
    background: #262931;
    min-height: 100vh;
    transition: width 0.5s;
  }
  .side-bar .user-p {
    text-align: center;
    padding: 20px 0 10px;
  }
  .side-bar .user-p img {
    width: 100px;
    height : 100px;
    border-radius: 50%;
  }
  .side-bar .user-p h4 {
    color: white;
    font-family: Georgia, serif;
    margin-top: 10px;
  }
  .side-bar ul {
    list-style: none;
    margin-top: 20px;
  }
  .side-bar ul li {
    padding: 15px 20px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    transition: background 0.5s;
  }
  .side-bar ul li:hover {
    background: #127b8e;
  }
  .side-bar ul li a {
    text-decoration: none;
    color: #eee;
    display: flex;
    align-items: center;
  }
  .side-bar ul li a i {
    margin-right: 10px;
    font-size: 23px;
  }
  #checkbox {
    display: none;
  }
  #checkbox:checked ~ .body .side-bar {
    width: 60px;
  }
  #checkbox:checked ~ .body .side-bar .user-p,
  #checkbox:checked ~ .body .side-bar ul li a span {
    display: none;
  }

  .confirm-overlay {
    position: fixed;
    top: 0; left: 0;
    width: 100vw; height: 100vh;
    background: rgba(0,0,0,0.6);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 9999;
  }
  .confirm-box {
    background: #fff;
    padding: 30px;
    border-radius: 8px;
    text-align: center;
    width: 300px;
    box-shadow: 0 0 10px rgba(0,0,0,0.3);
  }
  .confirm-box h2 {
    margin-bottom: 20px;
  }
  .confirm-box button {
    padding: 10px 20px;
    margin: 0 10px;
    font-size: 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }
  .yes-btn { background: #28a745; color: #fff; }
  .no-btn  { background: #dc3545; color: #fff; }

</style>
<input type="checkbox" id="checkbox">
    <header class="header">
        <h2 class="u-name"><b>company</b>
            <label for="checkbox">
                <i id="navbtn" class="fa fa-bars" aria-hidden="true"></i>
            </label>
        </h2>
        <img src="images/logo.jpg">
    </header>
    <?php
        $logo_path = "images/logos/company_" . $cid . ".png";
        if (!file_exists($logo_path)) 
        {
          $logo_path = "images/logos/default.png"; // fallback image
        }
      ?>
    <div class="body">
        <nav class="side-bar">
            <div class="user-p">
                <img src="<?php echo $logo_path; ?>">
                <h4><?php echo htmlspecialchars($name); ?></h4>
            </div>
            <ul>
                <li>
                    <a href="postedjobs.php">
                        <i class="fa fa-briefcase" aria-hidden="true"></i>
                        <span>posted jobs</span>
                    </a>
                </li>
                <li>
                    <a href="post_job.php">


                        <i class="fa fa-plus" aria-hidden="true"></i>
                        <span>New job</span>
                    </a>
                </li>
                <li>
                    <a href="company_profile.php">
                        <i class="fa fa-user" aria-hidden="true"></i>
                        <span>profile</span>
                    </a>
                </li>
                <li>
                    <a onclick="showLogoutConfirm(); return false;">
                        <i class="fa fa-sign-out" aria-hidden="true"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </nav>

    <div class="confirm-overlay" id="logoutConfirmBox">
        <div class="confirm-box">
            <h2>Do you want to logout?</h2>
            <form method="post">
                <button type="submit" name="logout_confirm" class="yes-btn">Yes</button>
                <button type="button" class="no-btn" onclick="hideLogoutConfirm()">No</button>
            </form>
        </div>
    </div>

    <script>
        function showLogoutConfirm()
        {
            document.getElementById('logoutConfirmBox').style.display = 'flex';
        }

        function hideLogoutConfirm() 
        {
            document.getElementById('logoutConfirmBox').style.display = 'none';
        }
    </script>