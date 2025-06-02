<!DOCTYPE html>
<html>
<head>
    <title>Customer Search</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        form {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        input[type="text"] {
            width: calc(100% - 20px);
            padding: 8px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .search-button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 10px;
        }
        .search-button:hover {
            background-color: #45a049;
        }
        .clear-button {
            background-color: #f44336;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .clear-button:hover {
            background-color: #e53935;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        #results {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow: auto; /* Add this line */
        }
    </style>
    <script>
        function clearResults() {
            document.getElementById('results').innerHTML = '';
        }
    </script>
</head>
<body>
    <h1>Search Customer</h1>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" onreset="clearResults()">
        <label for="klantnummer">Search by Klantnummer:</label>
        <input type="text" id="klantnummer" name="klantnummer">
        <button type="submit" class="search-button" name="search_klantid">Search</button>
        <br><br>
        <label for="geboortejaar">Search by Geboortejaar:</label>
        <input type="text" id="geboortejaar" name="geboortejaar">
        <button type="submit" class="search-button" name="search_geboortejaar">Search</button>
        <br><br>
        <label for="naam">Search by Voornaam and Achternaam:</label>
        <input type="text" id="voornaam" name="voornaam" placeholder="Voornaam">
        <input type="text" id="achternaam" name="achternaam" placeholder="Achternaam">
        <button type="submit" class="search-button" name="search_naam">Search</button>
        <br><br>
        <button type="reset" class="clear-button">Clear</button>
    </form>
    <div id="results"></div>

    <?php
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Database connection parameters
        $host = '127.0.0.1'; // Use the service name from docker-compose.yml
        $user = 'kevinpvb4fciRQcj';
        $password = 'jEgz17C6nVNF4Rr2XplbYaLf';
        $dbname = 'kevin_pvb4_fcict_nl_KsgOMiDt';
        $port = 3306;

        $mysqli = new mysqli($host, $user, $password, $dbname, $port);

        if ($mysqli->connect_error) {
            die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
        }

        $query = "SELECT * FROM your_table WHERE 1=1";
        $params = [];
        $types = "";

        if (isset($_POST['search_klantid']) && !empty($_POST['klantnummer'])) {
            $query .= " AND KlantID = ?";
            $params[] = $_POST['klantnummer'];
            $types .= "s";
        }

        if (isset($_POST['search_geboortejaar']) && !empty($_POST['geboortejaar'])) {
            $query .= " AND YEAR(geboortedatum) = ?";
            $params[] = $_POST['geboortejaar'];
            $types .= "i";
        }

        if (isset($_POST['search_naam']) && !empty($_POST['voornaam']) && !empty($_POST['achternaam'])) {
            $query .= " AND Voornaam = ? AND Achternaam = ?";
            $params[] = $_POST['voornaam'];
            $params[] = $_POST['achternaam'];
            $types .= "ss";
        }

        if ($types) {
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param($types, ...$params);

            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo "<div id='results'>";
                echo "<table><tr><th>KlantID</th><th>Voornaam</th><th>Achternaam</th><th>Sekse</th><th>Emailadres</th><th>Geboortedatum</th></tr>";
                while ($row = $result->fetch_assoc()) {
                    $klantID = htmlspecialchars($row["KlantID"] ?? '');
                    $voornaam = htmlspecialchars($row["Voornaam"] ?? '');
                    $achternaam = htmlspecialchars($row["Achternaam"] ?? '');
                    $sekse = htmlspecialchars($row["Sekse"] ?? '');
                    $emailadres = htmlspecialchars($row["Emailadres"] ?? '');
                    $geboortedatum = htmlspecialchars($row["geboortedatum"] ?? '');

                    echo "<tr><td>{$klantID}</td><td>{$voornaam}</td><td>{$achternaam}</td><td>{$sekse}</td><td>{$emailadres}</td><td>{$geboortedatum}</td></tr>";
                }
                echo "</table>";
                echo "</div>";
            } else {
                echo "<p style='text-align: center; color: red;'>No results found.</p>";
            }

            $stmt->close();
        } else {
            echo "<p style='text-align: center; color: red;'>Please provide search criteria.</p>";
        }

        $mysqli->close();
    }
    ?>
</body>
</html>
