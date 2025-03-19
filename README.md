# Scandiweb Full Stack Developer Test Task

This project is a solution to the Scandiweb Full Stack Developer Test Task, aiming to develop a simple eCommerce web application with product listing and cart functionality. The application comprises a backend built with PHP and a frontend developed using React.

## Table of Contents

- [Technologies Used](#technologies-used)
- [Project Structure](#project-structure)
- [Installation and Setup](#installation-and-setup)
  - [Backend Setup](#backend-setup)
  - [Frontend Setup](#frontend-setup)
- [Usage](#usage)
- [Contributing](#contributing)
- [License](#license)

## Technologies Used

- **Backend**: PHP 8.1+, MySQL 5.6+, GraphQL
- **Frontend**: React, JavaScript, CSS

## Project Structure

The repository is organized as follows:

- `backend/`: Contains the PHP backend code.
- `frontend/`: Contains the React frontend code.

## Installation and Setup

### Backend Setup

1. **Clone the Repository**:

   ```bash
   git clone https://github.com/sadkingo/scandiweb-task.git
   cd scandiweb-task/backend
   ```

2. **Database Configuration**:

   - Create a MySQL database named `scandiweb`.
   - Import the provided `data.json` file into the database to populate initial data.
   - Update the database configuration in `/config/db_params.php` with your database credentials.

3. **Install Dependencies**:

   Ensure you have Composer installed. Navigate to the `backend` directory and run:

   ```bash
   composer install
   ```

4. **Start the Backend Server**:

   Configure your web server (e.g., Apache or Nginx) to serve the PHP application. Ensure the server points to the `backend` directory.

### Frontend Setup

1. **Navigate to the Frontend Directory**:

   ```bash
   cd ../frontend
   ```

2. **Install Dependencies**:

   Ensure you have Node.js and npm installed. Run:

   ```bash
   npm install
   ```

3. **Start the Frontend Development Server**:

   ```bash
   npm start
   ```

   This will launch the application at `http://localhost:5173`.

## Usage

After setting up both the backend and frontend:

- Access the application at `http://localhost:5173`.
- Browse products, add them to the cart, and proceed with the checkout process.
