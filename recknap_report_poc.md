# 🚀 ReckNap — Dynamic Report Export POC (Markdown Documentation)

## 📌 Objective
Build a **dynamic reporting system** in **CakePHP 3** where fields shown in the Excel export can be:

- Added dynamically from Frontend  
- Removed anytime  
- Reordered via drag-and-drop  
- Mapped to DB tables/columns  
- Exported in the same order  
- Without requiring backend code changes  

The entire solution should be configurable and reusable.

---

# 📚 Tech Stack

| Layer | Technology |
|-------|------------|
| Backend | **CakePHP 3** |
| Database | **MySQL** |
| Frontend | HTML + JavaScript |
| Drag & Drop Library | **SortableJS** |
| Excel Generation | **PhpSpreadsheet** |

---

# 🎯 High-Level Requirement

1. Reports currently have fixed checkboxes & sequence.
2. We need to convert this to a **fully dynamic system**:

### Frontend Should Be Able To:
- Add new report fields  
- Map fields to DB table + DB column  
- Select fields to export  
- Reorder them (drag & drop)  
- Click export → Excel downloaded using the new configuration

### Backend Should:
- Store FE configuration in DB  
- Dynamically build SELECT query  
- Follow FE-selected column order  
- Export Excel with **phpSpreadsheet**  
- Never require code changes for new report fields  

---

# 🗄 Database Structure

Create the table: **report_fields**

```sql
CREATE TABLE report_fields (
    id INT AUTO_INCREMENT PRIMARY KEY,
    field_key VARCHAR(100) UNIQUE,
    label VARCHAR(255),
    table_name VARCHAR(100),
    column_name VARCHAR(100),
    data_type VARCHAR(50),
    active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created DATETIME,
    modified DATETIME
);
```

### Example Field Entries

| field_key | label | table_name | column_name | data_type |
|-----------|--------|-------------|-------------|-----------|
| customer_name | Customer Name | customers | name | string |
| product_code | Product Code | products | code | string |
| customer_segment | Customer Segment | customers | segment | string |

---

# 📥 API Endpoints

### 1. **Add New Field**
`POST /report-fields/add`

Request Body:
```json
{
  "field_key": "customer_segment",
  "label": "Customer Segment",
  "table_name": "customers",
  "column_name": "segment",
  "data_type": "string"
}
```

### 2. **Get All Fields**
`GET /report-fields`

Frontend uses this to build the popup UI.

### 3. **Export Excel**
`POST /reports/export`

Frontend sends selected + ordered fields:
```json
{
  "selected": ["customer_name", "amount", "segment"],
  "order": ["customer_name", "segment", "amount"]
}
```

---

# 🧠 Dynamic Query Builder (Backend)

Backend fetches mapping:

```php
$fields = $this->ReportFields
    ->find()
    ->where(['field_key IN' => $selected])
    ->order(['FIELD(field_key, ' . implode(',', $order) . ')'])
    ->toArray();
```

Build SELECT dynamically:

```php
$select = [];
foreach ($fields as $f) {
    $select[$f->field_key] = $f->table_name . "." . $f->column_name;
}
```

Generate query:

```php
$query = $this->Reports->find()
    ->select($select)
    ->join([...])   // existing joins
    ->where([...])  // existing filters
    ->all();
```

---

# 📤 Excel Export Using PhpSpreadsheet

Excel column order must match FE-selected order.

- Row 1 → Labels (from DB `label`)
- Row 2+ → Data from dynamic query

```php
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$col = 1;
foreach ($fields as $f) {
    $sheet->setCellValueByColumnAndRow($col, 1, $f->label);
    $col++;
}

// Fill rows
$row = 2;
foreach ($query as $data) {
    $col = 1;
    foreach ($fields as $f) {
        $sheet->setCellValueByColumnAndRow($col, $row, $data[$f->field_key]);
        $col++;
    }
    $row++;
}
```

---

# 🎨 Frontend Requirements

## 1. Popup With All Available Fields
Data fetched via:  
`GET /report-fields`

Displayed as:

- Checkbox for selection  
- Drag handle  
- Label  

## 2. Drag & Drop (SortableJS)

```javascript
new Sortable(document.getElementById('field-list'), {
    animation: 150,
    handle: '.drag-handle'
});
```

## 3. "Add New Field" Form

- Field Label  
- Field Key (auto-generated)  
- Table Name (dropdown)  
- Column Name  
- Data Type  

POST → `/report-fields/add`

## 4. Export Button

Send selected + order:

```javascript
{
  selected: [...],
  order: [...]
}
```

---

# 🔐 Security Rules

- Backend must **NOT** trust FE table_name/column_name blindly.
- Maintain a whitelist for:
  - Valid tables
  - Valid columns
- Validate before building SELECT.
- Reject unauthorized mappings.

---

# 📦 Output of This POC

1. Dynamic DB-driven field system  
2. Frontend drag & drop reorder  
3. Frontend field creation UI  
4. Backend dynamic SQL builder  
5. Excel export with variable columns  
6. Zero backend code change when FE adds a new field  

---

# ✔ Ready for Cursor Prompt

This markdown file contains everything needed for the Cursor AI environment to generate:

- Controllers  
- Models  
- Migrations  
- FE UI  
- Excel export logic  
- Dynamic query builder  
