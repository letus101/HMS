drop database if exists hms;
create database hms;
use hms;
create table role
(
    roleID int primary key auto_increment,
    roleName varchar(25) not null,
    roleDescription varchar(100) not null
);
create table user(
    userID int primary key auto_increment,
    firstName varchar(25) not null,
    lastName varchar(25) not null,
    Phone varchar(25) not null,
    Address varchar(100) not null,
    username varchar(50) not null,
    passwordHash varchar(60) not null,
    roleID int not null,
    foreign key(roleID) references role (roleID)
);
create table patient(
    patientID int primary key auto_increment,
    firstName varchar(25) not null,
    lastName varchar(25) not null,
    dateOfBirth date not null ,
    gender ENUM('Male','Female') not null,
    Phone varchar(25) not null,
    Address varchar(100) not null
);
create table appointment(
    appointmentID int primary key auto_increment,
    patientID int not null,
    userID int not null,
    appointmentDate date not null,
    appointmentTime time not null,
    appointmentDescription varchar(100) not null,
    status ENUM('Scheduled', 'Completed') not null,
    foreign key(patientID) references patient(patientID),
    foreign key(userID) references user(userID)
);
create table visit(
    visitID int primary key auto_increment,
    visitDate date not null,
    visitTime time not null,
    Diagnosis varchar(100) not null,
    status ENUM('Scheduled', 'Completed') not null,
    appointmentID int not null,
    foreign key(appointmentID) references appointment(appointmentID)
);
create table type(
    typeID int primary key auto_increment,
    typeName varchar(25) not null,
    department varchar(25) not null
);
create table test(
    testID int primary key auto_increment,
    testName varchar(25) not null,
    testDate date not null,
    testResult varchar(100) not null,
    status ENUM('Scheduled', 'Completed') not null,
    visitID int not null,
    typeID int not null,
    foreign key(visitID) references visit(visitID),
    foreign key(typeID) references type(typeID)
);
create table medicalRecord(
   RecordID int primary key auto_increment,
    RecordDate date not null,
    Diagnosis varchar(100) not null,
    Treatment varchar(100) not null,
    patientID int not null,
    foreign key(patientID) references patient(patientID)
);
create table prescription(
    prescriptionID int primary key auto_increment,
    prescriptionDate date not null,
    visitID int not null,
    foreign key(visitID) references visit(visitID)
);
create table drug(
    drugID int primary key auto_increment,
    drugName varchar(25) not null,
    drugDescription varchar(100) not null,
    drugPrice double not null
);
create table prescriptionDetails(
    prescriptionID int not null,
    drugID int not null,
    dose varchar(25) not null,
    frequency varchar(25) not null,
    foreign key(prescriptionID) references prescription(prescriptionID),
    foreign key(drugID) references drug(drugID),
    primary key(prescriptionID,drugID)
);
create table stock(
    stockID int primary key auto_increment,
    drugID int not null,
    quantity int not null,
    expiryDate date not null,
    foreign key(drugID) references drug(drugID)
);
create table inpatient(
    inpatientID int primary key auto_increment,
    patientID int not null,
    roomNo int not null,
    admissionDate date not null,
    dischargeDate date not null,
    foreign key(patientID) references patient(patientID)
);
create table dailycheckup(
    checkupID int primary key auto_increment,
    checkupDate date not null,
    checkupTime time not null,
    notification varchar(100) not null,
    inpatientID int not null,
    foreign key(inpatientID) references inpatient(inpatientID)
);
create table vitals(
    vitalID int primary key auto_increment,
    vitalName varchar(25) not null
);
create table vitalDetails(
    vitalID int not null,
    checkupID int not null,
    vitalValue varchar(25) not null,
    foreign key(vitalID) references vitals(vitalID),
    foreign key(checkupID) references dailycheckup(checkupID),
    primary key(vitalID,checkupID)
);
