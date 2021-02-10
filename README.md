# Reebee

## Installation

##### 1. In root directory execute 
```
cd src && php composer.phar install
```
##### 2. Go back to root directory and execute 
```
docker-compose -f docker/docker-compose.yml --env-file docker/sample.env up --build
```

## API endpoints

To be able to use API make sure that request are JSON (header "Content-Type: application/json" has to be sent with a request).

#### Create user

Use `YWRtaW46c29tZXBhc3M=` Basic Authentication Header token to be able to create users:

- POST **http://localhost:8080/users**

#### Flyers

- GET **http://localhost:8080/flyers** *(find all Active)*
- GET **http://localhost:8080/flyers/{id}** *(get one)*
- POST **http://localhost:8080/flyers** *(create one)*
- PATCH **http://localhost:8080/flyers/{id}** *(update one)*
- DELETE **http://localhost:8080/flyers/{id}** *(delete one)*

#### Pages

- GET **http://localhost:8080/flyers/{flyerID}/pages** *(get Pages of the Flyer)*
- POST **http://localhost:8080/pages** *(create one)*
- PATCH **http://localhost:8080/pages/{id}** *(update one)*
- DELETE **http://localhost:8080/pages/{id}** *(delete one)*

## Tests

PHP version > 7.1 needed. 

Run tests:
```
cd src && php composer.phar test

```

Watch tests:
```
cd src && php composer.phar watch
```
