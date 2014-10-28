# Digital Program Logs

# LICENSE
[The MIT License (MIT)](http://opensource.org/licenses/MIT)

Copyright (c) 2014 James Oliver, [CKXU Radio Society](http://www.ckxu.com/development/tps)

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
--# Requirements
2. Setting up a station
3. Genres
4. DJs and Programs
5. Reporting

# 1. Installation
Installation of this platform during the Alpha and Beta phases is to place the entire directory within a location that is available to the web server.
Configuration of the XML connection file located in the TPSBIN/XML/Settings.xml file define the connection type to the Database server
as well as the authentication type used for user login. supported methods of Login are LDAP or LDAPS authentication CHAP or RADIUS is not supported as of current.
The database must always use a MySQL server at the current time.

## 1.1 Requirements
This program requires a Web Server (such as apache or IIS) with PHP installed as well as a MySQL 5+ database.
The user will use a web browser or other supported application to connect to the database. 
