<?php
include 'db.php';

// Initialize results array and get the type of inquiry from GET parameters
$results = [];
$type = $_GET['type'] ?? '';

// If searching by specialization
if ($type == 'specialization' && isset($_GET['special'])) {
    $special = $conn->real_escape_string($_GET['special']);
    // Query doctors by specialization
    $sql = "SELECT * FROM doctor WHERE docSpecial LIKE '%$special%'";
    $results = $conn->query($sql);
}
// If searching consultations by doctor ID
elseif ($type == 'consult_by_doc' && isset($_GET['docID'])) {
    $docID = intval($_GET['docID']);
    // Query consultations for a specific doctor, joining with patient info
    $sql = "SELECT c.*, p.patFName, p.patLName FROM consultation c
            JOIN patient p ON c.patID = p.patID
            WHERE c.docID = $docID";
    $results = $conn->query($sql);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Consultations Inquiry</title>
</head>
<body>
<div class="container">
    <h2>Consultations Inquiry</h2>
    <!-- Navigation link back to menu -->
    <a href="index.php" class="btn btn-secondary mb-3">Back to Menu</a>

    <!-- Form to search doctors by specialization -->
    <form method="get" class="mb-3">
        <input type="hidden" name="type" value="specialization">
        <div class="input-group">
            <input type="text" name="special" placeholder="Search Doctors by Specialization" required>
            <button class="btn btn-primary" type="submit">Search</button>
        </div>
    </form>

    <!-- Form to search consultations by doctor ID -->
    <form method="get" class="mb-3">
        <input type="hidden" name="type" value="consult_by_doc">
        <div class="input-group">
            <input type="number" name="docID" placeholder="Consultations by Doctor ID" required>
            <button class="btn btn-primary" type="submit">Search</button>
        </div>
    </form>

    <!-- Display results for specialization search -->
    <?php if ($type == 'specialization'): ?>
        <h4>Doctors with Specialization: <?= htmlspecialchars($_GET['special']) ?></h4>
        <table>
            <thead>
                <tr>
                    <th>License/ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Specialization</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($results && $results->num_rows > 0): while($row = $results->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['docID'] ?></td>
                    <td><?= $row['docFName'] ?></td>
                    <td><?= $row['docLName'] ?></td>
                    <td><?= $row['docSpecial'] ?></td>
                </tr>
                <?php endwhile; else: ?>
                <tr><td colspan="4">No results found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    <!-- Display results for consultations by doctor -->
    <?php elseif ($type == 'consult_by_doc'): ?>
        <h4>Consultations for Doctor ID: <?= htmlspecialchars($_GET['docID']) ?></h4>
        <table>
            <thead>
                <tr>
                    <th>Consultation ID</th>
                    <th>Date</th>
                    <th>Patient</th>
                    <th>Diagnosis</th>
                    <th>Prescription</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($results && $results->num_rows > 0): while($row = $results->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['consultID'] ?></td>
                    <td><?= $row['consultDate'] ?></td>
                    <td><?= $row['patFName'] . ' ' . $row['patLName'] ?></td>
                    <td><?= $row['diagnosis'] ?></td>
                    <td><?= $row['prescription'] ?></td>
                </tr>
                <?php endwhile; else: ?>
                <tr><td colspan="5">No results found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>