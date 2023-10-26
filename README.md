# I. Introduce
Name: mathema-ebook-reader-admin

- Provide API used by Mathema Ebook app
- Provide API used by Mathema Ebook Frontend
- Provide Admin view used by system admin

# II. SETUP PROJECT VIA DOCKER
Prerequisites
- Docker
- Docker composer

---

0. setup eviromment for development
1. repository clone
2. setup env
3. build docker
4. create table for database
5. create data sample
6. confirmation of app communication

---

#### 0. setup environment for development
- Docker
- Docker composer

#### 1. repository clone

`$ git clone git@github.com:tda-corp/mathema-ebook-reader-admin.git`

### 2. setup env

- copy file .env.local in \backed folder and rename to .env
  `\backend cp .env.local .env`
- update database information of local in your .env file

```
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel_local
DB_USERNAME=phper
DB_PASSWORD=secret
```

#### 3. build docker
```
docker-compose up -d
```

### 4. create table for database

- run command migrate

```
$ docker exec -it app_1 sh
$ php artisan migrate
```

### 5. create data sample

- run command db seed

```
$ docker exec -it app_1 sh
$ php artisan db:seed
```

### 6. confirmation of app communication

```
http://localhost
```

# III. SETUP PROJECT DIRECTLY

---

0. setup eviromment for development
1. repository clone
2. library install
3. setup env
4. create table for database
5. create data sample
6. confirmation of app communication

---

### 0. setup environment for development

* For setup direct
  - PHP version 8.0
      - How to switch php on Mac [[reference]](https://suwaru.tokyo/m1-mac2021%E3%81%A7anyenv-phpenv%E3%81%AE%E5%88%9D%E6%9C%9F%E8%A8%AD%E5%AE%9A%EF%BC%81/)
  - composer 2 [[download]](https://getcomposer.org/download/)
      - Mac:`brew install composer`
  - Mysql lasted
  - IDE vscode version 1.69.0 or high (extension include sonarlint)

* For setup via docker
  - Docker
  - Docker composer

### 1. repository clone

`$ git clone git@github.com:tda-corp/mathema-ebook-reader-admin.git`

### 2. library install

1. `$ cd ./backend`
2. `$ composer install`
3. `$ npm install`

### 3. setup env

- copy file .env.local in \backed folder and rename to .env
  `\backend cp .env.local .env`
- update database information of local in your .env file

```
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel_local
DB_USERNAME=phper
DB_PASSWORD=secret
```

### 4. create table for database

- run command migrate

```
$ php artisan migrate
```

### 5. create data sample

- run command db seed

```
$ php artisan db:seed
```

### 6. confirmation of app communication

```
http://localhost
```

## Run Test

```
$ php artisan test
```

<details>
  <summary>test result</summary>

```shell
> % php artisan test

   PASS  Tests\Unit\ExampleTest
  ✓ example

   PASS  Tests\Feature\AAA
  ✓ get show aaa success
  ✓ get show aaa failed

  Tests:  3 passed
  Time:   1.15s
```

</details>

--- 
