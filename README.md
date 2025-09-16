# Learning Management System (LMS) - AI Quiz Maker

This project is a web-based Learning Management System (LMS) designed for educational institutions. A key feature is the **AI-Powered Quiz Maker**, enabling faculty to effortlessly create diverse assessments (quizzes, exams, exercises) using AI generation or manual input.

## Table of Contents

- [Features](#features)
- [Technologies Used](#technologies-used)
- [Getting Started](#getting-started)
  - [Prerequisites](#prerequisites)
  - [Installation](#installation)
  - [Database Setup](#database-setup)
- [Usage](#usage)
  - [Faculty Login](#faculty-login)
  - [AI Quiz Generation](#ai-quiz-generation)
  - [Manual Quiz Creation](#manual-quiz-creation)
- [Project Structure](#project-structure)
- [Contributing](#contributing)
- [License](#license)
- [Acknowledgements](#acknowledgements)

## Features

*   **User Roles:** Separate interfaces for Faculty (Instructors) and potentially Students (Student interface details not included here).
*   **AI-Powered Quiz Generation:**
    *   Faculty can describe a topic, upload a document (TXT, PDF, DOC, DOCX, PPT, PPTX), or select an existing study material.
    *   An AI backend (Python Flask) processes the input using an LLM (e.g., Llama-3.3) to generate questions.
    *   Supports various question types: Multiple Choice (MCQ), True/False, Enumeration, Essay, Identification, Matching.
    *   Generated questions are displayed for review, editing, and posting.
*   **Manual Quiz Creation:**
    *   Faculty can build quizzes entirely by hand.
    *   Structured creation with support for multiple parts (e.g., Part 1: MCQs, Part 2: True/False).
    *   Define the number of questions and type for each part.
    *   Dynamic form generation for question input based on type.
    *   Real-time total score calculation.
*   **Quiz Management:**
    *   Configure quiz details: Title, Description, Instructions, Activity Type (Quiz/Exam/Exercise), Deadline, Duration.
    *   Associate quizzes with Subjects, Branches, and Semesters.
    *   Edit generated or manually created questions before posting.
    *   Post quizzes for student access.
*   **Database Integration:** Stores quizzes, questions, answers, faculty details, subjects, branches, etc., in a MySQL/MariaDB database.

## Technologies Used

*   **Frontend:**
    *   HTML5
    *   CSS3 (Bootstrap 5)
    *   JavaScript (Vanilla JS, potentially with list.js for sorting/filtering)
    *   Mapbox GL JS (for map features, if applicable)
*   **Backend:**
    *   PHP 8.x (Primary server-side language)
    *   MySQL/MariaDB 10.x (Database)
    *   Python 3.x (Flask API for AI integration)
*   **AI Integration:**
    *   Flask (Python web framework)
    *   Together AI API (or similar LLM API)
    *   Libraries: `PyPDF2`, `python-docx`, `python-pptx` (for document text extraction)
*   **Other:**
    *   phpMyAdmin (for database management)
    *   Apache/Nginx (Web server)

## Getting Started

These instructions will get you a copy of the project up and running on your local machine.

### Prerequisites

*   Web Server (Apache/Nginx) with PHP 8.x support
*   MySQL/MariaDB 10.x database server
*   Composer (for potential PHP dependencies, if used)
*   Python 3.x installed
*   Git (for cloning the repository)
*   phpMyAdmin (recommended for database setup)
*   A valid API key for an LLM provider (e.g., Together AI)

### Installation

1.  **Clone the Repository:**
    ```bash
    git clone https://github.com/yourusername/lms-ai-quiz-maker.git
    cd lms-ai-quiz-maker
    ```
2.  **Set Up Web Server:**
    *   Place the project files in your web server's document root (e.g., `htdocs` for XAMPP, `/var/www/html` for LAMP).
    *   Ensure the web server can serve PHP files and access the database.
3.  **Install Python Dependencies (for Flask AI Backend):**
    *   Navigate to the directory containing your `app.py` (likely a subdirectory like `backend/ai_api/`).
    *   Create a virtual environment (recommended):
        ```bash
        python -m venv venv
        source venv/bin/activate # On Windows: venv\Scripts\activate
        ```
    *   Install required packages:
        ```bash
        pip install flask flask-cors PyPDF2 python-docx python-pptx requests
        ```
4.  **Configure `config.php`:**
    *   Locate `config.php` (likely in the root or a `includes/` directory).
    *   Update database connection details (`$servername`, `$username`, `$password`, `$dbname`).

### Database Setup

1.  **Create Database:**
    *   Use phpMyAdmin or a MySQL client to create a database named `lms`.
2.  **Import Schema:**
    *   Import your existing LMS database schema (tables like `facultymaster`, `studentmaster`, `subjectmaster`, `branchmaster`, etc.).
    *   Import the provided quiz schema SQL file (e.g., `quiz_schema.sql` from the previous conversation) to create/update the quiz-related tables (`quizmaster`, `quizparts`, `quizquestions`, `quizquestionoptions`, etc.).
3.  **Verify Structure:**
    *   Ensure all necessary tables exist and foreign key relationships are correctly defined.

## Usage

### Faculty Login

1.  Access the LMS login page via your web browser.
2.  Log in using valid Faculty credentials.

### AI Quiz Generation

1.  Navigate to the **Quiz Maker** section.
2.  Configure the activity details (Title, Type, Deadline, etc.).
3.  Select the **AI Chat Generation** tab.
4.  Describe the quiz topic in the chat interface, upload a document, or select an existing study material.
5.  Specify the number of questions, difficulty, and question types.
6.  Click **Generate Questions**.
7.  Review the AI-generated questions.
8.  Make any necessary edits.
9.  Click **Post Activity** to save the quiz to the database.

### Manual Quiz Creation

1.  Navigate to the **Quiz Maker** section.
2.  Configure the activity details (Title, Type, Deadline, etc.).
3.  Select the **Manual Creation** tab.
4.  Define quiz **Parts** (add/remove parts, set number of questions, type for each part).
5.  Click **Generate Question Structure**.
6.  Fill in the question details, choices (for MCQ/TF), and model answers/guidelines (for Enum/Essay/etc.) in the generated form.
7.  Review the questions.
8.  Click **Post Activity** to save the manually created quiz to the database.

## Project Structure
