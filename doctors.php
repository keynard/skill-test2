<?php

include 'db.php';

// Handle Add Doctor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $docFName = $conn->real_escape_string($_POST['docFName']);
    $docLName = $conn->real_escape_string($_POST['docLName']);
    $docAddress = $conn->real_escape_string($_POST['docAddress']);
    $docSpecial = $conn->real_escape_string($_POST['docSpecial']);
    $conn->query("INSERT INTO doctor (docFName, docLName, docAddress, docSpecial)
                  VALUES ('$docFName', '$docLName', '$docAddress', '$docSpecial')");
    header("Location: doctors.php");
    exit;
}

// Handle Update Doctor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $docID = intval($_POST['docID']);
    $docFName = $conn->real_escape_string($_POST['docFName']);
    $docLName = $conn->real_escape_string($_POST['docLName']);
    $docAddress = $conn->real_escape_string($_POST['docAddress']);
    $docSpecial = $conn->real_escape_string($_POST['docSpecial']);
    $conn->query("UPDATE doctor SET docFName='$docFName', docLName='$docLName', docAddress='$docAddress', docSpecial='$docSpecial' WHERE docID=$docID");
    header("Location: doctors.php");
    exit;
}

// Handle Delete Doctor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $delete_id = intval($_POST['delete_id']);
    $conn->query("DELETE FROM doctor WHERE docID = $delete_id");
    header("Location: doctors.php");
    exit;
}

// Search functionality
$search = '';
$where = '';
if (isset($_GET['search']) && $_GET['search'] !== '') {
    $search = $conn->real_escape_string($_GET['search']);
    $where = "WHERE docFName LIKE '%$search%' OR docLName LIKE '%$search%' OR docSpecial LIKE '%$search%'";
}

$sql = "SELECT * FROM doctor $where ORDER BY docFName";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Doctors Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Doctors Management</h2>
    <a href="index.php" class="btn btn-secondary mb-3">Back to Menu</a>

    <!-- Add Doctor Form -->
    <form method="post" class="mb-4 border p-3 bg-light rounded">
        <h5>Add Doctor</h5>
        <div class="row mb-2">
            <div class="col">
                <input type="text" name="docFName" class="form-control" placeholder="First Name" required>
            </div>
            <div class="col">
                <input type="text" name="docLName" class="form-control" placeholder="Last Name" required>
            </div>
            <div class="col">
                <input type="text" name="docAddress" class="form-control" placeholder="Address" required>
            </div>
            <div class="col">
                <input type="text" name="docSpecial" class="form-control" placeholder="Specialization" required>
            </div>
        </div>
        <button type="submit" name="add" class="btn btn-primary">Add Doctor</button>
    </form>

    <!-- Search Form -->
    <form method="get" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search by Name or Specialization" value="<?= htmlspecialchars($search) ?>">
            <button class="btn btn-outline-primary" type="submit">Search</button>
            <a href="doctors.php" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>

    <table class="table table-bordered">
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
                    <form method="post">
                        <td><?= $row['docID'] ?><input type="hidden" name="docID" value="<?= $row['docID'] ?>"></td>
                        <td><input type="text" name="docFName" value="<?= htmlspecialchars($row['docFName']) ?>" class="form-control" required></td>
                        <td><input type="text" name="docLName" value="<?= htmlspecialchars($row['docLName']) ?>" class="form-control" required></td>
                        <td><input type="text" name="docAddress" value="<?= htmlspecialchars($row['docAddress']) ?>" class="form-control" required></td>
                        <td><input type="text" name="docSpecial" value="<?= htmlspecialchars($row['docSpecial']) ?>" class="form-control" required></td>
                        <td>
                            <button type="submit" name="update" class="btn btn-success btn-sm">Save</button>
                            <a href="doctors.php" class="btn btn-secondary btn-sm">Cancel</a>
                        </td>
                    </form>
                <?php else: ?>
                    <td><?= $row['docID'] ?></td>
                    <td><?= $row['docFName'] ?></td>
                    <td><?= $row['docLName'] ?></td>
                    <td><?= $row['docAddress'] ?></td>
                    <td><?= $row['docSpecial'] ?></td>
                    <td>
                        <a href="doctors.php?edit=<?= $row['docID'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="delete_id" value="<?= $row['docID'] ?>">
                            <button type="submit" name="delete" class="btn btn-danger btn-sm" onclick="return confirm('Delete this doctor?')">Delete</button>
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