<?php
include 'config/database.php';
$conn = getDBConnection();

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = (int)$_GET['id'];

if (isset($_GET['confirm']) && $_GET['confirm'] == 'yes') {
    $sql = "DELETE FROM posts WHERE id = $id";
    $conn->query($sql);
    header("Location: index.php");
    exit();
}

$sql = "SELECT * FROM posts WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    header("Location: index.php");
    exit();
}

$post = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Post - Advanced Blog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">Advanced Blog</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php">Back to Posts</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h3 class="card-title">Confirm Delete</h3>
                    </div>
                    <div class="card-body">
                        <p>Are you sure you want to delete the post: <strong><?php echo htmlspecialchars($post['title']); ?></strong>?</p>
                        <p>This action cannot be undone.</p>
                        
                        <div class="d-flex gap-2">
                            <a href="delete.php?id=<?php echo $id; ?>&confirm=yes" class="btn btn-danger">Yes, Delete</a>
                            <a href="index.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>
