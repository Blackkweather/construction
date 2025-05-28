import mysql.connector
from faker import Faker
import random

# Database connection config
db_config = {
    'host': 'localhost',
    'user': 'root',
    'password': 'root',  # Update with your DB password
    'database': 'construction_rental'  # Update with your DB name
}

def get_user_id_by_email(cursor, email):
    cursor.execute("SELECT id FROM utilisateurs WHERE email = %s", (email,))
    result = cursor.fetchone()
    return result[0] if result else None

def main():
    conn = mysql.connector.connect(**db_config)
    cursor = conn.cursor()

    user_email = 'loc@loc.com'
    user_id = get_user_id_by_email(cursor, user_email)
    if not user_id:
        print(f"User with email {user_email} not found.")
        cursor.close()
        conn.close()
        return

    fake = Faker('fr_FR')

    for i in range(20):  # Number of fake vehicles to create
        nom = fake.company() + " " + fake.word().capitalize()
        type_vehicule = random.choice(['Camion', 'Pelleteuse', 'Grue', 'Bétonnière', 'Niveleuse'])
        prix_par_jour = round(random.uniform(50, 200), 2)
        description = fake.text(max_nb_chars=200)
        annee = random.randint(2000, 2023)
        puissance_moteur = random.randint(50, 500)
        type_carburant = random.choice(['diesel', 'electric', 'gasoline'])
        capacite_poids = round(random.uniform(1.0, 20.0), 2)
        dimensions = f"{random.randint(1,10)}x{random.randint(1,10)}x{random.randint(1,10)} m"
        caracteristiques = fake.sentence(nb_words=10)
        disponibilite = True
        image_path = ''  # Could be left empty or set to a placeholder

        cursor.execute(
            "INSERT INTO vehicules (nom, type, prix_par_jour, disponibilite, proprietaire_id, image_path, description, annee, puissance_moteur, type_carburant, capacite_poids, dimensions, caracteristiques) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
            (nom, type_vehicule, prix_par_jour, disponibilite, user_id, image_path, description, annee, puissance_moteur, type_carburant, capacite_poids, dimensions, caracteristiques)
        )
        conn.commit()
        print(f"Created fake vehicle {i+1}: {nom}")

    cursor.close()
    conn.close()
    print("Fake vehicles generation for locataire completed.")

if __name__ == "__main__":
    main()
