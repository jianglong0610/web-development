<!DOCTYPE HTML>
<html>

<head>
    <title>PDO - Read Records - PHP CRUD Tutorial</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <style>
        .m-r-1em {
            margin-right: 1em;
        }

        .m-b-1em {
            margin-bottom: 1em;
        }

        .m-l-1em {
            margin-left: 1em;
        }

        .mt0 {
            margin-top: 0;
        }
    </style>
</head>

<body>
    <div class="container" style="background-image:url('image/background.jpg')">
        <?php
        include 'top_nav.php'
        ?>
        <div class="page-header">
            <h1>Update Customers</h1>
        </div>
        <?php
        // get passed parameter value, in this case, the record ID
        // isset() is a PHP function used to verify if a value is there or not
        $id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: Record ID not found.');

        //include database connection
        include 'config/database.php';

        // read current record's data
        try {
            // prepare select query
            $query = "SELECT * FROM customers WHERE id = :id";
            $stmt = $con->prepare($query);

            // this is the first question mark
            $stmt->bindParam(":id", $id);

            // execute our query
            $stmt->execute();

            // store retrieved row to a variable
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // values to fill up our form
            $Username = $row['Username'];
            $First_name = $row['First_name'];
            $Last_name = $row['Last_name'];
            $Gender = $row['Gender'];
            $Date_of_birth = $row['Date_of_birth'];
            $Account_status = $row['Account_status'];
            $image = $row['image'];
        }

        // show error
        catch (PDOException $exception) {
            die('ERROR: ' . $exception->getMessage());
        }
        ?>

        <!-- PHP post to update record will be here -->
        <?php
        // check if form was submitted
        if (isset($_POST['update'])) {
            try {
                $Username = $_POST['Username'];
                $First_name = $_POST['First_name'];
                $Last_name = $_POST['Last_name'];
                $Date_of_birth = $_POST['Date_of_birth'];
                $image = !empty($_FILES["image"]["name"])
                    ? sha1_file($_FILES['image']['tmp_name']) . "-" . basename($_FILES["image"]["name"])
                    : htmlspecialchars($image, ENT_QUOTES);
                $error_message = "";
                $today = date("Y-m-d");
                $date1 = date_create($Date_of_birth);
                $date2 = date_create($today);
                $diff = date_diff($date1, $date2);
                $result = $diff->format("%a");
                $pass = true;
                $passw = md5($_POST['passw']);
                $new_pass = md5($_POST['new_pass']);
                $comfirm_password = md5($_POST['comfirm_password']);
                $select = "SELECT Password FROM customers WHERE id = :id ";
                $stmt = $con->prepare($select);

                // Bind the parameter
                $stmt->bindParam(":id", $id);
                // execute our query
                $stmt->execute();

                // store retrieved row to a variable
                $count = $stmt->rowCount();
                if ($count > 0) {
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    extract($row);
                }
                // values to fill up our form
                $space = " ";
                if (strlen($Username) < 6) {
                    echo "<div class='alert alert-danger'>Username should have at least 6 character.</div>";
                    $pass = false;
                }

                if (strpos($Username, $space) !== false) {
                    echo "<div class='alert alert-danger'>Username cannot space.</div>";
                    $pass = false;
                }
                $keeppass = false;
                if ($passw == md5("") && $new_pass == md5("") && $comfirm_password == md5("")) {
                    $emptypass = true;
                } else {
                    if ($row['password'] == $passw) {
                        if (!preg_match('/[a-z]/', $new_pass)) {
                            $error_message .= "<div class='alert alert-danger'>Password need include lowercase</div>";
                        } elseif (!preg_match('/[0-9]/', $new_pass)) {
                            $error_message .= "<div class='alert alert-danger'>Password need include number</div>";
                        } elseif (strlen($new_pass) < 8) {
                            $error_message .= "<div class='alert alert-danger'>Password need at least 8 charecter</div>";
                        }
                        if ($passw == $new_pass) {
                            $error_message .= "<div class='alert alert-danger'>New password cannot same with old password</div>";
                        }
                        if ($passw != md5("") && $password != md5("") && $comfirm_password == md5("")) {
                            $error_message .= "<div class='alert alert-danger'>Please enter confirm password</div>";
                        }
                        if ($passw != md5("") && $password != md5("") && $comfirm_password != md5("") && $new_pass != $comfirm_password) {
                            $error_message .= "<div class='alert alert-danger'>confirm password need to same with password</div>";
                        }
                    } else {
                        $error_message .= "<div class='alert alert-danger'>Password incorrect</div>";
                    }
                }

                if ($_FILES["image"]["name"]) {

                    // upload to file to folder
                    $target_directory = "uploads/customer/";
                    $target_file = $target_directory . $image;
                    $file_type = pathinfo($target_file, PATHINFO_EXTENSION);

                    // make sure that file is a real image
                    $check = getimagesize($_FILES["image"]["tmp_name"]);
                    if ($check === false) {
                        $error_message .= "<div class='alert alert-danger'>Submitted file is not an image.</div>";
                    }
                    // make sure certain file types are allowed
                    $allowed_file_types = array("jpg", "jpeg", "png", "gif");
                    if (!in_array($file_type, $allowed_file_types)) {
                        $error_message .= "<div class='alert alert-danger'>Only JPG, JPEG, PNG, GIF files are allowed.</div>";
                    }
                    // make sure file does not exist
                    if (file_exists($target_file)) {
                        $error_message .= "<div class='alert alert-danger'>Image already exists. Try to change file name.</div>";
                    }
                    // make sure submitted file is not too large, can't be larger than 1 MB
                    if ($_FILES['image']['size'] > (1024000)) {
                        $error_message .= "<div class='alert alert-danger'>Image must be less than 1 MB in size.</div>";
                    }
                    // make sure the 'uploads' folder exists
                    // if not, create it
                    if (!is_dir($target_directory)) {
                        mkdir($target_directory, 0777, true);
                    }
                    // if $file_upload_error_messages is still empty
                    if (empty($error_message)) {
                        // it means there are no errors, so try to upload the file
                        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                            $error_message .= "<div class='alert alert-danger>Unable to upload photo.</div>";
                            $error_message .= "<div class='alert alert-danger>Update the record to upload photo.</div>";
                        }
                    }
                } elseif (empty($image)) {
                    $image = "profile.jpg";
                }

                if (!empty($error_message)) {
                    echo "<div class='alert alert-danger'>{$error_message}</div>";
                }


                if ($result < 6570) {
                    echo "<div class='alert alert-danger'>Only above 18 years old can save.</div>";
                    $pass = false;
                }
                if ($Date_of_birth > $today) {
                    echo "<div class='alert alert-danger'>You cannot input the future date</div>";
                    $pass = false;
                }
                // write update query
                // in this case, it seemed like we have so many fields to pass and
                // it is better to label them and not use question marks
                if ($pass == true) {
                    $query = "UPDATE customers
                  SET Username=:Username, Password=:new_pass, First_name=:First_name, Last_name=:Last_name, Gender=:Gender, Date_of_birth=:Date_of_birth, Account_status=:Account_status, image=:image WHERE id = :id";
                    // prepare query for excecution
                    $stmt = $con->prepare($query);
                    // posted values
                    $Username = htmlspecialchars(strip_tags($_POST['Username']));
                    if ($keeppass == true) {
                        $new_pass = $row['Password'];
                    } else {
                        $new_pass = htmlspecialchars(strip_tags(md5($_POST['new_pass'])));
                    }
                    $first_name = htmlspecialchars(strip_tags($_POST['First_name']));
                    $last_name = htmlspecialchars(strip_tags($_POST['Last_name']));
                    $Gender = htmlspecialchars(strip_tags($_POST['Gender']));
                    $date_of_birth = htmlspecialchars(strip_tags($_POST['Date_of_birth']));
                    $Account_status = htmlspecialchars(strip_tags($_POST['Account_status']));
                    // bind the parameters
                    $stmt->bindParam(':Username', $Username);
                    $stmt->bindParam(':new_pass', $new_pass);
                    $stmt->bindParam(':First_name', $First_name);
                    $stmt->bindParam(':Last_name', $Last_name);
                    $stmt->bindParam(':Gender', $Gender);
                    $stmt->bindParam(':Date_of_birth', $Date_of_birth);
                    $stmt->bindParam(':Account_status', $Account_status);
                    $stmt->bindParam(':image', $image);
                    $stmt->bindParam(':id', $id);
                    // Execute the query
                    if ($stmt->execute()) {
                        header("Location: customer_read.php?update={$id}");
                    } else {
                        echo "<div class='alert alert-danger'>Unable to update record. Please try again.</div>";
                    }
                }
            }
            // show errors
            catch (PDOException $exception) {
                die('ERROR: ' . $exception->getMessage());
            }
        } ?>

        <?php
        if (isset($_POST['delete'])) {
            $image = htmlspecialchars(strip_tags($image));

            $image = !empty($_FILES["image"]["name"])
                ? sha1_file($_FILES['image']['tmp_name']) . "-" . basename($_FILES["image"]["name"])
                : "";
            $target_directory = "uploads/customer/";
            $target_file = $target_directory . $image;
            $file_type = pathinfo($target_file, PATHINFO_EXTENSION);
            if ($row['image'] == 'profile.jpg') {
                echo "<div class='alert alert-danger'>This customer dont have an image so cant be deleted</div>";
            } else {
                unlink("uploads/customer/" . $row['image']);
                $_POST['image'] = null;
                $query = "UPDATE customers
        SET image=:image WHERE id = :id";
                // prepare query for excecution
                $stmt = $con->prepare($query);
                $stmt->bindParam(':image', $image);
                $stmt->bindParam(':id', $id);
                // Execute the query
                $stmt->execute();
            }
        }

        ?>

        <!--we have our html form here where new record information can be updated-->
        <!-- HTML form to update record will be here -->

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id={$id}"); ?>" method="post" enctype="multipart/form-data">
            <table class='table table-hover table-responsive table-bordered'>
                <tr>
                    <td>Username</td>
                    <td><input type='text' name='Username' value="<?php echo htmlspecialchars($Username, ENT_QUOTES);  ?>" class='form-control' /></td>
                </tr>
                <tr>
                    <td>Old Password</td>
                    <td><input type='password' name='passw' class='form-control' /></td>
                </tr>
                <tr>
                    <td>New Password</td>
                    <td><input type='password' name='new_pass' class='form-control' /></td>
                </tr>
                <tr>
                    <td>Comfirm Password</td>
                    <td><input type='password' name='comfirm_password' class='form-control' /></td>
                </tr>
                <tr>
                    <td>First Name</td>
                    <td><input type='text' name='First_name' value="<?php echo htmlspecialchars($First_name, ENT_QUOTES);  ?>" class='form-control' /></td>
                </tr>
                <tr>
                    <td>Last Name</td>
                    <td><input type='text' name='Last_name' value="<?php echo htmlspecialchars($Last_name, ENT_QUOTES);  ?>" class='form-control' /></td>
                </tr>
                <tr>
                    <td>Gender</td>
                    <td>
                        <?php
                        $account = "SELECT Gender FROM customers WHERE id=:id";
                        $stmt = $con->prepare($account);
                        $stmt->bindParam(":id", $id);
                        $stmt->execute();
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);
                        extract($row);
                        if ($Gender == "male") { ?>

                            <div class="ms-4 col-2 form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Gender" id="inlineRadio3" value="male" checked>
                                <label class="form-check-label" for="inlineRadio3">Male</label>
                            </div>
                            <div class="col-2 form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Gender" id="inlineRadio4" value="female">
                                <label class="form-check-label" for="inlineRadio4">Female</label>
                            </div>
                        <?php
                        } else { ?>
                            <div class="ms-4 col-2 form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Gender" id="inlineRadio3" value="male">
                                <label class="form-check-label" for="inlineRadio3">Male</label>
                            </div>
                            <div class="col-2 form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Gender" id="inlineRadio4" value="female" checked>
                                <label class="form-check-label" for="inlineRadio4">Female</label>
                            </div>
                        <?php
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>Birthday</td>
                    <td><input placeholder="Select birthday" type='date' name='Date_of_birth' value="<?php echo htmlspecialchars($Date_of_birth, ENT_QUOTES);  ?>" class='form-control ' /></td>
                </tr>
                <tr>
                    <td>Account Status</td>
                    <td>
                        <?php
                        $account = "SELECT Account_status FROM customers WHERE id=:id";
                        $stmt = $con->prepare($account);
                        $stmt->bindParam(":id", $id);
                        $stmt->execute();
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);
                        extract($row);
                        if ($Account_status == "opened") { ?>

                            <div class="ms-4 col-2 form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Account_status" id="inlineRadio3" value="opened" checked>
                                <label class="form-check-label" for="inlineRadio3">Active</label>
                            </div>
                            <div class="col-2 form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Account_status" id="inlineRadio4" value="closed">
                                <label class="form-check-label" for="inlineRadio4">Closed</label>
                            </div>
                        <?php
                        } else { ?>
                            <div class="ms-4 col-2 form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Account_status" id="inlineRadio3" value="opened">
                                <label class="form-check-label" for="inlineRadio3">Active</label>
                            </div>
                            <div class="col-2 form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Account_status" id="inlineRadio4" value="closed" checked>
                                <label class="form-check-label" for="inlineRadio4">Closed</label>
                            </div>
                        <?php
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>Images</td>
                    <td>
                        <div><img src="uploads/customer/<?php echo htmlspecialchars($image, ENT_QUOTES);  ?>" /></div>
                        <div><input type="file" name="image" value="<?php echo htmlspecialchars($image, ENT_QUOTES);  ?>" /></div>
                        <div><input type='submit' name='delete' value='Delete Image' class='btn btn-danger' /></div>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <input type='submit' name="update" value='Save Changes' class='btn btn-primary' />
                        <a href='customer_read.php' class='btn btn-danger'>Back to read customers</a>
                    </td>
                </tr>
            </table>
        </form>


    </div>
    <!-- end .container -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</body>

</html>