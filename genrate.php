<?php
/**
 * AI Post Generator - Queue Processing Page
 * 
 * Processes queued AI content generation requests
 * Displays prompt and handles WordPress post creation
 */

require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');

// Check user authentication
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url());
    exit;
}

// Get current queue from WordPress options
$queue = get_option('ai_post_queue', []);

// Get first item from queue
$current = $queue[0] ?? null;

// If queue is empty
if (!$current) {
    echo '
    <!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="UTF-8">
    <title>Queue is Empty</title>
    <style>
    @import url("https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap");

    * {
        box-sizing: border-box;
    }

    body {
        font-family: "Inter", sans-serif;
        background-color: #1e1e1e;
        color: #d4d4d4;
        text-align: center;
        padding: 80px 20px;
        line-height: 1.9;
    }

    .container {
        max-width: 700px;
        margin: 0 auto;
        background: #252526;
        border: 1px solid #333;
        border-radius: 16px;
        padding: 50px 30px;
        box-shadow: 0 0 30px rgba(0,0,0,0.4);
        animation: fadeIn 0.8s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    h2 {
        color: #ffcc00;
        font-size: 30px;
        font-weight: 600;
        margin-bottom: 25px;
    }

    p {
        font-size: 20px;
        color: #cccccc;
        margin-bottom: 40px;
    }

    a.button {
        display: inline-block;
        background: #007acc;
        color: #fff;
        padding: 12px 28px;
        border-radius: 10px;
        text-decoration: none;
        font-size: 18px;
        font-weight: 600;
        transition: background 0.25s ease, transform 0.15s ease;
    }

    a.button:hover {
        background: #005f99;
        transform: translateY(-2px);
    }

    .vscode-line {
        background: linear-gradient(90deg, #007acc 0%, #ffcc00 100%);
        height: 4px;
        width: 60%;
        margin: 25px auto 40px;
        border-radius: 10px;
    }

    footer {
        margin-top: 50px;
        font-size: 15px;
        color: #888;
    }
    </style>
    </head>
    <body>

    <div class="container">
        <h2>Queue is Empty — No Items to Process</h2>
        <div class="vscode-line"></div>
        <a href="insert.php" class="button">Back to Add Post</a>
    </div>

    </body>
    </html>
    ';
    exit;
}


// Handle form submission (insert post)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ai_output'])) {
    $content = $_POST['ai_output'];
    $title = sanitize_text_field($_POST['title']);
    $slug = sanitize_title($_POST['slug']);
    $category = sanitize_text_field($_POST['category']);

    // Insert post into WordPress
    $new_post = [
        'post_title'    => $title,
        'post_name'     => $slug,
        'post_content'  => $content,
        'post_status'   => 'draft',
        'post_author'   => get_current_user_id(),
        'post_category' => [$category]
    ];

    $post_id = wp_insert_post($new_post);

    if ($post_id) {
        // Remove processed item from queue
        array_shift($queue);
        update_option('ai_post_queue', $queue);

        echo '
        <!DOCTYPE html>
        <html lang="en">
        <head>
        <meta charset="UTF-8">
        <title>Post Successfully Created</title>
        <style>
        @import url("https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap");

        * {
            box-sizing: border-box;
        }

        body {
            font-family: "Inter", sans-serif;
            background-color: #1e1e1e;
            color: #d4d4d4;
            text-align: center;
            padding: 80px 20px;
            line-height: 1.8;
        }

        .container {
            max-width: 700px;
            margin: 0 auto;
            background: #252526;
            border: 1px solid #333;
            border-radius: 16px;
            padding: 50px 30px;
            box-shadow: 0 0 30px rgba(0,0,0,0.4);
            animation: fadeIn 0.8s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h2 {
            color: #00e676;
            font-size: 32px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        p {
            font-size: 20px;
            color: #cccccc;
            margin-bottom: 35px;
        }

        a.button {
            display: inline-block;
            background: #007acc;
            color: white;
            padding: 12px 28px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 18px;
            margin: 10px;
            transition: background 0.25s ease, transform 0.15s ease;
        }

        a.button:hover {
            background: #005f99;
            transform: translateY(-2px);
        }

        a.link {
            color: #4fc3f7;
            font-size: 18px;
            text-decoration: none;
        }

        a.link:hover {
            text-decoration: underline;
        }

        .vscode-line {
            background: linear-gradient(90deg, #007acc 0%, #00e676 100%);
            height: 4px;
            width: 60%;
            margin: 25px auto 40px;
            border-radius: 10px;
        }

        footer {
            margin-top: 50px;
            font-size: 15px;
            color: #888;
        }

        .error {
            color: #ff5252;
            font-size: 28px;
            font-weight: bold;
        }
        </style>
        </head>
        <body>

        <div class="container">
            <h2>Post Successfully Created as Draft</h2>
            <div class="vscode-line"></div>
            <p><a href="' . get_edit_post_link($post_id) . '" class="link">Edit This Post in WordPress</a></p>
            <a href="insert.php" class="button">Back to Add New Post</a>
        </div>

        </body>
        </html>';
        exit;

    } else {
        echo '
        <!DOCTYPE html>
        <html lang="en">
        <head>
        <meta charset="UTF-8">
        <title>Error Creating Post</title>
        <style>
        body {
            font-family: "Inter", sans-serif;
            background-color: #1e1e1e;
            color: #ff5252;
            text-align: center;
            padding-top: 150px;
            font-size: 28px;
            font-weight: bold;
        }
        </style>
        </head>
        <body>
            <div>Error Creating Post</div>
        </body>
        </html>';
    }
}

/**
 * Default AI Prompt Template
 * 
 * IMPORTANT: Customize this prompt according to your content generation needs
 * Variables available:
 * - $current['title']: The post title
 * - $current['slug']: The post slug/URL
 * - $current['prompt']: User-provided additional context
 * 
 * This prompt will be combined with the user's custom prompt
 */
$default_prompt = '';

// Combine default prompt with user's custom prompt
$final_prompt = $default_prompt . "\n\n" . $current['prompt'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Content Generation - Queue Item</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap');

* {
    box-sizing: border-box;
}

body {
    font-family: 'Inter', sans-serif;
    background-color: #1e1e1e;
    color: #d4d4d4;
    padding: 60px 30px;
    font-size: 20px;
    line-height: 1.9;
    animation: fadeIn 0.7s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.container {
    max-width: 950px;
    margin: auto;
}

.header {
    text-align: center;
    margin-bottom: 50px;
}

.header h1 {
    color: #fff;
    font-size: 36px;
    font-weight: 600;
    margin-bottom: 15px;
}

.vscode-line {
    background: linear-gradient(90deg, #007acc 0%, #00e676 100%);
    height: 4px;
    width: 60%;
    margin: 0 auto 40px;
    border-radius: 10px;
}

.box {
    background: #252526;
    padding: 35px;
    border-radius: 14px;
    box-shadow: 0 0 25px rgba(0,0,0,0.4);
    margin-bottom: 35px;
    border: 1px solid #333;
    transition: background 0.3s ease, transform 0.2s ease;
}

.box:hover {
    background: #2d2d2d;
    transform: translateY(-2px);
}

h2, h3 {
    color: #ffffff;
    margin-bottom: 20px;
    font-weight: 600;
    font-size: 24px;
}

textarea {
    width: 100%;
    padding: 18px;
    border: 1px solid #3c3c3c;
    border-radius: 10px;
    margin-top: 12px;
    background-color: #1e1e1e;
    color: #e0e0e0;
    font-size: 18px;
    resize: vertical;
    transition: border 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
}

textarea:focus {
    outline: none;
    border-color: #007acc;
    background-color: #2a2a2a;
    box-shadow: 0 0 10px rgba(0,122,204,0.3);
}

button {
    background: #007acc;
    color: white;
    border: none;
    padding: 14px 28px;
    border-radius: 10px;
    cursor: pointer;
    font-size: 18px;
    font-weight: 600;
    transition: background 0.2s ease, transform 0.1s ease, box-shadow 0.2s ease;
}

button:hover {
    background: #005f99;
    transform: translateY(-2px);
    box-shadow: 0 0 10px rgba(0,122,204,0.3);
}

a {
    color: #4fc3f7;
    text-decoration: none;
    font-weight: 500;
}

a:hover {
    text-decoration: underline;
}

.success {
    color: #00e676;
    font-weight: bold;
}

.error {
    color: #ff5252;
    font-weight: bold;
}

footer {
    margin-top: 60px;
    text-align: center;
    color: #777;
    font-size: 15px;
}
</style>

<script>
// Copy prompt to clipboard
function copyPrompt() {
    const text = document.getElementById("full_prompt");
    text.select();
    document.execCommand("copy");
    alert("Prompt copied to clipboard!");
}
</script>
</head>
<body>

<div class="container">

    <div class="header">
        <h1>AI Content Generation</h1>
        <div class="vscode-line"></div>
    </div>

    <div class="box">
        <h2>Generation Prompt for: <?= esc_html($current['title']); ?></h2>
        <textarea id="full_prompt" rows="12" readonly><?= esc_html($final_prompt); ?></textarea>
        <br>
        <button onclick="copyPrompt()">Copy Prompt</button>
    </div>

    <div class="box">
        <h3>Paste AI Output Below:</h3>
        <form method="POST">
            <input type="hidden" name="title" value="<?= esc_attr($current['title']); ?>">
            <input type="hidden" name="slug" value="<?= esc_attr($current['slug']); ?>">
            <input type="hidden" name="category" value="<?= esc_attr($current['category']); ?>">
            <textarea name="ai_output" rows="10" placeholder="Paste the AI-generated content here..." required></textarea>
            <br>
            <button type="submit">Create Post (Draft)</button>
        </form>
    </div>

</div>

</body>
</html>
