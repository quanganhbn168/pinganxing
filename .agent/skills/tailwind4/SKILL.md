---
description: Hướng dẫn tiêu chuẩn và các điểm mới khi code Tailwind CSS v4
---
# Tailwind CSS v4 Reference

## Khi nào dùng skill này
Dùng khi viết các component HTML/Blade cho Frontend, thiết lập layout, cấu hình theme cho dự án mà không dùng Filament component.

## 1. Cấu hình CSS-First
Trong v4, `tailwind.config.js` thường được giản lược hoặc bỏ hẳn. Cấu hình biến màu, font trực tiếp từ `app.css`:

```css
@import "tailwindcss";

@theme {
  --color-primary-50: #eff6ff;
  --color-primary-600: #2563eb;
  --font-sans: 'Inter', ui-sans-serif, system-ui, sans-serif;
}
```

## 2. Quy tắc Viết Class CNETPos
- Đừng lạm dụng mã hardcode HEX (`text-[#ff0000]`). Luôn sử dụng biến `--color-primary-*` để Dark Mode tự tương thích hoặc dễ đổi màu thương hiệu.
- Thẻ Container chuẩn: Dùng class `container mx-auto px-4 lg:px-8`.
- Responsive luôn làm dạng Mobile First: Xây giao diện cho Mobile bằng class gốc, sau đó dùng `sm:`, `md:`, `lg:` cho tablet và màn hình lớn.

## 3. SEO Tagging in HTML
- Các đoạn văn dài dùng class từ `@tailwindcss/typography`: `<article class="prose lg:prose-xl">`.
- Để ẩn text nhưng vẫn để ScreenReader/Bot SEO đọc được: dùng `sr-only`.
