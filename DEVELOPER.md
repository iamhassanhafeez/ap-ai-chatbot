# Developer Guide

## Architecture Overview

The Chat AI Chatbot plugin follows WordPress plugin best practices with a modular architecture:

```
Frontend (User Interaction)
        ↓
JavaScript (frontend.js)
        ↓
REST API Endpoint
        ↓
Webhook (External AI Service)
        ↓
Response → Display in Chat
```

## Class Structure

### Main Class: `CAC_AI_Chatbot`

The plugin is built around a single main class that handles:

#### Initialization

- `__construct()` - Hooks all actions
- `define_constants()` - Sets CAC_PLUGIN_DIR and CAC_PLUGIN_URL
- `init()` - Loads text domain for translations

#### Admin Panel

- `admin_menu()` - Registers the settings page
- `register_settings()` - Registers all settings and fields
- `section_callback()` - Renders section description
- `field_*()` - Individual field rendering methods
- `sanitize_settings()` - Validates and sanitizes user input
- `settings_page()` - Renders the main settings page
- `enqueue_admin_assets()` - Loads admin CSS/JS

#### Frontend

- `enqueue_frontend_assets()` - Loads widget CSS/JS with settings
- REST API integration via `register_rest_routes()`

#### REST API

- `register_rest_routes()` - Registers the message endpoint
- `rest_message_handler()` - Processes incoming messages

## Configuration Constants

```php
define( 'CAC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CAC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
```

## Settings Structure

All settings are stored in a single option:

```php
$settings = get_option( 'cac_ai_chat_settings' );
// Returns:
array(
  'enabled' => 1,
  'webhook_url' => 'https://...',
  'position' => 'right',
  'vertical_offset' => 120,
  'avatar_collapsed' => '',
  'avatar_expanded' => ''
)
```

## Extending the Plugin

### Adding New Settings

1. Add field in `register_settings()`:

```php
add_settings_field(
    'my_new_field',
    __( 'My Field', 'cac-ai-chat' ),
    array( $this, 'field_my_new_field' ),
    'cac-ai-chat-settings',
    'cac_main_section'
);
```

2. Create field callback:

```php
public function field_my_new_field() {
    $options = get_option( self::OPTION_KEY, array() );
    $value = $options['my_new_field'] ?? '';
    printf(
        '<input type="text" name="%s[my_new_field]" value="%s" />',
        esc_attr( self::OPTION_KEY ),
        esc_attr( $value )
    );
}
```

3. Sanitize in `sanitize_settings()`:

```php
$san['my_new_field'] = sanitize_text_field( $input['my_new_field'] ?? '' );
```

### Adding New Assets

To add new CSS/JS files:

```php
// In enqueue_frontend_assets()
wp_enqueue_script(
    'cac-my-script',
    CAC_PLUGIN_URL . 'assets/js/my-script.js',
    array( 'cac-frontend-js' ),
    '1.0.0',
    true
);
```

### Modifying Webhook Response Handling

Edit `rest_message_handler()` to customize webhook response processing:

```php
public function rest_message_handler( WP_REST_Request $request ) {
    // ... existing code ...

    $response = wp_remote_post( $webhook, $args );
    $body = wp_remote_retrieve_body( $response );

    // Your custom response processing here

    return new WP_REST_Response( $processed_response, $code );
}
```

### Adding Hooks for Developers

Add custom hooks to allow other plugins to extend functionality:

```php
// Before sending message to webhook
do_action( 'cac_before_webhook_send', $message );

// After receiving response
apply_filters( 'cac_webhook_response', $response );

// Before localizing script
apply_filters( 'cac_localize_settings', $localized );
```

## Frontend JavaScript API

The frontend script exposes settings via `window.CAC_CHAT`:

```javascript
window.CAC_CHAT = {
  rest_url: "/wp-json/ai-chatbot/v1/message",
  nonce: "wordpress_nonce_value",
  settings: {
    position: "right",
    vertical_offset: 120,
    avatar_collapsed: "",
    avatar_expanded: "",
  },
};
```

## REST API Reference

### Endpoint

```
POST /wp-json/ai-chatbot/v1/message
```

### Request

```javascript
fetch("/wp-json/ai-chatbot/v1/message", {
  method: "POST",
  headers: {
    "Content-Type": "application/json",
    "X-WP-Nonce": window.CAC_CHAT.nonce,
  },
  body: JSON.stringify({ message: "Hello" }),
});
```

### Response

The endpoint forwards the webhook response directly:

```json
{
  "reply": "Bot response"
}
```

## CSS Architecture

### BEM Naming Convention

Classes follow BEM (Block Element Modifier):

- `.cac-widget-root` - Block
- `.cac-widget-root__expanded` - Element
- `.cac-widget-root--open` - Modifier

### Mobile Responsive

Breakpoints:

- Default: Desktop
- `@media (max-width: 480px)` - Mobile

## PHP Standards

The plugin follows WordPress Coding Standards:

### Function Documentation

```php
/**
 * Description of what the function does
 *
 * @param type $param Description
 * @return type Description
 */
public function my_function( $param ) {
    // Implementation
}
```

### Escaping for Security

- `esc_html()` - Display text
- `esc_attr()` - HTML attributes
- `esc_url_raw()` - URLs (storage)
- `esc_js()` - JavaScript strings

### Data Validation

```php
// Always sanitize inputs
$value = sanitize_text_field( $_POST['field'] );

// Always validate options
$value = intval( $options['number'] ?? 0 );
```

## Testing the Plugin

### Manual Testing Checklist

- [ ] Widget appears on frontend
- [ ] Enable/disable toggle works
- [ ] Position setting changes widget location
- [ ] Vertical offset adjusts spacing
- [ ] Avatar uploads work
- [ ] Messages send to webhook
- [ ] Responses display correctly
- [ ] Mobile responsive
- [ ] Works with different themes

### Browser DevTools Testing

```javascript
// Check if settings loaded
console.log(window.CAC_CHAT);

// Check widget element
document.querySelector(".cac-widget-root");

// Test message sending
fetch(window.CAC_CHAT.rest_url, {
  method: "POST",
  headers: {
    "Content-Type": "application/json",
    "X-WP-Nonce": window.CAC_CHAT.nonce,
  },
  body: JSON.stringify({ message: "test" }),
});
```

## Performance Optimization

### Current Optimizations

- Conditional script loading (only if enabled)
- CSS/JS enqueued properly
- Minimal DOM manipulation
- Event delegation for dynamically created elements

### Future Optimizations

- Lazy load widget on first interaction
- Cache webhook responses
- Implement message history in localStorage
- Use Web Workers for message processing

## Security Considerations

### Implemented

- Nonce verification on REST endpoint
- Input sanitization on all settings
- URL validation for webhook
- Output escaping in all templates

### Best Practices

- Never trust user input
- Always use WordPress sanitization functions
- Keep webhook URL private
- Validate webhook responses
- Implement rate limiting on webhook

## Debugging

### Enable Debug Logging

```php
// In wp-config.php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
```

### Check Plugin Logs

```
/wp-content/debug.log
```

### Browser Console

```javascript
// Monitor REST requests
fetch(url, options).then((r) => {
  console.log("Response:", r);
  return r;
});
```

## Contributing

### Code Style

- Use tabs for indentation (WordPress standard)
- Follow WordPress Coding Standards
- Comment complex logic
- Use meaningful variable names

### Testing Before Submit

```bash
# PHP Linting
php -l chat-ai-chatbot.php

# Check WordPress standards
phpcs --standard=WordPress chat-ai-chatbot.php
```

## Version History

### 1.0.0 (Current)

- Initial release
- Basic chatbot widget
- Enable/disable functionality
- Avatar customization
- Position and offset settings
- Webhook integration

### Planned Features

- Message history
- Multiple chat personas
- Analytics dashboard
- Advanced styling options
- Multi-language support

---

For detailed API documentation, see the main README.md file.
