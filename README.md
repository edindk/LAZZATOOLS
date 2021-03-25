<h1>LAZZATOOLS</h1>

<details open="open">
  <summary>Indholdsfortegnelse</summary>
  <ol>
    <li>
      <a href="#om-lazzatools">Om LAZZATOOLS</a>
      <ul>
        <li><a href="#bygget-med">Bygget med</a></li>
      </ul>
    </li>
    <li>
      <a href="#opsætning-af-projektet">Ops&aelig;tning af projektet</a>
      <ul>
        <li><a href="#forudsætninger">Forudsætninger</a></li>
        <li><a href="#installering">Installering</a></li>
      </ul>
    </li>
    <li><a href="#deployment">Deployment</a></li>
  </ol>
</details>

## Om LAZZATOOLS

LAZZATOOLS er en applikation der er udviklet til LAZZAWEB, som skulle være med til at optimere virksomhedens arbejdsprocesser. Projektet består af en frontend der er udviklet i Vue.js og en backend der er udviklet i Laravel.


### Bygget med

* [Laravel](https://laravel.com/)
* [Vue.js](https://vuejs.org/)
* [Vuetify](https://vuetifyjs.com/en/)
* [CoreUI](https://coreui.io/vue/)

## Opsætning af projektet

### Forudsætninger

Det er et krav at npm og Composer er installeret. Eksekver nedenstående i terminalen.
* npm
  ```sh
  npm install npm@latest -g
  ```
* Composer
  ```sh
  php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === '756890a4488ce9024fc62c56153228907f1545c228516cbf63f885e036d37e9a59d27d63f46af1d4d07ee0f76181c7d3') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"
  ```

### Installering
1. Clone projektet. Eksekver følgende i terminalen.
```sh
git clone https://github.com/edindk/LAZZATOOLS.git
```
2. Tilgå frontend igennem terminalen.
```sh
npm install
```
3. Tilgå backend igennem terminalen.
```sh
composer install
```
4. Opdater database oplysningerne i .env filen på Backend.
```sh
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=DATABASE
DB_USERNAME=USERNAME
DB_PASSWORD=PASSWORD
```
5. Importer SQL Dump ind i databasen.
```sh
Indsæt link her!!
```

## Deployment

* Kopier indholdet af Backend og deploy det på en server.
* Opdater URL'en til API-kaldet i store.js og alle komponenterne hvor der laves et API-kald til api.lazzatools.dk på frontend.
* Tilgå frontend projektet igennem terminalen og eksekver kommandoen. Deploy indholdet af dist folderen på en server
```sh
npm run build
```
