<?php
include 'config/database.php';
$conn = getDBConnection();

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = (int)$_GET['id'];
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
    <title><?php echo htmlspecialchars($post['title']); ?> - Advanced Blog</title>
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
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-body">
                        <h1 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h1>
                        <p class="text-muted">By <?php echo htmlspecialchars($post['author'] ?? 'Unknown Author'); ?> on <?php echo $post['created_at']; ?></p>
                        <div class="card-text">
                            <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                        </div>
                        <div class="mt-4">
                            <a href="index.php" class="btn btn-primary">Back to Posts</a>
                            <a href="update.php?id=<?php echo $post['id']; ?>" class="btn btn-warning">Edit</a>
                            <a href="delete.php?id=<?php echo $post['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>
