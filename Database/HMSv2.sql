create table drug
(
    drugID          int auto_increment
        primary key,
    drugName        varchar(25)  not null,
    drugDescription varchar(100) not null,
    drugPrice       double       not null
);

create table patient
(
    patientID   int auto_increment
        primary key,
    firstName   varchar(25)             not null,
    lastName    varchar(25)             not null,
    dateOfBirth date                    not null,
    gender      enum ('Male', 'Female') not null,
    Phone       varchar(25)             not null,
    Address     varchar(100)            not null
);

create table inpatient
(
    inpatientID   int auto_increment
        primary key,
    patientID     int                                                not null,
    status        enum ('Admitted', 'Discharged') default 'Admitted' not null,
    admissionDate date                                               not null,
    constraint inpatient_ibfk_1
        foreign key (patientID) references patient (patientID)
);

create table dailycheckup
(
    checkupID   int auto_increment
        primary key,
    checkupDate date not null,
    checkupTime time not null,
    inpatientID int  not null,
    constraint dailycheckup_ibfk_1
        foreign key (inpatientID) references inpatient (inpatientID)
);

create index inpatientID
    on dailycheckup (inpatientID);

create index patientID
    on inpatient (patientID);

create table prices
(
    priceID  int auto_increment
        primary key,
    itemName varchar(255)   not null,
    price    decimal(10, 2) not null
);

create table role
(
    roleID          int auto_increment
        primary key,
    roleName        varchar(25)  not null,
    roleDescription varchar(100) not null
);

create table stock
(
    stockID    int auto_increment
        primary key,
    drugID     int  not null,
    quantity   int  not null,
    expiryDate date not null,
    constraint stock_ibfk_1
        foreign key (drugID) references drug (drugID)
);

create index drugID
    on stock (drugID);

create table type
(
    typeID     int auto_increment
        primary key,
    typeName   varchar(25) not null,
    department varchar(25) not null
);

create table user
(
    userID       int auto_increment
        primary key,
    firstName    varchar(25)  not null,
    lastName     varchar(25)  not null,
    Phone        varchar(25)  not null,
    Address      varchar(100) not null,
    username     varchar(50)  not null,
    passwordHash varchar(60)  not null,
    image        varchar(100) not null,
    status       varchar(30)  not null,
    roleID       int          not null,
    constraint user_ibfk_1
        foreign key (roleID) references role (roleID)
);

create table appointment
(
    appointmentID          int auto_increment
        primary key,
    patientID              int                                         not null,
    userID                 int                                         not null,
    appointmentDate        date                                        not null,
    appointmentTime        time                                        not null,
    appointmentDescription varchar(100)                                not null,
    status                 enum ('Scheduled', 'Completed', 'Canceled') not null,
    constraint appointment_ibfk_1
        foreign key (patientID) references patient (patientID),
    constraint appointment_ibfk_2
        foreign key (userID) references user (userID)
);

create index patientID
    on appointment (patientID);

create index userID
    on appointment (userID);

create index roleID
    on user (roleID);

create table visit
(
    visitID       int auto_increment
        primary key,
    visitDate     date                            not null,
    visitTime     time                            not null,
    Diagnosis     varchar(100)                    not null,
    appointmentID int                             not null,
    paid          enum ('yes', 'no') default 'no' not null,
    constraint visit_ibfk_1
        foreign key (appointmentID) references appointment (appointmentID)
);

create table prescription
(
    prescriptionID   int auto_increment
        primary key,
    prescriptionDate date not null,
    visitID          int  not null,
    constraint prescription_ibfk_1
        foreign key (visitID) references visit (visitID)
);

create index visitID
    on prescription (visitID);

create table prescriptiondetails
(
    prescriptionID int         not null,
    drugID         int         not null,
    dose           varchar(25) not null,
    frequency      varchar(25) not null,
    primary key (prescriptionID, drugID),
    constraint prescriptiondetails_ibfk_1
        foreign key (prescriptionID) references prescription (prescriptionID),
    constraint prescriptiondetails_ibfk_2
        foreign key (drugID) references drug (drugID)
);

create index drugID
    on prescriptiondetails (drugID);

create table test
(
    testID     int auto_increment
        primary key,
    testDate   date                            null,
    testResult varchar(100)                    null,
    status     enum ('Scheduled', 'Completed') not null,
    visitID    int                             not null,
    typeID     int                             not null,
    constraint test_ibfk_1
        foreign key (visitID) references visit (visitID),
    constraint test_ibfk_2
        foreign key (typeID) references type (typeID)
);

create index typeID
    on test (typeID);

create index visitID
    on test (visitID);

create index appointmentID
    on visit (appointmentID);

create table vitals
(
    vitalID   int auto_increment
        primary key,
    vitalName varchar(25) not null
);

create table vitaldetails
(
    vitalID    int         not null,
    checkupID  int         not null,
    vitalValue varchar(25) not null,
    primary key (vitalID, checkupID),
    constraint vitaldetails_ibfk_1
        foreign key (vitalID) references vitals (vitalID),
    constraint vitaldetails_ibfk_2
        foreign key (checkupID) references dailycheckup (checkupID)
);

create index checkupID
    on vitaldetails (checkupID);

