# Configuration Guide

## Setting Up the Chat AI Chatbot

### Step 1: Install the Plugin

1. Navigate to your WordPress admin panel
2. Go to **Plugins → Add New**
3. Upload the `cac-ai-chatbot` plugin zip file
4. Click **Install Now** and then **Activate**

### Step 2: Access Plugin Settings

1. In WordPress admin, go to **Settings → AI Chatbot Settings**
2. You should see the "Chatbot Widget Settings" page

### Step 3: Configure Basic Settings

#### Enable Widget (Checkbox)

- **Default**: Unchecked (disabled)
- **Purpose**: Turn the chatbot widget on or off
- **Note**: When disabled, the widget won't appear on your site

#### Webhook URL (Text Field)

- **Required**: Yes (for the widget to function)
- **Format**: Full URL (e.g., `https://api.example.com/chat`)
- **Purpose**: The endpoint where user messages are sent
- **Important**: Keep this URL private and secure

### Step 4: Customize Appearance

#### Widget Position (Dropdown)

- **Options**:
  - Left
  - Right (default)
- **Purpose**: Which side of the screen the widget appears on

#### Vertical Offset (Number Field)

- **Default**: 120 (pixels)
- **Range**: 0 or higher
- **Purpose**: Distance from the bottom of the screen
- **Example**:
  - 120px: Widget sits 120 pixels from bottom
  - 0px: Widget sticks to the very bottom

#### Collapsed Avatar (Image Upload)

- **Purpose**: Image shown when widget is closed/collapsed
- **Recommended Size**: Square image (e.g., 100x100px)
- **Supported Formats**: JPG, PNG, GIF, WebP
- **Note**: If not set, a blue gradient will be used

#### Expanded Avatar (Image Upload)

- **Purpose**: Larger image shown when widget is open/expanded
- **Recommended Size**: Square image (e.g., 200x200px)
- **Supported Formats**: JPG, PNG, GIF, WebP
- **Note**: If not set, the collapsed avatar will be used

### Step 5: Save Settings

1. After configuring all options, click **Save Changes**
2. The settings are stored in the WordPress options table
3. Settings are automatically sanitized for security

### Step 6: Test the Widget

1. Visit the frontend of your site
2. You should see the chatbot widget at the configured position and offset
3. Click the "Talk to AI" button to open the chat
4. Send a test message to verify webhook integration

## Webhook Response Formats

The plugin is flexible and accepts multiple response formats from your webhook:

### Format 1: Using 'reply' key

```json
{
  "reply": "Hello! How can I help you?"
}
```

### Format 2: Using 'message' key

```json
{
  "message": "Hello! How can I help you?"
}
```

### Format 3: Using 'response' key

```json
{
  "response": "Hello! How can I help you?"
}
```

### Fallback: Raw JSON

If none of the above keys are found, the entire response is converted to a string.

## CSS Customization

To customize the widget appearance beyond the avatar, add custom CSS to your theme:

### Change button color:

```css
.cac-talk-btn {
  background: #your-color;
}
```

### Adjust message bubbles:

```css
.cac-message {
  font-size: 16px;
}

.cac-bot {
  background: #f0f0f0;
}

.cac-user {
  background: #0066cc;
}
```

### Modify widget spacing:

```css
.cac-widget-root {
  bottom: 30px !important;
  right: 20px !important;
}
```

## Troubleshooting

### Widget doesn't appear

- ✓ Check if "Enable Widget" is checked in settings
- ✓ Verify you're not excluding the page with a filter
- ✓ Check browser console for JavaScript errors

### Messages not sending

- ✓ Verify the Webhook URL is correct
- ✓ Check webhook endpoint is accessible
- ✓ Verify webhook is accepting POST requests
- ✓ Check browser Network tab for failed requests

### Avatar not showing

- ✓ Re-upload the image in settings
- ✓ Verify image URL is accessible
- ✓ Check image format is supported
- ✓ Try without query parameters in URL

### Settings not saving

- ✓ Check WordPress user has admin capability
- ✓ Verify nonce is valid (browser developer tools)
- ✓ Check for PHP errors in debug.log

## API Endpoint Details

**Endpoint**: `wp-json/ai-chatbot/v1/message`

**Method**: POST

**Required Headers**:

```
Content-Type: application/json
X-WP-Nonce: [WordPress REST nonce]
```

**Request Body**:

```json
{
  "message": "User's message"
}
```

**Response** (from webhook):

```json
{
  "reply": "Bot's response"
}
```

## Database Storage

Settings are stored in the WordPress `wp_options` table with the key:

```
cac_ai_chat_settings
```

Stored data structure:

```php
array(
  'enabled' => 1,                    // 1 or 0
  'webhook_url' => 'https://...',    // URL string
  'position' => 'right',             // 'left' or 'right'
  'vertical_offset' => 120,          // Integer (pixels)
  'avatar_collapsed' => 'https://...', // URL or empty
  'avatar_expanded' => 'https://...'   // URL or empty
)
```

## Advanced: Filtering Messages

You can hook into the message handler (developer feature):

```php
// In your theme's functions.php or custom plugin
add_filter('cac_message_before_send', function($message) {
  // Modify message before sending to webhook
  return strtolower($message);
});

add_filter('cac_response_before_display', function($response) {
  // Modify webhook response before displaying
  return $response;
});
```

## Performance Notes

- Widget is loaded only on frontend (not admin)
- CSS and JS are minified
- Uses efficient DOM manipulation
- REST endpoint uses WordPress caching
- No external dependencies beyond WordPress

---

**Last Updated**: December 2025  
**Plugin Version**: 1.0.0
