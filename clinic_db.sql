CREATE DATABASE IF NOT EXISTS clinic_db;
USE clinic_db;

CREATE TABLE doctor (
    docID INT PRIMARY KEY AUTO_INCREMENT,
    docFName VARCHAR(50),
    docLName VARCHAR(50),
    docAddress VARCHAR(100),
    docSpecial VARCHAR(50)
);

CREATE TABLE patient (
    patID INT PRIMARY KEY AUTO_INCREMENT,
    patFName VARCHAR(50),
    patLName VARCHAR(50),
    patBDate DATE,
    patTelNo VARCHAR(20)
);

CREATE TABLE consultation (
    consultID INT PRIMARY KEY AUTO_INCREMENT,
    patID INT,
    docID INT,
    consultDate DATETIME,
    diagnosis TEXT,
    prescription TEXT,
    FOREIGN KEY (patID) REFERENCES patient(patID),
    FOREIGN KEY (docID) REFERENCES doctor(docID)
);