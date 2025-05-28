import mysql.connector
from mysql.connector import Error
from faker import Faker
import random

def create_connection():
    try:
        connection = mysql.connector.connect(
            host='localhost',
            port=3306,
            user='root',
            password='root',
            database='bibliotheque'
        )
        if connection.is_connected():
            print("Connected to MySQL database")
            return connection
    except Error as e:
        print(f"Error connecting to MySQL: {e}")
        return None

def fetch_french_books():
    # List of some French book titles and authors for seeding
    books = [
        {"title": "Le Petit Prince", "author": "Antoine de Saint-Exupéry", "category": "Fiction", "year": 1943, "isbn": "9780156013987"},
        {"title": "Les Misérables", "author": "Victor Hugo", "category": "Classique", "year": 1862, "isbn": "9782070409180"},
        {"title": "Madame Bovary", "author": "Gustave Flaubert", "category": "Roman", "year": 1856, "isbn": "9782070360427"},
        {"title": "L'Étranger", "author": "Albert Camus", "category": "Philosophie", "year": 1942, "isbn": "9782070360021"},
        {"title": "Candide", "author": "Voltaire", "category": "Philosophie", "year": 1759, "isbn": "9782070360428"},
        {"title": "Germinal", "author": "Émile Zola", "category": "Roman", "year": 1885, "isbn": "9782070360429"},
        {"title": "Le Rouge et le Noir", "author": "Stendhal", "category": "Roman", "year": 1830, "isbn": "9782070360430"},
        {"title": "La Peste", "author": "Albert Camus", "category": "Philosophie", "year": 1947, "isbn": "9782070360431"},
        {"title": "Les Fleurs du mal", "author": "Charles Baudelaire", "category": "Poésie", "year": 1857, "isbn": "9782070360432"},
        {"title": "Notre-Dame de Paris", "author": "Victor Hugo", "category": "Classique", "year": 1831, "isbn": "9782070360433"},
    ]
    return books

def get_or_create_author(cursor, author_name):
    cursor.execute("SELECT IdAuteur FROM Auteurs WHERE CONCAT(PrenomAuteur, ' ', NomAuteur) = %s", (author_name,))
    result = cursor.fetchone()
    if result:
        return result[0]
    else:
        # Split author name into first and last name (simple split)
        parts = author_name.split(' ', 1)
        prenom = parts[0]
        nom = parts[1] if len(parts) > 1 else ''
        cursor.execute("INSERT INTO Auteurs (PrenomAuteur, NomAuteur) VALUES (%s, %s)", (prenom, nom))
        return cursor.lastrowid

def get_or_create_category(cursor, category_name):
    cursor.execute("SELECT IdCategorie FROM Categories WHERE NomCategorie = %s", (category_name,))
    result = cursor.fetchone()
    if result:
        return result[0]
    else:
        cursor.execute("INSERT INTO Categories (NomCategorie) VALUES (%s)", (category_name,))
        return cursor.lastrowid

def book_exists(cursor, isbn):
    cursor.execute("SELECT 1 FROM Livres WHERE ISBN = %s", (isbn,))
    return cursor.fetchone() is not None

def insert_book(cursor, title, author_id, category_id, year, isbn):
    if book_exists(cursor, isbn):
        print(f"Book with ISBN {isbn} already exists. Skipping insertion.")
        return
    cursor.execute(
        "INSERT INTO Livres (Titre, IdAuteur, IdCategorie, AnneePublication, ISBN) VALUES (%s, %s, %s, %s, %s)",
        (title, author_id, category_id, year, isbn)
    )

def main():
    connection = create_connection()
    if not connection:
        return

    cursor = connection.cursor()

    books = fetch_french_books()
    # Insert 5 to 10 books randomly selected
    num_books = random.randint(5, 10)
    selected_books = random.sample(books, num_books)

    for book in selected_books:
        author_id = get_or_create_author(cursor, book["author"])
        category_id = get_or_create_category(cursor, book["category"])
        insert_book(cursor, book["title"], author_id, category_id, book["year"], book["isbn"])
        print(f"Inserted book: {book['title']} by {book['author']}")

    connection.commit()
    cursor.close()
    connection.close()
    print("Finished inserting books.")

if __name__ == "__main__":
    main()
