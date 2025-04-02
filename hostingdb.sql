-- SQLBook: Code
CREATE TABLE user (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(20) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    forename VARCHAR(50) NOT NULL,
    surname VARCHAR(50) NOT NULL,
    birthdate DATE NOT NULL,
    email VARCHAR(50) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    CONSTRAINT chk_email_bad_format CHECK (email REGEXP '^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\\.[A-Za-z]+$'),
    CONSTRAINT chk_forename_initcap CHECK (forename REGEXP '^[A-Z][a-z]*$'),
    CONSTRAINT chk_surname_initcap CHECK (surname REGEXP '^[A-Z][a-z]*$')
);

CREATE TABLE container (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    lxcid VARCHAR(50) NOT NULL UNIQUE,
    user_id INT NOT NULL,
    cpu INT NOT NULL,
    memory INT NOT NULL,
    disk INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    CONSTRAINT chk_cpu_positive CHECK (cpu > 0),
    CONSTRAINT chk_memory_positive CHECK (memory > 0),
    CONSTRAINT chk_disk_positive CHECK (disk > 0),

    FOREIGN KEY (user_id) REFERENCES user(id)
);

CREATE TABLE virtual_machine (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    vmid VARCHAR(50) NOT NULL UNIQUE,
    user_id INT NOT NULL,
    cpu INT NOT NULL,
    memory INT NOT NULL,
    disk INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    CONSTRAINT chk_cpu_positive CHECK (cpu > 0),
    CONSTRAINT chk_memory_positive CHECK (memory > 0),
    CONSTRAINT chk_disk_positive CHECK (disk > 0),
    FOREIGN KEY (user_id) REFERENCES user(id)
);

CREATE TABLE invoice (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    date DATE NOT NULL,
    paid BOOLEAN NOT NULL DEFAULT FALSE,
    CONSTRAINT chk_amount_positive CHECK (amount > 0),
    FOREIGN KEY (user_id) REFERENCES user(id)
);
