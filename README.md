# API Symfony 5.0 / API Platform

On veut réaliser une application de rencontres.

Dans notre API, on voudra une entité `User`.

## Création du projet, installation des dépendances

On peut créer notre API avec la commande `composer create-project symfony/skeleton my_project_name` ([documentation](https://symfony.com/doc/current/setup.html#creating-symfony-applications))

>Si vous voulez créer un projet sur une version spécifique de Symfony, ajoutez la version voulue. Par exemple, nous allons créer notre projet avec la dernière version disponible dans la version majeure n°4 :
>
>`composer create-project symfony/skeleton my_project_name 4.4.*`
>
>[Documentation](https://getcomposer.org/doc/03-cli.md#create-project)

### Note sur la mise en route du serveur

#### Avant 5.0

Une fois le projet créé, on peut lancer l'application en local avec `php bin/console server:run` ou avec l'exécutable Symfony.

#### A partir de 5.0

Selon [cette note d'upgrade](https://github.com/symfony/symfony/blob/master/UPGRADE-5.0.md#webserverbundle), Il semble préférable d'utiliser [l'exécutable Symfony](https://symfony.com/download) lui-même (**Note : il est également possible de l'utiliser avant la version 5.0**) :

```bash
# Pour afficher les commandes disponibles
symfony --help

# Pour afficher la version de l'exécutable installé
symfony -v

# Pour générer et installer un certificat local sur votre machine, et utiliser votre application locale avec HTTPS
symfony server:ca:install

# Pour lancer votre serveur
symfony server:start
```

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

## Configuration de la base de données

### Si on utilise WAMP/LAMP/MAMP/XAMPP

On peut ouvrir PhpMyAdmin, créer un nouvel utilisateur, créer une base de données portant son nom et lui donner tous les privilèges sur cette base.

>**Ne pas donner de droits globaux à l'utilisateur fraîchement créé. On veut compartimenter l'utilisation du système de gestion de bases de données. Notre utilisateur ne doit donc pouvoir requêter que sur cette base de données et aucune autre**

Une fois l'utilisateur et la base de données créés, on peut renseigner les coordonnées d'accès à la base de données pour notre ORM.

>Fichier : .env.local

```env
DATABASE_URL=mysql://mon-user:mon-mot-de-passe@127.0.0.1:3306/ma-base-de-donnees?serverVersion=5.7
```

*Vous pouvez remplacer la version du serveur suivant la version de MySQL que vous utilisez.*

>On renseigne ces coordonnées dans le fichier `.env.local` car :
>
>1. Il n'est pas versionné (on ne commitera ni ne pushera aucune information confidentielle sur notre dépôt distant)
>
>2. Il est propre à chaque machine et prend la priorité sur le fichier `.env`, qui lui est versionné. On pourra donc créer un fichier `.env.local` par environnement (dev, test, prod), avec des coordonnées de base de données différentes suivant le contexte

## Création de l'entité User

Nous allons utiliser la commande de la console `make:user` pour créer notre utilisateur. [Plus d'infos ici](https://symfony.com/blog/new-in-makerbundle-1-8-instant-user-login-form-commands#make-user).

>La différence essentielle entre la commande `make:entity` et la commande `make:user` est l'orientation donnée par `make:user` sur le fait de créer explicitement un utilisateur.
>
>La commande nous pose des questions bien plus spécifiques pour créer un utilisateur, et l'entité créée implémente l'interface `UserInterface`, qui est le type utilisé par un fournisseur (ou encore un [provider](https://symfony.com/doc/current/security/user_provider.html)).

### Structure de la table User

Une fois le squelette de l'entité User créé avec la commande `make:user`, on peut compléter l'entité avec `make:entity`.

>Quand la commande `make:entity` vous demande le type de champ à créer, vous pouvez taper '?' pour avoir la liste des types disponibles

La structure finale devrait ressembler à ça :

![Structure de la table User](docs/img/user_struct.png "Structure de la table User")

### Migration

Quand on a terminé d'ajouter des champs dans notre entité, on peut créer une migration pour pousser les changements dans notre base de données.

`php bin/console make:migration`

Puis :

`php bin/console doctrine:migrations:migrate`

>Les migrations permettent de garder un historique de l'évolution de la structure de notre base de données dans le temps

## Fixtures & Faker

Nous allons maintenant remplir notre base de données avec des données de test. Pour faire ça, on va créer des [fixtures](https://symfony.com/doc/current/bundles/DoctrineFixturesBundle/index.html) et leur assigner des données aléatoires avec le package [Faker](https://github.com/fzaninotto/Faker).

Le but est de créer un utilisateur administrateur (nous) ainsi que des utilisateurs aléatoires.

### Générer un mot de passe chiffré à la volée dans la méthode de chargement des fixtures

Pour pouvoir créer un utilisateur, on doit lui définir un mot de passe.

Or, ce mot de passe doit être chiffré dans la base de données.

Pour chiffrer le mot de passe, nous allons donc avoir besoin d'un service nous permettant de le chiffrer.

La question est donc la suivante : **comment trouver le service qui nous permettra de chiffrer un mot de passe ?**

Et une autre question qui suivrait : **comment injecter ce service dans notre méthode de chargement des fixtures ?**

Dans un premier temps, nous allons explorer notre container de services à la recherche d'un service de chiffrement de mot de passe.

`php bin/console debug:container password`

>Le terme `password` permet d'effectuer une recherche dans le container de services. Quand vous cherchez un service, remplacez ce terme par un mot-clé correspondant à votre recherche.

Exemple de résultats :

```bash
Select one of the following services to display its information:
  [0] validator.not_compromised_password
  [1] security.user_password_encoder.generic
  [2] security.validator.user_password
  [3] security.command.user_password_encoder
  [4] security.password_encoder
  [5] Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface
 >
```

Entrez le numéro correspondant au service dont vous voulez voir les détails.

Dans notre exemple, on peut commencer par s'intéresser au service [1] : `security.user_password_encoder.generic`.

Les détails de ce service nous indiquent la classe `Symfony\Component\Security\Core\Encoder\UserPasswordEncoder`.

Par ailleurs, nous constatons que le service est privé (Public => no), et qu'il n'est pas autowiré.

#### L'autowiring

Selon la [documentation officielle de Symfony](https://symfony.com/doc/current/service_container/autowiring.html) :

>Autowiring allows you to manage services in the container with minimal configuration. It reads the type-hints on your constructor (or other methods) and automatically passes the correct services to each method.

L'autowiring est donc l'outil qui nous permet de **type-hinter** un service dans un constructeur ou une méthode.

En **type-hintant** un service, on va déclencher l'injection d'un objet dans notre classe.

**Nous avons donc besoin de type-hinter notre service d'encodage de mot de passe dans notre classe de fixtures.**

Avec la console, de la même manière que nous pouvons rechercher un service dans le container, nous pouvons rechercher un service autowiré :

`php bin/console debug:autowiring password`

Le résultat nous montre un type qu'il est possible de type-hinter :

```bash
php bin/console debug:autowiring password

Autowirable Types
=================

 The following classes & interfaces can be used as type-hints when autowiring:
 (only showing classes/interfaces matching password)

 UserPasswordEncoderInterface is the interface for the password encoder service.
 Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface (security.user_password_encoder.generic)
```

Nous allons donc pouvoir type-hinter ce service dans nos fixtures. A la construction d'un objet de notre classe de fixtures, l'autowiring va analyser le type inscrit dans la signature de notre constructeur, et injecter le service concret correspondant :

>Fichier : src/DataFixtures/AppFixtures.php

```php
<?php

namespace App\DataFixtures;

use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
  private $encoder;

  public function __construct(UserPasswordEncoderInterface $encoder)
  {
    $this->encoder = $encoder;
  }

  public function load(ObjectManager $manager)
  {
    $faker = Faker\Factory::create('fr_FR');

    $adminUser = new User();
    $adminUser->setActive(true)
      ->setRoles(['ROLE_ADMIN'])
      ->setBirthDate(new DateTime('1990-02-16'))
      ->setGender('Madame')
      ->setLogin('Bobby')
      ->setEmail('bobby@madmoizelle.com')
      ->setProfilePic($faker->imageUrl(150, 150))
      ->setPassword(
        $this->encoder->encodePassword(
          $adminUser,
          'Bobbybob57'
        )
      );

    $manager->persist($adminUser);

    for ($i = 0; $i < 70; $i++) {
      $user = new User();

      $login = $faker->userName;

      $user->setActive($faker->boolean(75))
        ->setBirthDate($faker->dateTimeBetween('-40 years', '-22 years'))
        ->setEmail($faker->email)
        ->setGender($faker->title())
        ->setLogin($login)
        ->setPassword($this->encoder->encodePassword(
          $user,
          'pass_' . $login
        ))
        ->setProfilePic($faker->imageUrl(150, 150));

      $manager->persist($user);
    }

    $manager->flush();
  }
}
```

Nos fixtures sont donc divisées en 2 parties : la création d'un administrateur, puis la création de plusieurs dizaines d'utilisateurs.

>Quand on crée une instance d'une entité, elle n'existe pas dans la base de données. Pour que le gestionnaire d'entités (`entitymanager`) l'intègre, il faut **persister** cet objet. C'est pourquoi on effectue autant de `persist` que de création d'objets `User`.
>
>Ensuite, pour valider les changements et insérer les nouveaux objets dans la base de données, on utilisera la méthode `flush` du gestionnaire d'entités.

Une fois le fichier enregistré, on peut charger les fixtures dans la base de données avec la commande suivante :

`php bin/console doctrine:fixtures:load`

Patientez le temps de la création et de l'enregistrement de tous les objets, puis consultez les changements effectués depuis PhpMyAdmin.

## API Platform

Nous souhaiterons réaliser un CRUD sur nos utilisateurs dans notre application ReactJS.

Avec API Platform, l'utilisation de l'annotation `@ApiResource` sur une entité permet d'exposer les endpoints nécessaires pour faire un CRUD.

On va utiliser cette annotation :

> Fichier : src/Entity/User.php

```diff
//...
use Symfony\Component\Security\Core\User\UserInterface;
+ use ApiPlatform\Core\Annotation\ApiResource;

/**
+ * @ApiResource
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
  //...
```

Automatiquement, une série de routes est exposée :

```bash
  api_entrypoint              ANY      ANY      ANY    /api/{index}.{_format}
  api_doc                     ANY      ANY      ANY    /api/docs.{_format}
  api_jsonld_context          ANY      ANY      ANY    /api/contexts/{shortName}.{_format}
  api_users_get_collection    GET      ANY      ANY    /api/users.{_format}
  api_users_post_collection   POST     ANY      ANY    /api/users.{_format}
  api_users_get_item          GET      ANY      ANY    /api/users/{id}.{_format}
  api_users_delete_item       DELETE   ANY      ANY    /api/users/{id}.{_format}
  api_users_put_item          PUT      ANY      ANY    /api/users/{id}.{_format}
  api_users_patch_item        PATCH    ANY      ANY    /api/users/{id}.{_format}
 --------------------------- -------- -------- ------ -------------------------------------
```

Par ailleurs, quand on se rend, dans le navigateur, sur **`/api`** :

![Documentation de l'API](docs/img/api_doc.png "Documentation de l'API")

La documentation de l'API a été générée automatiquement !
