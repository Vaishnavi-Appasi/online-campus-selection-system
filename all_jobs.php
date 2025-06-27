<?php 
session_start();
include('connection.php'); 

if (!isset($_SESSION['sid'])) {
  die("You must be logged in to view jobs.");
}

$sid = $_SESSION['sid'];
$locationQuery = "SELECT DISTINCT location FROM css.job WHERE location IS NOT NULL AND location <> '' ORDER BY location";
$locationResult = mysqli_query($con, $locationQuery);
$locations = [];
if ($locationResult) {
    while ($row = mysqli_fetch_assoc($locationResult)) {
        $locations[] = $row['location'];
    }
  }
$skills = [];
$skillsQuery = "SELECT skills FROM css.job WHERE skills IS NOT NULL AND skills <> ''";
$skillsResult = mysqli_query($con, $skillsQuery);
if ($skillsResult) {
    while ($row = mysqli_fetch_assoc($skillsResult)) {
        $jobSkills = explode(',', $row['skills']);
        foreach ($jobSkills as $skill) {
            $skill = trim($skill);
            if ($skill && !in_array($skill, $skills)) {
                $skills[] = $skill;
            }
        }
    }
    sort($skills);
}

$where = [];

if (!empty($_GET['location']) && is_array($_GET['location'])) {
    $loc_filters = array_map(function($loc) use ($con) {
        return mysqli_real_escape_string($con, $loc);
    }, $_GET['location']);

    $loc_conditions = array_map(function($loc) {
        return "location LIKE '%$loc%'";
    }, $loc_filters);

    if (!empty($loc_conditions)) {
        $where[] = '(' . implode(' OR ', $loc_conditions) . ')';
    }
}

if (!empty($_GET['skills']) && is_array($_GET['skills'])) {
    $skill_filters = array_map(function($skill) use ($con) {
        return mysqli_real_escape_string($con, $skill);
    }, $_GET['skills']);

    $skill_conditions = array_map(function($skill) {
        return "skills LIKE '%$skill%'";
    }, $skill_filters);

    if (!empty($skill_conditions)) {
        $where[] = '(' . implode(' OR ', $skill_conditions) . ')';
    }
}
if (!empty($_GET['date'])) {
    $date = mysqli_real_escape_string($con, $_GET['date']);
    $where[] = "posted_on >= '$date'";
}

$query = "SELECT * FROM css.job";
if (!empty($where)) {
    $query .= " WHERE " . implode(" AND ", $where);
}

$result = mysqli_query($con, $query);
if (!$result) {
    die("Connection error: " . mysqli_error($con));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>JOBS</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
    }
    .welcome {
      text-align: center;
      margin-top: 20px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      height: 150px;
    }
    .welcome h1 {
      font-size: 50px;
      margin: 0;
    }
    .welcome h2 {
      font-size: 15px;
      margin: 0;
    }

    .filter-sidebar {
      position: fixed;
      right: -320px;
      top: 0;
      width: 320px;
      height: 100%;
      background-color: #f1f1f1;
      box-shadow: -2px 0 5px rgba(0,0,0,0.5);
      padding: 20px;
      transition: right 0.3s ease;
      z-index: 999;
      overflow-y: auto;
    }

    .filter-sidebar.open {
      right: 0;
    }

    .filter-sidebar h3 {
      text-align: center;
    }

    .filter-sidebar fieldset {
      border: none;
      margin-bottom: 25px;
      padding: 0;
    }

    .filter-sidebar legend {
      font-weight: bold;
      margin-bottom: 10px;
      font-size: 1.1rem;
    }

    .filter-sidebar label {
      display: block;
      margin-bottom: 8px;
      font-size: 1rem;
      cursor: pointer;
    }

    .filter-sidebar input[type="checkbox"] {
      margin-right: 10px;
    }

    .filter-sidebar input[type="date"],
    .filter-sidebar button {
      width: 100%;
      padding: 10px;
      margin: 10px 0;
      font-size: 1rem;
      cursor: pointer;
    }

    .filter-icon {
      position: fixed;
      top: 60px;
      right: 20px;
      background-color: #007bff;
      color: white;
      padding: 12px 18px;
      border: none;
      border-radius: 50%;
      font-size: 18px;
      cursor: pointer;
      z-index: 1000;
    }

    .job-listings {
      margin-left: 25px;
      margin-top: 60px;
      width: 100%;
    }

    .job-card-horizontal {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      background: #fff;
      border-radius: 10px;
      padding: 10px;
      box-shadow: 2px 2px 8px rgba(7, 7, 7, 0.4);
      width: 1250px;
      box-sizing: border-box;
      gap: 2rem;
      margin: 15px 0;
    }

    .job-left {
      flex: 1.5;
    }

    .company-description {
      font-size: 1rem;
      color: #555;
      margin-bottom: 1rem;
      line-height: 1.5;
    }

    .job-left p {
      margin: 0.3rem 0;
      font-size: 1rem;
      color: #444;
    }

    .job-right {
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
      justify-content: flex-start;
      align-items: flex-end;
    }

    .job-right a {
      padding: 0.5rem 1.2rem;
      border-radius: 6px;
      font-weight: bold;
      width: 100px;
      margin: 10px 0;
      text-align: center;
      text-decoration: none;
      display: inline-block;
      color: white;
    }

    .apply-btn {
      background-color: #007bff;
    }

    .details-btn {
      background-color: #6c757d;
    }

    .no-jobs {
      text-align: center;
      margin-top: 40px;
    }
  </style>
</head>
<body>

<?php include "student.php" ?>

<button class="filter-icon" onclick="toggleSidebar()">☰</button>



<div class="filter-sidebar" id="filterSidebar">
  <h3>Filter Jobs</h3>
  <form method="GET" action="">
    <fieldset>
      <legend>Location</legend>
      <?php foreach ($locations as $loc): ?>
        <label>
          <input type="checkbox" name="location[]" value="<?php echo htmlspecialchars($loc); ?>"
            <?php 
            if (!empty($_GET['location']) && is_array($_GET['location']) && in_array($loc, $_GET['location'])) {
              echo "checked";
            }
            ?>>
          <?php echo htmlspecialchars($loc); ?>
        </label>
      <?php endforeach; ?>
    </fieldset>

    <fieldset>
      <legend>Skills</legend>
      <?php foreach ($skills as $skill): ?>
        <label>
          <input type="checkbox" name="skills[]" value="<?php echo htmlspecialchars($skill); ?>"
            <?php 
            if (!empty($_GET['skills']) && is_array($_GET['skills']) && in_array($skill, $_GET['skills'])) {
              echo "checked";
            }
            ?>>
          <?php echo htmlspecialchars($skill); ?>
        </label>
      <?php endforeach; ?>
    </fieldset>

    <label for="date"><strong>Posted Since:</strong></label>
    <input type="date" name="date" id="date" value="<?php echo isset($_GET['date']) ? htmlspecialchars($_GET['date']) : ''; ?>">

    <button type="submit">Apply Filter</button>
    <button type="button" onclick="window.location.href='jobs.php'">Reset</button>
  </form>
</div>

<div class="body">
  <div class="home" id="home">
    <?php
      if (mysqli_num_rows($result) === 0) {
        echo '<div class="welcome">
                <h1>No jobs found!</h1>
                <h2>Try changing your filters.</h2>
              </div>';
      } else {
        echo '<div class="welcome">
                <h1>WELCOME BACK!</h1>
                <h2>Start your journey here. Your dream job is just a click away.</h2>
              </div>';
        echo '<section class="job-listings">';
        while ($row = mysqli_fetch_assoc($result)) {
          echo '
          <div class="job-card-horizontal">
            <div class="job-left">
              <h2 class="Job-name">' . htmlspecialchars($row['title']) . '</h2>
              <p><strong>📍 Location:</strong> ' . htmlspecialchars($row['location']) . '</p>
              <p><strong>💸 Salary:</strong> ' . htmlspecialchars($row['salary']) . '</p>
              <p><strong>💼 Experience:</strong> ' . htmlspecialchars($row['experience']) . '</p>
              <p><strong>🎓 Qualification:</strong> ' . htmlspecialchars($row['qualification']) . '</p>
              <p><strong>🗓️ Posted On:</strong> ' . htmlspecialchars($row['date_posted']) . '</p>
              <p class="company-description">' . nl2br(htmlspecialchars($row['description'])) . '</p>
            </div>
            <div class="job-right">
              <a href="applyjob.php?jid=' . urlencode($row['id']) . '" class="apply-btn">Apply</a>
              <a href="jobdetails.php?jid=' . urlencode($row['id']) . '" class="details-btn">Details</a>
            </div>
          </div>
          ';
        }
        echo '</section>';
      }
    ?>
  </div>
</div>

<script>
  function toggleSidebar() {
    document.getElementById('filterSidebar').classList.toggle('open');
  }
</script>

</body>
</html>
