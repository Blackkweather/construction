import requests
from bs4 import BeautifulSoup
import os
import re

BASE_URL = "https://www.bigrentz.com/blog/types-of-construction-vehicles"
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

    # The vehicle names and images are likely in article or div elements
    # Inspecting the page structure is needed, but assuming h2 or h3 tags with images nearby

    # Find all vehicle sections
    for section in soup.find_all(['h2', 'h3']):
        name = section.get_text(strip=True)
        img = None

        # Look for next sibling image or image inside the section
        next_img = section.find_next('img')
        if next_img and next_img.has_attr('src'):
            img = next_img['src']

        if name and img:
            vehicles.append({'name': name, 'image_url': img})

    # Download images and save locally
    if not os.path.exists(UPLOADS_DIR):
        os.makedirs(UPLOADS_DIR)

    for vehicle in vehicles:
        img_url = vehicle['image_url']
        name = vehicle['name']
        ext = os.path.splitext(img_url)[1].split('?')[0] or '.jpg'
        filename = slugify(name) + ext
        filepath = os.path.join(UPLOADS_DIR, filename)

        try:
            img_data = requests.get(img_url, headers=HEADERS).content
            with open(filepath, 'wb') as f:
                f.write(img_data)
            vehicle['local_path'] = filepath
            print(f"Downloaded {name} image to {filepath}")
        except Exception as e:
            print(f"Failed to download image for {name}: {e}")
            vehicle['local_path'] = None

    # Output vehicle data for import
    for vehicle in vehicles:
        print(f"Name: {vehicle['name']}, Image Path: {vehicle.get('local_path', 'N/A')}")

if __name__ == "__main__":
    main()
