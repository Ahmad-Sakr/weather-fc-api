## Weather Forecast Application - Ahmad Sakr

This is a test application developed by Ahmad Sakr as a test for Omure.

## How to Run

Please run the below command to migrate the database and seed cities

<code>php artisan migrate:fresh --seed</code> 

## How to Add/Update/Remove Cities

Use the current routes: 

<code>api/v1/cities</code> **[GET]** to list all available cities

<code>api/v1/cities/{city}</code> **[GET]** to list a single city ({city} stand to the name of the city)

<code>api/v1/cities</code> **[POST]** to add new city (name is required and unique)

<code>api/v1/cities/{city}</code> **[PATCH]** to update existing city

<code>api/v1/cities/{city}</code> **[DELETE]** to delete existing city

### How Manually Test Pulling Data From Vendor API

To manually test pulling data from the 3rd party API please use the below commands: 

<code>php artisan weather:pull</code>

<code>php artisan queue:work --queue=pulling --stop-when-empty</code>

Or you can run <code>php artisan schedule:work</code> to simulate the cron jobs.

### How To Pull Weather Forecast

To pull data from our API ending point, use the following url: 

<code>api/v1/forecast?date={date}&city={city}</code> **[GET]**

- The parameter **[date]** is required and should be with 'Ymd' format (Example: 20220306)
- The parameter **[city]** is optional and should be a valid name of a city already added to database.
If not set, data will be returned for all available cities in the database.

### Unit Test

Please check the written test to test cities CRUD and pulling data (Jobs, events...).

