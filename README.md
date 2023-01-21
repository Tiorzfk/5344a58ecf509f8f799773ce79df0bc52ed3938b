
<a name="readme-top"></a>

<br />
<div align="center">

  <h3 align="center">Backend</h3>

</div>



<!-- TABLE OF CONTENTS -->
<details>
  <summary>Table of Contents</summary>
  <ol>
    <li>
      <a href="#about-the-project">About The Project</a>
      <ul>
        <li><a href="#built-with">Built With</a></li>
      </ul>
    </li>
    <li>
      <a href="#getting-started">Getting Started</a>
      <ul>
        <li><a href="#prerequisites">Prerequisites</a></li>
        <li><a href="#installation">Installation</a></li>
         <li><a href="#documentation-for-api-endpoints">Documentation for API Endpoints</a></li>
      </ul>
    </li>
    <li><a href="#license">License</a></li>
    <li><a href="#contact">Contact</a></li>
  </ol>
</details>



<!-- ABOUT THE PROJECT -->
## About The Project

This code test for Backend position
<p align="right">(<a href="#readme-top">back to top</a>)</p>



### Built With

This project built with : 

* [![Php][PHP 7 >= ][https://php.net]
* [![Composer][Composer]]
* [![Postgres][Postgres]]
* [![Oauth2-Server][Oauth2-Server]][https://github.com/bshaffer/oauth2-server-php]

<p align="right">(<a href="#readme-top">back to top</a>)</p>

### Prerequisites

You will need the following software installed in your machine.
* PHP
  ```sh
  PHP 7.0 or Higher
  ```
### Installation 

1. Run composer install:
    ```composer
    composer install
    ```
2. Run docker-compose using this command:
    ```docker
    docker-compose build
    ```
    ```docker
    docker-compose up -d
    ```
3. Run Application:
     ```php
    php -S localhost:8080 -t .
    ```


<a name="documentation-for-api-endpoints"></a>
## Documentation for API Endpoints

All URIs are relative to *http://127.0.0.1:8000*

Class | Method | HTTP request | Description
------------ | ------------- | ------------- | -------------
*tokenApi* | [ **POST** | /api/token | endpoint to issued the access token
*sendMail* |  [ **POST** | /api/mail/send | param = email password 
*statusSendMail* |  [ **POST** | /api/mail/status/:id | param = id send email, endpoint for checking status sending email
*registerApi* | [ **POST** | /api/register | param = username email password first_name last_name
*loginApi* | [ **POST** | /api/login | param = email password, endpoint for verify the user and get auth code


## Flow System Design
```


                                              +------------+                   +------------+
                                              |   2. PHP   |                   | 1. Worker  |
                                              |  MAILER    |-----------------  | With Cron  |
                                              |            |                   |            |
                                              +------------+                   +============+
                                                    |
                                                    |
+------------+                                  +------------+
|            |                                  |    5       |
| 1. Client  |                                  | Database   |
|            |                                  +  PostgreSQL|
|            |                                  |            |
+------------+                                  +------------+
    |                                                |
    |                                                |
    |                                                |
+------------+          +------------+          +------------+
|            |          |     3.     |          |            |
| 2. API     |          | Middleware |          |    4.      |
| ENDPOINT   |----------| OAuth2     |          | Controller |
|            |          |   Server   |----------|  & Service |
|            |          |            |          |            |
+------------+          +------------+          +------------+

```

<!-- LICENSE -->
## License

Distributed under the MIT License. See `LICENSE.txt` for more information.

<p align="right">(<a href="#readme-top">back to top</a>)</p>