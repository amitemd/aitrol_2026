CREATE TABLE patients (
    id INTEGER PRIMARY KEY,
    name TEXT NOT NULL,
    registered_on TEXT NOT NULL
);

CREATE TABLE patient_activity (
    year INTEGER NOT NULL,
    month INTEGER NOT NULL CHECK (month BETWEEN 1 AND 12),
    total INTEGER NOT NULL CHECK (total >= 0),
    admitted INTEGER NOT NULL CHECK (admitted BETWEEN 0 AND total),
    PRIMARY KEY (year, month)
);

CREATE TABLE schedule (
    id INTEGER PRIMARY KEY,
    day TEXT NOT NULL,
    starts_at TEXT NOT NULL,
    ends_at TEXT NOT NULL,
    title TEXT NOT NULL,
    kind TEXT NOT NULL CHECK (kind IN ('appointment', 'consultation', 'surgery'))
);
CREATE INDEX schedule_by_day ON schedule (day, starts_at);

CREATE TABLE ledger (
    id INTEGER PRIMARY KEY,
    income INTEGER NOT NULL CHECK (income >= 0),
    expenses INTEGER NOT NULL CHECK (expenses >= 0)
);

CREATE TABLE rooms (
    id INTEGER PRIMARY KEY,
    label TEXT NOT NULL,
    capacity INTEGER NOT NULL CHECK (capacity >= 0),
    occupied INTEGER NOT NULL CHECK (occupied BETWEEN 0 AND capacity)
);

CREATE TABLE reports (
    id INTEGER PRIMARY KEY,
    title TEXT NOT NULL,
    detail TEXT NOT NULL,
    area TEXT NOT NULL,
    status TEXT NOT NULL
);

INSERT INTO ledger (income, expenses) VALUES (86300, 64120), (39450, 27680);
INSERT INTO rooms (label, capacity, occupied) VALUES ('Hospitalización general', 60, 43), ('Habitaciones privadas', 28, 18);
INSERT INTO reports (title, detail, area, status) VALUES
    ('Inventario de insumos', 'Revisar el stock mínimo del turno de tarde.', 'Farmacia', 'En revisión'),
    ('Equipo pendiente de revisión', 'Mantenimiento preventivo programado para el próximo turno.', 'Hospitalización', 'Pendiente');
