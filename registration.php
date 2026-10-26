<?php
date_default_timezone_set("Asia/Kolkata");

$message = "";

if (isset($_POST["register"])) {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $mobile = trim($_POST["mobile"]);
    $course = trim($_POST["course"]);
    $password = $_POST["password"];

    if (empty($name) || empty($email) || empty($mobile) || empty($course) || empty($password)) {
        $message = "Please fill all fields.";
    } elseif (!preg_match("/^[A-Za-z ]{2,50}$/", $name)) {
        $message = "Enter a valid name.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Enter a valid email.";
    } elseif (!preg_match("/^[0-9]{10}$/", $mobile)) {
        $message = "Mobile number must contain 10 digits.";
    } elseif (strlen($password) < 8) {
        $message = "Password must contain at least 8 characters.";
    } else {

        $date = date("d-m-Y h:i:s A");
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $name = htmlspecialchars($name);
        $email = htmlspecialchars($email);
        $mobile = htmlspecialchars($mobile);
        $course = htmlspecialchars($course);

        $csvName = "students.csv";

        $newFile = !file_exists($csvName);
        $file = fopen($csvName, "a");

        if ($newFile) {
            fputcsv($file, ["Name", "Email", "Mobile", "Course", "Password", "Date"]);
        }

        fputcsv($file, [
            $name,
            $email,
            $mobile,
            $course,
            $hash,
            $date
        ]);

        fclose($file);

        $jsonName = "students.json";

        if (file_exists($jsonName)) {
            $jsonData = json_decode(file_get_contents($jsonName), true);

            if (!is_array($jsonData)) {
                $jsonData = [];
            }
        } else {
            $jsonData = [];
        }

        $student = [
            "name" => $name,
            "email" => $email,
            "mobile" => $mobile,
            "course" => $course,
            "password" => $hash,
            "date" => $date
        ];

        $jsonData[] = $student;

        file_put_contents(
            $jsonName,
            json_encode($jsonData, JSON_PRETTY_PRINT),
            LOCK_EX
        );

        $message = "Registration completed successfully.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
    <link rel="stylesheet" href="registration.css">
</head>

<body>

<header>
    <h1>Student Registration</h1>
</header>

<div class="container">

    <h2>Create Account</h2>

    <form method="POST">

        <label>Full Name</label>
        <input type="text" name="name" placeholder="Enter your name" required>

        <label>Email Address</label>
        <input type="email" name="email" placeholder="Enter your email" required>

        <label>Mobile Number</label>
        <input type="text" name="mobile" placeholder="10 digit mobile number" maxlength="10" required>

        <label>Select Course</label>
        <select name="course" required>
            <option value="">Choose your course</option>
            <option value="Computer Engineering">Computer Engineering</option>
            <option value="Information Technology">Information Technology</option>
            <option value="Computer Science">Computer Science</option>
            <option value="Artificial Intelligence">Artificial Intelligence</option>
            <option value="Data Science">Data Science</option>
        </select>

        <label>Password</label>
        <input type="password" name="password" placeholder="Enter password" required>

        <button type="submit" name="register">Create Account</button>

    </form>

    <?php if ($message != "") { ?>
        <div class="message">
            <?php echo $message; ?>
        </div>
    <?php } ?>

</div>

<footer>
    <p>© 2026 Student Registration Portal | PHP Form Processing</p>
</footer>

</body>
</html>