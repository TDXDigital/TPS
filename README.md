# Technical Program Schedule  
_(Previously Digital Program Logs)_

# LICENSE
[The MIT License (MIT)](http://opensource.org/licenses/MIT)

Copyright (c) 2016 James Oliver, [CKXU Radio Society](http://www.ckxu.com/development/tps)

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

# Purpose
This software is designed to suit the needs of campus and community based radio in Canada. Through logging and analysis of content played on air, reporting compliance will become simpler and streamlined

# Operations
1. Installation
  1. Requirements
  1. Tutorial
1. Setting up a station
1. Genres and Categories

# 1. Installation
Installation of this platform during the Alpha and Beta phases is to place the entire directory within a location that is available to the web server.
Configuration of the XML connection file located in the TPSBIN/XML/DBSETTINGS.xml file define the connection type to the Database server
as well as the authentication type used for user login. supported methods of Login are LDAP or LDAPS authentication CHAP or RADIUS is not supported as of current.
The database must always use a MySQL server at the current time.

## 1.1 Requirements
This program requires a Web Server (such as apache or IIS) with PHP 5.4+ installed as well as a MySQL 5.6.5+ database.
The user will use a web browser or other supported application to connect to the database. 
####The following PHP extensions are required:
* LDAP
* mcrypt
* mysqli
* mysqlnd [recommended]
* PDO
* composer

## 1.2 Tutorial
This is a general tutorial on how to setup a server, this will be a high level overview of how to configure a production server but environments may vary. For example demo.ckxu.com uses an NGINX gateway service to multiple compute nodes on the backend with a distributed filesystem and varnish cache. yet ckxu.uleth.ca utilizes a simpler but more high powered VPS. providing walkthroughs for each one would be extermely extensive so we will assume you have a fully working and basically configured server (can load apache default page).  

### 1.2.1 Useful Links:
here are some useful pages on by [Digital Ocean](https://m.do.co/c/6965f10ddbd5), you can use this [link to get a free $10 credit](https://m.do.co/c/6965f10ddbd5) toward your servers VPS servers hosted with [Digital Ocean](https://m.do.co/c/6965f10ddbd5).
* [Introduction to LEMP on Ubuntu 14.04](https://www.digitalocean.com/community/tutorial_series/introduction-to-nginx-and-lemp-on-ubuntu-14-04)
* [LAMP on Ubuntu 16.04](https://www.digitalocean.com/community/tutorials/how-to-install-linux-apache-mysql-php-lamp-stack-on-ubuntu-16-04)
* [NGINX Load Balancer](https://www.digitalocean.com/community/tutorials/how-to-set-up-nginx-load-balancing)
* [Gluster Redundant Storage Pool](https://www.digitalocean.com/community/tutorials/how-to-create-a-redundant-storage-pool-using-glusterfs-on-ubuntu-servers)
* [Serial Com. to TCP software](http://www.pira.cz/show.asp?art=piracom)
 
### 1.2.2 Walkthrough:
2. Clone from Git or download and extract an archive of TPS.
2. upload files to server if needed
2. access the server terminal (SSH)
2. navigate to the location of the webserver root directory
2. (Optional but Recommended) to customize your server configuration copy the config `./public/slimConfig.example.php` to the root directory and rename to `slimConfig.php`. i.e. `cp ./public/slimConfig.example.php ./slimConfig.php`
2. (Optional but Recommended) edit slimConfig.php and change the following parameters: 
```php
$debug = True; #enables detailed exceptions (leave set to false on production systems)
$sessionExpiry = "10minutes" # adjusts the length of time a session will be kept open. once defined duration is passed a user will have to sign in again
$temp_path = false; # can be chagned to specify a cache directory for Slim (provides speed improvements)
$sessionName = "SomeSessionName" # should be changed from default to a custom value to improve security
$sessionSecret = "SOME_HASH_VALUE" # session secret, recommend using value from [GRC Perfect Passwords](https://www.grc.com/passwords.htm)
```
2. Run `composer install --no-dev` in the root directory to install external requirements
2. open web browser and navigate to site URL. you will be automatically directed to a first time setup walkthrough. 
2. follow the in browser steps. 
2. after setup, login using the credentials you set in the Setup process. and go to Advanced -> Updates. apply any missing updates
2. in the terminal edit `CONFIG.php`, if you do not have a Broadcast Tools ACS8.2 plus switcher set the following line to prevent attempts to query the switch:
```php
$switch_enabled = False;
```
  if you do have an appropriate switch change the following line:
```php
$switch = url_or_ip_of_tcp_com
```
2. party! you have installed TPS
 

## 2.0 Setting up a(n additional) station
Most of the process is handled by the setup script but more configuration can be done on stations to enhance reporting. To change these or to add an additional station to your TPS Broadcast navigate to management -> station. you can now either edit an existing station or create a new one.

## 3.0 Genres and Categories
In TPS Broadcast there is a distinction made between Genres and Categories but they are somewhat related. 

### 3.1 Categories
Categories are content and management groups for programs, this defines the type of content that is expected of programs and groups their reporting.

### 3.2 Genres
Genres relate to musical genres but have in the past referenced program categories as well. If you notice a reference to a `program category` in the UI please submit an issue on GitHub so it can be resolved. Genres are a group that can be expanded into Government Categories (i.e. CRTC 21 - Pop, Rock, Dance) this allows reporting on the actual content played by talent (10% Category 3 etc.)

