<!DOCTYPE html>
<html>
<head>
    <title>Contact Form</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Populate states based on selected country
            $('#country').change(function() {
                var country = $(this).val();
                $.ajax({
                    url: 'get_states_districts.php',
                    method: 'POST',
                    data: { country: country },
                    dataType: 'html',
                    success: function(response) {
                        $('#state').html(response);
                        $('#district').html('<option value="">Select District</option>');
                    }
                });
            });

            // Populate districts based on selected state
            $('#state').change(function() {
                var state = $(this).val();
                $.ajax({
                    url: 'get_states_districts.php',
                    method: 'POST',
                    data: { state: state },
                    dataType: 'html',
                    success: function(response) {
                        $('#district').html(response);
                    }
                });
            });

            // Form submission using AJAX
            $('#contactForm').submit(function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                $.ajax({
                    url: 'submit_form.php',
                    method: 'POST',
                    data: formData,
                    dataType: 'html',
                    success: function(response) {
                        $('#formResponse').html(response);
                    }
                });
            });
        });
    </script>
</head>
<body>
    <?php
        // Process form submission and store data in the database
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $address = $_POST['address'];
            $country = $_POST['country'];
            $state = $_POST['state'];
            $district = $_POST['district'];
            $gender = $_POST['gender'];
            $mobile = $_POST['mobile'];

            // Perform database insertion
            // Replace database connection details with your own
            $dbHost = 'your_host';
            $dbName = 'your_database';
            $dbUser = 'your_username';
            $dbPass = 'your_password';

            try {
                $conn = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Insert data into the database
                $stmt = $conn->prepare("INSERT INTO contacts (name, email, address, country, state, district, gender, mobile) 
                                        VALUES (:name, :email, :address, :country, :state, :district, :gender, :mobile)");
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':address', $address);
                $stmt->bindParam(':country', $country);
                $stmt->bindParam(':state', $state);
                $stmt->bindParam(':district', $district);
                $stmt->bindParam(':gender', $gender);
                $stmt->bindParam(':mobile', $mobile);
                $stmt->execute();

                echo 'Form submitted successfully!';
            } catch (PDOException $e) {
                echo 'Error: ' . $e->getMessage();
            }

            $conn = null;
        }
    ?>
    <h1>Contact Form</h1>
    <div id="formResponse"></div>
    <form id="contactForm" method="POST">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br>

        <label for="address">Address:</label>
        <textarea id="address" name="address" required></textarea><br>

        <label for="country">Country:</label>
        <select id="country" name="country" required>
            <option value="">Select Country</option>
            <!-- Populate options dynamically -->
        </select><br>

        <label for="state">State:</label>
        <select id="state" name="state" required>
            <option value="">Select State</option>
        </select><br>

        <label for="district">District:</label>
        <select id="district" name="district" required>
            <option value="">Select District</option>
        </select><br>

        <label>Gender:</label>
        <input type="radio" id="male" name="gender" value="male" required>
        <label for="male">Male</label>
        <input type="radio" id="female" name="gender" value="female" required>
        <label for="female">Female</label><br>

        <label for="mobile">Mobile Number:</label>
        <input type="text" id="mobile" name="mobile" required><br>

        <input type="submit" value="Submit">
    </form>
    <?php
        // Retrieve states and districts based on selected country/state
        if (isset($_POST['country']) || isset($_POST['state'])) {
            $country = $_POST['country'];
            $state = $_POST['state'];

            // Perform database queries to fetch states and districts
            // Replace database connection details with your own
            $dbHost = 'your_host';
            $dbName = 'your_database';
            $dbUser = 'your_username';
            $dbPass = 'your_password';

            $conn = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
            $states = [];
            $districts = [];

            // Fetch states based on selected country
            if ($country) {
                $stmt = $conn->prepare("SELECT * FROM states WHERE country = :country");
                $stmt->bindParam(':country', $country);
                $stmt->execute();
                $states = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            // Fetch districts based on selected state
            if ($state) {
                $stmt = $conn->prepare("SELECT * FROM districts WHERE state = :state");
                $stmt->bindParam(':state', $state);
                $stmt->execute();
                $districts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            // Generate HTML options for states and districts
            $stateOptions = '';
            foreach ($states as $state) {
                $stateOptions .= '<option value="' . $state['state_id'] . '">' . $state['state_name'] . '</option>';
            }

            $districtOptions = '';
            foreach ($districts as $district) {
                $districtOptions .= '<option value="' . $district['district_id'] . '">' . $district['district_name'] . '</option>';
            }

            echo '<script>';
            echo '$("#state").html(\'' . $stateOptions . '\');';
            echo '$("#district").html(\'' . $districtOptions . '\');';
            echo '</script>';
        }
    ?>
</body>
</html>

