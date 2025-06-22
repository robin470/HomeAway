HOMEAWAY Project:

SETUP STEPS:
1. Download Website folder from github
2. Locate Website folder in File Explorer or Finder
3. Xammp -- Download verison 3.3.0 from https://www.apachefriends.org/download.html
4. Locate C:\xampp\htdocs and go inside htdocs folder.
5. Delete all files inside htdocs already.
6. Place Website folder inside htdocs folder
7. Run Xammp and Click on start Apache Server
8. Check status Running on Apache Server after clicking.
9. Go to web (google chrome recommended) and type localhost and search
10. if it gives options click /website
11. Optional - Run Xammp and start mysql server then in browser go to http://localhost/phpmyadmin/index.php to run sql create tables and other necessary queries to setup the database (HOMEAWAYDB).

12. Database: 
Username: root
password: rootp

13. Please change your user name and password for database to Above information. Otherwise you will have to change the values inside code files.


Division of Work:

Robin Goswami
-Inititated Er diagram 
-Wrote query to find hotel based on city
-Connected Homepage to Property Page
-Passed homepage property id to property page


Balkarandeep Singh
- Setup  
- UI Design HomePage
- Signup Page Backed 
- Connect Homepage and LoginPage 

Sandra Le
- UI Design: Login, Signup, Property, Cart, Profile, History pages
- Implemented Property page frontend: property.php, property.css
- Implemented Profile page frontend: profile.php, profile.css
- Implemented Cart page frontend: cart.php, cart.css
- Implemented History page frontend: history.php, history.css


Chandler Lu
- Designed ER Diagram
- Migrated ER Diagram to Relational Model
- Normalized the Relational Model down to Boyce-Codd Normal Form
- Created the database in mySQL with schema based on BCNF normalization

Ziqing Jiang
- Implement property.php - Display the information of property, reservation function
- Implement profile.php - Display the user's information, add property info, review upcoming reservation
- Implement history.php - Display the user's reservation
