<?php
include 'db.php';

// Handle Add Consultation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    // Get and sanitize form inputs
    $patID = intval($_POST['patID']);
    $docID = intval($_POST['docID']);
    $consultDate = $conn->real_escape_string($_POST['consultDate']);
    $diagnosis = $conn->real_escape_string($_POST['diagnosis']);
    $prescription = $conn->real_escape_string($_POST['prescription']);
    // Insert new consultation record
    $conn->query("INSERT INTO consultation (patID, docID, consultDate, diagnosis, prescription)
                  VALUES ($patID, $docID, '$consultDate', '$diagnosis', '$prescription')");
    header("Location: consultations.php");
    exit;
}

// Handle Update Consultation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    // Get and sanitize form inputs for update
    $consultID = intval($_POST['consultID']);
    $patID = intval($_POST['patID']);
    $docID = intval($_POST['docID']);
    $consultDate = $conn->real_escape_string($_POST['consultDate']);
    $diagnosis = $conn->real_escape_string($_POST['diagnosis']);
    $prescription = $conn->real_escape_string($_POST['prescription']);
    // Update consultation record
    $conn->query("UPDATE consultation SET patID=$patID, docID=$docID, consultDate='$consultDate', diagnosis='$diagnosis', prescription='$prescription' WHERE consultID=$consultID");
    header("Location: consultations.php");
    exit;
}

// Handle Delete Consultation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    // Get consultation ID to delete
    $delete_id = intval($_POST['delete_id']);
    // Delete consultation record
    $conn->query("DELETE FROM consultation WHERE consultID = $delete_id");
    header("Location: consultations.php");
    exit;
}

// Fetch doctors and patients for dropdowns in the form
$doctors = $conn->query("SELECT docID, docFName, docLName FROM doctor");
$patients = $conn->query("SELECT patID, patFName, patLName FROM patient");

// Search functionality for consultations
$search = '';
$where = '';
if (isset($_GET['search']) && $_GET['search'] !== '') {
    $search = $conn->real_escape_string($_GET['search']);
    // Filter consultations by diagnosis or prescription
    $where = "WHERE c.diagnosis LIKE '%$search%' OR c.prescription LIKE '%$search%'";
}

// Query to join consultation, doctor, and patient tables for display
$sql = "SELECT c.consultID, c.consultDate, c.diagnosis, c.prescription,
               d.docID, d.docFName, d.docLName,
               p.patID, p.patFName, p.patLName
        FROM consultation c
        JOIN doctor d ON c.docID = d.docID
        JOIN patient p ON c.patID = p.patID
        $where
        ORDER BY c.consultDate DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Consultations Management</title>
</head>
<body>
<div class="container">
    <h2>Consultations Management</h2>
    <!-- Navigation link back to menu -->
    <a href="index.php" class="btn btn-secondary mb-3">Back to Menu</a>

    <!-- Add Consultation Form -->
    <form method="post" class="mb-4 border p-3 bg-light rounded">
        <h5>Add Consultation</h5>
        <div class="form-row mb-2">
            <!-- Patient dropdown -->
            <div>
                <select name="patID" required>
                    <option value="">Select Patient</option>
                    <?php
                    $patients2 = $conn->query("SELECT patID, patFName, patLName FROM patient");
                    while($p = $patients2->fetch_assoc()): ?>
                        <option value="<?= $p['patID'] ?>"><?= $p['patFName'] . ' ' . $p['patLName'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <!-- Doctor dropdown -->
            <div>
                <select name="docID" required>
                    <option value="">Select Doctor</option>
                    <?php
                    $doctors2 = $conn->query("SELECT docID, docFName, docLName FROM doctor");
                    while($d = $doctors2->fetch_assoc()): ?>
                        <option value="<?= $d['docID'] ?>"><?= $d['docFName'] . ' ' . $d['docLName'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <!-- Consultation date input -->
            <div>
                <input type="datetime-local" name="consultDate" required>
            </div>
        </div>
        <!-- Diagnosis input -->
        <div class="mb-2">
            <input type="text" name="diagnosis" placeholder="Diagnosis" required>
        </div>
        <!-- Prescription input -->
        <div class="mb-2">
            <input type="text" name="prescription" placeholder="Prescription" required>
        </div>
        <!-- Submit button for adding consultation -->
        <button type="submit" name="add">Add Consultation</button>
    </form>

    <!-- Search Form for filtering consultations -->
    <form method="get" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" placeholder="Search Diagnosis or Prescription" value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Search</button>
            <a href="consultations.php">Reset</a>
        </div>
    </form>

    <!-- Table displaying consultations -->
    <div class="thread">
    <table>
        <thead>
            <tr>
                <th>Consultation ID</th>
                <th>Date</th>
                <th>Doctor</th>
                <th>Patient</th>
                <th>Diagnosis</th>
                <th>Prescription</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <?php if (isset($_GET['edit']) && $_GET['edit'] == $row['consultID']): ?>
                    <!-- Edit mode: show editable fields for the selected consultation -->
                    <form method="post">
                        <td><?= $row['consultID'] ?><input type="hidden" name="consultID" value="<?= $row['consultID'] ?>"></td>
                        <td><input type="datetime-local" name="consultDate" value="<?= date('Y-m-d\TH:i', strtotime($row['consultDate'])) ?>" required></td>
                        <td>
                            <select name="docID" required>
                                <?php
                                $doctors3 = $conn->query("SELECT docID, docFName, docLName FROM doctor");
                                while($d = $doctors3->fetch_assoc()):
                                    $selected = ($d['docID'] == $row['docID']) ? 'selected' : '';
                                ?>
                                    <option value="<?= $d['docID'] ?>" <?= $selected ?>><?= $d['docFName'] . ' ' . $d['docLName'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </td>
                        <td>
                            <select name="patID" required>
                                <?php
                                $patients3 = $conn->query("SELECT patID, patFName, patLName FROM patient");
                                while($p = $patients3->fetch_assoc()):
                                    $selected = ($p['patID'] == $row['patID']) ? 'selected' : '';
                                ?>
                                    <option value="<?= $p['patID'] ?>" <?= $selected ?>><?= $p['patFName'] . ' ' . $p['patLName'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </td>
                        <td><input type="text" name="diagnosis" value="<?= htmlspecialchars($row['diagnosis']) ?>" required></td>
                        <td><input type="text" name="prescription" value="<?= htmlspecialchars($row['prescription']) ?>" required></td>
                        <td>
                            <button type="submit" name="update">Save</button>
                            <a href="consultations.php">Cancel</a>
                        </td>
                    </form>
                <?php else: ?>
                    <!-- Display mode: show consultation details -->
                    <td><?= $row['consultID'] ?></td>
                    <td><?= $row['consultDate'] ?></td>
                    <td><?= $row['docFName'] . ' ' . $row['docLName'] ?></td>
                    <td><?= $row['patFName'] . ' ' . $row['patLName'] ?></td>
                    <td><?= $row['diagnosis'] ?></td>
                    <td><?= $row['prescription'] ?></td>
                    <td>
                        <!-- Edit and Delete actions -->
                        <a href="consultations.php?edit=<?= $row['consultID'] ?>">Edit</a>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="delete_id" value="<?= $row['consultID'] ?>">
                            <button type="submit" name="delete" onclick="return confirm('Delete this consultation?')">Delete</button>
                        </form>
                    </td>
                <?php endif; ?>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    </div>
</div>
</body>
</html>