---
name: Filament 5 Reference
description: Tài liệu tham chiếu Filament 5 — cấu trúc Resource, namespace, Schema, Table, Action, Layout, Infolist.
---

# Filament 5 Reference

## When to use this skill

Dùng skill này **BẮT BUỘC** khi:
- Tạo mới bất kỳ Filament Resource, Page, Widget, Form, Table, Infolist
- Thêm Action, Column, Filter, Section, Tabs vào Filament
- Gặp bất kỳ class nào trong namespace `Filament\*`
- Sửa hoặc debug code liên quan đến Filament

---

## ⚠️ CRITICAL RULES — ĐỌC TRƯỚC KHI LÀM BẤT CỨ ĐIỀU GÌ

> [!CAUTION]
> **VI PHẠM CÁC QUY TẮC SAU LÀ LỖI NGHIÊM TRỌNG:**
>
> **1. TUYỆT ĐỐI KHÔNG dùng FQCN.** Luôn khai báo `use` ở đầu file, gọi tên ngắn trong code body.
> ```php
> // ❌ SAI
> \Filament\Schemas\Components\Section::make('...')
> // ✅ ĐÚNG
> use Filament\Schemas\Components\Section;
> Section::make('...')
> ```
>
> **2. Layout components KHÔNG nằm trong `Forms` hay `Infolists`:**
> - `Section`, `Tabs`, `Grid`, `Fieldset`, `Wizard`, `Group` → `Filament\Schemas\Components\*`
> - Form fields (`TextInput`, `Select`, `Toggle`...) → `Filament\Forms\Components\*`
> - Tất cả Actions → `Filament\Actions\*`
>
> **3. Cấu trúc thư mục Resource BẮT BUỘC:**
> ```
> app/Filament/Resources/{Models}/{Model}Resource.php
> app/Filament/Resources/{Models}/Pages/
> app/Filament/Resources/{Models}/Schemas/{Model}Form.php
> app/Filament/Resources/{Models}/Tables/{Models}Table.php
> ```
> Namespace: `App\Filament\Resources\{Models}` — **KHÔNG PHẢI** `App\Filament\Resources`
>
> **4. `form()` và `infolist()` nhận `Schema $schema`, KHÔNG PHẢI `Form $form` hay `Infolist $infolist`**

---

## 1. Namespace Map (Quan trọng nhất)

Filament 5 tách hoàn toàn các package. Namespace khác biệt lớn so với v3/v4.

### 1.1 Schema & Layout Components


| Class | Namespace |
|---|---|
| `Schema` | `Filament\Schemas\Schema` |
| `Section` | `Filament\Schemas\Components\Section` |
| `Tabs` | `Filament\Schemas\Components\Tabs` |
| `Tab` | `Filament\Schemas\Components\Tabs\Tab` |
| `Grid` | `Filament\Schemas\Components\Grid` |
| `Fieldset` | `Filament\Schemas\Components\Fieldset` |
| `Flex` | `Filament\Schemas\Components\Flex` |
| `Group` | `Filament\Schemas\Components\Group` |
| `Wizard` | `Filament\Schemas\Components\Wizard` |
| `Wizard\Step` | `Filament\Schemas\Components\Wizard\Step` |
| `Component` (base) | `Filament\Schemas\Components\Component` |

> [!WARNING]
> Trong Filament 5, **Section, Tabs, Grid, Fieldset, Wizard, Group** đều nằm trong `Filament\Schemas\Components\*`.
> **KHÔNG PHẢI** `Filament\Forms\Components\Section` hay `Filament\Infolists\Components\Section` nữa.

### 1.2 Form Fields (Input Components)

| Class | Namespace |
|---|---|
| `TextInput` | `Filament\Forms\Components\TextInput` |
| `Textarea` | `Filament\Forms\Components\Textarea` |
| `Select` | `Filament\Forms\Components\Select` |
| `Toggle` | `Filament\Forms\Components\Toggle` |
| `Checkbox` | `Filament\Forms\Components\Checkbox` |
| `CheckboxList` | `Filament\Forms\Components\CheckboxList` |
| `Radio` | `Filament\Forms\Components\Radio` |
| `DateTimePicker` | `Filament\Forms\Components\DateTimePicker` |
| `DatePicker` | `Filament\Forms\Components\DatePicker` |
| `TimePicker` | `Filament\Forms\Components\TimePicker` |
| `FileUpload` | `Filament\Forms\Components\FileUpload` |
| `RichEditor` | `Filament\Forms\Components\RichEditor` |
| `MarkdownEditor` | `Filament\Forms\Components\MarkdownEditor` |
| `ColorPicker` | `Filament\Forms\Components\ColorPicker` |
| `TagsInput` | `Filament\Forms\Components\TagsInput` |
| `KeyValue` | `Filament\Forms\Components\KeyValue` |
| `Repeater` | `Filament\Forms\Components\Repeater` |
| `Builder` | `Filament\Forms\Components\Builder` |
| `Hidden` | `Filament\Forms\Components\Hidden` |
| `Placeholder` | `Filament\Forms\Components\Placeholder` |
| `ToggleButtons` | `Filament\Forms\Components\ToggleButtons` |
| `Slider` | `Filament\Forms\Components\Slider` |
| `OneTimeCodeInput` | `Filament\Forms\Components\OneTimeCodeInput` |
| `MorphToSelect` | `Filament\Forms\Components\MorphToSelect` |
| `Field` (base) | `Filament\Forms\Components\Field` |

> [!IMPORTANT]
> Layout components (Section, Tabs, Grid...) nằm ở `Filament\Schemas\Components\*`.
> Form fields (TextInput, Select, Toggle...) vẫn nằm ở `Filament\Forms\Components\*`.

### 1.3 Table Components

| Class | Namespace |
|---|---|
| `Table` | `Filament\Tables\Table` |
| `TextColumn` | `Filament\Tables\Columns\TextColumn` |
| `ImageColumn` | `Filament\Tables\Columns\ImageColumn` |
| `IconColumn` | `Filament\Tables\Columns\IconColumn` |
| `BooleanColumn` | `Filament\Tables\Columns\BooleanColumn` |
| `SelectColumn` | `Filament\Tables\Columns\SelectColumn` |
| `CheckboxColumn` | `Filament\Tables\Columns\CheckboxColumn` |
| `TextInputColumn` | `Filament\Tables\Columns\TextInputColumn` |
| `ToggleColumn` | `Filament\Tables\Columns\ToggleColumn` |
| `ColorColumn` | `Filament\Tables\Columns\ColorColumn` |
| `Column` (base) | `Filament\Tables\Columns\Column` |

### 1.4 Table Filters

| Class | Namespace |
|---|---|
| `SelectFilter` | `Filament\Tables\Filters\SelectFilter` |
| `Filter` | `Filament\Tables\Filters\Filter` |
| `TernaryFilter` | `Filament\Tables\Filters\TernaryFilter` |
| `TrashedFilter` | `Filament\Tables\Filters\TrashedFilter` |
| `QueryBuilder` | `Filament\Tables\Filters\QueryBuilder` |

### 1.5 Actions (Unified Package)

| Class | Namespace |
|---|---|
| `Action` | `Filament\Actions\Action` |
| `CreateAction` | `Filament\Actions\CreateAction` |
| `EditAction` | `Filament\Actions\EditAction` |
| `DeleteAction` | `Filament\Actions\DeleteAction` |
| `ViewAction` | `Filament\Actions\ViewAction` |
| `ReplicateAction` | `Filament\Actions\ReplicateAction` |
| `ForceDeleteAction` | `Filament\Actions\ForceDeleteAction` |
| `RestoreAction` | `Filament\Actions\RestoreAction` |
| `ImportAction` | `Filament\Actions\ImportAction` |
| `ExportAction` | `Filament\Actions\ExportAction` |
| `ActionGroup` | `Filament\Actions\ActionGroup` |
| `BulkAction` | `Filament\Actions\BulkAction` |
| `BulkActionGroup` | `Filament\Actions\BulkActionGroup` |
| `DeleteBulkAction` | `Filament\Actions\DeleteBulkAction` |

> [!WARNING]
> Trong Filament 5, **TẤT CẢ Action** đều nằm trong `Filament\Actions\*`.
> **KHÔNG CÒN** `Filament\Tables\Actions\EditAction`, `Filament\Tables\Actions\DeleteAction` riêng nữa.
> Tuy nhiên khi dùng **prefix import** `use Filament\Tables;` thì gọi `Tables\Actions\EditAction`
> vẫn hoạt động được vì Filament tự alias. Nhưng cách chuẩn nhất là import từ `Filament\Actions\*`.

### 1.6 Infolist Entries

| Class | Namespace |
|---|---|
| `TextEntry` | `Filament\Infolists\Components\TextEntry` |
| `ImageEntry` | `Filament\Infolists\Components\ImageEntry` |
| `IconEntry` | `Filament\Infolists\Components\IconEntry` |
| `ColorEntry` | `Filament\Infolists\Components\ColorEntry` |
| `KeyValueEntry` | `Filament\Infolists\Components\KeyValueEntry` |
| `RepeatableEntry` | `Filament\Infolists\Components\RepeatableEntry` |

> [!NOTE]
> Infolist dùng chung layout components từ `Filament\Schemas\Components\*` (Section, Tabs, Grid).

### 1.7 Notifications

| Class | Namespace |
|---|---|
| `Notification` | `Filament\Notifications\Notification` |

> [!CAUTION]
> **Notification Actions** trong Filament 5 dùng `Filament\Actions\Action`, **KHÔNG PHẢI** `Filament\Notifications\Actions\Action`.
> ```php
> use Filament\Actions\Action as NotificationAction;
> use Filament\Notifications\Notification;
>
> Notification::make()
>     ->title('...')
>     ->actions([
>         NotificationAction::make('view')->label('Xem')->url('/path'),
>     ])
>     ->sendToDatabase($user);
> ```

### 1.8 Resource & Pages

| Class | Namespace | Ghi chú |
|---|---|---|
| `Resource` | `Filament\Resources\Resource` | Base cho CRUD resources |
| `Page` (resource) | `Filament\Resources\Pages\Page` | Base cho resource pages |
| `Page` (custom) | `Filament\Pages\Page` | Base cho custom pages (report, dashboard) |
| `ListRecords` | `Filament\Resources\Pages\ListRecords` | |
| `CreateRecord` | `Filament\Resources\Pages\CreateRecord` | |
| `EditRecord` | `Filament\Resources\Pages\EditRecord` | |
| `ViewRecord` | `Filament\Resources\Pages\ViewRecord` | |
| `ManageRecords` | `Filament\Resources\Pages\ManageRecords` | |
| `RelationManager` | `Filament\Resources\RelationManagers\RelationManager` | |

> [!CAUTION]
> **Custom Pages** (như report, dashboard) dùng `Filament\Pages\Page`.
> **Resource Pages** (List, Create, Edit) dùng `Filament\Resources\Pages\*`.
> Hai class `Page` này **KHÁC NHAU**, không nhầm lẫn!

---

## 2. Cấu trúc Resource (Project Convention)

Filament 5 đặt Resource bên trong thư mục group (số nhiều):

```
app/Filament/Resources/
└── {Models}/
    ├── {Model}Resource.php                      ← Resource chính
    ├── Pages/
    │   ├── List{Models}.php                     ← extends ListRecords
    │   ├── Create{Model}.php                    ← extends CreateRecord
    │   └── Edit{Model}.php                      ← extends EditRecord
    ├── Schemas/
    │   └── {Model}Form.php                      ← Form schema class
    └── Tables/
        └── {Models}Table.php                    ← Table config class
```

> [!IMPORTANT]
> Namespace là `App\Filament\Resources\{Models}`, **KHÔNG PHẢI** `App\Filament\Resources`.

### 2.1 Resource chính (`{Model}Resource.php`)

```php
<?php

namespace App\Filament\Resources\{Models};

use App\Filament\Resources\{Models}\Pages\Create{Model};
use App\Filament\Resources\{Models}\Pages\Edit{Model};
use App\Filament\Resources\{Models}\Pages\List{Models};
use App\Filament\Resources\{Models}\Schemas\{Model}Form;
use App\Filament\Resources\{Models}\Tables\{Models}Table;
use App\Models\{Model};
use BackedEnum;
use Filament\Resources\Resource;
use UnitEnum;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class {Model}Resource extends Resource
{
    protected static ?string $model = {Model}::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedXxx;

    protected static ?string $navigationLabel = 'Tên hiển thị';

    protected static ?string $modelLabel = 'Tên đơn';

    protected static ?string $pluralModelLabel = 'Tên số nhiều';

    protected static string|UnitEnum|null $navigationGroup = 'Nhóm menu';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return {Model}Form::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return {Models}Table::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => List{Models}::route('/'),
            'create' => Create{Model}::route('/create'),
            'edit' => Edit{Model}::route('/{record}/edit'),
        ];
    }
}
```

> [!IMPORTANT]
> Method `form()` nhận và trả về `Schema $schema` (từ `Filament\Schemas\Schema`), **KHÔNG PHẢI** `Form $form`.
> Method `infolist()` cũng nhận `Schema $schema`, **KHÔNG PHẢI** `Infolist $infolist`.

### 2.2 Form Schema (`Schemas/{Model}Form.php`)

```php
<?php

namespace App\Filament\Resources\{Models}\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class {Model}Form
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Tiêu đề section')
                    ->schema([
                        TextInput::make('name')
                            ->label('Tên')
                            ->required()
                            ->maxLength(255),
                        Select::make('category_id')
                            ->label('Danh mục')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload(),
                        Toggle::make('is_active')
                            ->label('Hoạt động')
                            ->default(true),
                        Textarea::make('description')
                            ->label('Mô tả')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
```

> [!NOTE]
> Section dùng `->schema([...])` để khai báo nội dung bên trong.
> Schema root dùng `->components([...])`.

### 2.3 Table Config (`Tables/{Models}Table.php`)

Có 2 cách import table components:

```php
<?php

namespace App\Filament\Resources\{Models}\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class {Models}Table
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Tên')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('name')
            ->filters([
                SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'active' => 'Hoạt động',
                        'inactive' => 'Ngưng',
                    ]),
            ])
            ->actions([                           // alias: ->recordActions()
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([                         // alias: ->toolbarActions()
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
```

> [!NOTE]
> Filament 5 có **2 cách gọi**, cả hai đều hợp lệ:
> - `->actions()` = `->recordActions()` (hiện action trên mỗi row)
> - `->bulkActions()` = `->toolbarActions()` (hiện bulk action trên toolbar)
> Cách phổ biến nhất: `->actions()` và `->bulkActions()`.

### 2.4 Page classes

```php
<?php
// ListRecords page
namespace App\Filament\Resources\{Models}\Pages;

use App\Filament\Resources\{Models}\{Model}Resource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class List{Models} extends ListRecords
{
    protected static string $resource = {Model}Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Thêm mới'),
        ];
    }
}
```

```php
<?php
// CreateRecord page
namespace App\Filament\Resources\{Models}\Pages;

use App\Filament\Resources\{Models}\{Model}Resource;
use Filament\Resources\Pages\CreateRecord;

class Create{Model} extends CreateRecord
{
    protected static string $resource = {Model}Resource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
```

```php
<?php
// EditRecord page
namespace App\Filament\Resources\{Models}\Pages;

use App\Filament\Resources\{Models}\{Model}Resource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class Edit{Model} extends EditRecord
{
    protected static string $resource = {Model}Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
```

---

## 3. Layout Components — Cách dùng

Tất cả layout components thuộc `Filament\Schemas\Components\*`.

### Section
```php
use Filament\Schemas\Components\Section;

Section::make('Thông tin chung')
    ->description('Mô tả section')
    ->icon('heroicon-o-information-circle')
    ->schema([
        // form fields ở đây
    ])
    ->columns(2)
    ->collapsible()
    ->collapsed(false)
    ->aside()           // hiển thị dạng aside (label bên trái, form bên phải)
    ->compact()         // giảm padding
    ->headerActions([   // actions trên header của section
        Action::make('loadDefaults')->label('Tải mặc định'),
    ]);
```

### Tabs
```php
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

Tabs::make('Tabs label')
    ->tabs([
        Tab::make('Tab 1')
            ->icon('heroicon-o-user')
            ->badge(5)
            ->schema([
                // components
            ]),
        Tab::make('Tab 2')
            ->schema([
                // components
            ]),
    ])
    ->persistTabInQueryString('tab')   // lưu tab active vào URL
    ->contained(false);                // bỏ border container
```

### Grid
```php
use Filament\Schemas\Components\Grid;

Grid::make(3)   // 3 columns
    ->schema([
        // components
    ]);
```

### Fieldset
```php
use Filament\Schemas\Components\Fieldset;

Fieldset::make('Địa chỉ')
    ->schema([
        // components
    ])
    ->columns(2);
```

### Wizard
```php
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;

Wizard::make([
    Step::make('Bước 1')
        ->icon('heroicon-o-user')
        ->schema([...]),
    Step::make('Bước 2')
        ->schema([...]),
]);
```

---

## 4. Actions — Các lưu ý

### 4.1 Tất cả Action từ `Filament\Actions\*`

```php
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Actions\ActionGroup;
```

### 4.2 Actions trong Header Pages

```php
use Filament\Actions;

// Trong Page class
protected function getHeaderActions(): array
{
    return [
        Actions\CreateAction::make(),
        Actions\DeleteAction::make(),
    ];
}
```

### 4.3 Action modal với form

```php
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;

Action::make('doSomething')
    ->label('Thực hiện')
    ->icon('heroicon-o-plus')
    ->color('primary')
    ->schema([           // Filament 5 dùng ->schema() thay vì ->form()
        Section::make()
            ->schema([
                TextInput::make('name')->required(),
            ]),
    ])
    ->action(function (array $data) {
        // xử lý
    })
    ->requiresConfirmation()        // hỏi xác nhận trước khi thực hiện
    ->modalHeading('Tiêu đề modal')
    ->modalDescription('Mô tả')
    ->modalSubmitActionLabel('Xác nhận')
    ->slideOver();                   // hiện dạng drawer
```

### 4.4 Section headerActions

```php
use Filament\Actions\Action;
use Filament\Schemas\Components\Section;

Section::make('Thông tin')
    ->headerActions([
        Action::make('refresh')
            ->label('Làm mới')
            ->icon('heroicon-o-arrow-path')
            ->action(fn () => /* ... */),
    ])
    ->schema([...]);
```

---

## 5. Notification

```php
use Filament\Notifications\Notification;

Notification::make()
    ->title('Thành công!')
    ->body('Đã lưu dữ liệu.')
    ->success()     // hoặc ->danger(), ->warning(), ->info()
    ->send();
```

---

## 6. Checklist khi tạo Resource mới

1. Tạo Model + Migration
2. Tạo Resource: `{Model}Resource.php`
3. Tạo thư mục con:
   - `{Model}Resource/Pages/` — List, Create, Edit
   - `{Model}Resource/Schemas/` — `{Model}Form.php`
   - `{Model}Resource/Tables/` — `{Models}Table.php`
4. Kiểm tra namespace:
   - [ ] `Schema` from `Filament\Schemas\Schema`
   - [ ] Layout (Section, Tabs, Grid) from `Filament\Schemas\Components\*`
   - [ ] Form fields from `Filament\Forms\Components\*`
   - [ ] Table from `Filament\Tables\Table`
   - [ ] Columns from `Filament\Tables\Columns\*`
   - [ ] Filters from `Filament\Tables\Filters\*`
   - [ ] Actions from `Filament\Actions\*`
   - [ ] Notifications from `Filament\Notifications\Notification`
5. Không dùng FQCN trong code body

---

## 7. TextInput — Mask & Money Formatting (Alpine.js x-mask)

Filament 5 TextInput hỗ trợ `mask()` sử dụng **Alpine.js `x-mask:dynamic`** bên dưới. Dùng `RawJs` để truyền JavaScript expression.

### 7.1 Money Input (VNĐ) — Pattern chuẩn

```php
use Filament\Forms\Components\TextInput;
use Filament\Support\RawJs;

TextInput::make('amount')
    ->label('Số tiền')
    ->prefix('₫')
    ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
    ->stripCharacters('.')
    ->numeric()
    ->placeholder('VD: 1.000.000');
```

**Giải thích:**
- `mask(RawJs::make(...))` — Alpine.js format live: `1500000` → `1.500.000` (dấu chấm phân cách hàng nghìn, precision 0 = không thập phân)
- `stripCharacters('.')` — xóa dấu chấm trước khi validate & lưu DB → giá trị thực `1500000`
- `numeric()` — validate số (mask tự override `type` từ `number` → `text`)
- `prefix('₫')` — hiển thị ký hiệu tiền tệ

> [!CAUTION]
> **PHẢI dùng single quotes `'`** trong `RawJs::make()`, **KHÔNG dùng double quotes `"`**.
> `$money` và `$input` là biến **JavaScript**, dùng double quotes sẽ khiến PHP cố interpolate → crash.
> ```php
> // ❌ SAI — PHP sẽ cố nội suy $money, $input
> ->mask(RawJs::make("$money($input, ',', '.')"))
>
> // ✅ ĐÚNG — Single quotes giữ nguyên JS expression
> ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
> ```

### 7.2 Mask Patterns khác

```php
// Số điện thoại
TextInput::make('phone')
    ->mask('9999 999 999');

// Dynamic mask (Nowdoc - cách an toàn nhất)
TextInput::make('code')
    ->mask(RawJs::make(<<<'JS'
        $input.startsWith('34') ? '99 99 999 999' : '999 999 999'
    JS));
```

> [!IMPORTANT]
> Khi dùng `mask()`, `type` input tự chuyển thành `text`.
> Muốn validate số → kết hợp `->numeric()` + `->stripCharacters([...])`.

---

## 8. Sai lầm thường gặp

| ❌ SAI | ✅ ĐÚNG |
|---|---|
| `namespace App\Filament\Resources` | `namespace App\Filament\Resources\{Models}` |
| `Resources/{Model}Resource.php` (ngoài) | `Resources/{Models}/{Model}Resource.php` (trong) |
| `protected static ?string $navigationGroup` | `protected static string\|UnitEnum\|null $navigationGroup` |
| `->actions([...])` trong Table | ✅ OK — alias cho `->recordActions([...])` |
| `->bulkActions([...])` trong Table | ✅ OK — alias cho `->toolbarActions([...])` |
| `use Filament\Forms\Components\Section` | `use Filament\Schemas\Components\Section` |
| `use Filament\Forms\Components\Tabs` | `use Filament\Schemas\Components\Tabs` |
| `public static function form(Form $form)` | `public static function form(Schema $schema)` |
| `$form->schema([...])` | `$schema->components([...])` |
| `use Filament\Tables\Actions\EditAction` | `use Filament\Actions\EditAction` |
| `->form([...])` trong Action | `->schema([...])` trong Action |
| `Pages\List{Models}::route('/')` | `List{Models}::route('/')` (import trực tiếp) |
| `Repeater::headerActions([...])` | Dùng Section bọc ngoài + `->headerActions([...])` |
| `protected static string $view` (Page) | `protected string $view` (KHÔNG có `static`) |
| `use Filament\Resources\Pages\Page` (custom) | `use Filament\Pages\Page` (custom page) |
| `->size(TextEntry\TextEntrySize::Large)` | ❌ `TextEntrySize` không tồn tại — bỏ hoặc dùng `->size('lg')` |
| `public function infolist(Infolist $infolist)` | `public function infolist(Schema $schema)` |
| `protected static ?string $heading` (Widget) | `protected ?string $heading` (KHÔNG có `static`) |
| `protected static ?string $maxHeight` (Chart) | `protected ?string $maxHeight` (KHÔNG có `static`) |
| `protected static string $view` (Widget) | `protected string $view` (KHÔNG có `static`) |
| `<x-filament::icon icon="..." class="w-8 h-8">` | `<x-filament::icon icon="..." :size="IconSize::Large">` (dùng prop size) |

---

## 9. Icon Rendering trong Blade Views

Filament 5 có Blade component `<x-filament::icon>` (file: `vendor/filament/support/resources/views/components/icon.blade.php`).
Bên trong nó gọi `generate_icon_html()` và áp dụng CSS class `fi-icon fi-size-{size}`.

### 8.1 Cách dùng đúng

```blade
@php
    use Filament\Support\Enums\IconSize;
@endphp

{{-- Cách 1: Kích thước mặc định (Medium) --}}
<x-filament::icon icon="heroicon-o-user" />

{{-- Cách 2: Chọn kích thước qua prop --}}
<x-filament::icon icon="heroicon-o-cog" :size="IconSize::Small" />
<x-filament::icon icon="heroicon-o-cog" :size="IconSize::Large" />

{{-- Cách 3: Thêm class cho màu sắc --}}
<div class="text-emerald-500">
    <x-filament::icon icon="heroicon-o-check" :size="IconSize::Medium" />
</div>
```

### 8.2 Component Props

| Prop | Type | Mô tả |
|------|------|--------|
| `icon` | `string` | Tên icon (VD: `heroicon-o-user`) |
| `alias` | `?string` | Filament icon alias |
| `size` | `?IconSize` | Enum control kích thước |

### 8.3 IconSize Enum (`Filament\Support\Enums\IconSize`)

| Enum | Value |
|------|-------|
| `IconSize::ExtraSmall` | `xs` |
| `IconSize::Small` | `sm` |
| `IconSize::Medium` | `md` |
| `IconSize::Large` | `lg` |
| `IconSize::ExtraLarge` | `xl` |
| `IconSize::TwoExtraLarge` | `2xl` |

> [!CAUTION]
> **KHÔNG dùng Tailwind class** (`w-5 h-5`, `w-8 h-8`) để control kích thước icon trong Filament views.
> Filament CSS sẽ override → icon bị khổng lồ hoặc sai size.
> **LUÔN dùng** prop `:size="IconSize::Large"` để Filament quản lý kích thước qua class `fi-size-*`.

---

## 10. Quick Reference — Import thường dùng

```php
// Schema & Layout
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Fieldset;

// Form Fields
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\ToggleButtons;

// Table
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;

// Actions
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use UnitEnum;

class MyReport extends Page
{
    use HasFiltersForm; // ← Filament\Pages\Dashboard\Concerns\HasFiltersForm

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Báo cáo';
    protected static ?string $title = 'Báo cáo';
    protected static string|UnitEnum|null $navigationGroup = 'Thống kê';
    protected static ?int $navigationSort = 1;

    // ⚠️ Dùng view mặc định của Filament — KHÔNG CẦN tạo Blade view riêng
    protected string $view = 'filament-panels::pages.page';

    public function filtersForm(Schema $schema): Schema
    {
        return $schema->components([
            DatePicker::make('dateFrom')
                ->label('Từ ngày')
                ->default(now()->startOfMonth()),
            DatePicker::make('dateTo')
                ->label('Đến ngày')
                ->default(now()),
        ]);
    }

    // content() wires up filter form + widgets
    public function content(Schema $schema): Schema
    {
        return $schema->components([
            EmbeddedSchema::make('filtersForm'),
            Grid::make(1)
                ->schema(fn (): array => $this->getWidgetsSchemaComponents($this->getHeaderWidgets())),
            Grid::make(2) // 2 cột cho charts
                ->schema(fn (): array => $this->getWidgetsSchemaComponents($this->getFooterWidgets())),
        ]);
    }

    protected function getHeaderWidgets(): array
    {
        return [
            MyStatsOverview::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            MyChart::class,
            MyTable::class,
        ];
    }
}
```

> [!WARNING]
> - `HasFiltersForm` nằm ở `Filament\Pages\Dashboard\Concerns\HasFiltersForm` (KHÔNG PHẢI `Filament\Pages\Concerns`)
> - Method là `filtersForm()` (KHÔNG PHẢI `filtersSchema()`)
> - `protected string $view = 'filament-panels::pages.page';` — dùng view mặc định, KHÔNG cần tạo Blade
> - Phải override `content()` để render filter form + widgets

### 9.2 StatsOverviewWidget

```php
<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MyStatsOverview extends BaseWidget
{
    use InteractsWithPageFilters; // ← nhận $this->filters từ parent page

    protected static bool $isDiscovered = false; // chỉ hiện qua getHeaderWidgets()

    protected function getStats(): array
    {
        $dateFrom = $this->filters['dateFrom'] ?? now()->startOfMonth();
        $dateTo = $this->filters['dateTo'] ?? now();

        return [
            Stat::make('Tổng', '100')
                ->description('Trong kỳ')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('primary'),
            Stat::make('Hoàn thành', '80')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
        ];
    }
}
```

### 9.3 ChartWidget

```php
<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class MyChart extends ChartWidget
{
    protected static bool $isDiscovered = false;

    protected ?string $heading = 'Biểu đồ theo tháng';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Doanh thu',
                    'data' => [10, 20, 30, 40],
                    'borderColor' => 'rgb(245, 158, 11)',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => ['T1', 'T2', 'T3', 'T4'],
        ];
    }

    // Loại chart: 'line', 'bar', 'doughnut', 'pie', 'polarArea', 'radar'
    protected function getType(): string
    {
        return 'line';
    }
}
```

### 9.4 TableWidget

```php
<?php

namespace App\Filament\Widgets;

use App\Models\WorkOrder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class MyTable extends TableWidget
{
    use InteractsWithPageFilters;

    protected static bool $isDiscovered = false;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $from = Carbon::parse($this->filters['dateFrom'] ?? now()->startOfMonth());
        $to = Carbon::parse($this->filters['dateTo'] ?? now());

        return $table
            ->query(fn (): Builder => WorkOrder::whereBetween('created_at', [$from, $to]))
            ->heading('Dữ liệu')
            ->columns([
                TextColumn::make('code')
                    ->label('Mã')
                    ->searchable()
                    ->fontFamily('mono'),
                TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge(), // tự dùng enum color/label
                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->defaultPaginationPageOption(10);
    }
}
```

> [!IMPORTANT]
> **Quy tắc quan trọng cho Custom Page Widgets:**
> - `protected static bool $isDiscovered = false;` — KHÔNG hiện trên dashboard, chỉ render qua getHeaderWidgets/getFooterWidgets
> - `use InteractsWithPageFilters;` — để nhận `$this->filters` từ parent page (qua HasFiltersForm)
> - `$columnSpan = 'full'` trên TableWidget — chiếm toàn bộ chiều rộng
> - TableWidget có full power: columns, search, sort, pagination, badges, filters — KHÔNG CẦN viết HTML table

### 9.5 Panel Registration

Custom Pages tự động được scan nếu cấu hình `discoverPages()` trong PanelProvider:

```php
// AdminPanelProvider.php
->discoverPages(
    in: app_path('Filament/Pages'),
    for: 'App\\Filament\\Pages'
)
```

Widgets KHÔNG cần đăng ký riêng nếu `$isDiscovered = false` — chúng chỉ render qua `getHeaderWidgets()` / `getFooterWidgets()`.

> [!CAUTION]
> **Static vs Non-static properties trong Widgets:**
>
> | Property | ChartWidget | TableWidget | StatsOverviewWidget |
> |----------|-------------|-------------|---------------------|
> | `$heading` | `protected ?string` | `protected static ?string` | N/A |
> | `$sort` | `protected static ?int` | `protected static ?int` | `protected static ?int` |
> | `$maxHeight` | `protected ?string` | N/A | N/A |
> | `$columnSpan` | `protected int\|string\|array` | `protected int\|string\|array` | N/A |

---

## 10. PHP Enums cho Status Fields

Filament 5 hỗ trợ native PHP Enums trong Select, Badge, Filter.

### 10.1 Tạo Enum

```php
<?php

namespace App\Enums;

enum CourtStatus: string
{
    case Active = 'active';
    case Maintenance = 'maintenance';
    case Inactive = 'inactive';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Hoạt động',
            self::Maintenance => 'Bảo trì',
            self::Inactive => 'Ngưng',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::Maintenance => 'warning',
            self::Inactive => 'danger',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Active => 'heroicon-o-check-circle',
            self::Maintenance => 'heroicon-o-wrench',
            self::Inactive => 'heroicon-o-x-circle',
        };
    }
}
```

### 10.2 Dùng Enum trong Resource

```php
// Form: Select với Enum
Select::make('status')
    ->label('Trạng thái')
    ->options(CourtStatus::class)  // Filament tự gọi ->label()
    ->default(CourtStatus::Active)
    ->required();

// Table: Badge với Enum
TextColumn::make('status')
    ->label('Trạng thái')
    ->badge()
    ->color(fn (CourtStatus $state): string => $state->color());

// Filter: SelectFilter với Enum
SelectFilter::make('status')
    ->label('Trạng thái')
    ->options(CourtStatus::class);
```

### 10.3 Model Cast

```php
// Trong Model
protected $casts = [
    'status' => CourtStatus::class,
];
```

> [!TIP]
> Khi Model có cast Enum, Filament tự detect và truyền Enum instance vào `$state`.
> Dùng `fn (CourtStatus $state)` thay vì `fn (string $state)` trong closures.

---

## 11. Form Field Patterns

### 11.1 Reactive Fields (afterStateUpdated)

Khi field A thay đổi → tự động cập nhật field B:

```php
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Set;
use Filament\Forms\Get;

TextInput::make('warranty_months')
    ->label('Số tháng BH')
    ->numeric()
    ->default(12)
    ->live()                          // BẮT BUỘC để reactive hoạt động
    ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
        if ($state && $get('start_date')) {
            $expiry = \Carbon\Carbon::parse($get('start_date'))
                ->addMonths((int) $state);
            $set('expiration_date', $expiry->format('Y-m-d'));
        }
    }),
```

> [!IMPORTANT]
> **`->live()`** (hoặc `->reactive()`) BẮT BUỘC phải có trước `->afterStateUpdated()`.
> Nếu thiếu `->live()`, callback sẽ KHÔNG ĐƯỢC GỌI.

### 11.2 Conditional Visibility

Hiện/ẩn field dựa trên giá trị field khác:

```php
Select::make('payment_method')
    ->label('Phương thức')
    ->options([
        'cash' => 'Tiền mặt',
        'transfer' => 'Chuyển khoản',
        'card' => 'Thẻ tín dụng',
    ])
    ->live(),                         // ← cũng cần live()

TextInput::make('transfer_target')
    ->label('Số tài khoản')
    ->visible(fn (Get $get) => $get('payment_method') === 'transfer'),

// Hoặc dùng ->hidden() ngược lại
TextInput::make('receipt_number')
    ->hidden(fn (Get $get) => $get('payment_method') !== 'cash'),
```

### 11.3 Validation Rules

```php
TextInput::make('email')
    ->email()
    ->required()
    ->unique(ignoreRecord: true)                    // unique trừ record hiện tại
    ->maxLength(255),

TextInput::make('amount')
    ->numeric()
    ->minValue(0)
    ->maxValue(999999999)
    ->step(1000)
    ->prefix('₫')
    ->inputMode('numeric'),

TextInput::make('phone')
    ->tel()
    ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/'),

Select::make('status')
    ->required()
    ->in(['active', 'inactive'])      // server-side validation
    ->native(false),                  // dropdown đẹp hơn native select
```

### 11.4 Placeholder & Disabled States

```php
// Read-only computed value
Placeholder::make('total_formatted')
    ->label('Tổng cộng')
    ->content(fn ($record) => number_format($record?->total ?? 0) . 'đ'),

// Disabled nhưng vẫn submit value
TextInput::make('code')
    ->disabled()
    ->dehydrated(),                   // GIỮ giá trị khi submit dù disabled

// Disabled và KHÔNG submit value
TextInput::make('computed_field')
    ->disabled()
    ->dehydrated(false),              // BỎ khỏi submitted data
```

### 11.5 Default Values & Mutators

```php
// Default khi create
TextInput::make('code')
    ->default(fn () => 'WO-' . str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT)),

// Modify value trước khi save
TextInput::make('amount')
    ->dehydrateStateUsing(fn ($state) => (int) str_replace(['.', ','], '', $state)),

// Modify value sau khi load từ DB
TextInput::make('amount')
    ->formatStateUsing(fn ($state) => number_format($state ?? 0)),
```

---

## 12. Table Column Patterns

### 12.1 Format & Display

```php
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ToggleColumn;

// Format tiền VNĐ
TextColumn::make('amount')
    ->label('Số tiền')
    ->formatStateUsing(fn ($state) => number_format($state) . 'đ')
    ->sortable()
    ->alignEnd(),

// Hoặc dùng money()
TextColumn::make('total')
    ->money('VND'),

// Format ngày tiếng Việt
TextColumn::make('created_at')
    ->dateTime('d/m/Y H:i')
    ->sortable()
    ->since(),                        // "2 giờ trước" — tooltip full date

// Limit text dài
TextColumn::make('description')
    ->limit(50)
    ->tooltip(fn ($record) => $record->description),   // hover xem full

// Wrap text
TextColumn::make('notes')
    ->wrap()
    ->lineClamp(3),                   // max 3 dòng
```

### 12.2 Badge Columns

```php
// Badge với Enum (khuyên dùng — xem Section 11)
TextColumn::make('status')
    ->badge()
    ->color(fn (WorkOrderStatus $state) => $state->color()),

// Badge với string values
TextColumn::make('priority')
    ->badge()
    ->formatStateUsing(fn (string $state) => match($state) {
        'urgent' => 'Khẩn cấp',
        'high' => 'Cao',
        'normal' => 'Bình thường',
        'low' => 'Thấp',
        default => $state,
    })
    ->color(fn (string $state) => match($state) {
        'urgent' => 'danger',
        'high' => 'warning',
        'normal' => 'primary',
        'low' => 'gray',
        default => 'gray',
    })
    ->icon(fn (string $state) => match($state) {
        'urgent' => 'heroicon-o-fire',
        'high' => 'heroicon-o-arrow-up',
        default => null,
    }),
```

### 12.3 Toggleable & Hidden Columns

```php
// Hiện mặc định, cho phép ẩn
TextColumn::make('email')
    ->toggleable(),

// Ẩn mặc định, cho phép hiện
TextColumn::make('created_at')
    ->toggleable(isToggledHiddenByDefault: true),
```

### 12.4 Aggregate & Relationship Columns

```php
// Count relationship
TextColumn::make('tasks_count')
    ->counts('tasks')
    ->label('Số việc')
    ->sortable(),

// Nested relationship
TextColumn::make('customer.name')
    ->label('Khách hàng')
    ->searchable(),

// Summarize (footer)
TextColumn::make('amount')
    ->summarize([
        Sum::make()->label('Tổng'),
        Average::make()->label('TB'),
    ]),
```

### 12.5 Copyable & URL Columns

```php
TextColumn::make('serial_number')
    ->copyable()                      // click to copy
    ->copyMessage('Đã copy!')
    ->copyMessageDuration(1500),

TextColumn::make('website')
    ->url(fn ($record) => $record->website, shouldOpenInNewTab: true),
```

---

## 13. AdminPanelProvider Configuration

### 13.1 Navigation Groups (Ordering & Icons)

```php
use Filament\Navigation\NavigationGroup;

->navigationGroups([
    NavigationGroup::make('CRM'),
    NavigationGroup::make('Nghiệp vụ'),
    NavigationGroup::make('Bảo hành'),
    NavigationGroup::make('Hệ thống'),
])
```

> [!WARNING]
> Nếu navigation group có `->icon()`, thì **tất cả items** bên trong group **KHÔNG ĐƯỢC** có icon riêng.
> Filament 5 ném exception: *"Navigation group has an icon but one or more of its items also have icons."*
> → Chọn 1: hoặc group có icon, hoặc items có icon. **Không được cả hai.**

### 13.2 Discover Resources & Widgets

```php
// Auto-discover tất cả resources/pages/widgets trong thư mục
->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
```

### 13.3 Bỏ Default Widgets

```php
// Bỏ AccountWidget (Đăng xuất) và FilamentInfoWidget
->widgets([])       // mảng rỗng = không widget mặc định
```

### 13.4 Database Notifications

```php
->databaseNotifications()           // bật notification bell trên header
->databaseNotificationsPolling('30s')  // poll mỗi 30s (optional)
```

### 13.5 Full Example

```php
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\Support\Colors\Color;

public function panel(Panel $panel): Panel
{
    return $panel
        ->default()
        ->id('admin')
        ->path('admin')
        ->login()
        ->colors(['primary' => Color::Amber])
        ->databaseNotifications()
        ->navigationGroups([
            NavigationGroup::make('CRM'),
            NavigationGroup::make('Nghiệp vụ'),
            NavigationGroup::make('Hệ thống'),
        ])
        ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
        ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
        ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
        ->widgets([])
        ->plugins([
            FilamentShieldPlugin::make()
                ->navigationGroup('Hệ thống')
                ->navigationSort(12),
        ]);
}
```

---

## 14. Relationship Handling in Forms

### 14.1 Select + BelongsTo

```php
Select::make('customer_id')
    ->label('Khách hàng')
    ->relationship('customer', 'name')    // auto-load từ relationship
    ->searchable()                         // search trong dropdown
    ->preload()                            // preload tất cả options
    ->createOptionForm([                   // tạo mới ngay trong dropdown
        TextInput::make('name')->required(),
        TextInput::make('phone'),
    ])
    ->editOptionForm([                     // sửa ngay trong dropdown
        TextInput::make('name')->required(),
    ]),
```

### 14.2 Select + BelongsToMany (Multiple)

```php
Select::make('assignees')
    ->label('Nhân viên phụ trách')
    ->relationship('assignees', 'name')
    ->multiple()                           // chọn nhiều
    ->searchable()
    ->preload()
    ->pivotData([                          // thêm data vào pivot table
        'assigned_by' => auth()->id(),
    ]),
```

### 14.3 CheckboxList + BelongsToMany

```php
CheckboxList::make('tags')
    ->label('Nhãn')
    ->relationship('tags', 'name')
    ->columns(3)                           // 3 cột checkbox
    ->searchable()
    ->bulkToggleable(),                    // nút chọn/bỏ chọn tất cả
```

### 14.4 Repeater + HasMany

```php
use Filament\Forms\Components\Repeater;

Repeater::make('items')
    ->relationship('items')                // auto CRUD qua relationship
    ->schema([
        TextInput::make('name')->required(),
        TextInput::make('quantity')->numeric()->default(1),
        TextInput::make('unit_price')->numeric()->prefix('₫'),
    ])
    ->columns(3)
    ->defaultItems(1)                      // bắt đầu với 1 row
    ->addActionLabel('Thêm mục')
    ->reorderable()                        // kéo thả sắp xếp
    ->cloneable()                          // nút clone row
    ->collapsible()
    ->itemLabel(fn (array $state): ?string =>
        $state['name'] ?? 'Mục mới'        // label hiển thị khi collapsed
    ),
```

> [!IMPORTANT]
> Khi dùng `Repeater::make('items')->relationship('items')`, Filament tự động:
> - **Create**: tạo record con khi tạo record cha
> - **Update**: cập nhật record con khi sửa record cha
> - **Delete**: xóa record con khi xóa row trong repeater
>
> **KHÔNG CẦN** xử lý thủ công trong `afterCreate()` / `afterSave()`.

### 14.5 MorphToSelect (Polymorphic)

```php
use Filament\Forms\Components\MorphToSelect;

MorphToSelect::make('commentable')
    ->label('Gắn vào')
    ->types([
        MorphToSelect\Type::make(WorkOrder::class)
            ->titleAttribute('code'),
        MorphToSelect\Type::make(Task::class)
            ->titleAttribute('title'),
    ])
    ->searchable()
    ->preload(),
```

---

## 15. Set/Get Utilities & Tabs\Tab — Lưu ý quan trọng

### 15.1 Set và Get (afterStateUpdated)

Trong Filament 5, `Set` và `Get` đã chuyển sang namespace mới:

| ❌ SAI (v3/v4) | ✅ ĐÚNG (v5) |
|---|---|
| `use Filament\Forms\Set` | `use Filament\Schemas\Components\Utilities\Set` |
| `use Filament\Forms\Get` | `use Filament\Schemas\Components\Utilities\Get` |

> [!CAUTION]
> **Cách an toàn nhất: KHÔNG type-hint Set/Get**. Để Filament tự inject qua tên biến.

```php
// ❌ SAI — crash vì sai namespace
use Filament\Forms\Set;
->afterStateUpdated(function (string $state, Set $set) { ... })

// ✅ ĐÚNG — không type-hint, Filament tự nhận qua tên biến $set, $get
->afterStateUpdated(function (string $state, $set) {
    $set('customer_id', $customer->id);
    $set('contact_person', $customer->name);
})

// ✅ ĐÚNG — nếu muốn type-hint thì dùng namespace mới
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Utilities\Get;
->afterStateUpdated(function (string $state, Set $set, Get $get) { ... })
```

### 15.2 Tabs\Tab — Luôn import riêng

```php
// ❌ SAI — dùng inline Tabs\Tab::make()
use Filament\Schemas\Components\Tabs;

Tabs::make('label')->tabs([
    Tabs\Tab::make('Tab 1')->schema([...]),  // ← dễ lỗi namespace
]);

// ✅ ĐÚNG — import Tab riêng
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

Tabs::make('label')->tabs([
    Tab::make('Tab 1')->schema([...]),       // ← sạch, rõ ràng
    Tab::make('Tab 2')->schema([...]),
]);
```

> [!IMPORTANT]
> Tương tự cho `Wizard\Step`:
> ```php
> use Filament\Schemas\Components\Wizard;
> use Filament\Schemas\Components\Wizard\Step;
> ```


---

## 16. Custom Theme & Tailwind CSS — BẮT BUỘC cho Custom Views

> [!CAUTION]
> **Filament v5 dùng Tailwind CSS v4 nhưng với `source(none)` — tắt auto-scanning.**
> Nghĩa là: Tailwind classes trong custom Blade views (Livewire, widget, page) **KHÔNG ĐƯỢC COMPILE** mặc định.
> **PHẢI TẠO custom theme** để các Tailwind classes trong views hoạt động.

### 16.1 Tại sao cần Custom Theme?

Filament v5 `theme.css` gốc (vendor):
```css
@import 'tailwindcss' source(none);  /* ← TẮT auto-scanning */
@import './index.css';               /* ← Chỉ compile Filament internal classes */
```

`source(none)` = Tailwind v4 **không scan** bất kỳ file nào → chỉ compile classes mà Filament tự define. Các class custom như `bg-primary-500`, `rounded-2xl`, `max-w-[75%]`, `text-[10px]` trong views sẽ **KHÔNG có CSS**.

### 16.2 Tạo Custom Theme (Cách đúng cho Tailwind v4)

**File cần tạo:** `resources/css/filament/{panel}/theme.css`

```css
@import 'tailwindcss' source(none);
@import '../../../../vendor/filament/filament/resources/css/index.css';

/* Scan custom views & PHP files cho Tailwind classes */
/* From: resources/css/filament/admin/theme.css */
/* ../../../ = resources/ , ../../../../ = project root */
@source '../../../views/filament';
@source '../../../views/livewire';
@source '../../../views/components';
@source '../../../../app/Filament';
@source '../../../../app/Livewire';
```

> [!IMPORTANT]
> **`@source` (Tailwind v4)** thay thế `content` array trong tailwind.config.js (TW v3).
> Mỗi `@source` directive chỉ định **thư mục** để Tailwind scan tìm class names.
> Path là **relative từ file CSS**, KHÔNG PHẢI từ project root.
> **KHÔNG CẦN** tạo `tailwind.config.js` riêng khi dùng Tailwind v4.

### 16.3 Đăng ký Theme

**1. Thêm vào `vite.config.js` input:**
```js
input: [
    'resources/css/app.css',
    'resources/js/app.js',
    // ... other entries
    'resources/css/filament/admin/theme.css',  // ← thêm dòng này
],
```

**2. Đăng ký trong PanelProvider:**
```php
return $panel
    ->default()
    ->id('admin')
    ->path('admin')
    ->viteTheme('resources/css/filament/admin/theme.css')  // ← thêm dòng này
    // ... rest of config
```

**3. Build:**
```bash
npm run build   # production
npm run dev     # development
```

### 16.4 Khi nào CẦN custom theme?

| Trường hợp | Cần theme? |
|---|---|
| Dùng Filament components tiêu chuẩn (Resource, Form, Table) | ❌ Không |
| Custom Blade views (`@livewire`, widget views, custom pages) với Tailwind | ✅ BẮT BUỘC |
| Override Filament styles bằng `fi-` classes | ✅ BẮT BUỘC |
| Dùng arbitrary values (`text-[10px]`, `max-w-[75%]`) | ✅ BẮT BUỘC |
| Thêm `@source` mới khi tạo thư mục view mới | ✅ Cần thêm @source |

### 16.5 Override Styles bằng `fi-` Classes

Filament dùng semantic CSS class prefix `fi-`:

| Class | Vùng |
|-------|------|
| `.fi-main` | Khu vực content chính |
| `.fi-sidebar` | Thanh sidebar |
| `.fi-topbar` | Thanh top navigation |
| `.fi-btn` | Tất cả buttons |
| `.fi-section` | Section containers |
| `.fi-table` | Data tables |

Override trong `theme.css`:
```css
.fi-btn { @apply rounded-sm; }
.fi-sidebar { @apply bg-gray-900; }
```

### 16.6 Tuỳ biến màu sắc & Font (trong PanelProvider)

```php
use Filament\Support\Colors\Color;

->colors([
    'primary' => Color::Amber,
    'danger'  => Color::Rose,
    'success' => Color::Emerald,
])
->font('Nunito')                   // Google Fonts (tự load)
->darkMode(false)                  // Tắt dark mode
->brandName('CNET Tech')
->sidebarCollapsibleOnDesktop()
->spa()
```

### 16.7 Sai lầm thường gặp

| ❌ SAI | ✅ ĐÚNG |
|---|---|
| Viết Tailwind class trong custom view mà không có theme | Tạo theme + `@source` trước |
| Tạo `tailwind.config.js` riêng cho Filament (TW v3 cách cũ) | Dùng `@source` trong theme.css (TW v4) |
| Quên `->viteTheme(...)` trong PanelProvider | Luôn add `->viteTheme(...)` |
| Quên thêm CSS vào `vite.config.js` input | Luôn add vào input array |
| Dùng inline style thay vì Tailwind (workaround) | Tạo theme + compile đúng cách |
| IDE báo lỗi `@source` unknown → sửa file | Bỏ qua — IDE chưa hiểu TW v4 syntax |

