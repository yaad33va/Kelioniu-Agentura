# Kelionių Agentūra

Kelionių Agentūra is a travel agency management system designed to streamline booking, user management, and travel planning processes. The system is developed using PHP and Blade to provide a reliable and dynamic platform for both administrators and users.

## Setup Instructions

Follow the steps below to set up the project on your local system:

1. **Clone the Repository**:  
   ```bash
   git clone https://github.com/yaad33va/Kelioniu-Agentura.git
   ```
   Navigate to the project directory:  
   ```bash
   cd Kelioniu-Agentura
   ```

2. **Install Dependencies**:  
   Make sure you have [Composer](https://getcomposer.org/) installed. Run:  
   ```bash
   composer install
   ```

3. **Set Up Environment Variables**:  
   Create a `.env` file by copying `.env.example`:  
   ```bash
   cp .env.example .env
   ```  
   Update the `.env` file with your database credentials and other configuration.

4. **Generate Application Key**:  
   ```bash
   php artisan key:generate
   ```

5. **Run Database Migrations**:  
   Ensure your database is set up and accessible, then run:  
   ```bash
   php artisan migrate
   ```

6. **Start the Development Server**:  
   ```bash
   php artisan serve
   ```
   The application will run at `http://127.0.0.1:8000` by default.

---
