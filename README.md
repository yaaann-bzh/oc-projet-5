YannsJobs
==========

Yannsjobs 1.0 - Dec 2019

by Yann Tachier  
<https://www.yaaann.ovh/>


Introduction
------------

YannsJobs is a PHP job offers application.
This application was realized as part of my developer training with OpenClassrooms (https://openclassrooms.com).

It is the fifth and last project of my course "Developpeur Web Junior".

The instruction can be read on : https://www.yaaann.ovh/consigne_5.php

You can see running application on : https://projet5.yaaann.ovh/


Usage
-----

Composer :

    This application use composer and his autoloader.

    $ composer install on root directory to add externals librairies.

Root :

    Hosts directory should be /public. Contain bootstrap and .htaccess

Database :

    Yannsjobs use PDO to connect with DB. Connexion settings on : /config/pdo.xml

    You can import the complete empty DB with the file /yannsjobs.sql or with parsed files in /yannsjobs.sql.zip
    
    Tables names can be modified in /config/tables.xml

Twig :

    Twig settings (cache, debug, etc) in : /lib/framework/Page::__contruct()

