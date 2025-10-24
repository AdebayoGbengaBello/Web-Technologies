-- Active: 1760961327235@@127.0.0.1@3306@team_project
CREATE Table Developers(
    developer_id INT(11) NOT NULL AUTO_INCREMENT,
    company_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(15) DEFAULT NULL,
    password VARCHAR(255) NOT NULL,
    PRIMARY KEY (developer_id)
);

CREATE TABLE Products(
    product_id INT(11) NOT NULL AUTO_INCREMENT,
    developer_id INT(11) NOT NULL,
    product_name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    linked_url VARCHAR(255),
    PRIMARY KEY (product_id),
    FOREIGN KEY (developer_id) REFERENCES Developers(developer_id)
);
