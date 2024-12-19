# Project Management (Easy plan)

A web-based project management tool with features like Kanban boards, team collaboration, and task management.

## Installation Instructions

### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Composer
- Web server (Apache)

### Windows Installation

1. Clone the repository:

2. Install Composer (if not installed):
- Download and run the Composer-Setup.exe from https://getcomposer.org/download/
- Follow the installation wizard

3. Install project dependencies:
   
    composer install

 If you encounter any issues or want to ensure you have the latest versions of the dependencies, you can run:
   
    composer update

5. Create and configure the .env file, Edit the .env file with your database and SMTP settings:

   DB_HOST=localhost

   DB_USER=your_database_username

   DB_PASS=your_database_password

   DB_NAME=project-management

   SMTP_HOST=smtp.example.com

   SMTP_USERNAME=your_smtp_username

   SMTP_PASSWORD=your_smtp_password

   SMTP_PORT=2525

6. Set up the database:
- Create a new MySQL database
- Import the SQL scripts from the `database` directory

6. Configure your web server:
- For Apache: Update your httpd.conf or create a new virtual host pointing to the `public` directory

7. Start your web server and access the application through your browser



### Linux Installation

1. Clone the repository:

2. Install Composer (if not installed):
   curl -sS https://getcomposer.org/installer | php
   sudo mv composer.phar /usr/local/bin/composer
3. Install project dependencies:

4. Create and configure the .env file, Edit the .env file with your database and SMTP settings:

   DB_HOST=localhost

   DB_USER=your_database_username

   DB_PASS=your_database_password

   DB_NAME=project-management

   SMTP_HOST=smtp.example.com

   SMTP_USERNAME=your_smtp_username

   SMTP_PASSWORD=your_smtp_password

   SMTP_PORT=2525



5. Set up the database: run the following command to install the project dependencies:
    composer install

   If you encounter any issues or want to ensure you have the latest versions of the dependencies, you can run:
   
    composer update


7. Configure your web server:
 - For Apache: Create a new .conf file in /etc/apache2/sites-available/

7. Set appropriate permissions:
   chmod -R 755 storage
   chmod -R 755 bootstrap/cache

8. Restart your web server:
- For Apache: `sudo service apache2 restart`


9. Access the application through your browser

## Troubleshooting

- If you encounter class not found errors, run:
  composer dump-autoload
- Check your PHP error logs if you experience any issues during setup or runtime.
- Ensure all required PHP extensions are enabled (pdo_mysql, mbstring, etc.).



## Testing Email Functionality

This project uses Mailtrap for testing email functionality in a safe, development environment. To test the email features:

1. Sign up for a free account at [Mailtrap](https://mailtrap.io/).

2. After logging in, go to your Mailtrap inbox.

3. In the SMTP Settings section, you'll find your unique SMTP credentials.

4. In the .env file update the SMTP settings:

       SMTP_HOST=your_smtp_host
       SMTP_USERNAME=your_smtp_username
       SMTP_PASSWORD=your_smtp_password
       SMTP_PORT=your_smtp_port


