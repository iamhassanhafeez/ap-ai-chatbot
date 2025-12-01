# Quick Start Guide

## 🚀 Get Started in 3 Steps

### Step 1: Install

Upload the `cac-ai-chatbot` folder to WordPress `/wp-content/plugins/` and activate.

### Step 2: Configure

Go to **Settings → AI Chatbot Settings** in WordPress admin.

### Step 3: Add Webhook

Enter your AI webhook URL and check "Enable Widget" - that's it!

---

## 📍 Widget Position

The chatbot widget appears at the **bottom** of your page:

- **Bottom Right** (default)
- **Bottom Left** (if selected)
- **Custom Offset** (adjust vertical distance from bottom)

---

## ⚙️ Dashboard Settings

| Setting          | Purpose                   | Default          |
| ---------------- | ------------------------- | ---------------- |
| Enable Widget    | Turn chatbot on/off       | OFF              |
| Webhook URL      | AI service endpoint       | Required         |
| Position         | Left or Right side        | Right            |
| Vertical Offset  | Distance from bottom (px) | 120              |
| Collapsed Avatar | Image when closed         | Blue gradient    |
| Expanded Avatar  | Image when open           | Collapsed avatar |

---

## 🎨 Customization

### Change Widget Colors

Add to your theme's CSS:

```css
.cac-talk-btn {
  background: #your-color;
}
.cac-user {
  background: #0066cc;
}
.cac-bot {
  background: #f0f0f0;
}
```

### Adjust Spacing

```css
.cac-widget-root {
  bottom: 30px !important;
  right: 20px !important;
}
```

---

## 🔗 Webhook Format

Send your webhook URL. It should return:

```json
{ "reply": "Hello! How can I help?" }
```

Or use any of these formats:

- `{ "message": "..." }`
- `{ "response": "..." }`

---

## ❓ Common Issues

**Widget not showing?**

- Check "Enable Widget" is ON in settings
- Verify Webhook URL is entered

**Messages not sending?**

- Check webhook URL is correct and accessible
- Open browser DevTools → Network tab to debug

**Avatar not showing?**

- Re-upload the image
- Check image is publicly accessible

---

## 📚 Documentation

- **README.md** - Full feature overview
- **CONFIGURATION.md** - Detailed setup guide
- **DEVELOPER.md** - For developers/extensions
- **CHANGELOG.md** - Version history

---

## 🆘 Support

For help:

1. Check CONFIGURATION.md troubleshooting section
2. Review browser console (F12) for errors
3. Check WordPress debug.log file

---

**Version**: 1.0.0  
**Last Updated**: December 2025
