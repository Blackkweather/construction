import mysql.connector

def fetch_vehicules():
    try:
        connection = mysql.connector.connect(
            host='localhost',
            user='root',
            password='root',
            database='construction_rental'
        )
        cursor = connection.cursor()

        query = "SELECT * FROM vehicules"
        cursor.execute(query)

        rows = cursor.fetchall()
        print("Data in the `vehicules` table:")
        for row in rows:
            print(row)

    except mysql.connector.Error as err:
        print(f"Database error: {err}")

    finally:
        if connection and connection.is_connected():
            cursor.close()
            connection.close()
            print("Database connection closed.")

if __name__ == "__main__":
    fetch_vehicules()
