<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Online campus selection system</title>
</head>
<body>


<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}
html,body {
  color: aliceblue;
  width: 100%;
  height:100vh;
  min-height: 100vh;
  background-image: url("images/bg.jpg");
  background-repeat: no-repeat;
  background-position: center;
  background-size: cover;
  font-family: sans-serif;
  overflow: hidden;
}
nav {
  background: none;
  padding: 10px 20px;
}

.navlist {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: none;
}

.logo {
  width: 80px;
  height: auto;
  border-radius:50%;
}

ul {
  display: flex;
  gap: 40px;
  list-style: none;
  background: none;
}

ul li a {
  color: ghostwhite;
  text-decoration: none;
  font-size: 18px;
  transition: color 0.3s;
}

ul li a:hover {
  color: rgb(20, 225, 225);
}
.home {
  width: 100%;
  height:100vh;
  display: flex;
  justify-content: flex-start;
  align-items: center;
  padding-left:150px;
  background: none;
}
.home-details h1 {
  font-size: 60px;
  margin-bottom:100px;
  margin-top:-180px;
}
.buttons {
  display: flex;
  flex-direction:row;
  gap: 20px;
  align-items: center;
}
button {
  width: 250px;
  height: 60px;
  color: ghostwhite;
  border-radius: 8px;
  border: 2px solid rgb(20, 225, 225);
  font-size: 20px;
  background: none;
  cursor: pointer;
  transition: background 0.3s;
  margin-left:140px;
}
button:hover {
  background: rgba(20, 225, 225, 0.2);
}
button:active {
  background: rgb(20, 225, 225);
}
a {
  background: none;
}
  </style>


    <div class="first">
    <nav>
      <div class="navlist">
        <img src="images/logo.jpg" alt="Logo" class="logo" />
        <ul>
          <li><a href="index.php">Home</a></li>
          <li><a href="aboutus.html">About Us</a></li>
          <li><a href="contactus.html">Contact Us</a></li>
        </ul>
      </div>
    </nav>

    <div class="home" id="home">
      <div class="home-details">
        <h1><span class="name">Online Campus Selection System</span></h1>
        <div class="buttons">
          <a href="student_login.php"><button type="submit">Student Login</button></a>
          <a href="company_login.php"><button type="submit">Company Login</button></a>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
</html>