# Laravel Microservices Project

# Steps to get application run on local environment:

    Clone Repository: git clone <repository link>
    Copy .env.example to .env
    Setup .env files:
        Setup sql server connections, each microservice require an separate database.
        Set QUEUE_CONNECTION=database,
        Set mailtrap/smtp api connections in Notifications application.
    Install dependencies:
        Run 'composer update' and wait for dependencies to be installed.

    Setup apache sites conf as follows:
    Core_virtual_site
        https://raw.githubusercontent.com/sumitkumar33/Microservices-Laravel/master/Docs/Core.myproject.conf
    Users virtual site
        https://raw.githubusercontent.com/sumitkumar33/Microservices-Laravel/master/Docs/Users.myproject.conf
    Notifications virtual site
        https://raw.githubusercontent.com/sumitkumar33/Microservices-Laravel/master/Docs/Notifications.myproject.conf

    /*Virtual sites can be configured to any domain but laravel require access to follow symlink paths*/
        <Directory /var/www/hestabit/FinalSubmission>
            Options FollowSymLinks
            AllowOverride All
        </Directory>
    
    Migrate Database tables:
        php artisan migrate:fresh
    Seed Users database roles
        php artisan db:seed
        
# !!IMPORTANT!!

    Beta features make use of modifications of vendor packages to setup follow steps below:
    1. Edit .env file make sure that 'DB_CONNECTION' is pointing to notifications database and 'DB_CONNECTION2' is pointing to users database.
    2. .env.example is edited with the new environment configurations.

# API documentation
    URI Unauthenticated Users
        [POST]/api/login - Expects a email and password and returns api token if authenticated.
        [POST]/api/register - Expects multiple inputs for profile and student data and returns api token.
    URI for Authenticated Users
        [GET]/api/users - Returns data of registered users.
        [POST]/api/update - Updates any field of user profile (Except password).
        [GET]/api/logout - Logout user and revokes current token.
        [GET]/api/logoutAll - Logout user and revokes all tokens linked to user profile.
        [GET]/api/user/notifications - Returns all notifications associated with user profile.
        [GET]/api/user/notifications/read - Mark all notifications as read.
        [GET]/api/user/notifications/unread - Mark all notifications as unread.
        [GET]/api/user/notifications/delete - Delete all notifications associated with user profile.
    URI for Authenticated Administrators
        [GET]/api/show/approved - Returns all approved users.
        [GET]/api/show/approved/students - Returns all approved students.
        [GET]/api/show/approved/teachers - Returns all approved teachers.
        [GET]/api/show/notApproved - Returns all not Approved users.
        [GET]/api/show/notApproved/students - Returns all not Approved students.
        [GET]/api/show/notApproved/teachers - Returns all not Approved teachers.
        [GET]/api/approve/{id} - Expects teacher's user_id as id on url get request and Update user profile as approved.
        [POST]/api/assign - Expects student's user_id as student_user_id and teacher's user_id as teacher_user_id   to be assigned, Assign student to teacher and Mark student profile as approved.

# Database Schema followed by SchoolApp Microservices

![Database_schema](https://raw.githubusercontent.com/sumitkumar33/Microservices-Laravel/master/Docs/dbLaravel.png)
    
# List of dependencies used by Microservice SchoolApp application

    Laravel/Laravel -> dist-package for laravel framework
    Laravel/Passport -> Used for oAuth2 implementation (Authentication and Authorization)
    staudenmeir/eloquent-has-many-deep -> Used for accessing multiple tables that cannot be accessed by has on through
    fzaninotto/faker -> Used for generating fake informations in factory methods