After cloning
1. composer install
2. dump database dump-author_book-202409241438
3. update env input with your database
database.default.hostname = localhost
database.default.database = author_book
database.default.username = root
database.default.password = 
database.default.DBDriver = MySQLi
database.default.DBPrefix =
database.default.port = 3306

CI_ENVIRONMENT = development
app.baseURL = 'http://localhost:8080'

4. how to run "PHP SPARK SERVE"
5. for testing "composer test" Using PHPunit

documentation (link for documantion postmant)
https://warped-shuttle-987802.postman.co/workspace/55b4a9e7-c55b-4cd5-886f-443bbfa9e402/documentation/21836431-e0a841d2-ab77-48cf-b4a3-df645a16e0c1


optimization
 - query just defined the main data 
 - for the controller already add pagination (but for million data its not recommended still need to put limitation for pagination)
 - add filter for specifik data like title or author name