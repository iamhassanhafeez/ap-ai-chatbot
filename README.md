# Chat AI Chatbot (CAC) WordPress Plugin

A modern, floating AI chatbot widget for WordPress that proxies chat messages to a configured webhook.

## Features

✨ **Key Features:**

- Floating chatbot widget that appears at the bottom of the page
- Enable/disable toggle from WordPress dashboard settings
- Customizable position (left or right)
- Adjustable vertical offset from bottom
- Custom avatar support (collapsed and expanded states)
- Webhook integration for AI responses
- Modern, responsive design
- Mobile-optimized UI
- Accessibility features (ARIA labels, keyboard support)

## Installation

1. Upload the `cac-ai-chatbot` folder to `/wp-content/plugins/`
2. Activate the plugin through the WordPress admin dashboard
3. Configure settings in **Settings → AI Chatbot Settings**

## Configuration

Navigate to **Settings → AI Chatbot Settings** to configure:

### Basic Settings

- **Enable Widget**: Toggle the chatbot widget on/off
- **Webhook URL**: Enter your AI webhook endpoint (required to process messages)

### Appearance Settings

- **Widget Position**: Choose between left or right side (default: right)
- **Vertical Offset**: Distance from bottom in pixels (default: 120px)
- **Collapsed Avatar**: Image shown when widget is collapsed
- **Expanded Avatar**: Image shown when widget is expanded

## Plugin Structure

```
cac-ai-chatbot/
├── chat-ai-chatbot.php       # Main plugin file
├── README.md                  # This file
├── assets/
│   ├── css/
│   │   ├── frontend.css      # Frontend widget styles
│   │   └── admin.css         # Admin panel styles
│   └── js/
│       ├── frontend.js       # Frontend widget functionality
│       └── admin.js          # Admin media upload handler
└── languages/                # Translation files (for future use)
```

## How It Works

### Frontend Behavior

1. **Widget Positioning**: The chatbot widget is positioned at the bottom of the page (left or right)
2. **Collapsed State**: Shows avatar and "Talk to AI" button
3. **Expanded State**: Opens a chat interface above the button
4. **Message Handling**: User messages are sent to the webhook endpoint via REST API
5. **Responses**: Webhook responses are displayed in the chat

### Webhook Integration

The plugin sends POST requests to your configured webhook with:

```json
{
  "message": "User's message text"
}
```

The webhook should return a response in one of these formats:

```json
{
  "reply": "Bot response text"
}
```

or

```json
{
  "message": "Bot response text"
}
```

or

```json
{
  "response": "Bot response text"
}
```

## Styling & Customization

The widget uses minimal, modern CSS that can be easily customized via WordPress theme CSS or plugins like Custom CSS.

### CSS Classes Reference

- `.cac-widget-root` - Main widget container
- `.cac-collapsed` - Collapsed button area
- `.cac-expanded` - Expanded chat panel
- `.cac-avatar` - Avatar image circle
- `.cac-message` - Message bubble
- `.cac-user` - User message (black background)
- `.cac-bot` - Bot message (light gray background)
- `.cac-input-field` - Message input field
- `.cac-send` - Send button

## REST API Endpoint

**Endpoint**: `/wp-json/ai-chatbot/v1/message`  
**Method**: POST  
**Authentication**: WordPress nonce required

## Version History

- **1.0.0** - Initial release
  - Floating chatbot widget
  - Enable/disable toggle
  - Webhook integration
  - Avatar customization
  - Position and offset settings

## Security

- All URLs are properly escaped using `esc_url_raw()`
- Settings are sanitized before storage
- REST endpoints use WordPress nonce verification
- Webhook URLs must be properly validated before use

## Browser Support

- Chrome/Chromium (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## License

This plugin follows WordPress plugin standards.

## Support

For issues and feature requests, please refer to the GitHub repository:
https://github.com/iamhassanhafeez/ap-ai-chatbot

---

**Author**: Hassan "Cheeta" Hafeez  
**Author URL**: https://iamhassanhafeez.github.io/portfolio/
