import os
import json

def load_leagues_data(directory_path):
    """
    Carga todos los archivos .json de un directorio y crea un diccionario
    donde cada clave es el nombre del archivo (sin extensión)
    y el valor es el contenido del JSON.
    """
    if not os.path.exists(directory_path):
        print(f"❌ Error: El directorio '{directory_path}' no existe.")
        return {}

    leagues_data = {}
    for filename in os.listdir(directory_path):
        if filename.endswith(".json"):
            league_name = os.path.splitext(filename)[0]  # p.ej. "LaLiga", "Bundesliga"
            file_path = os.path.join(directory_path, filename)

            with open(file_path, "r", encoding="utf-8") as f:
                data = json.load(f)  # data debe ser una lista de URLs u otra estructura
            leagues_data[league_name] = data
    return leagues_data

def main():
    # Asegúrate de tener la ruta correcta al directorio
    directory_path = "."  # O la ruta completa si no está en el mismo directorio
    
    # 2. Cargar datos de ligas
    leagues_data = load_leagues_data(directory_path)
    
    if not leagues_data:
        print("❌ No se cargaron datos. Asegúrate de que los archivos .json estén en la carpeta.")
        return

    # 3. Recorrer ligas y URLs
    for league, data in leagues_data.items():
        print(f"Liga: {league}")
        for url in data:
            print(f"  - URL del equipo: {url}")

if __name__ == "__main__":
    main()
