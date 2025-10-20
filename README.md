# Semi-Auto WordPress Content Generator

A lightweight WordPress queue management system for AI-generated content creation. This tool provides a streamlined workflow for managing AI content generation requests, allowing you to efficiently batch-create WordPress posts using any AI tool.

## Overview

Semi-Auto WordPress Content Generator is a standalone WordPress integration that helps you organize and process AI-generated content. It uses a queue-based system where you add post requests, copy prompts to your preferred AI tool, and paste the generated content back to create WordPress draft posts.

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- MySQL 5.6 or higher
- Apache or Nginx with mod_rewrite enabled
- HTTPS recommended for secure access

## Installation

### Step 1: Download Files

Download or clone this repository:
```bash
git clone https://github.com/yourusername/semi-auto-wordpress-content-generator.git

### Step 2: Upload to WordPress

Copy the three main files to your WordPress installation root directory:

- `insert.php`
- `genrate.php`
- `queue.php`

You can upload them via FTP, cPanel File Manager, or command line:

bash
cp insert.php /path/to/wordpress/
cp genrate.php /path/to/wordpress/
cp queue.php /path/to/wordpress/

### Step 3: Set Permissions

Ensure proper file permissions:

bash
chmod 644 insert.php genrate.php queue.php

### Step 4: Access the System

Navigate to your WordPress site and access the tool:


https://yourdomain.com/insert.php

You'll be prompted to log in if you're not already authenticated.

## How to Use

### Adding Posts to Queue

1. Access `insert.php` or `queue.php` in your browser
2. Log in with your WordPress credentials if prompted
3. Fill out the form with the following information:
   - Select a category from your WordPress categories
   - Enter the post title
   - Enter a URL-friendly slug
   - Write your AI prompt describing the content you want
4. Click "Add to Queue"
5. You'll be automatically redirected to the generation page

### Processing Queue Items

After adding an item to the queue, you'll see the generation page (`genrate.php`) which displays:

1. The combined prompt (default system prompt plus your custom prompt)
2. A "Copy Prompt" button to copy the prompt to your clipboard
3. Instructions to use your AI tool

Follow these steps:

1. Click "Copy Prompt" to copy the prompt
2. Open your preferred AI tool (Claude, ChatGPT, etc.)
3. Paste the prompt and generate the content
4. Copy the AI-generated content
5. Return to `genrate.php`
6. Paste the content into the "AI Generated Content" textarea
7. Click "Generate Post"

### Post Creation

When you click "Generate Post":

- A new WordPress draft post is created with your specified title, slug, and category
- The AI-generated content is saved as the post content
- The processed item is removed from the queue
- You'll see a success message with a link to edit the post in WordPress admin

### Continuing with Queue

After processing one item:

- If more items remain in the queue, you can continue processing them
- If the queue is empty, you'll see a message with a link to add more posts
- Return to `insert.php` to add new items at any time

## File Structure

The system consists of three main PHP files:

### insert.php

Primary form for adding new posts to the queue. This file:

- Checks WordPress authentication
- Displays a form for category, title, slug, and prompt
- Saves data to WordPress options table
- Redirects to `genrate.php` after submission

### queue.php

Alternative form for adding posts (identical functionality to `insert.php`). This file redirects to `generate.php` instead of `genrate.php`.

### genrate.php

Queue processor and post creator. This file:

- Retrieves the first item from the queue
- Displays the combined prompt for copying
- Provides a form to paste AI-generated content
- Creates WordPress draft posts
- Removes processed items from queue
- Shows success message with edit link

## Configuration

### Changing Default Prompt

To customize the default system prompt that gets prepended to user prompts, edit `genrate.php`:

php
// Find this line:
$default_prompt = "";

// Change to your preferred default:
$default_prompt = "You are a professional content writer. Write in a clear, engaging style with proper headings. Include relevant examples.\n\n";

### Modifying Post Status

By default, posts are created as drafts. To change this, edit `genrate.php`:

php
// Find:
'post_status' => 'draft',

// Change to:
'post_status' => 'publish',  // Auto-publish posts
// or
'post_status' => 'pending',  // Mark as pending review

### Customizing Appearance

All three files include embedded CSS for styling. To customize colors or fonts:

- Search for color values like `#1e1e1e`, `#4fc3f7`, `#007acc`
- Replace with your preferred colors
- Change `font-family` from 'Inter' to your preferred font
- Modify padding, margins, and border-radius values as needed

## Data Storage

The queue is stored in WordPress options table under the key `ai_post_queue`. Each queue item contains:

php
[
'id'       => timestamp,         // Unique identifier
'category' => category_id,       // WordPress category ID
'title'    => 'Post Title',      // Post title
'slug'     => 'post-slug',       // URL slug
'prompt'   => 'AI prompt text'   // Content generation prompt
]

## Security

### Built-in Security Features

- WordPress authentication required for all pages
- Data sanitization using WordPress sanitize functions
  - `sanitize_text_field` for text inputs
  - `sanitize_title` for slugs
  - `sanitize_textarea_field` for prompts
- Safe redirects using `wp_redirect`
- No direct database queries

### Security Recommendations

- Always use HTTPS for accessing these files
- Keep WordPress core and PHP updated
- Use strong admin passwords
- Limit file permissions to 644
- Consider adding WordPress nonces for form submissions
- Add capability checks to limit access to editors and admins

### Adding Nonce Protection

To enhance security, add nonce verification:

In the form (`insert.php` or `queue.php`):

php
<?php wp_nonce_field('ai_queue_action', 'ai_queue_nonce'); ?>

In form processing:

php
if (!wp_verify_nonce($_POST['ai_queue_nonce'], 'ai_queue_action')) {
die('Security check failed');
}

## Troubleshooting

### WordPress Functions Not Found

If you see errors about undefined WordPress functions:

- Verify the `wp-load.php` path is correct
- For subdirectory installations, adjust the path
- Use absolute path if relative path doesn't work

php
// Try these alternatives:
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/wordpress/wp-load.php');
require_once('/var/www/html/wp-load.php');

### Queue Not Saving

If items aren't being saved to the queue:

- Check database write permissions
- Verify WordPress options table exists
- Test option writing capability

php
// Add this to debug:
if (!update_option('test_option', 'test_value')) {
die('Cannot write to WordPress options');
}

### Redirect Issues

If you experience redirect loops:

- Check that file names match exactly (`genrate.php` vs `generate.php`)
- Ensure consistent naming in `wp_redirect` calls
- Verify `.htaccess` rules aren't interfering

### Categories Not Displaying

If the category dropdown is empty:

- Create categories in WordPress admin first
- Check `get_categories` function is working
- Verify WordPress is properly loaded

### Styling Issues

If the interface doesn't display correctly:

- Clear browser cache
- Check browser console for CSS errors
- Verify HTML structure is valid
- Test in different browsers

## API Reference

### WordPress Functions Used

**Data Storage:**

php
get_option('ai_post_queue', [])           // Retrieve queue
update_option('ai_post_queue', $data)     // Save queue

**Post Creation:**

php
wp_insert_post([
'post_title'    => 'Title',
'post_content'  => 'Content',
'post_name'     => 'slug',
'post_status'   => 'draft',
'post_category' => [5]
])

**Sanitization:**

php
sanitize_text_field($text)      // Clean text input
sanitize_title($slug)           // Clean URL slug
sanitize_textarea_field($text)  // Clean textarea input

**Authentication:**

php
is_user_logged_in()            // Check if user is logged in
wp_login_url()                 // Get login page URL
wp_redirect($url)              // Safe redirect

**Categories:**

php
get_categories(['hide_empty' => false])  // Get all categories

### Custom Helper Functions

**Get Queue Count:**

php
function ai_queue_count() {
$queue = get_option('ai_post_queue', []);
return count($queue);
}

**Clear Queue:**

php
function ai_clear_queue() {
update_option('ai_post_queue', []);
}

**Get Specific Item:**

php
function ai_get_queue_item($id) {
$queue = get_option('ai_post_queue', []);
foreach ($queue as $item) {
if ($item['id'] == $id) {
return $item;
}
}
return null;
}

## Workflow Diagram


User Access
↓
insert.php / queue.php (Login Required)
↓
Fill Form (Category, Title, Slug, Prompt)
↓
Submit → Save to Queue
↓
Redirect to genrate.php
↓
Display First Queue Item
↓
Copy Prompt → Use AI Tool
↓
Paste AI Output
↓
Create WordPress Draft Post
↓
Remove Item from Queue
↓
Success Page with Edit Link
↓
Process Next Item or Add More

## Future Enhancements

Planned features for future versions:

- [ ] WordPress plugin version for easier installation
- [ ] Direct API integration with Claude and OpenAI
- [ ] Bulk operations for queue management
- [ ] Queue item editing capabilities
- [ ] Custom post type support
- [ ] Import and export queue functionality
- [ ] Scheduled publishing options
- [ ] Email notifications on completion
- [ ] REST API endpoints
- [ ] Visual dashboard for queue management
- [ ] Multi-language interface support
- [ ] Template system for common prompts
- [ ] Analytics and usage reports

## Contributing

Contributions are welcome! Here's how you can help:

- Report bugs by opening an issue
- Suggest new features in discussions
- Submit pull requests with improvements
- Improve documentation
- Share your use cases and workflows

### Development Guidelines

- Follow WordPress coding standards
- Add comments for complex logic
- Test on multiple WordPress versions
- Sanitize all user inputs
- Use WordPress core functions when available

## License

This project is licensed under the MIT License.

Permission is hereby granted, free of charge, to any person obtaining a copy of this software to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the software.

The software is provided "as is" without warranty of any kind.

## Support

For help and support:

- Read this documentation thoroughly
- Check the troubleshooting section
- Open an issue on GitHub for bugs
- Start a discussion for questions
- Review existing issues before creating new ones

## Credits

Built with WordPress core functions and modern web technologies. Designed for content creators who want to streamline their AI-assisted writing workflow.

## Changelog

### Version 1.0.0 - Initial Release

- Queue management system
- Three main interface files
- Dark mode VSCode-inspired UI
- WordPress integration
- Category and metadata support
- Clipboard copy functionality
- Draft post creation
- Sequential queue processing

---

**Elshan Gozali**
