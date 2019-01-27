# Install
Je n'utilise pas de docker mais le serveur interne de symfony + MAMP (BDD)
- user bdd : root
- password bdd: root

# Commandes
Pour créer un utilisateur: 
```bash
php bin/console create-admin {adresse-mail} {password}
```

Pour avoir le nombre de vidéo d'un utilisateur:
````bash
php bin/console count-videos-by-user {adresse-mail}
````