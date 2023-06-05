# Itembay




## Authors

- [@Arthur Lecompte](https://www.github.com/ExoTiiKzzz)
- [@Mattis Crouzet](https://github.com/KoroSai46)
- [@Thomas Arnaud](https://github.com/radeonne)


## Tech Stack

- **Client:** Bootstrap

- **Server:** Symfony, Mercure, Doctrine, Twig




## Installation

Install our project with git, composer and symfony CLI.

```bash
    # Clone the project
    git clone https://github.com/ExoTiiKzzz/itembay.git
  
    # Go to the project directory and install dependencies
    cd itembay
    composer install
  
    # Copy .env.example to .env and edit it
    cp .env.example .env
  
    # Create the database
    php bin/console doctrine:database:create
  
    # Create the tables
    php bin/console doctrine:migrations:migrate
  
    # Load the fixtures (can take a while)
    php bin/console doctrine:fixtures:load
    
    
    # Run the server
    symfony serve --no-tls
    
    # Start mercure in a new terminal
    .\start.bat
```