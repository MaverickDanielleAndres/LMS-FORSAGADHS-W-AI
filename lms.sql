-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 09, 2025 at 06:21 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lms`
--

-- --------------------------------------------------------

--
-- Table structure for table `accountquerymaster`
--

CREATE TABLE `accountquerymaster` (
  `QueryId` int(10) NOT NULL,
  `QueryFromId` int(10) NOT NULL,
  `QueryTopic` varchar(50) NOT NULL,
  `QueryQuestion` text NOT NULL,
  `QueryReply` text NOT NULL,
  `Querystatus` int(1) NOT NULL,
  `QueryGenDate` date NOT NULL,
  `QueryRepDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `accountquerymaster`
--

INSERT INTO `accountquerymaster` (`QueryId`, `QueryFromId`, `QueryTopic`, `QueryQuestion`, `QueryReply`, `Querystatus`, `QueryGenDate`, `QueryRepDate`) VALUES
(5, 23, 'Account Related Help', 'Change Contact number to 1234567896', 'your query will not solve', 2, '2022-03-29', '2022-04-05');

-- --------------------------------------------------------

--
-- Table structure for table `assignmentmaster`
--

CREATE TABLE `assignmentmaster` (
  `AssignmentId` int(10) NOT NULL,
  `AssignmentTitle` varchar(50) NOT NULL,
  `AssignmentDesc` text NOT NULL,
  `AssignmentSubject` int(50) NOT NULL,
  `AssignmentBranch` int(10) NOT NULL,
  `AssignmentStatus` varchar(20) NOT NULL,
  `AssignmentUploadedBy` int(10) NOT NULL,
  `AssignmentFile` varchar(100) NOT NULL,
  `AssignmentUploaddate` date NOT NULL,
  `AssignmentForSemester` int(1) NOT NULL,
  `AssignmentSubmissionDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assignmentmaster`
--

INSERT INTO `assignmentmaster` (`AssignmentId`, `AssignmentTitle`, `AssignmentDesc`, `AssignmentSubject`, `AssignmentBranch`, `AssignmentStatus`, `AssignmentUploadedBy`, `AssignmentFile`, `AssignmentUploaddate`, `AssignmentForSemester`, `AssignmentSubmissionDate`) VALUES
(41, 'Sample Assignment', 'description', 20240301, 3, '1', 25, 'Sample Assignment2024-12-12.pdf', '2024-12-12', 11, '2024-12-13'),
(42, 'sample assignment', 'sample', 20240202, 2, '1', 20, 'sample assignment2024-12-12.pdf', '2024-12-12', 2, '2024-12-14'),
(43, 'sample', 'sample', 20240101, 1, '1', 12, 'sample2024-12-12.pdf', '2024-12-12', 1, '2024-12-16'),
(44, 'sampleadding', 'sample', 20240101, 1, '1', 12, 'sampleadding2024-12-12.pdf', '2024-12-12', 1, '2024-12-15');

-- --------------------------------------------------------

--
-- Table structure for table `branchmaster`
--

CREATE TABLE `branchmaster` (
  `BranchId` int(10) NOT NULL,
  `BranchName` varchar(30) NOT NULL,
  `BranchCode` varchar(10) NOT NULL,
  `BranchSemesters` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `branchmaster`
--

INSERT INTO `branchmaster` (`BranchId`, `BranchName`, `BranchCode`, `BranchSemesters`) VALUES
(1, 'HUMSS', '001', 2),
(2, 'ABM', '002', 2),
(3, 'STEM', '003', 2);

-- --------------------------------------------------------

--
-- Table structure for table `facultymaster`
--

CREATE TABLE `facultymaster` (
  `FacultyId` int(10) NOT NULL,
  `FacultyUserName` varchar(20) NOT NULL,
  `FacultyPassword` varchar(200) NOT NULL,
  `FacultyFirstName` varchar(20) NOT NULL,
  `FacultyMiddleName` varchar(20) NOT NULL,
  `FacultyLastName` varchar(20) NOT NULL,
  `FacultyProfilePic` varchar(100) NOT NULL,
  `FacultyBranchCode` varchar(20) NOT NULL,
  `FacultySection` int(20) NOT NULL,
  `FacultyEmail` varchar(50) NOT NULL,
  `FacultyContactNo` varchar(20) NOT NULL,
  `FacultyQualification` varchar(50) NOT NULL,
  `FacultyOffice` varchar(10) NOT NULL,
  `FacultyCode` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `facultymaster`
--

INSERT INTO `facultymaster` (`FacultyId`, `FacultyUserName`, `FacultyPassword`, `FacultyFirstName`, `FacultyMiddleName`, `FacultyLastName`, `FacultyProfilePic`, `FacultyBranchCode`, `FacultySection`, `FacultyEmail`, `FacultyContactNo`, `FacultyQualification`, `FacultyOffice`, `FacultyCode`) VALUES
(12, 'FAHUMSS2', '1234', 'TeacherHtwo', 'N', 'TeacherHtwo', 'HUMSS2.png', '001', 3, 'humss2@gmail.com', '9418596475', 'M.E.', 'H-102', 'HUMSS2'),
(13, 'FAABM3', '1234', 'Mary', 'E', 'Taylor', 'ABM3.png', '002', 2, 'mary@gmail.com', '9273176316', 'B.E.', 'A-102', 'ABM3'),
(19, 'FAHUMSS1', '1234', 'Teacher 1', 'Teacher 1', 'Teacher 1', 'HUMSS1.png', '001', 3, 'teacher1@gmail.com', '9429794513', 'BSHM', 'H-101', 'HUMSS'),
(20, 'FAABM2', '1234', 'David', 'M', 'Johnson', 'ABM2.png', '002', 5, 'dav@gmail.com', '9895124569', 'B.E. ', 'A-101', 'ABM2'),
(21, 'FASTEM2', '1234', 'Robert', 'W', 'Smith', 'STEM2.png', '003', 4, 'robert@gmail.com', '9283812312', 'B.E.(Civil)', 'S-102', 'STEM2'),
(24, 'FAABM1', '1234', 'Patricia', 'A', 'Martinez', 'ABM1.png', '002', 2, 'pat@gmail.com', '9283127318', 'B.E. (IT)', 'A - 101', 'ABM1'),
(25, 'FASTEM1', '1234', 'IanEmmanuel', 'B', 'Palabrica', 'STEM1.png', '003', 1, 'ian@gmail.com', '9172312312', 'BSIT', 'S-101', 'STEM1'),
(39, 'FASTEM3', '1234', 'dwauhdawbw', 'dawduah', 'jkbwadu', 'STEM3.png', '003', 7, 'cuawbduwa@gmail.com', '9231823132', 'BSCS', 'S-103', 'STEM3'),
(40, 'FAHUMSS3', '1234', 'female', 'female', 'female', 'HUMSS3.png', '001', 9, 'female@gmail.com', '9237123172', 'Grad', 'H-103', 'HUMSS3'),
(41, 'FA12312412', '$2y$10$TdHThxYF/ZZFlvo6BaA2OeRRNidLIUqgaOl0D/j5sjpWMpA8Z7Swy', 'DFGA', 'ADFG', 'ADFG', '12312412.png', '001', 7, '234@gmail.com', '9632968188', '234', '234', '12312412'),
(42, 'FA2342352362', '$2a$12$GeqZkl.cxdLEA7vjd8wbI.t4vWZDpUFDii/AfbxjFqHLQdDHyFsvK', 'gfhfgh', 'adfgsdh', 'adfhadfh', '2342352362.png', '001', 6, 'dfgsdf@gmail.com', '9632968188', 'adfasdf', 'asdf', '2342352362');

-- --------------------------------------------------------

--
-- Table structure for table `institutemaster`
--

CREATE TABLE `institutemaster` (
  `InstituteId` int(10) NOT NULL,
  `InstituteUserName` varchar(20) NOT NULL,
  `InstitutePassword` varchar(300) NOT NULL,
  `InstituteName` varchar(50) NOT NULL,
  `InstituteRole` varchar(20) NOT NULL,
  `InstituteProfilePic` varchar(100) NOT NULL,
  `InstituteEmail` varchar(50) NOT NULL,
  `InstituteContactNo` varchar(20) NOT NULL,
  `InstituteAddress` varchar(200) NOT NULL,
  `InstituteOffice` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `institutemaster`
--

INSERT INTO `institutemaster` (`InstituteId`, `InstituteUserName`, `InstitutePassword`, `InstituteName`, `InstituteRole`, `InstituteProfilePic`, `InstituteEmail`, `InstituteContactNo`, `InstituteAddress`, `InstituteOffice`) VALUES
(1, 'INADMIN', '$2a$12$GeqZkl.cxdLEA7vjd8wbI.t4vWZDpUFDii/AfbxjFqHLQdDHyFsvK', 'Mr. ADMIN', 'Admin', 'INADMIN.png', 'aj@gmail.com', '1234567890', 'RHS', 'A-999');

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_type` varchar(20) NOT NULL,
  `attempts` int(11) DEFAULT 0,
  `last_attempt` timestamp NOT NULL DEFAULT current_timestamp(),
  `lockout_time` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_attempts`
--

INSERT INTO `login_attempts` (`id`, `ip_address`, `user_type`, `attempts`, `last_attempt`, `lockout_time`) VALUES
(1, '::1', 'FA', 8, '2025-07-07 23:28:43', '2025-07-07 17:31:43');

-- --------------------------------------------------------

--
-- Table structure for table `sectionmaster`
--

CREATE TABLE `sectionmaster` (
  `SectionId` int(20) NOT NULL,
  `SectionNumber` varchar(20) NOT NULL,
  `SectionBranch` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sectionmaster`
--

INSERT INTO `sectionmaster` (`SectionId`, `SectionNumber`, `SectionBranch`) VALUES
(1, '1-Diamond', 'STEM'),
(2, '1-Dollars', 'ABM'),
(3, '1-Aristotle', 'HUMSS'),
(4, '2-Emerald', 'STEM'),
(5, '2-Yen', 'ABM'),
(6, '2-Plato', 'HUMSS'),
(7, '3-Ruby', 'STEM'),
(8, '3-Peso', 'ABM'),
(9, '3-Socrates', 'HUMSS');

-- --------------------------------------------------------

--
-- Table structure for table `studentassignment`
--

CREATE TABLE `studentassignment` (
  `SAssignmentId` int(10) NOT NULL,
  `SAssignmentUploaderId` int(10) NOT NULL,
  `AssignmentId` int(10) NOT NULL,
  `SAssignmentFile` varchar(200) NOT NULL,
  `SAssignmentUploadDate` date NOT NULL,
  `SAssignmentStatus` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `studentassignment`
--

INSERT INTO `studentassignment` (`SAssignmentId`, `SAssignmentUploaderId`, `AssignmentId`, `SAssignmentFile`, `SAssignmentUploadDate`, `SAssignmentStatus`) VALUES
(59, 52, 44, '202412006918_44.pdf', '2024-12-12', 3);

-- --------------------------------------------------------

--
-- Table structure for table `studentmaster`
--

CREATE TABLE `studentmaster` (
  `StudentId` int(10) NOT NULL,
  `StudentEnrollmentNo` bigint(20) NOT NULL,
  `StudentUserName` varchar(19) NOT NULL,
  `StudentPassword` varchar(300) NOT NULL,
  `StudentFirstName` varchar(20) NOT NULL,
  `StudentMiddleName` varchar(20) NOT NULL,
  `StudentLastName` varchar(20) NOT NULL,
  `StudentProfilePic` varchar(100) NOT NULL,
  `StudentDOB` date NOT NULL,
  `StudentBranchCode` varchar(20) NOT NULL,
  `StudentSection` int(20) NOT NULL,
  `StudentSemester` int(1) NOT NULL,
  `StudentEmail` varchar(50) NOT NULL,
  `StudentContactNo` bigint(20) NOT NULL,
  `StudentAddress` varchar(200) NOT NULL,
  `ParentEmail` varchar(50) NOT NULL,
  `ParentContactNo` bigint(20) NOT NULL,
  `StudentRollNo` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `studentmaster`
--

INSERT INTO `studentmaster` (`StudentId`, `StudentEnrollmentNo`, `StudentUserName`, `StudentPassword`, `StudentFirstName`, `StudentMiddleName`, `StudentLastName`, `StudentProfilePic`, `StudentDOB`, `StudentBranchCode`, `StudentSection`, `StudentSemester`, `StudentEmail`, `StudentContactNo`, `StudentAddress`, `ParentEmail`, `ParentContactNo`, `StudentRollNo`) VALUES
(18, 202412006915, 'ST202412006915', '1234', 'Marie', 'Gonzales', 'Castillo', '202412006915.png', '2003-09-05', '002', 8, 2, 'marie@gmail.com', 9418599999, 'New beloved St.', 'marieparent@gmail.com', 9885621522, 6),
(23, 202412006913, 'ST202412006913', '$2y$10$gYjbhOiuXQeuqccPkS5CF.bYQUPQkAutFFupO14WnyKjS9Z/7Kuq.', 'Adrian Rusell', 'Rambutan', 'Tajan', '202412006913.png', '2003-11-28', '002', 2, 1, 'adrian@gmail.com', 9418524567, 'Sto Tomas', 'adrianparent@gmail.com', 7527895422, 4),
(50, 202412006914, 'ST202412006914', '1234', 'Danilo', 'Pogi', 'Gonzales', '202412006914.png', '2003-11-01', '003', 4, 1, 'danilogatch@gmail.com', 9936602786, 'Bagong Ilog, Pasig City', 'dani@gmail.com', 9998887777, 1),
(51, 202412006917, 'ST202412006917', '1234', 'MaverickDanielle', 'Pangan', 'Andres', '202412006917.png', '2004-04-01', '003', 8, 1, 'maverick@gmail.com', 9921439880, 'adress ni Mavs', 'parent@gmail.com', 9876543210, 7),
(52, 202412006918, 'ST202412006918', '1234', 'Kathleen', 'Sheesh', 'Dayne', '202412006918.png', '2004-09-10', '001', 6, 1, 'kath@gmail.com', 9873137162, 'Cainta Pasig City', 'kathparent@gmail.com', 9338392183, 9),
(53, 453567980987, 'ST453567980987', '$2y$10$c8SfgqYcV184i1aZJ7hMH.bfSGbmxVynhhOdPmWx3y0EFN0kLyaXK', 'ASDF', 'ASDF', 'ASDF', '453567980987.png', '2009-06-17', '001', 5, 3, 'ASDF@GMAIL.COM', 9632968188, '333 cp santos st. ugong pasig city', '4678689743@GMAIL.COM', 2345566234, 458);

-- --------------------------------------------------------

--
-- Table structure for table `studymaterialmaster`
--

CREATE TABLE `studymaterialmaster` (
  `MaterialId` int(10) NOT NULL,
  `SubjectCode` int(10) NOT NULL,
  `SubjectUnitNo` int(10) NOT NULL,
  `MaterialCode` varchar(50) NOT NULL,
  `SubjectUnitName` varchar(200) NOT NULL,
  `EngMaterialFile` varchar(100) NOT NULL,
  `GujMaterialFile` varchar(100) NOT NULL,
  `MaterialUploadDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `studymaterialmaster`
--

INSERT INTO `studymaterialmaster` (`MaterialId`, `SubjectCode`, `SubjectUnitNo`, `MaterialCode`, `SubjectUnitName`, `EngMaterialFile`, `GujMaterialFile`, `MaterialUploadDate`) VALUES
(1, 20240101, 2, '3330707_2', 'Electronics Knowledge', '3330707_2_ENG.pdf', '3330707_2_GUJ.pdf', '2022-02-21'),
(3, 20240101, 1, '3330707_1', 'Wiring Basics', '3330707_1_ENG.pdf', '3330707_1_GUJ.pdf', '2022-02-21'),
(4, 20240201, 1, '3350701_1', 'Basics of C Programming', '3350701_1_ENG.pdf', '3350701_1_GUJ.pdf', '2022-03-29'),
(6, 20240201, 2, '3350701_2', 'Data Types & Variables', '3350701_2_ENG.pdf', '3350701_2_GUJ.pdf', '2022-03-19'),
(12, 20240201, 5, '3350701_5', 'Wiring Basics', '3350701_5_ENG.pdf', '3350701_5_GUJ.pdf', '2022-03-23'),
(13, 20240101, 4, '3330707_4', 'ABC', '3330707_4_ENG.pdf', '3330707_4_GUJ.pdf', '2022-03-23'),
(16, 20240101, 3, '3330707_3', 'XYZ', '3330707_3_ENG.pdf', '3330707_3_GUJ.pdf', '2022-03-23'),
(17, 20240102, 1, '333701_1', 'test', '333701_1_ENG.pdf', '333701_1_GUJ.pdf', '2022-03-26'),
(19, 20240301, 1, '20240301_1', 'Sample Material', '20240301_1_ENG.pdf', '20240301_1_GUJ.pdf', '2024-12-12');

-- --------------------------------------------------------

--
-- Table structure for table `studyquerymaster`
--

CREATE TABLE `studyquerymaster` (
  `QueryId` int(10) NOT NULL,
  `QueryFromId` int(10) NOT NULL,
  `QueryToId` int(10) NOT NULL,
  `QueryTopic` varchar(50) NOT NULL,
  `QueryQuestion` text NOT NULL,
  `QueryReply` varchar(100) NOT NULL,
  `Querystatus` int(1) NOT NULL,
  `QuerySubject` int(10) DEFAULT NULL,
  `QueryDocument` varchar(50) NOT NULL,
  `QueryGenDate` date NOT NULL,
  `QueryRepDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `studyquerymaster`
--

INSERT INTO `studyquerymaster` (`QueryId`, `QueryFromId`, `QueryToId`, `QueryTopic`, `QueryQuestion`, `QueryReply`, `Querystatus`, `QuerySubject`, `QueryDocument`, `QueryGenDate`, `QueryRepDate`) VALUES
(24, 23, 13, 'Binary Conversion ', 'Sir , Binary Conversion  baki 6e , nthi avadtu .. ! .😁😁😁😁😁', '', 1, 20240202, '23_1645588207.jpg', '2022-02-23', '0000-00-00'),
(31, 18, 12, 'Can you Teach me ', 'Electric Circuits: Components, Types, and Related Concepts Electric Circuits', 'ok', 2, 20240101, '18_1647400669.jpg', '2022-03-16', '2022-03-17'),
(58, 23, 13, '', '', '', 1, 20240202, '', '2022-04-05', '0000-00-00'),
(59, 50, 25, 'pwede pakopya?', 'kkopya ako', 'AYOKKO NGA!', 2, 20240303, '', '2024-11-30', '2024-11-30');

-- --------------------------------------------------------

--
-- Table structure for table `subjectmaster`
--

CREATE TABLE `subjectmaster` (
  `SubjectId` int(10) NOT NULL,
  `SubjectCode` int(10) NOT NULL,
  `SubjectName` varchar(50) NOT NULL,
  `SubjectBranch` int(10) NOT NULL,
  `SubjectSemester` int(1) NOT NULL,
  `SubjectFacultyId` int(11) NOT NULL,
  `SubjectSyllabus` varchar(100) NOT NULL,
  `SemCode` varchar(20) NOT NULL,
  `SubjectPic` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjectmaster`
--

INSERT INTO `subjectmaster` (`SubjectId`, `SubjectCode`, `SubjectName`, `SubjectBranch`, `SubjectSemester`, `SubjectFacultyId`, `SubjectSyllabus`, `SemCode`, `SubjectPic`) VALUES
(24, 20240101, 'Oral Communication Context', 1, 1, 12, 'HUMSS002.pdf', '004_1', 'HUMSS002.png'),
(25, 20240102, 'Reading and Writing Skills', 1, 1, 19, 'HUMSS001.pdf', '004_2', 'HUMSS001.png'),
(26, 20240201, 'Business Math', 2, 2, 24, 'ABM001.pdf', '003_1', 'ABM001.png'),
(27, 20240202, 'Oral Communication Context', 2, 2, 20, 'ABM002.pdf', '003_1', 'ABM002.png'),
(29, 20240203, 'Business Finance', 2, 2, 13, 'ABM003.pdf', '003_5', 'ABM003.png'),
(40, 20240303, 'General Biology', 3, 1, 39, 'STEM003.pdf', '005_1', 'STEM003.png'),
(43, 20240302, 'General Mathematics', 3, 1, 21, 'STEM002.pdf', '005_1', 'STEM002.png'),
(44, 20240301, 'Basic Calculus', 3, 11, 25, 'STEM001.pdf', '005_4', 'STEM001.png'),
(60, 20240103, 'Understanding Culture, Society and Politics', 1, 1, 40, 'HUMSS003.pdf', '004_2', 'HUMSS003.png');

-- --------------------------------------------------------

--
-- Table structure for table `timetablemaster`
--

CREATE TABLE `timetablemaster` (
  `TimetableId` int(10) NOT NULL,
  `TimetableBranchCode` varchar(50) NOT NULL,
  `TimetableSemester` int(10) NOT NULL,
  `TimetableUploadedBy` varchar(30) NOT NULL,
  `TimetableUploadTime` datetime NOT NULL,
  `TimetableImage` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `timetablemaster`
--

INSERT INTO `timetablemaster` (`TimetableId`, `TimetableBranchCode`, `TimetableSemester`, `TimetableUploadedBy`, `TimetableUploadTime`, `TimetableImage`) VALUES
(5, '001', 1, 'Institute', '2024-12-11 17:46:57', '001_1.png'),
(19, '003', 2, 'Institute', '2024-12-11 17:44:38', '003_2.png'),
(36, '001', 2, 'Institute', '2022-03-23 03:24:03', '004_3.png'),
(40, '002', 2, 'Institute', '2024-12-11 17:45:48', '002_2.png');

-- --------------------------------------------------------

--
-- Table structure for table `updatemaster`
--

CREATE TABLE `updatemaster` (
  `UpdateId` int(10) NOT NULL,
  `UpdateTitle` varchar(100) NOT NULL,
  `UpdateDescription` text NOT NULL,
  `UpdateFile` varchar(100) NOT NULL,
  `UpdateUploadedBy` varchar(50) NOT NULL,
  `UpdateUploadDate` date NOT NULL,
  `UpdateType` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `updatemaster`
--

INSERT INTO `updatemaster` (`UpdateId`, `UpdateTitle`, `UpdateDescription`, `UpdateFile`, `UpdateUploadedBy`, `UpdateUploadDate`, `UpdateType`) VALUES
(7, 'Photos & Videos Competition', 'Hope that you all are enjoying the \"SPRINT 2022\" sports celebration at our university. There is one more surprise for you all; along with the sports celebration we are organizing a \"Photo & Video Reels Contest\".\r\n\r\nAny student can participate in this event. All you need to do is just click good photos or clips of current sport and upload it to the given link and get a chance to win exciting prizes.\r\n\r\nTo Upload Photo & Video Reels: https://forms.gle/v7Dpj1HxDKe7mJ8L9 \r\n\r\nRules:\r\n1. Each student can select any sport and are allowed to upload their best 5 photos and 2 clips (reels) of maximum 15 sec.', 'Photos & Videos Competition.png', 'Institute', '2022-03-15', 'GTU'),
(8, 'MAHA Sports Competition', '100+ Point Activities , Do not miss the opportunities ..', 'MAHA Sports Competition.png', 'Institute', '2022-03-15', 'GTU'),
(14, 'App test hai ', 'App is 😍', 'App test hai .png', 'Institute', '2022-04-10', 'GTU');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accountquerymaster`
--
ALTER TABLE `accountquerymaster`
  ADD PRIMARY KEY (`QueryId`),
  ADD KEY `QueryFromId` (`QueryFromId`);

--
-- Indexes for table `assignmentmaster`
--
ALTER TABLE `assignmentmaster`
  ADD PRIMARY KEY (`AssignmentId`),
  ADD KEY `AssignmentSubject` (`AssignmentSubject`);

--
-- Indexes for table `branchmaster`
--
ALTER TABLE `branchmaster`
  ADD PRIMARY KEY (`BranchId`),
  ADD UNIQUE KEY `BranchName` (`BranchName`),
  ADD UNIQUE KEY `BranchCode` (`BranchCode`);

--
-- Indexes for table `facultymaster`
--
ALTER TABLE `facultymaster`
  ADD PRIMARY KEY (`FacultyId`),
  ADD UNIQUE KEY `FacultyUserName` (`FacultyUserName`),
  ADD UNIQUE KEY `FacultyCode` (`FacultyCode`),
  ADD KEY `FacultyBranchCode` (`FacultyBranchCode`),
  ADD KEY `facultymaster_ibfk_2` (`FacultySection`);

--
-- Indexes for table `institutemaster`
--
ALTER TABLE `institutemaster`
  ADD PRIMARY KEY (`InstituteId`),
  ADD UNIQUE KEY `InstituteUserName` (`InstituteUserName`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ip_type` (`ip_address`,`user_type`),
  ADD KEY `idx_lockout` (`lockout_time`);

--
-- Indexes for table `sectionmaster`
--
ALTER TABLE `sectionmaster`
  ADD PRIMARY KEY (`SectionId`),
  ADD KEY `sectionmaster_ibfk_1` (`SectionBranch`);

--
-- Indexes for table `studentassignment`
--
ALTER TABLE `studentassignment`
  ADD PRIMARY KEY (`SAssignmentId`),
  ADD UNIQUE KEY `SAssignmentFile` (`SAssignmentFile`),
  ADD KEY `AssignmentId` (`AssignmentId`),
  ADD KEY `studentassignment_ibfk_2` (`SAssignmentUploaderId`);

--
-- Indexes for table `studentmaster`
--
ALTER TABLE `studentmaster`
  ADD PRIMARY KEY (`StudentId`),
  ADD UNIQUE KEY `StudentEnrollmentNo` (`StudentEnrollmentNo`),
  ADD UNIQUE KEY `StudentRollNo` (`StudentRollNo`),
  ADD UNIQUE KEY `StudentUserName` (`StudentUserName`),
  ADD KEY `StudentBranchCode` (`StudentBranchCode`),
  ADD KEY `studentmaster_ibfk_2` (`StudentSection`);

--
-- Indexes for table `studymaterialmaster`
--
ALTER TABLE `studymaterialmaster`
  ADD PRIMARY KEY (`MaterialId`),
  ADD UNIQUE KEY `MaterialCode` (`MaterialCode`),
  ADD KEY `SubjectCode` (`SubjectCode`);

--
-- Indexes for table `studyquerymaster`
--
ALTER TABLE `studyquerymaster`
  ADD PRIMARY KEY (`QueryId`),
  ADD KEY `QueryFromId` (`QueryFromId`),
  ADD KEY `QueryToId` (`QueryToId`),
  ADD KEY `QuerySubject` (`QuerySubject`);

--
-- Indexes for table `subjectmaster`
--
ALTER TABLE `subjectmaster`
  ADD PRIMARY KEY (`SubjectId`),
  ADD UNIQUE KEY `SubjectCode` (`SubjectCode`),
  ADD KEY `SubjectBranch` (`SubjectBranch`),
  ADD KEY `subjectmaster_ibfk_2` (`SubjectFacultyId`);

--
-- Indexes for table `timetablemaster`
--
ALTER TABLE `timetablemaster`
  ADD PRIMARY KEY (`TimetableId`),
  ADD UNIQUE KEY `TimetableImage` (`TimetableImage`),
  ADD KEY `TimetableBranchCode` (`TimetableBranchCode`);

--
-- Indexes for table `updatemaster`
--
ALTER TABLE `updatemaster`
  ADD PRIMARY KEY (`UpdateId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accountquerymaster`
--
ALTER TABLE `accountquerymaster`
  MODIFY `QueryId` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `assignmentmaster`
--
ALTER TABLE `assignmentmaster`
  MODIFY `AssignmentId` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `branchmaster`
--
ALTER TABLE `branchmaster`
  MODIFY `BranchId` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `facultymaster`
--
ALTER TABLE `facultymaster`
  MODIFY `FacultyId` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `institutemaster`
--
ALTER TABLE `institutemaster`
  MODIFY `InstituteId` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sectionmaster`
--
ALTER TABLE `sectionmaster`
  MODIFY `SectionId` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `studentassignment`
--
ALTER TABLE `studentassignment`
  MODIFY `SAssignmentId` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `studentmaster`
--
ALTER TABLE `studentmaster`
  MODIFY `StudentId` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `studymaterialmaster`
--
ALTER TABLE `studymaterialmaster`
  MODIFY `MaterialId` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `studyquerymaster`
--
ALTER TABLE `studyquerymaster`
  MODIFY `QueryId` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `subjectmaster`
--
ALTER TABLE `subjectmaster`
  MODIFY `SubjectId` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `timetablemaster`
--
ALTER TABLE `timetablemaster`
  MODIFY `TimetableId` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `updatemaster`
--
ALTER TABLE `updatemaster`
  MODIFY `UpdateId` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accountquerymaster`
--
ALTER TABLE `accountquerymaster`
  ADD CONSTRAINT `accountquerymaster_ibfk_1` FOREIGN KEY (`QueryFromId`) REFERENCES `studentmaster` (`StudentId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `assignmentmaster`
--
ALTER TABLE `assignmentmaster`
  ADD CONSTRAINT `assignmentmaster_ibfk_1` FOREIGN KEY (`AssignmentSubject`) REFERENCES `subjectmaster` (`SubjectCode`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `facultymaster`
--
ALTER TABLE `facultymaster`
  ADD CONSTRAINT `facultymaster_ibfk_1` FOREIGN KEY (`FacultyBranchCode`) REFERENCES `branchmaster` (`BranchCode`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `facultymaster_ibfk_2` FOREIGN KEY (`FacultySection`) REFERENCES `sectionmaster` (`SectionId`);

--
-- Constraints for table `sectionmaster`
--
ALTER TABLE `sectionmaster`
  ADD CONSTRAINT `sectionmaster_ibfk_1` FOREIGN KEY (`SectionBranch`) REFERENCES `branchmaster` (`BranchName`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `studentassignment`
--
ALTER TABLE `studentassignment`
  ADD CONSTRAINT `studentassignment_ibfk_1` FOREIGN KEY (`AssignmentId`) REFERENCES `assignmentmaster` (`AssignmentId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `studentassignment_ibfk_2` FOREIGN KEY (`SAssignmentUploaderId`) REFERENCES `studentmaster` (`StudentId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `studentmaster`
--
ALTER TABLE `studentmaster`
  ADD CONSTRAINT `studentmaster_ibfk_1` FOREIGN KEY (`StudentBranchCode`) REFERENCES `branchmaster` (`BranchCode`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `studentmaster_ibfk_2` FOREIGN KEY (`StudentSection`) REFERENCES `sectionmaster` (`SectionId`);

--
-- Constraints for table `studymaterialmaster`
--
ALTER TABLE `studymaterialmaster`
  ADD CONSTRAINT `studymaterialmaster_ibfk_1` FOREIGN KEY (`SubjectCode`) REFERENCES `subjectmaster` (`SubjectCode`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `studyquerymaster`
--
ALTER TABLE `studyquerymaster`
  ADD CONSTRAINT `studyquerymaster_ibfk_1` FOREIGN KEY (`QueryFromId`) REFERENCES `studentmaster` (`StudentId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `studyquerymaster_ibfk_2` FOREIGN KEY (`QueryToId`) REFERENCES `facultymaster` (`FacultyId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `studyquerymaster_ibfk_3` FOREIGN KEY (`QuerySubject`) REFERENCES `subjectmaster` (`SubjectCode`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `subjectmaster`
--
ALTER TABLE `subjectmaster`
  ADD CONSTRAINT `subjectmaster_ibfk_1` FOREIGN KEY (`SubjectBranch`) REFERENCES `branchmaster` (`BranchId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `subjectmaster_ibfk_2` FOREIGN KEY (`SubjectFacultyId`) REFERENCES `facultymaster` (`FacultyId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `timetablemaster`
--
ALTER TABLE `timetablemaster`
  ADD CONSTRAINT `timetablemaster_ibfk_1` FOREIGN KEY (`TimetableBranchCode`) REFERENCES `branchmaster` (`BranchCode`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
