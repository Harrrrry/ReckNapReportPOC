-- Sample Data for ReckNap Dynamic Report POC
-- This data represents typical business scenarios for testing

-- Insert sample customers
INSERT INTO customers (customer_code, name, email, phone, address, city, state, pincode, gst_number, segment, credit_limit, credit_days) VALUES
('CUST001', 'ABC Electronics Pvt Ltd', 'accounts@abcelectronics.com', '9876543210', '123 Industrial Area', 'Mumbai', 'Maharashtra', '400001', '27ABCDE1234F1Z5', 'Premium', 500000.00, 45),
('CUST002', 'XYZ Trading Co', 'info@xyztrading.com', '9876543211', '456 Market Street', 'Delhi', 'Delhi', '110001', '07XYZAB5678G2H9', 'Regular', 200000.00, 30),
('CUST003', 'PQR Industries', 'purchase@pqrindustries.com', '9876543212', '789 Factory Road', 'Pune', 'Maharashtra', '411001', '27PQRST9012I3J4', 'Premium', 750000.00, 60),
('CUST004', 'LMN Retail Store', 'orders@lmnretail.com', '9876543213', '321 Shopping Complex', 'Bangalore', 'Karnataka', '560001', '29LMNOP3456K7L8', 'Regular', 150000.00, 30),
('CUST005', 'RST Enterprises', 'billing@rstenterprises.com', '9876543214', '654 Business Park', 'Chennai', 'Tamil Nadu', '600001', '33RSTUV7890M1N2', 'VIP', 1000000.00, 90),
('CUST006', 'UVW Solutions', 'finance@uvwsolutions.com', '9876543215', '987 Tech Hub', 'Hyderabad', 'Telangana', '500001', '36UVWXY1234O5P6', 'Regular', 300000.00, 30),
('CUST007', 'GHI Distributors', 'accounts@ghidist.com', '9876543216', '147 Wholesale Market', 'Kolkata', 'West Bengal', '700001', '19GHIJK5678Q9R0', 'Premium', 600000.00, 45),
('CUST008', 'JKL Manufacturing', 'procurement@jklmfg.com', '9876543217', '258 Industrial Estate', 'Ahmedabad', 'Gujarat', '380001', '24JKLMN9012S3T4', 'Regular', 400000.00, 30);

-- Insert sample products
INSERT INTO products (product_code, name, description, category, unit, rate, gst_rate) VALUES
('PROD001', 'Laptop Computer', 'High Performance Business Laptop', 'Electronics', 'Pcs', 45000.00, 18.00),
('PROD002', 'Office Chair', 'Ergonomic Executive Chair', 'Furniture', 'Pcs', 8500.00, 18.00),
('PROD003', 'Printer Ink Cartridge', 'Original Black Ink Cartridge', 'Consumables', 'Pcs', 1200.00, 18.00),
('PROD004', 'Mobile Phone', 'Smartphone with 128GB Storage', 'Electronics', 'Pcs', 25000.00, 18.00),
('PROD005', 'Office Desk', 'Wooden Executive Desk', 'Furniture', 'Pcs', 15000.00, 18.00),
('PROD006', 'Software License', 'Annual Business Software License', 'Software', 'License', 12000.00, 18.00),
('PROD007', 'Stationery Kit', 'Complete Office Stationery Package', 'Supplies', 'Kit', 500.00, 18.00),
('PROD008', 'Projector', 'HD Business Projector', 'Electronics', 'Pcs', 35000.00, 18.00),
('PROD009', 'Conference Table', '10 Seater Conference Table', 'Furniture', 'Pcs', 25000.00, 18.00),
('PROD010', 'Networking Equipment', 'Enterprise Router', 'Electronics', 'Pcs', 18000.00, 18.00);

-- Insert sample invoices
INSERT INTO invoices (invoice_number, customer_id, invoice_date, due_date, subtotal, gst_amount, total_amount, balance_amount, status, payment_terms) VALUES
('INV-2024-001', 1, '2024-01-15', '2024-03-01', 90000.00, 16200.00, 106200.00, 106200.00, 'Sent', '45 Days'),
('INV-2024-002', 2, '2024-01-20', '2024-02-19', 33500.00, 6030.00, 39530.00, 19530.00, 'Paid', '30 Days'),
('INV-2024-003', 3, '2024-01-25', '2024-03-25', 125000.00, 22500.00, 147500.00, 147500.00, 'Sent', '60 Days'),
('INV-2024-004', 4, '2024-02-01', '2024-03-02', 15500.00, 2790.00, 18290.00, 0.00, 'Paid', '30 Days'),
('INV-2024-005', 5, '2024-02-05', '2024-05-05', 200000.00, 36000.00, 236000.00, 236000.00, 'Sent', '90 Days'),
('INV-2024-006', 6, '2024-02-10', '2024-03-11', 54000.00, 9720.00, 63720.00, 31720.00, 'Paid', '30 Days'),
('INV-2024-007', 7, '2024-02-15', '2024-04-01', 85000.00, 15300.00, 100300.00, 100300.00, 'Sent', '45 Days'),
('INV-2024-008', 8, '2024-02-20', '2024-03-21', 42000.00, 7560.00, 49560.00, 24560.00, 'Paid', '30 Days'),
('INV-2024-009', 1, '2024-02-25', '2024-04-10', 67500.00, 12150.00, 79650.00, 79650.00, 'Overdue', '45 Days'),
('INV-2024-010', 3, '2024-03-01', '2024-04-30', 95000.00, 17100.00, 112100.00, 112100.00, 'Sent', '60 Days');

-- Insert sample invoice items
INSERT INTO invoice_items (invoice_id, product_id, quantity, rate, amount, gst_rate, gst_amount, total_amount) VALUES
-- Invoice 1 items
(1, 1, 2, 45000.00, 90000.00, 18.00, 16200.00, 106200.00),
-- Invoice 2 items
(2, 2, 2, 8500.00, 17000.00, 18.00, 3060.00, 20060.00),
(2, 7, 33, 500.00, 16500.00, 18.00, 2970.00, 19470.00),
-- Invoice 3 items
(3, 5, 5, 15000.00, 75000.00, 18.00, 13500.00, 88500.00),
(3, 9, 2, 25000.00, 50000.00, 18.00, 9000.00, 59000.00),
-- Invoice 4 items
(4, 3, 10, 1200.00, 12000.00, 18.00, 2160.00, 14160.00),
(4, 7, 7, 500.00, 3500.00, 18.00, 630.00, 4130.00),
-- Invoice 5 items
(5, 1, 2, 45000.00, 90000.00, 18.00, 16200.00, 106200.00),
(5, 8, 2, 35000.00, 70000.00, 18.00, 12600.00, 82600.00),
(5, 6, 5, 12000.00, 60000.00, 18.00, 10800.00, 70800.00),
-- Invoice 6 items
(6, 10, 3, 18000.00, 54000.00, 18.00, 9720.00, 63720.00),
-- Invoice 7 items
(7, 4, 2, 25000.00, 50000.00, 18.00, 9000.00, 59000.00),
(7, 8, 1, 35000.00, 35000.00, 18.00, 6300.00, 41300.00),
-- Invoice 8 items
(8, 2, 3, 8500.00, 25500.00, 18.00, 4590.00, 30090.00),
(8, 7, 33, 500.00, 16500.00, 18.00, 2970.00, 19470.00),
-- Invoice 9 items
(9, 1, 1, 45000.00, 45000.00, 18.00, 8100.00, 53100.00),
(9, 4, 1, 25000.00, 25000.00, 18.00, 4500.00, 29500.00),
-- Invoice 10 items
(10, 5, 4, 15000.00, 60000.00, 18.00, 10800.00, 70800.00),
(10, 8, 1, 35000.00, 35000.00, 18.00, 6300.00, 41300.00);

-- Insert sample payments
INSERT INTO payments (payment_number, customer_id, invoice_id, payment_date, amount, payment_method, reference_number, status) VALUES
('PAY-2024-001', 2, 2, '2024-02-15', 20000.00, 'Bank Transfer', 'TXN123456789', 'Cleared'),
('PAY-2024-002', 4, 4, '2024-02-28', 18290.00, 'UPI', 'UPI987654321', 'Cleared'),
('PAY-2024-003', 6, 6, '2024-03-05', 32000.00, 'Cheque', 'CHQ001122334', 'Cleared'),
('PAY-2024-004', 8, 8, '2024-03-10', 25000.00, 'Bank Transfer', 'TXN445566778', 'Cleared'),
('PAY-2024-005', 1, NULL, '2024-03-15', 50000.00, 'Bank Transfer', 'TXN998877665', 'Cleared');

-- Insert sample credit/debit memos
INSERT INTO memos (memo_number, customer_id, invoice_id, memo_type, memo_date, amount, gst_amount, total_amount, reason, description, status) VALUES
('CM-2024-001', 2, 2, 'Credit', '2024-02-18', 1500.00, 270.00, 1770.00, 'Damaged goods return', 'Returned damaged stationery items', 'Applied'),
('DM-2024-001', 6, 6, 'Debit', '2024-03-08', 500.00, 90.00, 590.00, 'Late payment charges', 'Interest on delayed payment', 'Applied'),
('CM-2024-002', 8, 8, 'Credit', '2024-03-12', 800.00, 144.00, 944.00, 'Quantity shortage', 'Short delivery adjustment', 'Applied');

-- Update invoice paid amounts and balance amounts based on payments and memos
UPDATE invoices SET paid_amount = 20000.00, balance_amount = 19530.00 WHERE id = 2;
UPDATE invoices SET paid_amount = 18290.00, balance_amount = 0.00 WHERE id = 4;
UPDATE invoices SET paid_amount = 32000.00, balance_amount = 31720.00 WHERE id = 6;
UPDATE invoices SET paid_amount = 25000.00, balance_amount = 24560.00 WHERE id = 8;

-- Insert predefined report fields for dynamic reporting
INSERT INTO report_fields (field_key, label, table_name, column_name, data_type, field_type, active, sort_order) VALUES
-- Customer fields
('customer_code', 'Customer Code', 'customers', 'customer_code', 'string', 'simple', 1, 1),
('customer_name', 'Customer Name', 'customers', 'name', 'string', 'simple', 1, 2),
('customer_email', 'Customer Email', 'customers', 'email', 'string', 'simple', 1, 3),
('customer_phone', 'Customer Phone', 'customers', 'phone', 'string', 'simple', 1, 4),
('customer_city', 'Customer City', 'customers', 'city', 'string', 'simple', 1, 5),
('customer_state', 'Customer State', 'customers', 'state', 'string', 'simple', 1, 6),
('customer_gst', 'Customer GST Number', 'customers', 'gst_number', 'string', 'simple', 1, 7),
('customer_segment', 'Customer Segment', 'customers', 'segment', 'string', 'simple', 1, 8),
('customer_credit_limit', 'Credit Limit', 'customers', 'credit_limit', 'decimal', 'simple', 1, 9),
('customer_credit_days', 'Credit Days', 'customers', 'credit_days', 'integer', 'simple', 1, 10),

-- Invoice fields
('invoice_number', 'Invoice Number', 'invoices', 'invoice_number', 'string', 'simple', 1, 11),
('invoice_date', 'Invoice Date', 'invoices', 'invoice_date', 'date', 'simple', 1, 12),
('due_date', 'Due Date', 'invoices', 'due_date', 'date', 'simple', 1, 13),
('subtotal', 'Subtotal', 'invoices', 'subtotal', 'decimal', 'simple', 1, 14),
('gst_amount', 'GST Amount', 'invoices', 'gst_amount', 'decimal', 'simple', 1, 15),
('total_amount', 'Total Amount', 'invoices', 'total_amount', 'decimal', 'simple', 1, 16),
('paid_amount', 'Paid Amount', 'invoices', 'paid_amount', 'decimal', 'simple', 1, 17),
('balance_amount', 'Balance Amount', 'invoices', 'balance_amount', 'decimal', 'simple', 1, 18),
('invoice_status', 'Invoice Status', 'invoices', 'status', 'string', 'simple', 1, 19),
('payment_terms', 'Payment Terms', 'invoices', 'payment_terms', 'string', 'simple', 1, 20),

-- Product fields
('product_code', 'Product Code', 'products', 'product_code', 'string', 'simple', 1, 21),
('product_name', 'Product Name', 'products', 'name', 'string', 'simple', 1, 22),
('product_category', 'Product Category', 'products', 'category', 'string', 'simple', 1, 23),
('product_rate', 'Product Rate', 'products', 'rate', 'decimal', 'simple', 1, 24),

-- Payment fields
('payment_number', 'Payment Number', 'payments', 'payment_number', 'string', 'simple', 1, 25),
('payment_date', 'Payment Date', 'payments', 'payment_date', 'date', 'simple', 1, 26),
('payment_amount', 'Payment Amount', 'payments', 'amount', 'decimal', 'simple', 1, 27),
('payment_method', 'Payment Method', 'payments', 'payment_method', 'string', 'simple', 1, 28),
('payment_reference', 'Payment Reference', 'payments', 'reference_number', 'string', 'simple', 1, 29),

-- Calculated fields
('days_overdue', 'Days Overdue', 'invoices', 'due_date', 'integer', 'calculated', 1, 30),
('aging_bucket', 'Aging Bucket', 'invoices', 'due_date', 'string', 'calculated', 1, 31),
('outstanding_amount', 'Outstanding Amount', 'invoices', 'balance_amount', 'decimal', 'calculated', 1, 32);

-- Update calculated fields with their calculation logic
UPDATE report_fields SET calculation_logic = 'DATEDIFF(CURDATE(), due_date)' WHERE field_key = 'days_overdue';
UPDATE report_fields SET calculation_logic = 'CASE 
    WHEN DATEDIFF(CURDATE(), due_date) <= 0 THEN "Current"
    WHEN DATEDIFF(CURDATE(), due_date) <= 30 THEN "1-30 Days"
    WHEN DATEDIFF(CURDATE(), due_date) <= 60 THEN "31-60 Days"
    WHEN DATEDIFF(CURDATE(), due_date) <= 90 THEN "61-90 Days"
    ELSE "90+ Days"
END' WHERE field_key = 'aging_bucket';

-- Insert sample report configurations
INSERT INTO report_configurations (name, description, selected_fields, field_order, is_default) VALUES
('Customer Report', 'Basic customer information report', 
 '["customer_code", "customer_name", "customer_email", "customer_city", "customer_segment"]',
 '["customer_code", "customer_name", "customer_email", "customer_city", "customer_segment"]', 1),
 
('Invoice Summary', 'Invoice summary with customer details',
 '["customer_name", "invoice_number", "invoice_date", "total_amount", "balance_amount", "invoice_status"]',
 '["customer_name", "invoice_number", "invoice_date", "total_amount", "balance_amount", "invoice_status"]', 1),
 
('Aging Report', 'Customer aging analysis',
 '["customer_name", "invoice_number", "due_date", "balance_amount", "days_overdue", "aging_bucket"]',
 '["customer_name", "invoice_number", "due_date", "balance_amount", "days_overdue", "aging_bucket"]', 1),
 
('Payment Collection', 'Payment collection report',
 '["customer_name", "payment_date", "payment_amount", "payment_method", "payment_reference"]',
 '["customer_name", "payment_date", "payment_amount", "payment_method", "payment_reference"]', 1);
