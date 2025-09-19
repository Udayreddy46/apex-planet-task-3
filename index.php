<?php
// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'blog';

// Create connection
$conn = new mysqli($host, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS blog";
$conn->query($sql);
$conn->select_db($database);

// Create posts table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    author VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($sql);

// Handle search
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Pagination
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Count total posts
$count_sql = "SELECT COUNT(*) as total FROM posts";
if ($search) {
    $count_sql .= " WHERE title LIKE '%$search%' OR content LIKE '%$search%'";
}
$count_result = $conn->query($count_sql);
$total_posts = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_posts / $limit);

// Fetch posts
$sql = "SELECT * FROM posts";
if ($search) {
    $sql .= " WHERE title LIKE '%$search%' OR content LIKE '%$search%'";
}
$sql .= " ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advanced Blog - Task 3</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">Advanced Blog</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="create.php">Create Post</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <h1 class="text-center mb-4">Blog Posts</h1>
                
                <!-- Enhanced Search Form -->
                <div class="search-form fade-in">
                    <form method="GET" action="index.php">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search posts by title or content..." value="<?php echo htmlspecialchars($search); ?>" autocomplete="off">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i> Search
                            </button>
                            <?php if ($search): ?>
                                <a href="index.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i> Clear
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                    <?php if ($search): ?>
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="bi bi-info-circle"></i> 
                                Showing results for "<?php echo htmlspecialchars($search); ?>" 
                                (<?php echo $total_posts; ?> result<?php echo $total_posts != 1 ? 's' : ''; ?>)
                            </small>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Enhanced Posts Display -->
                <?php if ($result->num_rows > 0): ?>
                    <?php while($post = $result->fetch_assoc()): ?>
                        <div class="card post-card mb-4 fade-in">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h5>
                                <div class="post-meta">
                                    <i class="bi bi-person"></i> By <?php echo htmlspecialchars($post['author'] ?? 'Unknown Author'); ?> 
                                    <span class="mx-2">•</span>
                                    <i class="bi bi-calendar"></i> <?php echo date('M j, Y', strtotime($post['created_at'])); ?>
                                </div>
                                <p class="card-text"><?php echo substr(htmlspecialchars($post['content']), 0, 200) . (strlen($post['content']) > 200 ? '...' : ''); ?></p>
                                <div class="post-actions">
                                    <a href="read.php?id=<?php echo $post['id']; ?>" class="btn btn-primary">
                                        <i class="bi bi-eye"></i> Read More
                                    </a>
                                    <a href="update.php?id=<?php echo $post['id']; ?>" class="btn btn-warning">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <a href="delete.php?id=<?php echo $post['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this post?')">
                                        <i class="bi bi-trash"></i> Delete
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="bi bi-search"></i>
                        <h3>No posts found</h3>
                        <?php if ($search): ?>
                            <p>No posts match your search criteria. Try different keywords or <a href="index.php">clear the search</a>.</p>
                        <?php else: ?>
                            <p>No posts have been created yet. <a href="create.php" class="btn btn-primary">Create the first post</a></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Enhanced Pagination -->
                <?php if ($total_pages > 1): ?>
                    <nav aria-label="Page navigation" class="mt-5">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="pagination-info">
                                <small class="text-muted">
                                    Showing <?php echo (($page - 1) * $limit) + 1; ?> to <?php echo min($page * $limit, $total_posts); ?> of <?php echo $total_posts; ?> posts
                                </small>
                            </div>
                            <div class="pagination-controls">
                                <small class="text-muted">Page <?php echo $page; ?> of <?php echo $total_pages; ?></small>
                            </div>
                        </div>
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="index.php?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>" aria-label="Previous">
                                        <i class="bi bi-chevron-left"></i> Previous
                                    </a>
                                </li>
                            <?php else: ?>
                                <li class="page-item disabled">
                                    <span class="page-link" aria-label="Previous">
                                        <i class="bi bi-chevron-left"></i> Previous
                                    </span>
                                </li>
                            <?php endif; ?>
                            
                            <?php
                            // Smart pagination - show limited page numbers
                            $start = max(1, $page - 2);
                            $end = min($total_pages, $page + 2);
                            
                            if ($start > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="index.php?page=1&search=<?php echo urlencode($search); ?>">1</a>
                                </li>
                                <?php if ($start > 2): ?>
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php for($i = $start; $i <= $end; $i++): ?>
                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="index.php?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($end < $total_pages): ?>
                                <?php if ($end < $total_pages - 1): ?>
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                <?php endif; ?>
                                <li class="page-item">
                                    <a class="page-link" href="index.php?page=<?php echo $total_pages; ?>&search=<?php echo urlencode($search); ?>"><?php echo $total_pages; ?></a>
                                </li>
                            <?php endif; ?>
                            
                            <?php if ($page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="index.php?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>" aria-label="Next">
                                        Next <i class="bi bi-chevron-right"></i>
                                    </a>
                                </li>
                            <?php else: ?>
                                <li class="page-item disabled">
                                    <span class="page-link" aria-label="Next">
                                        Next <i class="bi bi-chevron-right"></i>
                                    </span>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>
