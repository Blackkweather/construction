import random
from faker import Faker
import mysql.connector

def generate_random_decimal(min_value=1.0, max_value=100.0):
    return round(random.uniform(min_value, max_value), 2)

def get_valid_proprietaire_ids():
    try:
        connection = mysql.connector.connect(
            host='localhost',
            user='root',
            password='root',
            database='construction_rental'
        )
        cursor = connection.cursor()

        query = "SELECT id FROM utilisateurs"
        cursor.execute(query)
        rows = cursor.fetchall()

        return [row[0] for row in rows]

    except mysql.connector.Error as err:
        print(f"Database error: {err}")
        return []

    finally:
        if connection and connection.is_connected():
            cursor.close()
            connection.close()

def generate_fake_vehicles(num_vehicles=10, valid_proprietaire_ids=[]):
    fake = Faker('fr_FR')
    vehicles = []

    for _ in range(num_vehicles):
        vehicle = {
            'nom': fake.company(),
            'type': random.choice(['Camion', 'Excavatrice', 'Chargeuse', 'Grue']),
            'prix_par_jour': generate_random_decimal(50.0, 500.0),
            'disponibilite': random.choice([True, False]),
            'image_path': 'uploads/no_image.jpg',
            'proprietaire_id': random.choice(valid_proprietaire_ids),
            'description': fake.text(max_nb_chars=200),
            'annee': random.randint(2000, 2025),
            'puissance_moteur': random.randint(50, 500),
            'capacite_poids': generate_random_decimal(1.0, 50.0),
            'dimensions': f"{random.randint(1, 10)}x{random.randint(1, 10)}x{random.randint(1, 10)}",
        }
        vehicles.append(vehicle)

    return vehicles

def insert_fake_vehicles_into_db(vehicles):
    try:
        connection = mysql.connector.connect(
            host='localhost',
            user='root',
            password='root',
            database='construction_rental'
        )
        cursor = connection.cursor()

        query = """
        INSERT INTO vehicules (nom, type, prix_par_jour, disponibilite, image_path, proprietaire_id, description, annee, puissance_moteur, capacite_poids, dimensions)
        VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
        """

        data = [
            (
                vehicle['nom'],
                vehicle['type'],
                vehicle['prix_par_jour'],
                vehicle['disponibilite'],
                vehicle['image_path'],
                vehicle['proprietaire_id'],
                vehicle['description'],
                vehicle['annee'],
                vehicle['puissance_moteur'],
                vehicle['capacite_poids'],
                vehicle['dimensions']
            )
            for vehicle in vehicles
        ]

        cursor.executemany(query, data)
        connection.commit()
        print(f"Inserted {cursor.rowcount} fake vehicles into the database.")

    except mysql.connector.Error as err:
        print(f"Database error: {err}")

    finally:
        if connection and connection.is_connected():
            cursor.close()
            connection.close()
            print("Database connection closed.")

if __name__ == "__main__":
    print("Fetching valid proprietaire IDs...")
    valid_proprietaire_ids = get_valid_proprietaire_ids()
    if not valid_proprietaire_ids:
        print("No valid proprietaire IDs found. Please ensure the utilisateurs table has data.")
        exit()

    print("Generating fake vehicles...")
    fake_vehicles = generate_fake_vehicles(20, valid_proprietaire_ids)  # Generate 20 fake vehicles

    print("Inserting fake vehicles into the database...")
    insert_fake_vehicles_into_db(fake_vehicles)

    print("Fake vehicle generation and insertion completed.")
