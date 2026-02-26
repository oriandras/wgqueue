# Követelmények

A projekt futtatásához az alábbi szoftverek és kiterjesztések szükségesek:

### Szerver és környezet
- **Webszerver:** Apache (mod_rewrite engedélyezésével) vagy Nginx
- **PHP verzió:** ^8.2
- **Composer:** ^2.0
- **Node.js:** ^18.0 (ajánlott)
- **NPM:** ^9.0
- **Adatbázis:** MySQL ^8.0 vagy MariaDB ^10.4

### PHP kiterjesztések (Extensions)
A Laravel 12 és a projekt függőségeihez szükséges kiterjesztések:
- `bcmath`
- `ctype`
- `curl`
- `fileinfo`
- `filter`
- `hash`
- `mbstring`
- `openssl`
- `pcre`
- `pdo_mysql` (vagy az alkalmazott adatbázishoz tartozó PDO driver)
- `session`
- `tokenizer`
- `xml`

### Frontend függőségek
- **Vite:** A build folyamatokhoz
- **Tailwind CSS:** Stílusok generálásához
