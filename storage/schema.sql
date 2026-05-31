PRAGMA foreign_keys = ON;

CREATE TABLE IF NOT EXISTS accounts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    type TEXT,
    currency TEXT DEFAULT 'DOP',
    balance REAL DEFAULT 0,
    purpose TEXT,
    active INTEGER DEFAULT 1
);

CREATE TABLE IF NOT EXISTS movements (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    date TEXT NOT NULL,
    account_origin_id INTEGER NOT NULL,
    account_dest_id INTEGER,
    fixed_expense_id INTEGER,
    savings_rule_id INTEGER,
    type TEXT NOT NULL,
    category TEXT,
    concept TEXT,
    amount REAL NOT NULL,
    currency TEXT DEFAULT 'DOP',
    reimbursable INTEGER DEFAULT 0,
    reimbursed INTEGER DEFAULT 0,
    note TEXT,
    created_at TEXT DEFAULT (datetime('now')),
    FOREIGN KEY(account_origin_id) REFERENCES accounts(id),
    FOREIGN KEY(account_dest_id) REFERENCES accounts(id),
    FOREIGN KEY(fixed_expense_id) REFERENCES fixed_expenses(id),
    FOREIGN KEY(savings_rule_id) REFERENCES savings_rules(id)
);

CREATE TABLE IF NOT EXISTS fixed_expenses (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    amount REAL NOT NULL,
    frequency TEXT NOT NULL,
    every_days INTEGER,
    account_id INTEGER,
    start_date TEXT,
    end_date TEXT,
    active INTEGER DEFAULT 1,
    note TEXT,
    FOREIGN KEY(account_id) REFERENCES accounts(id)
);

CREATE TABLE IF NOT EXISTS financings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    installment_amount REAL NOT NULL,
    frequency TEXT NOT NULL,
    total_payments INTEGER,
    payments_made INTEGER DEFAULT 0,
    start_date TEXT,
    end_date TEXT,
    status TEXT DEFAULT 'activo',
    total_paid REAL DEFAULT 0,
    total_pending REAL DEFAULT 0,
    next_date TEXT,
    note TEXT
);

CREATE TABLE IF NOT EXISTS transport_items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    amount REAL NOT NULL,
    active INTEGER DEFAULT 1
);

CREATE TABLE IF NOT EXISTS work_expenses (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    date TEXT NOT NULL,
    account_id INTEGER NOT NULL,
    concept TEXT NOT NULL,
    amount REAL NOT NULL,
    project TEXT,
    reimbursed INTEGER DEFAULT 0,
    note TEXT,
    FOREIGN KEY(account_id) REFERENCES accounts(id)
);

CREATE TABLE IF NOT EXISTS savings_rules (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    mode TEXT NOT NULL,
    percent REAL,
    amount REAL,
    frequency TEXT DEFAULT 'per_income',
    target_account_id INTEGER,
    priority INTEGER DEFAULT 0,
    active INTEGER DEFAULT 1,
    note TEXT,
    FOREIGN KEY(target_account_id) REFERENCES accounts(id)
);
