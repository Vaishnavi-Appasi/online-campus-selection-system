<?php
    session_start();
    include('connection.php');

    if (isset($_GET['job_id']) && ctype_digit($_GET['job_id'])) 
    {
        $job_id = $_GET['job_id'];

        $stmt = $con->prepare("DELETE FROM css.job WHERE id = ?");
        $stmt->bind_param("i", $job_id);

        if ($stmt->execute()) 
        {
            header("Location: postedjobs.php");
            exit();
        } else {
            echo "Error deleting job: " . $stmt->error;
        }
    } 
    else {
        echo "Invalid job ID.";
    }
?>
