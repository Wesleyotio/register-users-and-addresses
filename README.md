# register-users-and-addresses
Uma simples API para registro de usuários e seus endereços

# Instruções para uso da aplicação após fazer `git clone`

1. Copie o arquivo .env.example para .env usando o comando
   
```sh
cp .env.example .env
```

2. Levante os containers docker usando 

```sh
docker-compose up -d --build
```
3. Em seguida entre no container da aplicação 

```sh
docker exec -it app bash
```
4. Agora dentro dele vamos em busca de nossas dependências 

```sh
composer install
```
5. Com elas devidamente instaladas vamos ao processamento das migrations 

```sh
 php artisan migrate
```
Agora fica a seu critério, caso queira rodar os testes execute

```sh
 ./vendor/bin/phpunit
```
Caso queira consultar a documentação para teste vai o click em
[documentação swagger](http://localhost/api/documentation).

