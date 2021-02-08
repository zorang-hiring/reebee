## Backend Take-Home Assignment

### Background
As a mobile app serving flyers to our customers it's imperative that we're able to serve flyers and the associated pages to our users. We also need a way for our internal teams to edit and update the flyers and pages.


### Task Definition
Your goal for this task is to create a REST API utilizing a stack including Nginx, PHP and MySQL.
This API will handle the CRUD operations for Flyers and Pages and creation of Users. 

We have provided a Docker development stack with Nginx, PHP-FPM, MySQL. Feel free to use this stack and modify it any way required.

To run the stack you can use the following command from this directory:
```
docker-compose -f docker/docker-compose.yml --env-file docker/sample.env up --build
```

Then making a request on `http://localhost:8080/` will return `{"hello":"world"}`. In `src/index.php` we've included a sample query to get the name `world` from the MySQL database.

Some useful configuration is available under the `docker` directory. Specifically the `mysql` configuration can be found under `./docker/mysql/` and we are populating the `sample_table` that we're using with the script in `./docker/mysql/data/`. Additionally the `nginx` configuration can be found in `./docker/nginx/`. For changing any php configuration you can find our `php-fpm` configuration under `./docker/php-fpm` and specifically we've defined some environment variables available to the php code in `./docker/php-fpm/php-fpm.d/env-var.conf` and passing environment variables into the container using `./docker/docker-compose.yml`.

### Requirements:
- Allow for creation of Users using the API token: `secret-token`
    - Users should have at minimum a username and password to perform Basic Authentication
- Anyone should be able to perform the Read operation for flyers and pages
    - Flyers should be retrievable by requesting all valid flyers or by flyerID
    - Pages should be retrievable by requesting all pages for a flyerID or by pageID
- Require a User to use Basic Authentication to access the Create, Update, and Delete operations
- Accept and output JSON
- Do not use any frameworks (Symfony, Laravel, Slim, Lumen, Laminas, etc)
- Feel free to use any other libraries or tools that may help you along the way

The following are definitions of the flyer and page objects that should be returned by the REST API in a read operation.
### Flyer:
  - flyerID (required) - The identifier for the flyer
    - Integer
  - name (required) - The customer's description of the flyer
    - String
  - storeName (required) - The name of the customer's business. (This is reused *a lot*).
    - String
  - dateValid (required) - The date flyer becomes valid
    - YYYY-MM-DD
  - dateExpired (required) - The date that the flyer expires
    - YYYY-MM-DD
  - pageCount (required) - The number of pages in the flyer
    - Integer
  
### Page:
  - pageID (required) - The identifier for the page
    - Integer
  - dateValid (required) - The date page becomes valid
    - YYYY-MM-DD
  - dateExpired (required) - The date that the page expires
    - YYYY-MM-DD
  - pageNumber (required) - The numeric order that the page appears in the flyer
    - Integer
    
Make sure to include all files and instructions to get the environment running on our machines and how to create our user for testing.
