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

// Drop and recreate posts table to ensure correct schema
$sql = "DROP TABLE IF EXISTS posts";
$conn->query($sql);

// Create posts table with correct schema
$sql = "CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    author VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($sql);

// Insert sample data
    $sample_posts = [
        [
            'title' => 'Welcome to Advanced Blog',
            'content' => 'This is your first blog post! You can create, edit, and delete posts using the navigation above. The blog features search functionality and pagination for better user experience.',
            'author' => 'Admin'
        ],
        [
            'title' => 'Getting Started with PHP',
            'content' => 'PHP is a powerful server-side scripting language that is widely used for web development. It allows you to create dynamic web pages and interact with databases.',
            'author' => 'Developer'
        ],
        [
            'title' => 'Database Design Best Practices',
            'content' => 'When designing databases, it\'s important to follow normalization principles, use appropriate data types, and create proper indexes for better performance.',
            'author' => 'Database Admin'
        ],
        [
            'title' => 'Responsive Web Design',
            'content' => 'Responsive web design ensures that your website looks great on all devices, from desktop computers to mobile phones. Use CSS media queries and flexible layouts.',
            'author' => 'UI/UX Designer'
        ],
        [
            'title' => 'Security in Web Applications',
            'content' => 'Web security is crucial for protecting user data and preventing attacks. Always validate input, use prepared statements, and implement proper authentication.',
            'author' => 'Security Expert'
        ]
    ];
    
    foreach ($sample_posts as $post) {
        $title = $conn->real_escape_string($post['title']);
        $content = $conn->real_escape_string($post['content']);
        $author = $conn->real_escape_string($post['author']);
        
        $insert_sql = "INSERT INTO posts (title, content, author) VALUES ('$title', '$content', '$author')";
        $conn->query($insert_sql);
    }


// Function to get database connection
function getDBConnection() {
    global $host, $username, $password, $database;
    $conn = new mysqli($host, $username, $password, $database);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}
?>
