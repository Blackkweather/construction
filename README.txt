Construction Rental Website - Setup Instructions

1. Create MySQL database:
   - Database name: construction_rental
   - Run the following SQL to create the listings table:

CREATE TABLE listings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image_path VARCHAR(255) NOT NULL
);

2. Place the project folder "construction_rental_website" in your web server root (e.g., MAMP's htdocs).

3. Ensure the "uploads" directory inside the project folder is writable by the web server.

4. Access the website via your browser:
   - Homepage: http://localhost/construction_rental_website/index.php
   - Listings: http://localhost/construction_rental_website/listing.php
   - Add Listing: http://localhost/construction_rental_website/add_listing.php

5. Use the Add Listing form to add new rental listings with images.

6. Listings page will display all added listings.

7. Make sure PHP and MySQL are running on your local server (e.g., MAMP).

Enjoy!
