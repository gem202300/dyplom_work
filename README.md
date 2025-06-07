# Laravel Livewire

Projekt prezentujący przykład aplikacji napisanej z użyciem następującego stosu technologicznego:
- Laravel 11,
- Laravel Jetstream 5,
- LiveWire 3.

# Jak uruchomić projekt

## Odtworzenie katalogu `vendor`
W katalogu projektu należy uruchomić z linii komend skrypt  (skrypt musi posiadać uprawnienia do wykonywania `chmod 755`) wykonując polecenie:

    ./laravel_install_vendor.sh

## Plik konfiguracyjny env.ini
W przypadku nie korzystania z Docker'a, plik konfiguracyjny aplikacji o nazwie `env.ini` należy stosownie zmodyfikować.

## Uruchomienie kontenerów

### Uruchamianie VS Code z poziomu kontenera aplikacji
Uruchomienie wymaga zainstalowania rozszerzenia Dev Containers dla VS Code (https://marketplace.visualstudio.com/items?itemName=ms-vscode-remote.remote-containers). Mając otworzony projekt w VS Code z poziomu WSL należy otworzyć paletę poleceń (skrót `Ctrl + Shift + P`) i uruchomić polecenie:

    DevContainers: Reopen in Container

### Uruchamianie VS Code z poziomu WSL
Jeżeli projekt został otwarty z poziomu WSL, w katalogu głównym aplikacji należy wykonać polecenie:

    sail up -d        

**UWAGA:** Aby sprawdzanie składni języka PHP działało, należy mięć zainstalowany interpreter języka PHP w środowisku uruchamianym w podsystemie WSl.

Na podstawie pliku konfiguracyjnego `docker-compose.yml` zostaną uruchomione cztery kontenery:

- kontener aplikacji (nasłuchujący na porcie `:80`),
- kontener bazy danych (nasłuchujący na porcie `:3306`),
- kontener aplikacji `phpmyadmin` (nasłuchujący na porcie `:8081`),
- kontener klienta poczty `mailpit` (nasłuchujący na porcie `:8025`).

## Wygenerowanie klucza aplikacji
Przed użyciem narzędzia szyfrującego Laravel musisz ustawić konfigurację klucza w pliku konfiguracyjnym `config/app.php`. Ta wartość konfiguracyjna jest ustawiana na podstawie zmiennej środowiskowej `APP_KEY`. Aby ją ustawić należy wykonać polecenie:

     sail artisan key:generate

## Podlinkowanie publicznego folderu `storage`
Katalog `storage/app/public` może służyć do przechowywania plików, które powinny być publicznie dostępne (np. obrazy, pliki CSS lub JS). Należy utworzyć dowiązanie symboliczne w katalogu `public/storage`, które będzie wskazywało ten katalog. Aby to zrobić należy wykonać polecenie:

    sail artisan storage:link

## Odtworzenie katalogu `node_modules`
Katalog pakietów JavaScript można odtworzyć z użyciem menadżera pakietów dla języka JavaScript - `npm`. Pakiety zdefiniowane w pliku `package.json` należy pobrać poleceniem

    sail npm install

## Odtworzenie bazy danych
Bazę danych można stworzyć i wypełnić przykładowymi danymi wykonując polecenie:

    sail artisan migrate:fresh --seed

## Uruchomienie narzędzia do budowy plików JS i CSS 
Projekt wykorzystuje narzędzie do budowy plików JS i CSS. Uruchomione w trybie deweloperskim, pozwala na bieżąco śledzić zmiany w tego typu plikach w katalogu `resources` i udostępniać aplikacji zbudowane paczki. Narzędzie uruchamiamy poleceniem:

    sail npm run dev

**UWAGA:** Nie można zamknąć konsoli!

## Praca z kontenerami (tylko z poziomu podsystemu WSL)

### Uruchomienie kontenerów
    sail up -d

### Zatrzymanie kontenerów
    sail down

### Zatrzymanie kontenerów wraz z usunięciem wolumenów
    sail down -v

**UWAGA:** Spowoduje to usunięcie bazy danych!

## Logowanie do aplikacji
Aplikacja, poza przykładowymi kontami użytkowników, posiada trzy konta testowe:

- admin.test@localhost,
- worker.test@localhost,
- user.test@localhost.

Każde konto ma ustawione hasło `12345678`.