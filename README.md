Please run following command to setup project locally using Laravel Sail :

cp .env.example .env (copy .env.example file as .env in root folder)
composer install (install required dependencies)
./vendor/bin/sail up -d (start the project on Docker server using Laravel Sail)
sail artisan migrate (export or create database using Laravel Artisan CLI)
./vendor/bin/sail down (stop the docker server)


Rooms for improvements :

-> Table columns naming convention can be more precise(if the relative data of other airlines or different Rosters were provided)
-> Current logic is limited to single HTML file format(as only one test html was given)
-> With more details on actual usage/meaning of keys mentioned/given in roster, query optimization can be done
-> flights table can be divided further and categorised for better querying and parsing multiple formats of Roster.