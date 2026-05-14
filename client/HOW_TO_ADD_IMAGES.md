# How to Add Custom Images and Favicons

This guide explains how to add your own images and favicons to the Qelemeda Payroll System.

## 📁 Where to Place Images

All public images should be placed in the `client/public/` folder. This makes them accessible via the root URL path.

### Current Image Structure:
```
client/public/
├── favicon.ico          # Browser tab icon
├── login.png           # Login page reference image
├── logo.jpg            # Original logo
└── logo2.jpg           # Current logo used in login page
```

---

## 🖼️ Adding the Hero Illustration Image

### Step 1: Prepare Your Image
- **Recommended size**: 600px × 450px (or similar aspect ratio)
- **Format**: PNG, JPG, or SVG
- **File name**: `hero-illustration.png` (or any name you prefer)

### Step 2: Add to Public Folder
1. Copy your illustration image to `client/public/`
2. Example: `client/public/hero-illustration.png`

### Step 3: Update Home.vue
Open `client/src/views/Home.vue` and replace the placeholder section:

**Find this code (around line 30-40):**
```vue
<div class="illustration-placeholder">
  <div class="illustration-content">
    <i class="fas fa-chart-line illustration-icon"></i>
    <p class="illustration-text">Add your illustration image here</p>
    <p class="illustration-hint">Place image in /public/hero-illustration.png</p>
  </div>
</div>
```

**Replace with:**
```vue
<img src="/hero-illustration.png" alt="Payroll System Illustration" class="hero-image" />
```

**Add this CSS style (in the `<style scoped>` section):**
```css
.hero-image {
  width: 100%;
  max-width: 600px;
  height: auto;
  object-fit: contain;
  animation: float 3s ease-in-out infinite;
}

@keyframes float {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-20px);
  }
}
```

---

## 🎨 Changing the Favicon

### What is a Favicon?
A favicon is the small icon that appears in the browser tab next to your page title.

### Step 1: Prepare Your Favicon
- **Recommended size**: 32×32px or 64×64px
- **Format**: `.ico`, `.png`, or `.svg`
- **File name**: `favicon.ico`

### Step 2: Replace the Favicon
1. Delete or backup the existing `client/public/favicon.ico`
2. Copy your new favicon to `client/public/favicon.ico`

### Step 3: Update index.html (if needed)
Open `client/index.html` and ensure this line exists in the `<head>` section:
```html
<link rel="icon" type="image/x-icon" href="/favicon.ico">
```

### Alternative: Using PNG Favicon
If you prefer PNG format:
1. Save your favicon as `client/public/favicon.png`
2. Update `client/index.html`:
```html
<link rel="icon" type="image/png" href="/favicon.png">
```

---

## 🔄 Changing the Logo

### Current Logo Files:
- `logo.jpg` - Original logo
- `logo2.jpg` - Currently used in login page

### To Replace the Login Page Logo:
1. Prepare your logo image (recommended: square format, 400×400px or larger)
2. Save it as `client/public/logo2.jpg` (or use a different name)
3. If using a different name, update `client/src/views/Login.vue`:

**Find this line (around line 94):**
```vue
<img src="/logo2.jpg" alt="Qelem Meda Technologies Logo" class="company-logo" loading="eager" />
```

**Change to your filename:**
```vue
<img src="/your-logo-name.jpg" alt="Qelem Meda Technologies Logo" class="company-logo" loading="eager" />
```

---

## 📋 Quick Reference

### Image Paths in Vue Components:
- ✅ **Correct**: `<img src="/image.png" />` (starts with `/`)
- ❌ **Wrong**: `<img src="image.png" />` (no leading slash)

### Supported Image Formats:
- `.jpg` / `.jpeg` - Best for photos
- `.png` - Best for logos with transparency
- `.svg` - Best for scalable graphics
- `.webp` - Modern format, smaller file size

### After Adding Images:
1. Save all files
2. The Vite dev server will automatically reload
3. If images don't appear, try:
   - Hard refresh: `Ctrl + Shift + R` (Windows) or `Cmd + Shift + R` (Mac)
   - Clear browser cache
   - Restart the dev server

---

## 🎯 Example: Complete Hero Illustration Setup

### 1. Add your image:
```
client/public/payroll-hero.png
```

### 2. Update Home.vue template:
```vue
<div class="hero-illustration">
  <img src="/payroll-hero.png" alt="Payroll System" class="hero-image" />
</div>
```

### 3. Add CSS styling:
```css
.hero-image {
  width: 100%;
  max-width: 600px;
  height: auto;
  object-fit: contain;
  filter: drop-shadow(0 10px 40px rgba(0, 0, 0, 0.1));
}
```

---

## 🆘 Troubleshooting

### Images Not Showing?
1. Check file path is correct (case-sensitive!)
2. Ensure file is in `client/public/` folder
3. Verify file extension matches (`.jpg` vs `.jpeg`)
4. Clear browser cache and hard refresh
5. Check browser console for 404 errors

### Favicon Not Updating?
1. Clear browser cache completely
2. Close and reopen browser
3. Try incognito/private mode
4. Check `index.html` has correct favicon link

### Image Quality Issues?
1. Use higher resolution source images
2. Optimize images before adding (use tools like TinyPNG)
3. For logos, consider using SVG format

---

## 📞 Need Help?

If you encounter issues:
1. Check the browser console for errors (F12)
2. Verify file paths and names
3. Ensure dev server is running
4. Try restarting the development server

---

**Happy customizing! 🎨**
