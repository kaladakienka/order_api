##### Orders API

| Name   | Method      | URL                    
| ---    | ---         | ---                    
| List   | `GET`       | `/orders`              
| Place  | `POST`      | `/order`             
| Take   | `PUT`       | `/order/{id}`        

The following environment variables must be added to the .env file within the `/src` directory 

```
DB_CONNECTION=mysql
DB_HOST=orderapi_mysql
DB_PORT=3306
DB_DATABASE={your_database_name}
DB_USERNAME={your_username}
DB_PASSWORD={your_database_passord}
GOOGLE_API_KEY={your_api_key}
GOOGLE_MAPS_DISTANCE_MATRIX=https://maps.googleapis.com/maps/api/distancematrix/json
```

The `GOOGLE_API_KEY` is the api key required to make requests to the Google Maps API
The `GOOGLE_MAPS_DISTANCE_MATRIX` is the url of the Google Maps API used to fetch the distance

The following environment variables must also  be added to the .env file in the root directory
```
DB_DATABASE={your_database_name}
DB_USERNAME={your_username}
DB_PASSWORD={your_database_passord}
```

These will be used to configure the mysql container when docker is run, there is a sample 
`.env` provided in the root directory

##### Assumptions made when build the API

- Only the status is updated when "Taking an order"