import requests
from bs4 import BeautifulSoup
import os
import re

BASE_URL = "https://fr.freepik.com/search?format=search&last_filter=type&last_value=photo&query=construction+trucks&type=photo"
UPLOADS_DIR = "../uploads"

HEADERS = {
    "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko)"
                  " Chrome/58.0.3029.110 Safari/537.3"
}

def slugify(text):
    return re.sub(r'[^a-z0-9]+', '-', text.lower()).strip('-')

def main():
    try:
        response = requests.get(BASE_URL, headers=HEADERS)
        response.raise_for_status()
    except requests.RequestException as e:
        print(f"Failed to fetch page: {e}")
        return

    soup = BeautifulSoup(response.text, 'html.parser')

    vehicles = []

    # Find all image containers
    # Images are in <img> tags with class 'showcase__image' or similar
-    images = soup.find_all('img', class_='showcase__image')

-    if not images:
-        print("No images found on the page.")
-        return
+    images = []
+    for img in soup.find_all('img'):
+        src = img.get('src') or img.get('data-src')
+        if src and ('construction' in src.lower() or 'truck' in src.lower()):
+            images.append(img)

+    if not images:
+        print("No construction truck images found on the page.")
+        return

+    if not os.path.exists(UPLOADS_DIR):
+        os.makedirs(UPLOADS_DIR)

+    for idx, img in enumerate(images, start=1):
+        img_url = img.get('src') or img.get('data-src')
+        if not img_url:
+            continue

+        name = f"Construction Truck {idx}"
+        ext = os.path.splitext(img_url)[1].split('?')[0] or '.jpg'
+        filename = slugify(name) + ext
+        filepath = os.path.join(UPLOADS_DIR, filename)

+        try:
+            img_data = requests.get(img_url, headers=HEADERS).content
+            with open(filepath, 'wb') as f:
+                f.write(img_data)
+            vehicles.append({'name': name, 'local_path': filepath})
+            print(f"Downloaded {name} image to {filepath}")
+        except Exception as e:
+            print(f"Failed to download image for {name}: {e}")

+    # Output vehicle data for import
+    for vehicle in vehicles:
+        print(f"Name: {vehicle['name']}, Image Path: {vehicle['local_path']}")

if __name__ == "__main__":
    main()
