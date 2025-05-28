<?php include 'header.php'; ?>

<h2>Ajouter un véhicule</h2>

<form action="process_listing.php" method="post" enctype="multipart/form-data" id="addListingForm">
    <div class="mb-3">
        <label for="title" class="form-label">Nom du véhicule</label>
        <input type="text" class="form-control" id="title" name="title" required maxlength="255" />
    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control" id="description" name="description" rows="4" required maxlength="1000"></textarea>
    </div>
    <div class="mb-3">
        <label for="vehicle_type" class="form-label">Type de véhicule</label>
        <input type="text" class="form-control" id="vehicle_type" name="vehicle_type" required />
    </div>
    <div class="mb-3">
        <label for="brand_model" class="form-label">Marque/Modèle</label>
        <input type="text" class="form-control" id="brand_model" name="brand_model" required />
    </div>
    <div class="mb-3">
        <label for="year" class="form-label">Année de fabrication</label>
        <input type="number" class="form-control" id="year" name="year" min="1900" max="2025" required />
    </div>
    <div class="mb-3">
        <label for="engine_power" class="form-label">Puissance moteur (CV)</label>
        <input type="number" class="form-control" id="engine_power" name="engine_power" required />
    </div>
    <div class="mb-3">
        <label for="fuel_type" class="form-label">Type de carburant</label>
        <select class="form-control" id="fuel_type" name="fuel_type">
            <option value="diesel">Diesel</option>
            <option value="electric">Électrique</option>
            <option value="gasoline">Essence</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="weight_capacity" class="form-label">Capacité de poids (tonnes)</label>
        <input type="number" class="form-control" id="weight_capacity" name="weight_capacity" required />
    </div>
    <div class="mb-3">
        <label for="dimensions" class="form-label">Dimensions (L x l x H en mètres)</label>
        <input type="text" class="form-control" id="dimensions" name="dimensions" required />
    </div>
    <div class="mb-3">
        <label for="price" class="form-label">Prix par jour (€)</label>
        <input type="number" step="0.01" min="0" class="form-control" id="price" name="price" required />
    </div>
    <div class="mb-3">
        <label for="features" class="form-label">Caractéristiques supplémentaires</label>
        <textarea class="form-control" id="features" name="features" rows="2" placeholder="ex : GPS, Climatisation"></textarea>
    </div>
    <div class="mb-3">
        <label for="image" class="form-label">Image</label>
        <input type="file" class="form-control" id="image" name="image" accept="image/*" required />
    </div>
    <button type="submit" class="btn btn-primary">Ajouter le véhicule</button>
</form>

<?php include 'footer.php'; ?>
