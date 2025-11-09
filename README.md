# Mini-sites des antennes AFUP

Code source pour `{antenne}.afup.org`

## Setup de dev

Il faut installer [Docker](https://www.docker.com/) et [Docker Compose](https://docs.docker.com/compose/).

Suivez ensuite ces étapes :

1. Cloner le repository
2. Lancer `make docker-start`
3. Lancer `make install`
4. Ouvrir le navigateur à cette URL : [https://localhost:8443](https://localhost:8443)

Le projet se coupe avec `make docker-stop`.

## Qualité du code

Vous pouvez lancer divers commandes pour vérifier la qualité du code :

- `make qa-test` pour lancer les tests
- `make qa-phpstan` pour lancer PHPStan
- `make qa-cs-fix` pour lancer PHP-CS-Fixer

## Contribuer

Les contributions sont les bienvenues via des issues ou des pull-requests.
