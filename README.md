# API Symfony 4.4 / API Platform

On veut réaliser une application de rencontres.

Dans notre API, on voudra une entité `User`.

## Création du projet, installation des dépendances

On peut créer notre API avec la commande `composer create-project symfony/skeleton my_project_name` ([documentation](https://symfony.com/doc/current/setup.html#creating-symfony-applications))

>Si vous voulez créer un projet sur une version spécifique de Symfony, ajoutez la version voulue. Par exemple, nous allons créer notre projet avec la dernière version disponible dans la version majeure n°4 :
>
>`composer create-project symfony/skeleton my_project_name 4.4.*`
>
>[Documentation](https://getcomposer.org/doc/03-cli.md#create-project)

Ensuite, on va utiliser Composer & Flex pour installer nos dépendances.

Vous pouvez trouver les packages de Symfony Flex sur [ce site](https://flex.symfony.com/).

>Certaines dépendances ne seront requises que dans l'environnement de développement (DEV). Dans ce cas, ajouter l'option `--dev` à la commande `composer require`

Liste des dépendances :

| Nom | Description | Option |
|-----|-------------|--------|
|[symfony/orm-pack](https://packagist.org/packages/symfony/orm-pack)|Tout ce qui nous permettra de discuter avec une base de données|-|
|[sensio/framework-extra-bundle](https://packagist.org/packages/sensio/framework-extra-bundle)|Cela nous permettra de configurer nos routes avec les annotations dans les contrôleurs|-|
|[doctrine/doctrine-fixtures-bundle](https://packagist.org/packages/doctrine/doctrine-fixtures-bundle)|Pour pouvoir remplir notre base de données avec des données de tests|-|
|[api-platform/api-pack](https://packagist.org/packages/api-platform/api-pack)|Pour exposer une API automatiquement à partir d'annotations dans les entités|-|
|[fzaninotto/faker](https://github.com/fzaninotto/Faker)|Pour générer des données aléatoires mais réalistes dans nos fixtures|-|
|[symfony/maker-bundle](https://packagist.org/packages/symfony/maker-bundle)|Série de commandes à utiliser dans la console pour créer des entités, des contrôleurs, un utilisateurs, etc...|`--dev`|
|[symfony/profiler-pack](https://packagist.org/packages/symfony/profiler-pack)|En mode dev, nous permettra d'accéder au détail de chaque requête dans le navigateur web|`--dev`|
|[symfony/web-server-bundle](https://packagist.org/packages/symfony/web-server-bundle)|Pour lancer un serveur en local avec la console|`--dev`|

## Création de l'entité Utilisateur

Nous allons utiliser la commande de la console `make:user` pour créer notre utilisateur. [Plus d'infos ici](https://symfony.com/blog/new-in-makerbundle-1-8-instant-user-login-form-commands#make-user).

>La différence essentielle entre la commande `make:entity` et la commande `make:user` est l'orientation donnée par `make:user` sur le fait de créer explicitement un utilisateur.
>
>La commande nous pose des questions bien plus spécifiques pour créer un utilisateur, et l'entité créée implémente l'interface `UserInterface`, qui est le type utilisé par un fournisseur (ou encore un [provider](https://symfony.com/doc/current/security/user_provider.html)).

### Structure de la table User

Une fois le squelette de l'entité User créé avec la commande `make:user`, on peut compléter l'entité avec `make:entity`.

La structure finale devrait ressembler à ça :

![Structure de la table User](docs/img/user_struct.png "Structure de la table User")
