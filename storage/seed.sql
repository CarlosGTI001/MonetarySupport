INSERT INTO accounts (name, type, currency, balance, purpose, active) VALUES
('Banco Santa Cruz', 'banco', 'DOP', 0, 'cuenta principal', 1),
('Efectivo', 'efectivo', 'DOP', 0, 'gastos diarios', 1),
('Tarjeta Metro', 'tarjeta', 'DOP', 0, 'transporte', 1),
('BHD', 'banco', 'DOP', 0, 'ahorro', 1),
('Qik', 'banco', 'DOP', 0, 'ahorro', 1),
('Banreservas', 'banco', 'DOP', 0, 'ahorro', 1),
('Banreservas MIO', 'banco', 'DOP', 0, 'ahorro', 1),
('Popular', 'banco', 'DOP', 0, 'ahorro', 1),
('BDI pesos', 'banco', 'DOP', 0, 'ahorro', 1),
('BDI dolares', 'banco', 'USD', 0, 'ahorro', 1),
('Lafise pesos', 'banco', 'DOP', 0, 'ahorro', 1),
('Lafise dolares', 'banco', 'USD', 0, 'ahorro', 1);

INSERT INTO fixed_expenses (name, amount, frequency, every_days, account_id, start_date, end_date, active, note) VALUES
('Ayuda comida', 4000, 'biweekly', NULL, NULL, NULL, NULL, 1, ''),
('Luz hogar', 500, 'biweekly', NULL, (SELECT id FROM accounts WHERE name='Banco Santa Cruz'), NULL, NULL, 1, 'desde Santa Cruz'),
('Abuela', 1000, 'biweekly', NULL, NULL, NULL, NULL, 1, ''),
('Financiamiento temporal', 1050, 'biweekly', NULL, NULL, NULL, '2026-07-31', 1, 'hasta julio 2026'),
('Telefono Altice', 1229, 'monthly', NULL, NULL, NULL, NULL, 1, ''),
('Internet Altice HFC 100/20', 1597, 'monthly', NULL, NULL, NULL, NULL, 1, ''),
('TV financiada', 3000, 'monthly', NULL, NULL, NULL, NULL, 1, '10 pagos, mayo 2026 pagado'),
('Uso personal', 2000, 'monthly', NULL, NULL, NULL, NULL, 1, ''),
('Nintendo BHD Alcanza', 250, 'custom', 15, (SELECT id FROM accounts WHERE name='BHD'), NULL, NULL, 1, 'cada 15-17 dias');

INSERT INTO financings (name, installment_amount, frequency, total_payments, payments_made, start_date, end_date, status, total_paid, total_pending, next_date, note) VALUES
('TV financiada', 3000, 'monthly', 10, 1, '2025-08-01', '2026-05-31', 'activo', 3000, 27000, '2026-06-01', 'mayo 2026 pagado');

INSERT INTO transport_items (name, amount, active) VALUES
('Mexico ida', 35, 1),
('Mexico regreso', 70, 1),
('Corredor ida', 35, 1),
('Corredor regreso', 35, 1),
('Metro ida', 20, 1),
('Metro regreso', 20, 1);

INSERT INTO savings_rules (name, mode, percent, amount, target_account_id, priority, active, note) VALUES
('10% ingresos a Qik', 'percentage', 10, NULL, (SELECT id FROM accounts WHERE name='Qik'), 1, 1, ''),
('250 quincenal BHD Nintendo', 'fixed', NULL, 250, (SELECT id FROM accounts WHERE name='BHD'), 2, 1, ''),
('Fondo telefono nuevo', 'fixed', NULL, 500, (SELECT id FROM accounts WHERE name='Popular'), 3, 1, ''),
('Suscripciones Lafise', 'fixed', NULL, 300, (SELECT id FROM accounts WHERE name='Lafise pesos'), 4, 1, ''),
('Excedente ahorro/proyectos', 'remainder', 50, NULL, NULL, 5, 1, '50/50 ahorro vs proyectos');
