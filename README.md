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
    
# SchoolApp Follows following Database Schema

![Database_schema](https://raw.githubusercontent.com/sumitkumar33/Microservices-Laravel/master/Docs/dbLaravel.png)
    
# List of dependencies used by Microservice SchoolApp application

    Laravel/Laravel -> dist-package for laravel framework
    Laravel/Passport -> Used for oAuth2 implementation (Authentication and Authorization)
    staudenmeir/eloquent-has-many-deep -> Used for accessing multiple tables that cannot be accessed by has on through
    fzaninotto/faker -> Used for generating fake informations in factory methods