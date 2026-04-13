# Filament 5 Blade Components Reference

Tài liệu tham chiếu các Blade component có sẵn trong Filament 5. Prefix: `x-filament::`.

> [!IMPORTANT]
> Custom Blade views trong Filament **KHÔNG** compile Tailwind utilities mới. Dùng:
> - **Inline CSS** cho layout (flex, grid, gap)
> - **Filament CSS classes** (`fi-*`) đã pre-compiled
> - **CSS variables** (`var(--gray-500)`, `var(--primary-600)`) cho theme colors

---

## Section

Container chính, bo góc, có shadow. Dùng thay cho `<div>` wrapper.

```blade
<x-filament::section
    heading="Tiêu đề"
    description="Mô tả phụ"
    icon="heroicon-o-user"
    icon-color="primary"
    :collapsible="true"
    :collapsed="false"
    :compact="false"
    :aside="false"
    :divided="false"
    :contained="true"
>
    {{-- Content --}}
</x-filament::section>
```

| Prop | Type | Default | Mô tả |
|------|------|---------|-------|
| `heading` | string | null | Tiêu đề section |
| `description` | string | null | Mô tả dưới heading |
| `icon` | string | null | Heroicon name |
| `icon-color` | string | `gray` | primary, success, warning, danger, info, gray |
| `collapsible` | bool | false | Cho phép thu gọn |
| `collapsed` | bool | false | Mặc định thu gọn |
| `compact` | bool | false | Giảm padding |
| `aside` | bool | false | Layout aside (heading bên trái, content bên phải) |
| `divided` | bool | false | Đường kẻ giữa header và content |
| `contained` | bool | true | false = không viền, không shadow |
| `footer` | slot | null | Footer slot |

### Section không heading (card đơn giản)
```blade
<x-filament::section>
    Nội dung card đơn giản, có bo góc + shadow
</x-filament::section>
```

### Section aside layout
```blade
<x-filament::section aside heading="Thông tin" description="Chi tiết nhân viên">
    {{-- Form fields bên phải --}}
</x-filament::section>
```

---

## Card

Alias cho Section (không heading). Viết ngắn hơn:

```blade
<x-filament::card>
    Nội dung
</x-filament::card>
```

---

## Badge

Nhãn màu, dùng cho status, tags.

```blade
<x-filament::badge color="success" size="sm" icon="heroicon-o-check">
    Hoàn thành
</x-filament::badge>
```

| Prop | Type | Default | Mô tả |
|------|------|---------|-------|
| `color` | string | `primary` | primary, success, warning, danger, info, gray |
| `size` | string | `md` | xs, sm, md, lg |
| `icon` | string | null | Icon trước label |
| `icon-position` | string | `before` | before, after |
| `href` | string | null | Biến thành link |
| `tag` | string | `span` | span, a, button |
| `tooltip` | string | null | Tooltip hover |
| `disabled` | bool | false | Vô hiệu hóa |

---

## Tabs

Tab navigation.

```blade
<div x-data="{ tab: 'tab1' }">
    <x-filament::tabs :contained="true">
        <x-filament::tabs.item
            :alpine-active="'tab === \'tab1\''"
            x-on:click="tab = 'tab1'"
            icon="heroicon-o-home"
            :badge="5"
            badge-color="primary"
        >
            Tab 1
        </x-filament::tabs.item>

        <x-filament::tabs.item
            :alpine-active="'tab === \'tab2\''"
            x-on:click="tab = 'tab2'"
        >
            Tab 2
        </x-filament::tabs.item>
    </x-filament::tabs>

    <div x-show="tab === 'tab1'">Content 1</div>
    <div x-show="tab === 'tab2'" x-cloak>Content 2</div>
</div>
```

### Tabs props
| Prop | Type | Default | Mô tả |
|------|------|---------|-------|
| `contained` | bool | false | true = có viền container |
| `label` | string | null | aria-label |
| `vertical` | bool | false | Tab dọc |

### Tabs.item props
| Prop | Type | Default | Mô tả |
|------|------|---------|-------|
| `active` | bool | false | Active state (PHP) |
| `alpine-active` | string | null | Active state (Alpine.js expression) |
| `badge` | mixed | null | Badge count/text |
| `badge-color` | string | null | Badge color |
| `badge-tooltip` | string | null | Badge tooltip |
| `icon` | string | null | Heroicon |
| `icon-color` | string | `gray` | Icon color |
| `href` | string | null | Link thay vì button |

---

## Empty State

Placeholder khi không có dữ liệu.

```blade
<x-filament::empty-state
    heading="Chưa có dữ liệu"
    description="Hãy tạo mục đầu tiên"
    icon="heroicon-o-document"
    icon-color="primary"
>
    <x-slot name="footer">
        <x-filament::button href="/create">
            Tạo mới
        </x-filament::button>
    </x-slot>
</x-filament::empty-state>
```

| Prop | Type | Default | Mô tả |
|------|------|---------|-------|
| `heading` | string | required | Tiêu đề |
| `description` | string | null | Mô tả phụ |
| `icon` | string | null | Heroicon |
| `icon-color` | string | `primary` | primary, success, warning, danger, gray |
| `compact` | bool | false | Nhỏ gọn hơn |

---

## Callout

Alert/notification box.

```blade
<x-filament::callout
    color="warning"
    icon="heroicon-o-exclamation-triangle"
    heading="Cảnh báo"
    description="Nhân viên này đã nghỉ việc"
/>
```

| Prop | Type | Default | Mô tả |
|------|------|---------|-------|
| `color` | string | `gray` | primary, success, warning, danger, info, gray |
| `icon` | string | null | Heroicon |
| `heading` | string | null | Tiêu đề |
| `description` | string | null | Mô tả |

---

## Avatar

Hình đại diện.

```blade
<x-filament::avatar
    src="{{ $url }}"
    alt="User"
    size="lg"
    :circular="true"
/>
```

| Prop | Type | Default | Mô tả |
|------|------|---------|-------|
| `size` | string | `md` | sm, md, lg |
| `circular` | bool | true | Tròn hay vuông bo góc |

---

## Button

Nút bấm.

```blade
<x-filament::button
    color="primary"
    size="md"
    icon="heroicon-o-plus"
    :outlined="false"
    href="/create"
    tag="a"
    tooltip="Tạo mới"
>
    Tạo mới
</x-filament::button>
```

| Prop | Type | Default | Mô tả |
|------|------|---------|-------|
| `color` | string | `primary` | primary, success, warning, danger, gray |
| `size` | string | `md` | xs, sm, md, lg |
| `icon` | string | null | Heroicon |
| `icon-position` | string | `before` | before, after |
| `outlined` | bool | false | Kiểu viền, không fill |
| `disabled` | bool | false | Vô hiệu hóa |
| `href` | string | null | Biến thành link |
| `tag` | string | `button` | button, a |
| `tooltip` | string | null | Tooltip hover |

---

## Link

Text link với icon.

```blade
<x-filament::link
    href="/employees"
    color="primary"
    icon="heroicon-o-arrow-left"
    size="sm"
>
    Quay lại
</x-filament::link>
```

| Prop | Type | Default | Mô tả |
|------|------|---------|-------|
| `color` | string | `primary` | primary, success, warning, danger, gray |
| `size` | string | `md` | xs, sm, md, lg |
| `icon` | string | null | Heroicon |
| `href` | string | null | URL |
| `badge` | mixed | null | Badge count |

---

## Icon Button

Nút chỉ có icon.

```blade
<x-filament::icon-button
    icon="heroicon-o-pencil"
    color="primary"
    size="md"
    tooltip="Chỉnh sửa"
    label="Edit"
/>
```

---

## Fieldset

Nhóm trường có border + label.

```blade
<x-filament::fieldset label="Thông tin liên hệ" :contained="true">
    {{-- Fields --}}
</x-filament::fieldset>
```

---

## Toggle

Switch on/off.

```blade
<x-filament::toggle
    :state="true"
    on-color="success"
    off-color="gray"
    on-icon="heroicon-o-check"
    off-icon="heroicon-o-x-mark"
/>
```

---

## Dropdown

Menu thả xuống.

```blade
<x-filament::dropdown>
    <x-slot name="trigger">
        <x-filament::icon-button icon="heroicon-o-ellipsis-vertical" />
    </x-slot>

    <x-filament::dropdown.list>
        <x-filament::dropdown.list.item icon="heroicon-o-pencil">
            Chỉnh sửa
        </x-filament::dropdown.list.item>
        <x-filament::dropdown.list.item icon="heroicon-o-trash" color="danger">
            Xóa
        </x-filament::dropdown.list.item>
    </x-filament::dropdown.list>
</x-filament::dropdown>
```

---

## CSS Classes Convention

### Fi-section content
```
fi-section → container chính
fi-section-header → header row
fi-section-content → content wrapper
fi-section-footer → footer
```

### Colors available
`primary`, `success`, `warning`, `danger`, `info`, `gray`

### CSS Variables (theme-aware)
```css
var(--gray-50)   → background nhạt
var(--gray-100)  → border nhạt
var(--gray-200)  → border
var(--gray-400)  → text muted
var(--gray-500)  → text secondary
var(--gray-950)  → text primary
var(--primary-600) → accent color
```

### Table inside Section
Để table full-width trong section, dùng negative margin trên wrapper:
```blade
<x-filament::section>
    <div style="overflow-x:auto; margin:-16px">
        <table style="width:100%">...</table>
    </div>
</x-filament::section>
```

### Layout với `x-filament-panels::page`
`x-filament-panels::page` tự thêm gap giữa các child elements qua CSS class `fi-page-content` (gap mặc định ~1rem).
