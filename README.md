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

must:
- page and flyer indentifiers make guid
- get all pages - order by page number

maybe:
- make possible to update page numeric order?
- check and set app to utc
- swagger

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