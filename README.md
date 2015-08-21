# Skyflow.io

## Installation

### Sous MacOS X avec apache2

#### Installation du projet dans /Library/WebServer/Documents/skyflow.io

	cd /Library/WebServer/Documents/
	sudo git clone https://github.com/Sylpheo/skyflow.io
	sudo chown -R `whoami` skyflow.io
	sudo chgrp -R _www skyflow.io
	sudo chmod -R g+w skyflow.io/var
	cd skyflow.io
	php -r "readfile('https://getcomposer.org/installer');" | php
	php composer.phar install

#### Ajout du host dans /etc/hosts

Editer le fichier `/etc/hosts` et ajouter la ligne :

	127.0.0.1 skyflow.io

#### Configuration de Apache

Editer le fichier `/etc/apache2/httpd.conf`. Décommenter les lignes :

	LoadModule rewrite_module libexec/apache2/mod_rewrite.so
	LoadModule php5_module libexec/apache2/libphp5.so
	Include /private/etc/apache2/extra/httpd-vhosts.conf

#### Configuration du VirtualHost Apache

Editer le fichier `/etc/apache2/extra/httpd-vhosts.conf`. Ajouter le VirtualHost pour skyflow.io :

	<VirtualHost *:80>
	    ServerName skyflow.io
	    DocumentRoot "/Library/WebServer/Documents/skyflow.io/web"
	    ErrorLog "/private/var/log/apache2/skyflow.io-error_log"
	    CustomLog "/private/var/log/apache2/skyflow.io-access_log" common
	    <Directory "/Library/WebServer/Documents/skyflow.io/web">
	        AllowOverride All
	        Order allow,deny
	        Allow from all
	    </Directory>
	</VirtualHost>

Si le fichier `/etc/apache2/extra/httpd-vhosts.conf` ne contient pas de VirtualHost pour localhost, il faut le créer sinon il ne sera plus possible d'accéder à l'adresse http://localhost/ :

	<VirtualHost *:80>
	    ServerName localhost
	    DocumentRoot "/Library/WebServer/Documents"
	</VirtualHost>

#### Test du VirtualHost

Se rendre à l'adresse [http://skyflow.io/](http://skyflow.io/), la page "User authentication" doit s'afficher.

#### Création et installation de la base de données MySQL

[Télécharger et installer MySql](https://dev.mysql.com/downloads/mysql/) puis [sécuriser les comptes root](https://dev.mysql.com/doc/refman/5.1/en/default-privileges.html).

Le fichier `install.sql` cré la base de données `skyflow`, l'utilisateur `skyflow` (mot de passe `skyflow`), les tables nécéssaires au fonctionnement de l'application et quelques enregistrements de test. Le fichier doit être exécuté en tant qu'utilisateur `root` :

	mysql -h 'localhost' -u root -p < install.sql

#### Activation du module pdo_mysql pour PHP

Editer le fichier `/etc/php.ini.default` et modifier la ligne :

	;extension=php_pdo_mysql.dll

en :

	extension=php_pdo_mysql.so

## Utilisation

### Connexion avec l'utilisateur de test

Se rendre à l'adresse [http://skyflow.io/](http://skyflow.io/), la page "User authentication" doit s'afficher. Se connecter avec les identifiants :

	username: elodie
	password: elodie