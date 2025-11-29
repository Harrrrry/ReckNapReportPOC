# 🚀 ReckNap Dynamic Report Export POC

A powerful, dynamic reporting system built with **CakePHP 3** that allows users to create, customize, and export reports without requiring backend code changes.

## ✨ Features

- **🎯 Dynamic Field Selection**: Add/remove fields from reports via frontend interface
- **🔄 Drag & Drop Ordering**: Reorder report columns with intuitive drag-and-drop
- **📊 Real-time Preview**: See report data before exporting
- **📈 Excel Export**: Generate professional Excel files with PhpSpreadsheet
- **💾 Configuration Management**: Save and load report configurations
- **🔒 Security**: Whitelist-based table/column validation
- **⚡ Performance**: Optimized queries with proper indexing
- **📱 Responsive UI**: Modern Bootstrap-based interface

## 🏗️ Architecture

### Backend (CakePHP 3)
- **Models**: ReportField, ReportConfiguration, Report
- **Controllers**: ReportFieldsController, ReportsController
- **Dynamic Query Builder**: Generates SQL based on field selection
- **Excel Export**: PhpSpreadsheet integration

### Frontend
- **HTML5 + Bootstrap 5**: Modern, responsive UI
- **SortableJS**: Drag-and-drop functionality
- **Vanilla JavaScript**: No framework dependencies
- **AJAX**: Real-time data loading and preview

### Database (MySQL)
- **8 Tables**: Complete business data structure
- **Sample Data**: Ready-to-use test data
- **Flexible Schema**: Supports various report types

## 📋 Requirements

- **PHP**: 7.4 or higher
- **MySQL**: 5.7 or higher
- **Composer**: For dependency management
- **Web Server**: Apache/Nginx with mod_rewrite

## 🚀 Quick Setup

### 1. Clone/Download Project
```bash
# If using Git
git clone <repository-url>
cd recknapReportPoc

# Or extract downloaded files
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Database Setup
```bash
# Run the setup script
php setup.php
```

The setup script will:
- Create the `recknap_reports` database
- Create all required tables
- Insert sample data (customers, invoices, products, etc.)
- Verify the installation

### 4. Web Server Configuration

#### Apache (.htaccess)
Create `.htaccess` in project root:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^$ webroot/ [L]
    RewriteRule (.*) webroot/$1 [L]
</IfModule>
```

Create `.htaccess` in `webroot/` directory:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

#### Nginx
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/recknapReportPoc/webroot;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$args;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 5. Access Application
Open your browser and navigate to:
```
http://localhost/recknapReportPoc/
```

## 📊 Sample Data

The system comes with pre-loaded sample data:

### Business Entities
- **8 Customers**: Various segments (Premium, Regular, VIP)
- **10 Products**: Electronics, furniture, software, supplies
- **10 Invoices**: Different statuses and amounts
- **15+ Invoice Items**: Product line items
- **5 Payments**: Various payment methods
- **3 Credit/Debit Memos**: Adjustments and corrections

### Pre-configured Reports
1. **Customer Report**: Basic customer information
2. **Invoice Summary**: Invoice details with customer info
3. **Aging Report**: Overdue analysis with aging buckets
4. **Payment Collection**: Payment tracking report

## 🎯 Usage Guide

### Creating a Report

1. **Select Fields**: Choose from available fields in the left panel
2. **Reorder Fields**: Drag selected fields to change column order
3. **Preview Report**: Click "Generate Preview" to see data
4. **Export Excel**: Click "Export to Excel" for download

### Adding New Fields

1. Click "Add Field" button
2. Fill in field details:
   - **Label**: Display name for the field
   - **Field Key**: Unique identifier (auto-generated)
   - **Table**: Source database table
   - **Column**: Source database column
   - **Data Type**: Field data type for formatting

### Saving Configurations

1. Select and arrange your desired fields
2. Click "Save Config"
3. Enter configuration name and description
4. Configuration is saved for future use

### Loading Configurations

1. Click "Load Config"
2. Choose from saved configurations
3. Fields are automatically selected and ordered

## 🔧 Configuration

### Database Settings
Edit `app/Config/database.php`:
```php
public $default = array(
    'datasource' => 'Database/Mysql',
    'host' => 'localhost',
    'login' => 'your_username',
    'password' => 'your_password',
    'database' => 'recknap_reports',
    // ... other settings
);
```

### Security Settings
The system includes security features:

- **Table Whitelist**: Only predefined tables are accessible
- **Column Validation**: Validates column existence
- **SQL Injection Protection**: Parameterized queries
- **Input Sanitization**: All user inputs are validated

### Adding New Tables
To add new tables to the system:

1. **Add to Whitelist**: Edit `ReportField.php` model
```php
private $allowedTables = array(
    'customers',
    'products',
    'invoices',
    // Add your new table here
    'your_new_table'
);
```

2. **Define Relationships**: Update `buildJoinClauses()` in `Report.php`
3. **Add Sample Fields**: Insert into `report_fields` table

## 📁 Project Structure

```
recknapReportPoc/
├── app/
│   ├── Config/
│   │   ├── core.php          # Core configuration
│   │   ├── database.php      # Database settings
│   │   └── routes.php        # URL routing
│   ├── Controller/
│   │   ├── AppController.php
│   │   ├── ReportFieldsController.php
│   │   └── ReportsController.php
│   ├── Model/
│   │   ├── ReportField.php
│   │   ├── ReportConfiguration.php
│   │   └── Report.php
│   └── View/
│       └── Reports/
│           └── index.ctp     # Main interface
├── database/
│   ├── schema.sql           # Database schema
│   └── sample_data.sql      # Sample data
├── webroot/
│   ├── css/                 # Stylesheets
│   ├── js/
│   │   └── report-manager.js # Frontend logic
│   └── index.php           # Entry point
├── composer.json           # Dependencies
├── setup.php              # Setup script
└── README.md              # This file
```

## 🔌 API Endpoints

### Report Fields Management
- `GET /report-fields` - Get all fields
- `POST /report-fields/add` - Add new field
- `PUT /report-fields/edit/{id}` - Update field
- `DELETE /report-fields/delete/{id}` - Delete field
- `POST /report-fields/reorder` - Update field order
- `GET /report-fields/tables` - Get available tables
- `GET /report-fields/columns/{table}` - Get table columns

### Report Generation
- `GET /reports` - Main interface
- `POST /reports/generate` - Generate report data
- `POST /reports/export` - Export to Excel
- `POST /reports/save-config` - Save configuration
- `GET /reports/configs` - Get saved configurations
- `GET /reports/load-config/{id}` - Load configuration

## 🧪 Testing

### Manual Testing
1. **Field Management**: Add/edit/delete fields
2. **Report Generation**: Create various report combinations
3. **Excel Export**: Test different field types and data
4. **Configuration**: Save/load different setups
5. **Performance**: Test with large datasets

### Sample Test Cases
- Select all customer fields → Export
- Create aging report → Verify calculations
- Mix different data types → Check formatting
- Large dataset (1000+ records) → Performance test

## 🚀 Deployment

### Production Checklist
- [ ] Set `debug` to 0 in `core.php`
- [ ] Configure production database
- [ ] Set proper file permissions
- [ ] Enable HTTPS
- [ ] Configure backup strategy
- [ ] Set up monitoring

### Performance Optimization
- Enable MySQL query cache
- Use proper indexing on large tables
- Implement pagination for large reports
- Consider caching for frequently used configurations

## 🔮 Future Enhancements

### Planned Features
- **📊 Chart Generation**: Visual reports with Chart.js
- **📄 PDF Export**: Generate PDF reports
- **🔍 Advanced Filters**: Date ranges, complex conditions
- **👥 User Management**: Role-based access control
- **📧 Scheduled Reports**: Email automation
- **🔄 Real-time Data**: Live dashboard updates
- **📱 Mobile App**: React Native companion app

### Technical Improvements
- **GraphQL API**: More efficient data fetching
- **Redis Caching**: Improved performance
- **Docker Support**: Containerized deployment
- **Unit Tests**: Comprehensive test coverage
- **CI/CD Pipeline**: Automated deployment

## 🤝 Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open Pull Request

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 🆘 Support

### Common Issues

**Database Connection Error**
- Check database credentials in `app/Config/database.php`
- Ensure MySQL service is running
- Verify database exists

**Excel Export Not Working**
- Run `composer install` to install PhpSpreadsheet
- Check PHP memory limit (increase if needed)
- Verify write permissions on temp directory

**Fields Not Loading**
- Check browser console for JavaScript errors
- Verify API endpoints are accessible
- Check database table `report_fields` has data

### Getting Help
- Check the documentation above
- Review sample data and configurations
- Test with provided sample reports first
- Check server error logs for detailed errors

---

**Built with ❤️ for dynamic reporting needs**
