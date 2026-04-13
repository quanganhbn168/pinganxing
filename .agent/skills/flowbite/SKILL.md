---
description: Thư viện giao diện Component tĩnh Flowbite tương thích Tailwind CSS
---
# Flowbite UI Library

## Khi nào dùng
Dùng Flowbite khi phải làm nhanh các thành phần Web chuẩn mực như Mobile Menu, Accordion, Dropdown, Modal, Carousel mà KHÔNG MUỐN code lại Javascript.

## Cách thức Tích hợp
1. **Cài đặt**: `npm install flowbite`
2. **Tích hợp Plugin Tailwind**: 
   ```js
   // vite.config.js (or tailwind.config)
   plugins: [require('flowbite/plugin')]
   ```
3. **Kích hoạt JS**: Trong file Layout chính (`master.blade.php`), thêm link:
   ```html
   <script src="https://cdn.jsdelivr.net/npm/flowbite@latest/dist/flowbite.min.js"></script>
   ```

## Quy tắc Sử dụng Component Flowbite
- Lưu ý các thuộc tính `data-` HTML (VD: `data-dropdown-toggle="dropdownAvatar"`, `data-modal-target="defaultModal"`). Bắt buộc phải giữ lại các thẻ này khi copy HTML vào file Blade.
- Component Flowbite thuần HTML sẽ không có xung đột với hệ thống Livewire của backend, nên rất an toàn để xài cho phần Frontend.
