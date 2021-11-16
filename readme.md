# Installation 
    - cp .env.test .env
    - composer install 
    - avoir une bdd pour l'app est entrer :
      - php bin/console make:migration
      - php bin/console doctrine:migration:migrate
    - 