ModelGenerator
==============

This project is a simple prototype of DomainModel and DataMapper generation from Zend_Db_Table_Abstract objects.

Please don't use this prototype in production environment : the generator himself and the models generated aren't fully tested.

Usage
-----

Simply copy the library/ and scripts/ dirs into your project's root.

Ensure you're application.ini is on application/configs/ dir, then run :

	php scripts/generateModels.php -e environment -p /path/to/generate/models

This script will :
<ul>
<li>Connect to the database.</li>
<li>List all the tables (this might causes case issues in Windows environment with MySQL).</li>
<li>Generate DbTable object for every tables.</li>
<li>Generate Model and Mapper object for every DbTable.</li>
</ul>

Type :

	php scripts/generateModels.php --help
	
For more informations.