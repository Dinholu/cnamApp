php vendor/bin/doctrine orm:convert-mapping --namespace="" --force --from-database yml ./config/yaml
php vendor/bin/doctrine orm:generate-entities --generate-annotations=false --update-entities=true --generate-methods=false ./src
composer update
