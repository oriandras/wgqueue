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

## Telepítési útmutató
Kövesd az alábbi lépéseket a projekt helyi vagy tesztkörnyezetben történő beállításához:

### 1. Tároló klónozása
Klónozd a projektet a Bitbucket szerverről:

```bash
git clone https://bitbucket.org/cegnev/projekt-nev.git
cd projekt-nev
```

### 2. PHP függőségek telepítése
```bash
composer install
```

### 3. Frontend függőségek telepítése és build
```bash
npm install
npm run build
```

### 4. Környezeti változók beállítása
Másold le az .env.example fájlt .env néven:

```bash
cp .env.example .env
```

Nyisd meg a .env fájlt, és állítsd be az alábbiakat:

- Adatbázis elérés: `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- Session időtartam: `SESSION_LIFETIME=10` (A biztonsági kijelentkeztetéshez)
- Levelezés: Állítsd be az SMTP adatokat vagy teszteléshez használj Mailpit-et.

### 5. Alkalmazás kulcs generálása
```bash
php artisan key:generate
```

### 6. Adatbázis migráció és feltöltés
Hozd létre a táblákat és (ha van) töltsd fel az alapértelmezett adatokkal:

```bash
php artisan migrate --seed
```

### 7. Könyvtár jogosultságok (Linux esetén)
Gondoskodj róla, hogy a webszerver írhassa a megfelelő mappákat:

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 8. Futtatás
Indítsd el a fejlesztői szervert:

```bash
php artisan serve
```
Az alkalmazás ezután elérhető a http://localhost:8000 címen.

### Néhány tipp a tesztelőknek:
> [!IMPORTANT]
> - **Időtúllépés:** A rendszer 10 perc inaktivitás után automatikusan kijelentkeztet. Ezt a .env fájl SESSION_LIFETIME változójával tudod módosítani teszteléshez.
> - **Email teszt:** A jelszó-visszaállítási és üdvözlő emailek kiküldését ellenőrizd a beállított levelező kliensben (pl. Mailpit).
> - **Online státusz:** A Dashboardon található "Jelenleg online" widget 10 másodpercenként frissül automatikusan.
