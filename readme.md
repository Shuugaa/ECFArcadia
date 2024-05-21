- Télécharger Apache ainsi que le mod_fcgid via ce lien: https://www.apachelounge.com/download/
- Rajouter le mod_fcgid dans c:/php/modules
- rajouter cette config a la fin du httpd.conf:

<!--
LoadModule fcgid_module modules/mod_fcgid.so

<IfModule fcgid_module>


   FcgidMaxProcesses 300
   FcgidMaxProcessesPerClass 300

   FcgidOutputBufferSize 65536
   FcgidConnectTimeout 10
   FcgidProcessLifeTime 0
   FcgidMaxRequestsPerProcess 0
   FcgidMinProcessesPerClass 0
   FcgidFixPathinfo 0
   FcgidProcessLifeTime 0
   FcgidZombieScanInterval 20
   FcgidMaxRequestLen 536870912
   FcgidIOTimeout 120
   FcgidTimeScore 3

   FcgidPassHeader Authorization

   FcgidInitialEnv PHPRC "C:\\php-8.3.6-nts-Win32-vs16-x64"
   FcgidInitialEnv PATH "C:\\php-8.3.6-nts-Win32-vs16-x64;C:\\WINDOWS\\system32;C:\\WINDOWS;C:\\WINDOWS\\System32\\Wbem;"
   FcgidInitialEnv SystemRoot "C:\\Windows"
   FcgidInitialEnv SystemDrive "C:"
   FcgidInitialEnv TEMP "C:\\WINDOWS\\TEMP"
   FcgidInitialEnv TMP "C:\\WINDOWS\\TEMP"
   FcgidInitialEnv windir "C:\\WINDOWS"
   <Files ~ "\.php$">
      Options Indexes FollowSymLinks ExecCGI
      AddHandler fcgid-script .php
      FcgidWrapper "C:/php-8.3.6-nts-Win32-vs16-x64/php-cgi.exe" .php
   </Files>
</IfModule>

-->

- Télécharger mysql : https://dev.mysql.com/downloads/installer/
- Télécharger PHP NON THREAD SAFE : https://windows.php.net/download/
activer les extensions: intl, mbstring, mysqli, openssl et pdo_mysql dans le php.ini precedemment php.ini-production

- telecharger scoop : https://scoop.sh/
- telecharger symfony-cli : https://symfony.com/download
- telecharger composer : https://getcomposer.org/download/

- créer le squelette php symfony avec la commande: 

composer create-project symfony/skeleton:"7.0.*" my_project_directory
cd my_project_directory
composer require webapp

- Une fois que tout est installé et correctement configurer on peut lancer le serveur symfony:

symfony server:start -d
accessible a l'adresse : http://127.0.0.1:8000/

- Installer Doctrine et tout ce qui va avec : 
composer require symfony/orm-pack
<!-- Cette partie concerne uniquement le moment du dev-->
composer require --dev symfony/maker-bundle
composer update

- pour créer une base modifier le .env en créant un .env.local qui viendra surcharger le .env:
avec les identifiants au SGBD
- pour créer la base:
php bin/console doctrine:database:create
- ne pas oublier de créer une entité qui permettra de faire des requetes sur la base en fonction de ses relations avec les autres entités etc...:
php bin/console make:entity nom_de_lentité
php bin/console make:user
- une fois l'entité créer la migration permet de generer le code sql associer a chaque entitées:
php bin/console make:migration
- et pour executer ce SQL:
php bin/console doctrine:migrations:migrate

commandes utiles:
php bin/console debug:router
php bin/console cache:clear


Pour installer MongoDB avec Symfony:

https://www.mongodb.com/docs/manual/tutorial/install-mongodb-on-windows/
https://www.mongodb.com/docs/mongodb-shell/install/
https://github.com/mongodb/mongo-php-driver/releases

Ajouter l'extension dans le php.ini
https://www.php.net/manual/en/mongodb.installation.windows.php

https://www.doctrine-project.org/projects/doctrine-mongodb-bundle/en/5.0/index.html
composer require doctrine/mongodb-odm-bundle 

MONGODB_URL=mongodb://localhost:27017
MONGODB_DB=symfony