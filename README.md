# Quiet.ly

## Configuration

### 1. Create an AWS account
Remember to follow [AWS best practices](https://console.aws.amazon.com/iam/home)!
### 2. Create an EC2 instance
A free tier eligible instance will be enough for our application. You can skip steps 2-5. Remember to configure a Security Group in step 6:
(readme/securitygroups.png?raw=true "")
### 3. Access your new EC2 instance
You will find detailed instructions in the "Connect" tab.
### 4. Install required software
```sudo amazon-linux-extras install nginx1.12
sudo vim /etc/yum.repos.d/MariaDB.repo
    [mariadb]
    name = MariaDB
    baseurl = http://yum.mariadb.org/10.4/centos7-amd64
    gpgkey=https://yum.mariadb.org/RPM-GPG-KEY-MariaDB
    gpgcheck=1
sudo yum install mariadb-server
sudo systemctl enable --now mariadb```
Follow `mysql_secure_installation` installation guide.
```mysql -u root -p
    CREATE DATABASE db_name;
    CREATE USER 'username'@'localhost' IDENTIFIED BY 'password';
    GRANT ALL ON db_name.* TO 'username'@'localhost';
    FLUSH PRIVILEGES;
    EXIT;
sudo yum install php-xml php-process
sudo amazon-linux-extras install epel
sudo yum install npm
sudo amazon-linux-extras install php7.2
sudo mkdir /var/www
cd /var/www
sudo wget https://github.com/quiet-ly-chat/secure-chat/archive/master.zip
sudo unzip master.zip
sudo rm master.zip
sudo php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === '48e3236262b34d30969dca3c37281b3b4bbe3221bda826ac6a9a62d6444cdb0dcd0615698a5cbe587c3f0fe57a54d8f5') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
sudo php composer-setup.php
sudo php -r "unlink('composer-setup.php');"
cd secure-chat-master/
sudo php ../composer.phar install
sudo npm install
sudo cp .env.dist .env
sudo rm /etc/nginx/nginx.conf```
Paste nginx configuration here: ```sudo vim /etc/nginx/nginx.conf``` see: (readme/example.config)
Configure your database credentials here: ```sudo vim .env```
```sudo php bin/console doctrine:migrations:migrate
sudo npm run build
php bin/console gos:websocket:server & disown
sudo service php-fpm restart```
```sudo nginx```
### 5. Enable TLS
**_Coming _soon!**

### 6. Register user "admin" in the application at URL:31337!

## Built With
#### Frameworks:
* [Symfony 4.2](https://symfony.com/)
* [Vue 2.6](https://vuejs.org/)

#### Bundles:
* [GeniusesOfSymfony/WebSocketBundle](https://github.com/GeniusesOfSymfony/WebSocketBundle)
* [FriendsOfSymfony/FOSUserBundle](https://github.com/FriendsOfSymfony/FOSUserBundle)

## Database schema:
All tables are installed by migrations

![Alt text](readme/dbschema.png?raw=true "")

## Authors

* **Quiet.ly Team**

See also the list of [contributors](https://github.com/quiet-ly-chat/secure-chat/contributors) who participated in this project.