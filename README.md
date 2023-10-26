# I. Introduce
- Name: tb-ebook
- Implementer: Đào Văn Tài B19DCCN563


# II. SETUP PROJECT DIRECTLY

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

### 1. repository clone

`$ git clone https://github.com/taidao-0145/b19dccn563-ebook.git`

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
