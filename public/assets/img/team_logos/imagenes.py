import os
import json
from selenium import webdriver  # type: ignore
from selenium.webdriver.common.by import By  # type: ignore
from selenium.webdriver.chrome.options import Options
import time

# Función para obtener enlaces de partidos de diferentes ligas y guardarlos en archivos JSON
def get_match_links_from_urls(urls, output_folder="match_links"):
    # Configuración del navegador para Selenium
    options = Options()
    options.headless = False  # Cambia a True para modo headless
    driver = webdriver.Chrome(options=options)

    # Crear la carpeta de salida si no existe
    if not os.path.exists(output_folder):
        os.makedirs(output_folder)

    try:
        for url in urls:
            print(f"\nAccediendo a: {url}")
            driver.get(url)
            time.sleep(5)  # Espera para permitir la carga completa de la página

            # Identificar los enlaces de los partidos en la página actual
            match_links_elements = driver.find_elements(By.CSS_SELECTOR, "a.eventRowLink")
            match_links = [link.get_attribute("href") for link in match_links_elements[:20]]  # Limita a 20 partidos

            # Extraer el nombre de la liga o torneo de la URL
            league_name = url.split('/')[-3] if url.split('/')[-1] == '' else url.split('/')[-2]

            # Crear carpeta específica para la liga
            league_folder = os.path.join(output_folder, league_name)
            if not os.path.exists(league_folder):
                os.makedirs(league_folder)

            # Guardar los enlaces en un archivo JSON
            output_file = os.path.join(league_folder, f"{league_name}_matches.json")
            with open(output_file, "w") as f:
                json.dump(match_links, f, indent=4)

            print(f"Encontrados {len(match_links)} partidos en {url}")
            print(f"Enlaces guardados en {output_file}")
    finally:
        # Cerramos el navegador
        driver.quit()

# Función para descargar imágenes de los logos de los equipos de los partidos
def download_team_logos(input_folder="match_links", output_folder="team_logos"):
    from selenium.webdriver.common.by import By

    # Configuración del navegador para Selenium
    options = Options()
    options.headless = False  # Cambia a True para modo headless
    driver = webdriver.Chrome(options=options)

    # Crear la carpeta de salida si no existe
    if not os.path.exists(output_folder):
        os.makedirs(output_folder)

    try:
        for league_folder in os.listdir(input_folder):
            league_path = os.path.join(input_folder, league_folder)
            if not os.path.isdir(league_path):
                continue

            league_output_folder = os.path.join(output_folder, league_folder)
            if not os.path.exists(league_output_folder):
                os.makedirs(league_output_folder)

            visited_teams = set()

            for filename in os.listdir(league_path):
                if not filename.endswith(".json"):
                    continue

                file_path = os.path.join(league_path, filename)
                with open(file_path, "r") as f:
                    match_links = json.load(f)

                for match_url in match_links:
                    print(f"\nAccediendo a: {match_url}")
                    driver.get(match_url)
                    time.sleep(5)  # Espera para permitir la carga completa de la página

                    # Encontrar logos de los equipos en el partido
                    team_logo_elements = driver.find_elements(By.CSS_SELECTOR, "img.participant__image")
                    for logo in team_logo_elements:
                        team_name = logo.get_attribute("alt").strip()
                        logo_url = logo.get_attribute("src").strip()

                        if team_name in visited_teams:
                            print(f"El equipo {team_name} ya fue procesado, omitiendo...")
                            continue

                        visited_teams.add(team_name)

                        # Descargar y guardar la imagen
                        logo_path = os.path.join(league_output_folder, f"{team_name}.png")
                        download_image(logo_url, logo_path)
    finally:
        driver.quit()

# Función para descargar y guardar una imagen
def download_image(url, filename):
    import requests

    try:
        response = requests.get(url, stream=True)
        if response.status_code == 200:
            with open(filename, "wb") as f:
                for chunk in response.iter_content(1024):
                    f.write(chunk)
            print(f"Imagen guardada: {filename}")
        else:
            print(f"No se pudo descargar la imagen desde {url}. Status code: {response.status_code}")
    except Exception as e:
        print(f"Error al descargar la imagen desde {url}: {e}")

if __name__ == "__main__":
    # Lista de URLs para las diferentes ligas
    urls = [
        "https://www.flashscore.com/football/spain/laliga/results/",
        "https://www.flashscore.com/football/europe/champions-league/results/",
        "https://www.flashscore.com/football/england/premier-league/results/",
        "https://www.flashscore.com/football/germany/bundesliga/results/",
        "https://www.flashscore.com/football/italy/serie-a/results/",
    ]

    # Extraemos los enlaces de los partidos y guardamos en archivos JSON
    get_match_links_from_urls(urls)

    # Descargamos los logos de los equipos
    download_team_logos()

