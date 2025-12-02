import geopandas as gpd
import os
import json
import pymysql

def convert_shp_to_geojson(input_folder, input_shp, output_geojson):
    """
    Convertit un fichier .shp en GeoJSON propre.

    :param input_folder: Dossier contenant le fichier .shp
    :param input_shp: Nom du fichier .shp (ex. 'ne_110m_admin_0_countries.shp')
    :param output_geojson: Nom du fichier GeoJSON de sortie (ex. 'world.geojson')
    """
    try:
        # Construire le chemin complet du fichier .shp
        input_path = os.path.join(input_folder, input_shp)
        
        # Charger le fichier .shp avec Geopandas
        print(f"Chargement du fichier : {input_path}")
        gdf = gpd.read_file(input_path)

        # Vérifier les colonnes disponibles
        print("Colonnes disponibles :", gdf.columns)

        # Exporter en GeoJSON
        output_path = os.path.join(input_folder, output_geojson)
        gdf.to_file(output_path, driver="GeoJSON")
        print(f"Fichier GeoJSON généré : {output_path}")
    except Exception as e:
        print(f"Erreur lors de la conversion : {e}")

def get_pays_et_regions():
    """
    Récupérer les pays et leurs régions depuis la base de données.
    """
    try:
        # Connexion à la base de données
        conn = pymysql.connect(
            host="localhost",
            user="root",
            password="",
            database="economie_mondiale"
        )
        cursor = conn.cursor(pymysql.cursors.DictCursor)
        query = "SELECT p.code_pays, p.nom_pays, r.id_region, r.nom_region FROM pays AS p JOIN regions AS r ON p.id_region = r.id_region;"
        cursor.execute(query)
        result = cursor.fetchall()
        conn.close()
        return result
    except Exception as e:
        print(f"Erreur lors de la récupération des données : {e}")
        return []

def regrouper_par_regions(input_geojson, output_geojson):
    """
    Regrouper les pays par régions dans le GeoJSON et inclure les pays dans chaque région.
    """
    try:
        # Charger le GeoJSON
        print(f"Chargement du fichier GeoJSON : {input_geojson}")
        gdf = gpd.read_file(input_geojson)

        # Récupérer les pays et leurs régions depuis la base de données
        pays_et_regions = get_pays_et_regions()

        # Créer un dictionnaire pour regrouper les géométries et les pays par région
        regions = {}
        for row in pays_et_regions:
            code_pays = row["code_pays"]
            nom_pays = row["nom_pays"]
            nom_region = row["nom_region"]

            # Filtrer les géométries correspondant au code_pays
            geometrie_pays = gdf[gdf["ADM0_A3"] == code_pays].geometry
            if not geometrie_pays.empty:
                if nom_region not in regions:
                    regions[nom_region] = {"geometry": [], "countries": []}
                regions[nom_region]["geometry"].append(geometrie_pays.values[0])
                regions[nom_region]["countries"].append(nom_pays)

        # Combiner les géométries pour chaque région
        features = []
        for nom_region, data in regions.items():
            combined_geometry = gpd.GeoSeries(data["geometry"]).unary_union
            features.append({
                "type": "Feature",
                "properties": {
                    "region": nom_region,
                    "countries": data["countries"]
                },
                "geometry": json.loads(gpd.GeoSeries([combined_geometry]).to_json())["features"][0]["geometry"]
            })

        # Créer un nouveau GeoJSON
        geojson = {
            "type": "FeatureCollection",
            "features": features
        }

        # Sauvegarder le nouveau GeoJSON
        with open(output_geojson, "w") as f:
            json.dump(geojson, f)
        print(f"Fichier GeoJSON regroupé par régions généré : {output_geojson}")

    except Exception as e:
        print(f"Erreur lors du regroupement des régions : {e}")

# Chemin des fichiers
input_folder = r"c:\xampp\htdocs\DED\public\data"
input_shp = "ne_110m_admin_0_countries.shp"
output_geojson = "world.geojson"

# Convertir le fichier .shp en GeoJSON
convert_shp_to_geojson(input_folder, input_shp, output_geojson)

input_geojson = os.path.join(input_folder, "world.geojson")
output_geojson_regions = os.path.join(input_folder, "regions.geojson")

# Regrouper les pays par régions et inclure les pays dans chaque région
regrouper_par_regions(input_geojson, output_geojson_regions)
