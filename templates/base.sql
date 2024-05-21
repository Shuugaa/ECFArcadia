CREATE TABLE utilisateur
(
    username varchar(50) NOT NULL PRIMARY KEY,
    mdp varchar(50),
    nom varchar(50),
    prenom varchar(50)
);

CREATE TABLE role
(
    role_id int NOT NULL PRIMARY KEY AUTO_INCREMENT,
    label varchar(50)
);

CREATE TABLE service
(
    service_id int NOT NULL PRIMARY KEY AUTO_INCREMENT,
    nom varchar(50),
    description varchar(50)
);

CREATE TABLE animal
(
    animal_id int NOT NULL PRIMARY KEY AUTO_INCREMENT,
    prenom varchar(50),
    etat varchar(50)
);

CREATE TABLE rapport_veterinaire
(
    rapport_veterinaire_id int NOT NULL PRIMARY KEY AUTO_INCREMENT,
    DateDuJour date,
    detail varchar(50)
);

CREATE TABLE race
(
    race_id int NOT NULL PRIMARY KEY AUTO_INCREMENT,
    label varchar(50)
);

CREATE TABLE habitat
(
    habitat_id int NOT NULL PRIMARY KEY AUTO_INCREMENT,
    nom varchar(50),
    description varchar(50),
    commentaire_habitat varchar(50)
);

CREATE TABLE avis
(
    avis_id int NOT NULL PRIMARY KEY AUTO_INCREMENT,
    pseudo varchar(50),
    commentaire varchar(50),
    isVisible boolean
);

CREATE TABLE image
(
    image_id int NOT NULL PRIMARY KEY AUTO_INCREMENT,
    image_data BLOB
);