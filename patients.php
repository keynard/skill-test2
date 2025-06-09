<?php

include 'db.php';

// Handle Add Patient
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $patFName = $conn->real_escape_string($_POST['patFName']);
    $patLName = $conn->real_escape_string($_POST['patLName']);
    $patBDate = $conn->real_escape_string($_POST['patBDate']);
    $patTelNo = $conn->real_escape_string($_POST['patTelNo']);
    $conn->query("INSERT INTO patient (patFName, patLName, patBDate, patTelNo)
                  VALUES ('$patFName', '$patLName', '$patBDate', '$patTelNo')");
    header("Location: patients.php");
    exit;
}

// Handle Update Patient
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $patID = intval($_POST['patID']);
    $patFName = $conn->real_escape_string($_POST['patFName']);
    $patLName = $conn->real_escape_string($_POST['patLName']);
    $patBDate = $conn->real_escape_string($_POST['patBDate']);
    $patTelNo = $conn->real_escape_string($_POST['patTelNo']);
    $conn->query("UPDATE patient SET patFName='$patFName', patLName='$patLName', patBDate='$patBDate', patTelNo='$patTelNo' WHERE patID=$patID");
    header("Location: patients.php");
    exit;
}

// Handle Delete Patient
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $delete_id = intval($_POST['delete_id']);
    $conn->query("DELETE FROM patient WHERE patID = $delete_id");
    header("Location: patients.php");
    exit;
}

// Search functionality
$search = '';
$where = '';
if (isset($_GET['search']) && $_GET['search'] !== '') {
    $search = $conn->real_escape_string($_GET['search']);
    $where = "WHERE patFName LIKE '%$search%' OR patLName LIKE '%$search%' OR patTelNo LIKE '%$search%'";
}

$sql = "SELECT * FROM patient $where ORDER BY patFName";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Patients Management</title>
   
</head>
<body>
<div class="container">
    <h2>Patients Management</h2>
    <a href="index.php" class="btn btn-secondary mb-3">Back to Menu</a>

    <!-- Add Patient Form -->
    <form method="post" class="mb-4 border p-3 bg-light rounded">
        <h5>Add Patient</h5>
        <div class="row mb-2">
            <div class="col">
                <input type="text" name="patFName" placeholder="First Name" required>
            </div>
            <div class="col">
                <input type="text" name="patLName" placeholder="Last Name" required>
            </div>
            <div class="col">
                <input type="date" name="patBDate" placeholder="Birth Date" required>
            </div>
            <div class="col">
                <input type="text" name="patTelNo" placeholder="Telephone Number" required>
            </div>
        </div>
        <button type="submit" name="add" class="btn btn-primary">Add Patient</button>
    </form>

    <!-- Search Form -->
    <form method="get" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" placeholder="Search by Name or Telephone" value="<?= htmlspecialchars($search) ?>">
            <button class="btn btn-outline-primary" type="submit">Search</button>
            <a href="patients.php" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID Number</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Birth Date</th>
                <th>Telephone Number</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <?php if (isset($_GET['edit']) && $_GET['edit'] == $row['patID']): ?>
                    <form method="post">
                        <td><?= $row['patID'] ?><input type="hidden" name="patID" value="<?= $row['patID'] ?>"></td>
                        <td><input type="text" name="patFName" value="<?= htmlspecialchars($row['patFName']) ?>" required></td>
                        <td><input type="text" name="patLName" value="<?= htmlspecialchars($row['patLName']) ?>" required></td>
                        <td><input type="date" name="patBDate" value="<?= $row['patBDate'] ?>" required></td>
                        <td><input type="text" name="patTelNo" value="<?= htmlspecialchars($row['patTelNo']) ?>" required></td>
                        <td>
                            <button type="submit" name="update" class="btn btn-success btn-sm">Save</button>
                            <a href="patients.php" class="btn btn-secondary btn-sm">Cancel</a>
                        </td>
                    </form>
                <?php else: ?>
                    <td><?= $row['patID'] ?></td>
                    <td><?= $row['patFName'] ?></td>
                    <td><?= $row['patLName'] ?></td>
                    <td><?= $row['patBDate'] ?></td>
                    <td><?= $row['patTelNo'] ?></td>
                    <td>
                        <a href="patients.php?edit=<?= $row['patID'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="delete_id" value="<?= $row['patID'] ?>">
                            <button type="submit" name="delete" class="btn btn-danger btn-sm" onclick="return confirm('Delete this patient?')">Delete</button>
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