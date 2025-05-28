-- MySQL Database Schema for Vehicle Rental Website

CREATE DATABASE IF NOT EXISTS construction_rental;
USE construction_rental;

-- Table for vehicle listings
CREATE TABLE vehicles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    vehicle_type VARCHAR(100) NOT NULL,
    brand_model VARCHAR(100) NOT NULL,
    year_of_manufacture YEAR NOT NULL,
    engine_power INT NOT NULL,
    fuel_type ENUM('diesel', 'electric', 'gasoline') NOT NULL,
    weight_capacity FLOAT NOT NULL,
    dimensions VARCHAR(50) NOT NULL,
    price_per_day DECIMAL(10, 2) NOT NULL,
    additional_features TEXT,
    image_path VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Script SQL pour la base de données de la plateforme de location de matériel de construction

-- Table des utilisateurs
CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('admin', 'client', 'locataire') NOT NULL
);

-- Table des véhicules
CREATE TABLE vehicules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    type VARCHAR(50) NOT NULL,
    prix_par_jour DECIMAL(10, 2) NOT NULL,
    disponibilite BOOLEAN DEFAULT TRUE,
    image_path VARCHAR(255),
    proprietaire_id INT,
    FOREIGN KEY (proprietaire_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
);

-- Table des locations
CREATE TABLE locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT,
    vehicule_id INT,
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    statut ENUM('en_attente', 'confirmee', 'annulee') DEFAULT 'en_attente',
    FOREIGN KEY (client_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (vehicule_id) REFERENCES vehicules(id) ON DELETE CASCADE
);

-- Table des paiements
CREATE TABLE paiements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    location_id INT,
    montant DECIMAL(10, 2) NOT NULL,
    statut ENUM('reussi', 'echoue') DEFAULT 'reussi',
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (location_id) REFERENCES locations(id) ON DELETE CASCADE
);
