import os
import mysql.connector
from faker import Faker
from datetime import datetime, timedelta
import random

# Database connection config
db_config = {
    'host': 'localhost',
    'user': 'root',
    'password': 'root',  # Update with your DB password
    'database': 'construction_rental'  # Update with your DB name
}

UPLOADS_DIR = 'uploads'

HEADERS = {
    'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'
}

def main():
    conn = mysql.connector.connect(**db_config)
    cursor = conn.cursor(dictionary=True)

    # Fetch all user IDs
    cursor.execute("SELECT id FROM utilisateurs")
    user_ids = [row['id'] for row in cursor.fetchall()]

    # Fetch all vehicle IDs and prices
    cursor.execute("SELECT id, prix_par_jour FROM vehicules")
    vehicles_data = cursor.fetchall()

    if not user_ids or not vehicles_data:
        print("No users or vehicles found to create reservations.")
        cursor.close()
        conn.close()
        return

    fake = Faker('fr_FR')

    for i in range(50):
        user_id = random.choice(user_ids)
        vehicle = random.choice(vehicles_data)
        vehicle_id = vehicle['id']
        price_per_day = vehicle['prix_par_jour']

        start_date = fake.date_between(start_date='-1y', end_date='today')
        end_date = fake.date_between(start_date=start_date, end_date=start_date + timedelta(days=30))
        days = (end_date - start_date).days + 1
        total_price = price_per_day * days
        status = random.choice(['pending', 'confirmed'])

        cursor.execute(
            "INSERT INTO reservations (user_id, vehicle_id, start_date, end_date, total_price, status) VALUES (%s, %s, %s, %s, %s, %s)",
            (user_id, vehicle_id, start_date, end_date, total_price, status)
        )
        conn.commit()
        print(f"Created fake reservation {i+1} (status: {status})")

    cursor.close()
    conn.close()
    print("Fake reservations generation completed.")

if __name__ == "__main__":
    main()
