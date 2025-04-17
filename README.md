# Install
docker-compose up -d
docker exec -it parser-app bash
composer install

# Execute language (script defined in the command itself)
bin/console app:language 

# Parse & evaluate mathematical expressions
- bin/console app:calculator "7+7*2"
- bin/console app:calculator "a*3=3" --context="a=1"
- bin/console app:calculator "true AND false"


