# reebee

## Tests

PHP version > 7.1 needed. 

Run tests:
'''
cd src && php composer.phar test

'''

Watch tests:
```
cd src && php composer.phar watch
```

## todo
check and set app to utc

## API docs
[http://localhost:8080/api-ui/](http://localhost:8080/api-ui/)

## CLI

Useful commands
```
vendor/bin/doctrine orm:schema-tool:update --dump-sql
```
```
vendor/bin/doctrine orm:schema-tool:update --force
```