<?php
/**
 * AI Post Queue Management - Add New Post Form
 * 
 * Handles adding new AI-generated post requests to the queue
 * Requires WordPress authentication
 */

require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');

// Check user authentication
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url());
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = sanitize_text_field($_POST['category']);
    $title = sanitize_text_field($_POST['title']);
    $slug = sanitize_title($_POST['slug']);
    $prompt = sanitize_textarea_field($_POST['prompt']);

    // Get current queue from WordPress options
    $queue = get_option('ai_post_queue', []);

    // Create unique ID for queue item
    $new_id = time();

    // Add new item to queue
    $queue[] = [
        'id'       => $new_id,
        'category' => $category,
        'title'    => $title,
        'slug'     => $slug,
        'prompt'   => $prompt
    ];

    // Save updated queue
    update_option('ai_post_queue', $queue);

    // Redirect to generation page
    wp_redirect('genrate.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add New Post to Queue</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap');

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', 'Segoe UI', Tahoma, sans-serif;
    background: #1e1e1e;
    color: #d4d4d4;
    padding: 40px 20px;
    min-height: 100vh;
    font-size: 16px;
    line-height: 1.8;
}

h2 {
    text-align: center;
    color: #4fc3f7;
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 30px;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

form {
    background: #252526;
    padding: 35px;
    border-radius: 12px;
    max-width: 600px;
    margin: auto;
    box-shadow: 0 4px 20px rgba(0,0,0,0.5);
    border: 1px solid #3c3c3c;
}

label {
    display: block;
    margin-top: 20px;
    margin-bottom: 8px;
    font-weight: 500;
    color: #9cdcfe;
    font-size: 17px;
    letter-spacing: 0.5px;
}

input, textarea, select {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #3c3c3c;
    border-radius: 8px;
    background: #1e1e1e;
    color: #d4d4d4;
    font-size: 16px;
    font-family: 'Inter', sans-serif;
    transition: all 0.3s ease;
}

input:focus, textarea:focus, select:focus {
    outline: none;
    border-color: #007acc;
    background: #2d2d30;
    box-shadow: 0 0 0 2px rgba(0, 122, 204, 0.2);
}

input:valid {
    border-color: #4ec9b0;
}

input:invalid:not(:placeholder-shown) {
    border-color: #f48771;
}

textarea {
    resize: vertical;
    min-height: 120px;
    line-height: 1.6;
}

select {
    cursor: pointer;
    font-size: 16px;
}

select option {
    background: #252526;
    color: #d4d4d4;
    padding: 10px;
}

button {
    margin-top: 30px;
    background: linear-gradient(135deg, #007acc, #005a9e);
    color: white;
    padding: 14px 35px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 18px;
    font-weight: 500;
    font-family: 'Inter', sans-serif;
    width: 100%;
    transition: all 0.3s ease;
    box-shadow: 0 2px 10px rgba(0, 122, 204, 0.3);
}

button:hover {
    background: linear-gradient(135deg, #005a9e, #004080);
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0, 122, 204, 0.5);
}

button:active {
    transform: translateY(0);
}

/* Success message style */
.success {
    color: #4ec9b0;
    background: rgba(78, 201, 176, 0.1);
    padding: 10px;
    border-radius: 6px;
    margin: 10px 0;
}

/* Error message style */
.error {
    color: #f48771;
    background: rgba(244, 135, 113, 0.1);
    padding: 10px;
    border-radius: 6px;
    margin: 10px 0;
}

/* Scrollbar styling */
::-webkit-scrollbar {
    width: 10px;
}

::-webkit-scrollbar-track {
    background: #1e1e1e;
}

::-webkit-scrollbar-thumb {
    background: #4a4a4a;
    border-radius: 5px;
}

::-webkit-scrollbar-thumb:hover {
    background: #5a5a5a;
}
</style>
</head>
<body>
<h2>Add New Post to AI Generation Queue</h2>
<form method="POST">
    <label>Select Category:</label>
    <select name="category" required>
        <option value="">— Select Category —</option>
        <?php
        // Fetch all WordPress categories
        $cats = get_categories(['hide_empty' => false]);
        foreach ($cats as $cat) {
            echo "<option value='{$cat->term_id}'>{$cat->name}</option>";
        }
        ?>
    </select>

    <label>Post Title:</label>
    <input type="text" name="title" required>

    <label>Post Slug:</label>
    <input type="text" name="slug" required>

    <label>AI Prompt:</label>
    <textarea name="prompt" rows="5" required placeholder="Enter your prompt for AI content generation..."></textarea>

    <button type="submit">Add to Queue</button>
</form>
</body>
</html>
