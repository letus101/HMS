-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 02, 2024 at 12:57 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hms`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointment`
--

CREATE TABLE `appointment` (
  `appointmentID` int(11) NOT NULL,
  `patientID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `appointmentDate` date NOT NULL,
  `appointmentTime` time NOT NULL,
  `appointmentDescription` varchar(100) NOT NULL,
  `status` enum('Scheduled','Completed','Canceled') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dailycheckup`
--

CREATE TABLE `dailycheckup` (
  `checkupID` int(11) NOT NULL,
  `checkupDate` date NOT NULL,
  `checkupTime` time NOT NULL,
  `inpatientID` int(11) NOT NULL,
  `userID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `drug`
--

CREATE TABLE `drug` (
  `drugID` int(11) NOT NULL,
  `drugName` varchar(25) NOT NULL,
  `drugDescription` varchar(100) NOT NULL,
  `drugPrice` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `drug`
--

INSERT INTO `drug` (`drugID`, `drugName`, `drugDescription`, `drugPrice`) VALUES
(1, 'augmentin', 'amoxicilline', 168.2),
(2, 'doligrippe', 'paracétamol', 20),
(3, 'doliprane', 'paracetamol', 23);

-- --------------------------------------------------------

--
-- Table structure for table `inpatient`
--

CREATE TABLE `inpatient` (
  `inpatientID` int(11) NOT NULL,
  `patientID` int(11) NOT NULL,
  `status` enum('Admitted','Discharged') NOT NULL DEFAULT 'Admitted',
  `admissionDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patient`
--

CREATE TABLE `patient` (
  `patientID` int(11) NOT NULL,
  `firstName` varchar(25) NOT NULL,
  `lastName` varchar(25) NOT NULL,
  `dateOfBirth` date NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `Phone` varchar(25) NOT NULL,
  `Address` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `prescription`
--

CREATE TABLE `prescription` (
  `prescriptionID` int(11) NOT NULL,
  `prescriptionDate` date NOT NULL,
  `visitID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `prescriptiondetails`
--

CREATE TABLE `prescriptiondetails` (
  `prescriptionID` int(11) NOT NULL,
  `drugID` int(11) NOT NULL,
  `dose` varchar(25) NOT NULL,
  `frequency` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `prices`
--

CREATE TABLE `prices` (
  `priceID` int(11) NOT NULL,
  `itemName` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prices`
--

INSERT INTO `prices` (`priceID`, `itemName`, `price`) VALUES
(1, 'visit', 300.00),
(2, 'test', 200.00),
(3, 'dailyHospitalStay', 500.00);

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `roleID` int(11) NOT NULL,
  `roleName` varchar(25) NOT NULL,
  `roleDescription` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`roleID`, `roleName`, `roleDescription`) VALUES
(1, 'Admin', 'Admin'),
(2, 'Doctor', 'Doctor'),
(3, 'Nurse', 'Nurse'),
(4, 'Receptionist', 'Receptionist'),
(5, 'Pharmacist', 'Pharmacist'),
(6, 'Lab Technician', 'Lab Technician'),
(7, 'Radiologist', 'Radiologist');

-- --------------------------------------------------------

--
-- Table structure for table `stock`
--

CREATE TABLE `stock` (
  `stockID` int(11) NOT NULL,
  `drugID` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `expiryDate` date NOT NULL,
  `arrivalDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock`
--

INSERT INTO `stock` (`stockID`, `drugID`, `quantity`, `expiryDate`, `arrivalDate`) VALUES
(1, 1, 5, '2024-07-28', '2023-12-30'),
(3, 2, 13, '2026-11-28', '2023-12-30'),
(4, 3, 1, '2025-11-29', '2023-12-30'),
(5, 1, 30, '2025-12-30', '2023-12-30');

-- --------------------------------------------------------

--
-- Table structure for table `test`
--

CREATE TABLE `test` (
  `testID` int(11) NOT NULL,
  `testDate` date DEFAULT NULL,
  `testResult` varchar(100) DEFAULT NULL,
  `status` enum('Scheduled','Completed') NOT NULL,
  `visitID` int(11) NOT NULL,
  `typeID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `type`
--

CREATE TABLE `type` (
  `typeID` int(11) NOT NULL,
  `typeName` varchar(25) NOT NULL,
  `department` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `type`
--

INSERT INTO `type` (`typeID`, `typeName`, `department`) VALUES
(1, 'Lipid Profile', 'laboratory'),
(2, 'Complete Blood Count', 'laboratory'),
(3, 'Computed tomography ', 'radiology'),
(4, 'Magnetic resonance imagin', 'radiology');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `userID` int(11) NOT NULL,
  `firstName` varchar(25) NOT NULL,
  `lastName` varchar(25) NOT NULL,
  `Phone` varchar(25) NOT NULL,
  `Address` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `passwordHash` varchar(60) NOT NULL,
  `image` varchar(100) NOT NULL,
  `status` varchar(30) NOT NULL,
  `roleID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`userID`, `firstName`, `lastName`, `Phone`, `Address`, `username`, `passwordHash`, `image`, `status`, `roleID`) VALUES
(1, 'jhon', 'doe', '0625658456', 'Admin', 'admin', '$2y$10$ri9EqiMB.g/.iJlRthReAeGbBbw8bQGhQrwen4xLCmDRsi6OhEvpC', 'admin.jpg', 'active', 1),
(10, 'recep', 'test', '0600000000', 'test', 'recep.test@hospitalink.com', '$2y$10$YG6KZ.I.VYIZJkqryNJpPu3L6wEf7Vt59U9Y8kDNEgNQkJ.dzQh5q', 'recep.test@hospitalink.com.jpg', 'active', 4),
(11, 'doc', 'test', '0611111111', 'test', 'doc.test@hospitalink.com', '$2y$10$SACpmSJWVQhA2ClFSp3UyOhwfOO76lMEytcg0vJLIeuaB9sJvAOdi', 'doc.test@hospitalink.com.jpg', 'active', 2),
(12, 'nurse', 'test', '0622222222', 'test', 'nurse.test@hospitalink.com', '$2y$10$sIKWHGwQwWQ8dtzni1ywjOU12UfuKrNwZTDdor21E7mkCHQ2TkBzy', 'nurse.test@hospitalink.com.jpg', 'active', 3),
(13, 'lab', 'test', '0633333333', 'test', 'lab.test@hospitalink.com', '$2y$10$kUwrjdO.ZF7qmzh6oXb8LO6J5Sc6v4AEPReIST/Mrwyaz9FBXCyeS', 'lab.test@hospitalink.com.jpg', 'active', 6),
(14, 'rad', 'test', '0655555555', 'test', 'rad.test@hospitalink.com', '$2y$10$RuSrPCcixmfODrGzECg.wuFFdGBzdpv5Upn6ci6ruDWP26pngG8oO', 'rad.test@hospitalink.com.jpg', 'active', 7),
(15, 'phar', 'test', '0688888888', 'test', 'phar.test@hospitalink.com', '$2y$10$RxPfY4WrguXJgfx/4gc9mu1QzjMsAW7D4NvLtXVvL5Pa44tr6hlNC', 'phar.test@hospitalink.com.jpg', 'active', 5);

-- --------------------------------------------------------

--
-- Table structure for table `visit`
--

CREATE TABLE `visit` (
  `visitID` int(11) NOT NULL,
  `visitDate` date NOT NULL,
  `visitTime` time NOT NULL,
  `Diagnosis` varchar(100) NOT NULL,
  `appointmentID` int(11) NOT NULL,
  `paid` enum('yes','no') NOT NULL DEFAULT 'no'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vitaldetails`
--

CREATE TABLE `vitaldetails` (
  `vitalID` int(11) NOT NULL,
  `checkupID` int(11) NOT NULL,
  `vitalValue` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vitals`
--

CREATE TABLE `vitals` (
  `vitalID` int(11) NOT NULL,
  `vitalName` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vitals`
--

INSERT INTO `vitals` (`vitalID`, `vitalName`) VALUES
(1, 'Body temperature'),
(2, 'Blood pressure');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointment`
--
ALTER TABLE `appointment`
  ADD PRIMARY KEY (`appointmentID`),
  ADD KEY `patientID` (`patientID`),
  ADD KEY `userID` (`userID`);

--
-- Indexes for table `dailycheckup`
--
ALTER TABLE `dailycheckup`
  ADD PRIMARY KEY (`checkupID`),
  ADD KEY `inpatientID` (`inpatientID`),
  ADD KEY `FK3` (`userID`);

--
-- Indexes for table `drug`
--
ALTER TABLE `drug`
  ADD PRIMARY KEY (`drugID`);

--
-- Indexes for table `inpatient`
--
ALTER TABLE `inpatient`
  ADD PRIMARY KEY (`inpatientID`),
  ADD KEY `patientID` (`patientID`);

--
-- Indexes for table `patient`
--
ALTER TABLE `patient`
  ADD PRIMARY KEY (`patientID`);

--
-- Indexes for table `prescription`
--
ALTER TABLE `prescription`
  ADD PRIMARY KEY (`prescriptionID`),
  ADD KEY `visitID` (`visitID`);

--
-- Indexes for table `prescriptiondetails`
--
ALTER TABLE `prescriptiondetails`
  ADD PRIMARY KEY (`prescriptionID`,`drugID`),
  ADD KEY `drugID` (`drugID`);

--
-- Indexes for table `prices`
--
ALTER TABLE `prices`
  ADD PRIMARY KEY (`priceID`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`roleID`);

--
-- Indexes for table `stock`
--
ALTER TABLE `stock`
  ADD PRIMARY KEY (`stockID`),
  ADD KEY `drugID` (`drugID`);

--
-- Indexes for table `test`
--
ALTER TABLE `test`
  ADD PRIMARY KEY (`testID`),
  ADD KEY `visitID` (`visitID`),
  ADD KEY `typeID` (`typeID`);

--
-- Indexes for table `type`
--
ALTER TABLE `type`
  ADD PRIMARY KEY (`typeID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`userID`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `roleID` (`roleID`);

--
-- Indexes for table `visit`
--
ALTER TABLE `visit`
  ADD PRIMARY KEY (`visitID`),
  ADD KEY `appointmentID` (`appointmentID`);

--
-- Indexes for table `vitaldetails`
--
ALTER TABLE `vitaldetails`
  ADD PRIMARY KEY (`vitalID`,`checkupID`),
  ADD KEY `checkupID` (`checkupID`);

--
-- Indexes for table `vitals`
--
ALTER TABLE `vitals`
  ADD PRIMARY KEY (`vitalID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointment`
--
ALTER TABLE `appointment`
  MODIFY `appointmentID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dailycheckup`
--
ALTER TABLE `dailycheckup`
  MODIFY `checkupID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `drug`
--
ALTER TABLE `drug`
  MODIFY `drugID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `inpatient`
--
ALTER TABLE `inpatient`
  MODIFY `inpatientID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patient`
--
ALTER TABLE `patient`
  MODIFY `patientID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `prescription`
--
ALTER TABLE `prescription`
  MODIFY `prescriptionID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `prices`
--
ALTER TABLE `prices`
  MODIFY `priceID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `roleID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `stock`
--
ALTER TABLE `stock`
  MODIFY `stockID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `test`
--
ALTER TABLE `test`
  MODIFY `testID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `type`
--
ALTER TABLE `type`
  MODIFY `typeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `visit`
--
ALTER TABLE `visit`
  MODIFY `visitID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vitals`
--
ALTER TABLE `vitals`
  MODIFY `vitalID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointment`
--
ALTER TABLE `appointment`
  ADD CONSTRAINT `appointment_ibfk_1` FOREIGN KEY (`patientID`) REFERENCES `patient` (`patientID`),
  ADD CONSTRAINT `appointment_ibfk_2` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`);

--
-- Constraints for table `dailycheckup`
--
ALTER TABLE `dailycheckup`
  ADD CONSTRAINT `FK3` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`),
  ADD CONSTRAINT `dailycheckup_ibfk_1` FOREIGN KEY (`inpatientID`) REFERENCES `inpatient` (`inpatientID`);

--
-- Constraints for table `inpatient`
--
ALTER TABLE `inpatient`
  ADD CONSTRAINT `inpatient_ibfk_1` FOREIGN KEY (`patientID`) REFERENCES `patient` (`patientID`);

--
-- Constraints for table `prescription`
--
ALTER TABLE `prescription`
  ADD CONSTRAINT `prescription_ibfk_1` FOREIGN KEY (`visitID`) REFERENCES `visit` (`visitID`);

--
-- Constraints for table `prescriptiondetails`
--
ALTER TABLE `prescriptiondetails`
  ADD CONSTRAINT `prescriptiondetails_ibfk_1` FOREIGN KEY (`prescriptionID`) REFERENCES `prescription` (`prescriptionID`),
  ADD CONSTRAINT `prescriptiondetails_ibfk_2` FOREIGN KEY (`drugID`) REFERENCES `drug` (`drugID`);

--
-- Constraints for table `stock`
--
ALTER TABLE `stock`
  ADD CONSTRAINT `stock_ibfk_1` FOREIGN KEY (`drugID`) REFERENCES `drug` (`drugID`);

--
-- Constraints for table `test`
--
ALTER TABLE `test`
  ADD CONSTRAINT `test_ibfk_1` FOREIGN KEY (`visitID`) REFERENCES `visit` (`visitID`),
  ADD CONSTRAINT `test_ibfk_2` FOREIGN KEY (`typeID`) REFERENCES `type` (`typeID`);

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`roleID`) REFERENCES `role` (`roleID`);

--
-- Constraints for table `visit`
--
ALTER TABLE `visit`
  ADD CONSTRAINT `visit_ibfk_1` FOREIGN KEY (`appointmentID`) REFERENCES `appointment` (`appointmentID`);

--
-- Constraints for table `vitaldetails`
--
ALTER TABLE `vitaldetails`
  ADD CONSTRAINT `vitaldetails_ibfk_1` FOREIGN KEY (`vitalID`) REFERENCES `vitals` (`vitalID`),
  ADD CONSTRAINT `vitaldetails_ibfk_2` FOREIGN KEY (`checkupID`) REFERENCES `dailycheckup` (`checkupID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
