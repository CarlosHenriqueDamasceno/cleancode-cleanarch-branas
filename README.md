### Subir containers

```bash
 docker compose up -d
 ```

### Rodar testes

```bash
 ./vendor/bin/phpunit
 ```

### Gerar coverage

```bash 
XDEBUG_MODE=coverage \ 
./vendor/bin/phpunit --coverage-html ./tests/coverage
```