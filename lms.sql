-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 03, 2025 at 04:48 PM
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
-- Table structure for table `activitymaster`
--

CREATE TABLE `activitymaster` (
  `ActivityId` int(10) NOT NULL,
  `ActivityTitle` varchar(50) NOT NULL,
  `ActivityDesc` text NOT NULL,
  `ActivitySubject` int(50) NOT NULL,
  `ActivityBranch` int(10) NOT NULL,
  `ActivityStatus` varchar(20) NOT NULL,
  `ActivityUploadedBy` int(10) NOT NULL,
  `ActivityFile` varchar(100) NOT NULL,
  `ActivityUploaddate` date NOT NULL,
  `ActivityForQuarter` int(1) NOT NULL,
  `ActivitySubmissionDate` date NOT NULL,
  `totalscore` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activitymaster`
--

INSERT INTO `activitymaster` (`ActivityId`, `ActivityTitle`, `ActivityDesc`, `ActivitySubject`, `ActivityBranch`, `ActivityStatus`, `ActivityUploadedBy`, `ActivityFile`, `ActivityUploaddate`, `ActivityForQuarter`, `ActivitySubmissionDate`, `totalscore`) VALUES
(3, 'act1', 'do it', 20250705, 1, '1', 20, 'act12025-07-26.pdf', '2025-07-18', 1, '2025-07-29', 100),
(5, 'new', 'hehe', 20240701, 1, '1', 20, 'new2025-07-26.pdf', '2025-07-19', 1, '2025-07-25', 50);

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
  `AssignmentSubmissionDate` date NOT NULL,
  `totalscore` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assignmentmaster`
--

INSERT INTO `assignmentmaster` (`AssignmentId`, `AssignmentTitle`, `AssignmentDesc`, `AssignmentSubject`, `AssignmentBranch`, `AssignmentStatus`, `AssignmentUploadedBy`, `AssignmentFile`, `AssignmentUploaddate`, `AssignmentForSemester`, `AssignmentSubmissionDate`, `totalscore`) VALUES
(46, 'ASSIGNMENT 1', 'ANSWER CAREFULLY', 20240701, 1, '1', 20, 'ASSIGNMENT 12025-07-13.pdf', '2025-07-13', 1, '2025-07-20', 50),
(47, 'ASSIGNMENT 2', 'do this', 20240701, 1, '1', 20, 'Second assignment2025-07-20.pdf', '2025-07-29', 1, '2025-07-21', 100),
(51, 'ASSign 3', 'answer', 20240701, 1, '1', 20, 'ASSign 32025-08-01.pdf', '2025-08-01', 1, '2025-08-16', 50);

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
(1, 'Grade 7', '007', 4),
(2, 'Grade 8', '008', 4),
(3, 'Grade 9', '009', 4),
(4, 'Grade 10', '010', 4),
(41, 'ICT', '011', 2);

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
  `FacultyEmail` varchar(50) NOT NULL,
  `FacultyContactNo` varchar(20) NOT NULL,
  `FacultyQualification` varchar(50) NOT NULL,
  `FacultyOffice` varchar(10) NOT NULL,
  `FacultyCode` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `facultymaster`
--

INSERT INTO `facultymaster` (`FacultyId`, `FacultyUserName`, `FacultyPassword`, `FacultyFirstName`, `FacultyMiddleName`, `FacultyLastName`, `FacultyProfilePic`, `FacultyBranchCode`, `FacultyEmail`, `FacultyContactNo`, `FacultyQualification`, `FacultyOffice`, `FacultyCode`) VALUES
(13, 'FAGR8-01', '1234', 'Mary', 'E', 'Taylor', 'ABM3.png', '008', 'mary@gmail.com', '9273176316', 'B.E.', 'A-102', 'GR8-01'),
(20, 'FAGR7-01', '1234', 'David', 'M', 'Johnson', 'ABM2.png', '007', 'dav@gmail.com', '9895124569', 'B.E. ', 'A-101', 'GR7-01'),
(21, 'FAGR9-01', '1234', 'Robert', 'W', 'Smith', 'STEM2.png', '009', 'robert@gmail.com', '9283812312', 'B.E.(Civil)', 'S-102', 'GR9-01'),
(24, 'FAGR10-01', '1234', 'Patricia', 'A', 'Martinez', 'ABM1.png', '010', 'pat@gmail.com', '9283127318', 'B.E. (IT)', 'A - 101', 'GR10-01');

-- --------------------------------------------------------

--
-- Table structure for table `facultysection`
--

CREATE TABLE `facultysection` (
  `FacultySectionId` int(10) NOT NULL,
  `FacultyId` int(10) NOT NULL,
  `SectionId` int(20) NOT NULL,
  `AssignedDate` date NOT NULL DEFAULT curdate(),
  `IsActive` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `facultysection`
--

INSERT INTO `facultysection` (`FacultySectionId`, `FacultyId`, `SectionId`, `AssignedDate`, `IsActive`) VALUES
(1, 13, 2, '2025-07-23', 1),
(2, 13, 5, '2025-07-23', 1),
(3, 13, 8, '2025-07-23', 1),
(4, 20, 3, '2025-07-23', 1),
(5, 20, 6, '2025-07-23', 1),
(6, 20, 9, '2025-07-23', 1),
(7, 21, 1, '2025-07-23', 1),
(8, 21, 4, '2025-07-23', 1),
(9, 21, 7, '2025-07-23', 1),
(10, 24, 10, '2025-07-23', 1),
(11, 24, 11, '2025-07-23', 1),
(12, 24, 12, '2025-07-23', 1);

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
-- Table structure for table `quizaigeneration`
--

CREATE TABLE `quizaigeneration` (
  `GenerationId` int(10) NOT NULL,
  `QuizId` int(10) NOT NULL,
  `GenerationType` enum('full_quiz','questions_only','regenerate') DEFAULT 'full_quiz',
  `GenerationPrompt` text DEFAULT NULL,
  `GenerationParameters` longtext DEFAULT NULL COMMENT 'Store difficulty, type, etc as JSON',
  `GenerationStatus` enum('pending','completed','failed') DEFAULT 'pending',
  `GeneratedBy` int(10) NOT NULL,
  `GeneratedAt` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quizmaster`
--

CREATE TABLE `quizmaster` (
  `QuizId` int(10) NOT NULL,
  `QuizTitle` varchar(100) NOT NULL,
  `QuizDescription` text DEFAULT NULL,
  `QuizInstructions` text DEFAULT NULL,
  `QuizType` enum('quiz','exam','exercise') DEFAULT 'quiz',
  `QuizSubject` int(10) NOT NULL,
  `QuizBranch` int(10) NOT NULL,
  `QuizDuration` int(5) DEFAULT NULL COMMENT 'Duration in minutes',
  `QuizDeadline` datetime DEFAULT NULL,
  `QuizStatus` varchar(20) DEFAULT '1',
  `QuizUploadedBy` int(10) NOT NULL,
  `QuizUploadDate` date NOT NULL,
  `QuizForSemester` int(1) NOT NULL,
  `TotalScore` decimal(6,2) DEFAULT 0.00,
  `TotalQuestions` int(3) DEFAULT 0,
  `IsShuffled` tinyint(1) DEFAULT 0,
  `ShowResults` tinyint(1) DEFAULT 1,
  `AllowRetake` tinyint(1) DEFAULT 0,
  `CreatedAt` datetime DEFAULT current_timestamp(),
  `UpdatedAt` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quizmaster`
--

INSERT INTO `quizmaster` (`QuizId`, `QuizTitle`, `QuizDescription`, `QuizInstructions`, `QuizType`, `QuizSubject`, `QuizBranch`, `QuizDuration`, `QuizDeadline`, `QuizStatus`, `QuizUploadedBy`, `QuizUploadDate`, `QuizForSemester`, `TotalScore`, `TotalQuestions`, `IsShuffled`, `ShowResults`, `AllowRetake`, `CreatedAt`, `UpdatedAt`) VALUES
(1, 'ME!', 'no cheating', 'honest', 'exam', 20240701, 1, 20, '0000-00-00 00:00:00', '1', 20, '2025-09-01', 1, 0.00, 0, 0, 1, 0, '2025-09-01 23:58:39', '2025-09-01 23:58:39');

-- --------------------------------------------------------

--
-- Table structure for table `quizmaterials`
--

CREATE TABLE `quizmaterials` (
  `MaterialId` int(10) NOT NULL,
  `QuizId` int(10) NOT NULL,
  `MaterialType` enum('uploaded_file','existing_material','ai_prompt') DEFAULT 'ai_prompt',
  `MaterialReference` varchar(200) DEFAULT NULL COMMENT 'File path or material ID',
  `MaterialContent` longtext DEFAULT NULL COMMENT 'Extracted text content',
  `UploadDate` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quizparts`
--

CREATE TABLE `quizparts` (
  `PartId` int(10) NOT NULL,
  `QuizId` int(10) NOT NULL,
  `PartNumber` int(3) NOT NULL,
  `PartTitle` varchar(100) DEFAULT NULL,
  `PartType` enum('mcq','true_false','enumeration','essay','identification','matching') DEFAULT 'mcq',
  `NumQuestions` int(3) NOT NULL DEFAULT 1,
  `PartInstructions` text DEFAULT NULL,
  `PartOrder` int(3) NOT NULL DEFAULT 1,
  `CreatedAt` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quizquestionoptions`
--

CREATE TABLE `quizquestionoptions` (
  `OptionId` int(10) NOT NULL,
  `QuestionId` int(10) NOT NULL,
  `OptionLetter` char(1) NOT NULL,
  `OptionText` text NOT NULL,
  `IsCorrect` tinyint(1) DEFAULT 0,
  `OptionOrder` int(2) DEFAULT 1,
  `CreatedAt` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quizquestions`
--

CREATE TABLE `quizquestions` (
  `QuestionId` int(10) NOT NULL,
  `QuizId` int(10) NOT NULL,
  `PartId` int(10) NOT NULL,
  `QuestionNumber` int(3) NOT NULL,
  `QuestionText` text NOT NULL,
  `QuestionType` enum('mcq','true_false','enumeration','essay','identification','matching') NOT NULL,
  `QuestionPoints` decimal(5,2) DEFAULT 1.00,
  `QuestionImage` varchar(100) DEFAULT NULL,
  `QuestionOrder` int(3) NOT NULL DEFAULT 1,
  `CorrectAnswer` text DEFAULT NULL COMMENT 'For non-MCQ questions',
  `ExplanationText` text DEFAULT NULL,
  `CreatedAt` datetime DEFAULT current_timestamp(),
  `UpdatedAt` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, '9-Diamond', 'Grade 9'),
(2, '8-Faith', 'Grade 8'),
(3, '7-Agsunod', 'Grade 7'),
(4, '9-Emerald', 'Grade 9'),
(5, '8-Prudence', 'Grade 8'),
(6, '7-Bolisay', 'Grade 7'),
(7, '9-Ruby', 'Grade 9'),
(8, '8-Honesty', 'Grade 8'),
(9, '7-Cabico', 'Grade 7'),
(10, '10-Matapat', 'Grade 10'),
(11, '10-Matalino', 'Grade 10'),
(12, '10-Masunurin', 'Grade 10');

-- --------------------------------------------------------

--
-- Table structure for table `studentactivity`
--

CREATE TABLE `studentactivity` (
  `SActivityId` int(11) NOT NULL,
  `SActivityUploaderId` int(11) NOT NULL,
  `ActivityId` int(11) NOT NULL,
  `SActivityFile` varchar(100) NOT NULL,
  `SActivityUploadDate` int(11) NOT NULL,
  `SActivityStatus` int(11) NOT NULL,
  `studscore` int(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `studentactivity`
--

INSERT INTO `studentactivity` (`SActivityId`, `SActivityUploaderId`, `ActivityId`, `SActivityFile`, `SActivityUploadDate`, `SActivityStatus`, `studscore`) VALUES
(5, 52, 5, '202412006918_5.pdf', 2025, 3, 90);

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
  `SAssignmentStatus` int(10) NOT NULL,
  `studscore` int(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `studentassignment`
--

INSERT INTO `studentassignment` (`SAssignmentId`, `SAssignmentUploaderId`, `AssignmentId`, `SAssignmentFile`, `SAssignmentUploadDate`, `SAssignmentStatus`, `studscore`) VALUES
(60, 52, 46, '202412006918_46.pdf', '2025-07-19', 3, 45),
(61, 52, 47, '202412006918_47.pdf', '2025-07-20', 3, 50);

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
(18, 202412006915, 'ST202412006915', '1234', 'Marie', 'Gonzales', 'Castillo', '202412006915.png', '2003-09-05', '008', 8, 1, 'marie@gmail.com', 9418599999, 'New beloved St.', 'marieparent@gmail.com', 9885621522, 6),
(23, 202412006913, 'ST202412006913', '1234', 'Adrian Rusell', 'Rambutan', 'Tajan', '202412006913.png', '2003-11-28', '008', 2, 1, 'adrian@gmail.com', 9418524567, 'Sto Tomas', 'adrianparent@gmail.com', 7527895422, 4),
(50, 202412006914, 'ST202412006914', '1234', 'Danilo', 'Pogi', 'Gonzales', '202412006914.png', '2003-11-01', '010', 10, 1, 'danilogatch@gmail.com', 9936602786, 'Bagong Ilog, Pasig City', 'dani@gmail.com', 9998887777, 1),
(51, 202412006917, 'ST202412006917', '1234', 'MaverickDanielle', 'Pangan', 'Andres', '202412006917.png', '2004-04-01', '009', 1, 1, 'maverick@gmail.com', 9921439880, 'adress ni Mavs', 'parent@gmail.com', 9876543210, 7),
(52, 202412006918, 'ST202412006918', '1234', 'Kathleen', 'Sheesh', 'Dayne', '202412006918.png', '2004-09-10', '007', 6, 1, 'kath@gmail.com', 9873137162, 'Cainta Pasig City', 'kathparent@gmail.com', 9338392183, 9);

-- --------------------------------------------------------

--
-- Table structure for table `studentquizanswers`
--

CREATE TABLE `studentquizanswers` (
  `AnswerId` int(10) NOT NULL,
  `AttemptId` int(10) NOT NULL,
  `QuestionId` int(10) NOT NULL,
  `StudentAnswer` text DEFAULT NULL,
  `SelectedOption` char(1) DEFAULT NULL COMMENT 'For MCQ questions',
  `IsCorrect` tinyint(1) DEFAULT NULL,
  `PointsEarned` decimal(5,2) DEFAULT 0.00,
  `AnsweredAt` datetime DEFAULT NULL,
  `CreatedAt` datetime DEFAULT current_timestamp(),
  `UpdatedAt` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `studentquizattempts`
--

CREATE TABLE `studentquizattempts` (
  `AttemptId` int(10) NOT NULL,
  `QuizId` int(10) NOT NULL,
  `StudentId` int(10) NOT NULL,
  `AttemptNumber` int(2) DEFAULT 1,
  `StartTime` datetime DEFAULT NULL,
  `EndTime` datetime DEFAULT NULL,
  `SubmitTime` datetime DEFAULT NULL,
  `TimeSpent` int(6) DEFAULT NULL COMMENT 'Time in seconds',
  `Status` enum('in_progress','submitted','auto_submitted','not_started') DEFAULT 'not_started',
  `TotalScore` decimal(6,2) DEFAULT 0.00,
  `MaxScore` decimal(6,2) DEFAULT 0.00,
  `Percentage` decimal(5,2) DEFAULT 0.00,
  `IsCompleted` tinyint(1) DEFAULT 0,
  `CreatedAt` datetime DEFAULT current_timestamp(),
  `UpdatedAt` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `MaterialFile` varchar(100) NOT NULL,
  `MaterialUploadDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `studymaterialmaster`
--

INSERT INTO `studymaterialmaster` (`MaterialId`, `SubjectCode`, `SubjectUnitNo`, `MaterialCode`, `SubjectUnitName`, `MaterialFile`, `MaterialUploadDate`) VALUES
(21, 20240701, 1, '20240701_1_ID21', 'LESSON 1', '20240701_1_ID21_MATERIAL.pdf', '2025-07-26'),
(22, 20240701, 2, '20240701_2_ID22', 'LESSON 2', '20240701_2_ID22_MATERIAL.pdf', '2025-07-26');

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
(62, 20240701, 'ENGLISH_7_1', 1, 1, 20, '20240701.pdf', '007_1', '20240701.png'),
(63, 20240702, 'ENGLISH_7_2', 1, 2, 20, '20240702.pdf', '007_2', '20240702.png'),
(64, 20250705, 'FILIPINO_7_1', 1, 1, 20, '20250705.pdf', '007_1', '20250705.png'),
(66, 20250801, 'MATH_8_1', 2, 1, 13, '20250801.pdf', '008_1', '20250801.png'),
(68, 20250802, 'SCIENCE_8_1', 2, 1, 13, '20250802.pdf', '008_1', '20250802.png'),
(69, 20250901, 'TLE_9_1', 3, 1, 21, '20250901.pdf', '009_1', '20250901.png'),
(73, 20250902, 'ARALING PANLIPUNAN_9_1', 3, 1, 21, '20250902.pdf', '009_1', '20250902.png'),
(74, 20251001, 'MAPEH_10_1', 4, 1, 24, '20251001.pdf', '010_1', '20251001.png'),
(75, 20251002, 'ESP_10_1', 4, 1, 24, '20251002.pdf', '010_1', '20251002.png');

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
(5, '007', 1, 'Institute', '2024-12-11 17:46:57', '001_1.png'),
(19, '009', 2, 'Institute', '2024-12-11 17:44:38', '003_2.png'),
(36, '007', 2, 'Institute', '2022-03-23 03:24:03', '004_3.png'),
(40, '008', 2, 'Institute', '2024-12-11 17:45:48', '002_2.png');

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
(7, 'BRIGADA ESKWELA', 'Brigada Eskwela 2025\r\n\r\n📅 Hunyo 9–13, 2025\r\n📍 Mataas na Paaralan ng Sagad High School\r\n🎯 Tema: “Brigada Eskwela: Sama-sama para sa Bayang Bumabasa”\r\n\r\nInaanyayahan po ang lahat ng magulang, mag-aaral, guro, alumni, at mga katuwang sa komunidad na makiisa sa ating Brigada Eskwela ngayong taon!\r\nLayunin ng gawaing ito na paghandaan ang pagbubukas ng klase sa pamamagitan ng sama-samang paglilinis, pagkukumpuni, at pagsasaayos ng ating paaralan. Higit pa rito, ito ay hakbang tungo sa paglinang ng isang komunidad na nagbabasa at nagmamalasakit sa edukasyon.\r\n\r\nAno ang maiaambag mo?\r\n✔️ Oras at serbisyo\r\n✔️ Mga panlinis, pintura, kagamitan sa eskwela\r\n✔️ Suporta sa mga aktibidad para sa pagbabasa\r\n✔️ At higit sa lahat, ang iyong presensya at pakikiisa!\r\n\r\nTayo na’t magbayanihan para sa mas ligtas, maayos, at mababasa nating paaralan!\r\nBrigada Eskwela 2025 — Sama-sama para sa Bayang Bumabasa!', 'BRIGADA ESKWELA.png', 'Institute', '2022-06-07', 'Campus');

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
-- Indexes for table `activitymaster`
--
ALTER TABLE `activitymaster`
  ADD PRIMARY KEY (`ActivityId`),
  ADD KEY `activitymaster_ibfk_1` (`ActivitySubject`);

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
  ADD KEY `FacultyBranchCode` (`FacultyBranchCode`);

--
-- Indexes for table `facultysection`
--
ALTER TABLE `facultysection`
  ADD PRIMARY KEY (`FacultySectionId`),
  ADD UNIQUE KEY `unique_faculty_section` (`FacultyId`,`SectionId`),
  ADD KEY `FacultyId` (`FacultyId`),
  ADD KEY `SectionId` (`SectionId`);

--
-- Indexes for table `institutemaster`
--
ALTER TABLE `institutemaster`
  ADD PRIMARY KEY (`InstituteId`),
  ADD UNIQUE KEY `InstituteUserName` (`InstituteUserName`);

--
-- Indexes for table `quizaigeneration`
--
ALTER TABLE `quizaigeneration`
  ADD PRIMARY KEY (`GenerationId`),
  ADD KEY `QuizId` (`QuizId`),
  ADD KEY `GeneratedBy` (`GeneratedBy`);

--
-- Indexes for table `quizmaster`
--
ALTER TABLE `quizmaster`
  ADD PRIMARY KEY (`QuizId`),
  ADD KEY `QuizSubject` (`QuizSubject`),
  ADD KEY `QuizBranch` (`QuizBranch`),
  ADD KEY `QuizUploadedBy` (`QuizUploadedBy`);

--
-- Indexes for table `quizmaterials`
--
ALTER TABLE `quizmaterials`
  ADD PRIMARY KEY (`MaterialId`),
  ADD KEY `QuizId` (`QuizId`);

--
-- Indexes for table `quizparts`
--
ALTER TABLE `quizparts`
  ADD PRIMARY KEY (`PartId`),
  ADD KEY `QuizId` (`QuizId`);

--
-- Indexes for table `quizquestionoptions`
--
ALTER TABLE `quizquestionoptions`
  ADD PRIMARY KEY (`OptionId`),
  ADD KEY `QuestionId` (`QuestionId`);

--
-- Indexes for table `quizquestions`
--
ALTER TABLE `quizquestions`
  ADD PRIMARY KEY (`QuestionId`),
  ADD KEY `QuizId` (`QuizId`),
  ADD KEY `PartId` (`PartId`);

--
-- Indexes for table `sectionmaster`
--
ALTER TABLE `sectionmaster`
  ADD PRIMARY KEY (`SectionId`),
  ADD KEY `sectionmaster_ibfk_1` (`SectionBranch`);

--
-- Indexes for table `studentactivity`
--
ALTER TABLE `studentactivity`
  ADD PRIMARY KEY (`SActivityId`),
  ADD KEY `studentactivity_ibfk_1` (`ActivityId`),
  ADD KEY `studentactivity_ibfk_2` (`SActivityUploaderId`);

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
-- Indexes for table `studentquizanswers`
--
ALTER TABLE `studentquizanswers`
  ADD PRIMARY KEY (`AnswerId`),
  ADD UNIQUE KEY `unique_attempt_question` (`AttemptId`,`QuestionId`),
  ADD KEY `AttemptId` (`AttemptId`),
  ADD KEY `QuestionId` (`QuestionId`);

--
-- Indexes for table `studentquizattempts`
--
ALTER TABLE `studentquizattempts`
  ADD PRIMARY KEY (`AttemptId`),
  ADD UNIQUE KEY `unique_student_quiz_attempt` (`QuizId`,`StudentId`,`AttemptNumber`),
  ADD KEY `QuizId` (`QuizId`),
  ADD KEY `StudentId` (`StudentId`);

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
-- AUTO_INCREMENT for table `activitymaster`
--
ALTER TABLE `activitymaster`
  MODIFY `ActivityId` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `assignmentmaster`
--
ALTER TABLE `assignmentmaster`
  MODIFY `AssignmentId` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `branchmaster`
--
ALTER TABLE `branchmaster`
  MODIFY `BranchId` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `facultymaster`
--
ALTER TABLE `facultymaster`
  MODIFY `FacultyId` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `facultysection`
--
ALTER TABLE `facultysection`
  MODIFY `FacultySectionId` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `institutemaster`
--
ALTER TABLE `institutemaster`
  MODIFY `InstituteId` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `quizaigeneration`
--
ALTER TABLE `quizaigeneration`
  MODIFY `GenerationId` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quizmaster`
--
ALTER TABLE `quizmaster`
  MODIFY `QuizId` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `quizmaterials`
--
ALTER TABLE `quizmaterials`
  MODIFY `MaterialId` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quizparts`
--
ALTER TABLE `quizparts`
  MODIFY `PartId` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quizquestionoptions`
--
ALTER TABLE `quizquestionoptions`
  MODIFY `OptionId` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quizquestions`
--
ALTER TABLE `quizquestions`
  MODIFY `QuestionId` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sectionmaster`
--
ALTER TABLE `sectionmaster`
  MODIFY `SectionId` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `studentactivity`
--
ALTER TABLE `studentactivity`
  MODIFY `SActivityId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `studentassignment`
--
ALTER TABLE `studentassignment`
  MODIFY `SAssignmentId` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `studentmaster`
--
ALTER TABLE `studentmaster`
  MODIFY `StudentId` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `studentquizanswers`
--
ALTER TABLE `studentquizanswers`
  MODIFY `AnswerId` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `studentquizattempts`
--
ALTER TABLE `studentquizattempts`
  MODIFY `AttemptId` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `studymaterialmaster`
--
ALTER TABLE `studymaterialmaster`
  MODIFY `MaterialId` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `studyquerymaster`
--
ALTER TABLE `studyquerymaster`
  MODIFY `QueryId` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `subjectmaster`
--
ALTER TABLE `subjectmaster`
  MODIFY `SubjectId` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `timetablemaster`
--
ALTER TABLE `timetablemaster`
  MODIFY `TimetableId` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `updatemaster`
--
ALTER TABLE `updatemaster`
  MODIFY `UpdateId` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accountquerymaster`
--
ALTER TABLE `accountquerymaster`
  ADD CONSTRAINT `accountquerymaster_ibfk_1` FOREIGN KEY (`QueryFromId`) REFERENCES `studentmaster` (`StudentId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `activitymaster`
--
ALTER TABLE `activitymaster`
  ADD CONSTRAINT `activitymaster_ibfk_1` FOREIGN KEY (`ActivitySubject`) REFERENCES `subjectmaster` (`SubjectCode`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `assignmentmaster`
--
ALTER TABLE `assignmentmaster`
  ADD CONSTRAINT `assignmentmaster_ibfk_1` FOREIGN KEY (`AssignmentSubject`) REFERENCES `subjectmaster` (`SubjectCode`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `facultymaster`
--
ALTER TABLE `facultymaster`
  ADD CONSTRAINT `facultymaster_ibfk_1` FOREIGN KEY (`FacultyBranchCode`) REFERENCES `branchmaster` (`BranchCode`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `facultysection`
--
ALTER TABLE `facultysection`
  ADD CONSTRAINT `facultysection_ibfk_1` FOREIGN KEY (`FacultyId`) REFERENCES `facultymaster` (`FacultyId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `facultysection_ibfk_2` FOREIGN KEY (`SectionId`) REFERENCES `sectionmaster` (`SectionId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `quizaigeneration`
--
ALTER TABLE `quizaigeneration`
  ADD CONSTRAINT `quizaigeneration_ibfk_1` FOREIGN KEY (`QuizId`) REFERENCES `quizmaster` (`QuizId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `quizaigeneration_ibfk_2` FOREIGN KEY (`GeneratedBy`) REFERENCES `facultymaster` (`FacultyId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `quizmaster`
--
ALTER TABLE `quizmaster`
  ADD CONSTRAINT `quizmaster_ibfk_1` FOREIGN KEY (`QuizSubject`) REFERENCES `subjectmaster` (`SubjectCode`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `quizmaster_ibfk_2` FOREIGN KEY (`QuizBranch`) REFERENCES `branchmaster` (`BranchId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `quizmaster_ibfk_3` FOREIGN KEY (`QuizUploadedBy`) REFERENCES `facultymaster` (`FacultyId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `quizmaterials`
--
ALTER TABLE `quizmaterials`
  ADD CONSTRAINT `quizmaterials_ibfk_1` FOREIGN KEY (`QuizId`) REFERENCES `quizmaster` (`QuizId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `quizparts`
--
ALTER TABLE `quizparts`
  ADD CONSTRAINT `quizparts_ibfk_1` FOREIGN KEY (`QuizId`) REFERENCES `quizmaster` (`QuizId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `quizquestionoptions`
--
ALTER TABLE `quizquestionoptions`
  ADD CONSTRAINT `quizquestionoptions_ibfk_1` FOREIGN KEY (`QuestionId`) REFERENCES `quizquestions` (`QuestionId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `quizquestions`
--
ALTER TABLE `quizquestions`
  ADD CONSTRAINT `quizquestions_ibfk_1` FOREIGN KEY (`QuizId`) REFERENCES `quizmaster` (`QuizId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `quizquestions_ibfk_2` FOREIGN KEY (`PartId`) REFERENCES `quizparts` (`PartId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sectionmaster`
--
ALTER TABLE `sectionmaster`
  ADD CONSTRAINT `sectionmaster_ibfk_1` FOREIGN KEY (`SectionBranch`) REFERENCES `branchmaster` (`BranchName`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `studentactivity`
--
ALTER TABLE `studentactivity`
  ADD CONSTRAINT `studentactivity_ibfk_1` FOREIGN KEY (`ActivityId`) REFERENCES `activitymaster` (`ActivityId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `studentactivity_ibfk_2` FOREIGN KEY (`SActivityUploaderId`) REFERENCES `studentmaster` (`StudentId`) ON DELETE CASCADE ON UPDATE CASCADE;

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
-- Constraints for table `studentquizanswers`
--
ALTER TABLE `studentquizanswers`
  ADD CONSTRAINT `studentquizanswers_ibfk_1` FOREIGN KEY (`AttemptId`) REFERENCES `studentquizattempts` (`AttemptId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `studentquizanswers_ibfk_2` FOREIGN KEY (`QuestionId`) REFERENCES `quizquestions` (`QuestionId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `studentquizattempts`
--
ALTER TABLE `studentquizattempts`
  ADD CONSTRAINT `studentquizattempts_ibfk_1` FOREIGN KEY (`QuizId`) REFERENCES `quizmaster` (`QuizId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `studentquizattempts_ibfk_2` FOREIGN KEY (`StudentId`) REFERENCES `studentmaster` (`StudentId`) ON DELETE CASCADE ON UPDATE CASCADE;

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
