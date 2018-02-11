# SLEM Application Programming Interface
SLEM API is a collection of calls to enable the applications SLEM BackOffice and SLEM manage the status and the location of materials and equipment in an Enterprise firm that has more warehouses.
Here you can see a little presentation about it: 

https://www.slideshare.net/albertovolpe9/slem-status-and-location-of-materials-and-equipments

Following are the links of the source code of the apps: 

  https://github.com/TimeParadox89/SLEMapp
  
  https://github.com/TimeParadox89/SLEMApp_Backoffice
  
  https://github.com/TimeParadox89/SLEMApp-WriteToNFC
  
  
## Dependencies
There aren't particular dependencies, just note that the backend follows the .htaccess rules and DB schema that you can find in the utility folder.


## Setup

### Step 1
Edit the database.php file that is in the config folder in order to connect to your database.

### Step 2
Edit the clientSecret and clientID and the URL to your Auth Server in the index.php of the Authentication folder in order to implement OAuth Authentication with the Bearer Token.
