<?php

include 'db.php';

// Handle Add Doctor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    // Get and sanitize form inputs for new doctor
    $docFName = $conn->real_escape_string($_POST['docFName']);
    $docLName = $conn->real_escape_string($_POST['docLName']);
    $docAddress = $conn->real_escape_string($_POST['docAddress']);
    $docSpecial = $conn->real_escape_string($_POST['docSpecial']);
    // Insert new doctor record
    $conn->query("INSERT INTO doctor (docFName, docLName, docAddress, docSpecial)
                  VALUES ('$docFName', '$docLName', '$docAddress', '$docSpecial')");
    header("Location: doctors.php");
    exit;
}

// Handle Update Doctor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    // Get and sanitize form inputs for update
    $docID = intval($_POST['docID']);
    $docFName = $conn->real_escape_string($_POST['docFName']);
    $docLName = $conn->real_escape_string($_POST['docLName']);
    $docAddress = $conn->real_escape_string($_POST['docAddress']);
    $docSpecial = $conn->real_escape_string($_POST['docSpecial']);
    // Update doctor record
    $conn->query("UPDATE doctor SET docFName='$docFName', docLName='$docLName', docAddress='$docAddress', docSpecial='$docSpecial' WHERE docID=$docID");
    header("Location: doctors.php");
    exit;
}

// Handle Delete Doctor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    // Get doctor ID to delete
    $delete_id = intval($_POST['delete_id']);
    // Delete doctor record
    $conn->query("DELETE FROM doctor WHERE docID = $delete_id");
    header("Location: doctors.php");
    exit;
}

// Search functionality for doctors
$search = '';
$where = '';
if (isset($_GET['search']) && $_GET['search'] !== '') {
    $search = $conn->real_escape_string($_GET['search']);
    // Filter doctors by first name, last name, or specialization
    $where = "WHERE docFName LIKE '%$search%' OR docLName LIKE '%$search%' OR docSpecial LIKE '%$search%'";
}

// Query to get all doctors (with optional search filter)
$sql = "SELECT * FROM doctor $where ORDER BY docFName";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Doctors Management</title>
</head>
<body>
<div class="container">
    <h2>Doctors Management</h2>
    <!-- Navigation link back to menu -->
    <a href="index.php">Back to Menu</a>

    <!-- Add Doctor Form -->
    <form method="post">
        <h5>Add Doctor</h5>
        <div class="row">
            <div class="col">
                <input type="text" name="docFName" placeholder="First Name" required>
            </div>
            <div class="col">
                <input type="text" name="docLName" placeholder="Last Name" required>
            </div>
            <div class="col">
                <input type="text" name="docAddress" placeholder="Address" required>
            </div>
            <div class="col">
                <input type="text" name="docSpecial" placeholder="Specialization" required>
            </div>
        </div>
        <button type="submit" name="add">Add Doctor</button>
    </form>

    <!-- Search Form for filtering doctors -->
    <form method="get">
        <div class="input-group">
            <input type="text" name="search" placeholder="Search by Name or Specialization" value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Search</button>
            <a href="doctors.php">Reset</a>
        </div>
    </form>

    <!-- Table displaying doctors -->
    <table>
        <thead>
            <tr>
                <th>License/ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Address</th>
                <th>Specialization</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <?php if (isset($_GET['edit']) && $_GET['edit'] == $row['docID']): ?>
                    <!-- Edit mode: show editable fields for the selected doctor -->
                    <form method="post">
                        <td><?= $row['docID'] ?><input type="hidden" name="docID" value="<?= $row['docID'] ?>"></td>
                        <td><input type="text" name="docFName" value="<?= htmlspecialchars($row['docFName']) ?>" required></td>
                        <td><input type="text" name="docLName" value="<?= htmlspecialchars($row['docLName']) ?>" required></td>
                        <td><input type="text" name="docAddress" value="<?= htmlspecialchars($row['docAddress']) ?>" required></td>
                        <td><input type="text" name="docSpecial" value="<?= htmlspecialchars($row['docSpecial']) ?>" required></td>
                        <td>
                            <button type="submit" name="update">Save</button>
                            <a href="doctors.php">Cancel</a>
                        </td>
                    </form>
                <?php else: ?>
                    <!-- Display mode: show doctor details -->
                    <td><?= $row['docID'] ?></td>
                    <td><?= $row['docFName'] ?></td>
                    <td><?= $row['docLName'] ?></td>
                    <td><?= $row['docAddress'] ?></td>
                    <td><?= $row['docSpecial'] ?></td>
                    <td>
                        <!-- Edit and Delete actions -->
                        <a href="doctors.php?edit=<?= $row['docID'] ?>">Edit</a>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="delete_id" value="<?= $row['docID'] ?>">
                            <button type="submit" name="delete" onclick="return confirm('Delete this doctor?')">Delete</button>
                        </form>
                    </td>
                <?php endif; ?>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>