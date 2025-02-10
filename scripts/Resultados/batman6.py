import os
import json
import time
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.options import Options

def scrape_match_data_from_link(match_url):
    # Configuración del navegador para Selenium en modo sin interfaz (headless)
    options = Options()
    options.headless = True  # Ejecutar en modo sin interfaz
    options.add_argument('--no-sandbox')
    options.add_argument('--disable-dev-shm-usage')
    options.add_argument('--disable-gpu')
    options.add_argument('--window-size=1920,1080')
    driver = webdriver.Chrome(options=options)

    try:
        print(f"\nAccediendo a: {match_url}")
        driver.get(match_url)
        time.sleep(5)  # Espera para permitir la carga completa de la página

        try:
            # Extraer datos deseados utilizando selectores CSS actualizados
            team_home = driver.find_element(By.CSS_SELECTOR, "div.duelParticipant__home div.participant__participantName a").text
            team_away = driver.find_element(By.CSS_SELECTOR, "div.duelParticipant__away div.participant__participantName a").text
            score = driver.find_element(By.CSS_SELECTOR, "div.detailScore__wrapper").text
            match_time = driver.find_element(By.CSS_SELECTOR, "div.duelParticipant__startTime div").text

            # Dividir el marcador
            score_parts = score.split("\n-\n")  # Divide el marcador utilizando el formato específico
            score_home = score_parts[0] if len(score_parts) > 1 else None
            score_away = score_parts[1] if len(score_parts) > 1 else None

            # Almacenar los datos en formato estructurado
            match_data = {
                "url": match_url,
                "team_home": team_home,
                "team_away": team_away,
                "score_home": score_home,
                "score_away": score_away,
                "match_time": match_time
            }

            print(f"Datos extraídos: {team_home} vs {team_away} - {score_home}:{score_away} ({match_time})")
            return match_data

        except Exception as e:
            print(f"Error al extraer datos de {match_url}: {e}")
            return None

    finally:
        # Cerramos el navegador
        driver.quit()

def extract_match_urls_from_json(file_path):
    match_urls = []
    # Leer las URLs del archivo JSON
    with open(file_path, 'r') as file:
        data = json.load(file)
        if isinstance(data, list):
            match_urls = data
        else:
            print(f"Warning: El archivo {file_path} no contiene una lista de URLs.")
    return match_urls

if __name__ == "__main__":
    # Directorio donde se encuentran los archivos JSON con los links de los partidos
    directory = 'match_links'

    # Iteramos sobre todos los archivos JSON en el directorio
    for filename in os.listdir(directory):
        if filename.endswith(".json"):
            # Ruta del archivo de origen
            input_json_path = os.path.join(directory, filename)

            # Generar nombre del archivo de resultados
            output_json_path = os.path.join(directory, f"{os.path.splitext(filename)[0]}_results.json")

            # Leer las URLs de partidos
            match_urls = extract_match_urls_from_json(input_json_path)

            # Si existen URLs en el archivo, procesarlas
            if match_urls:
                results = []
                for match_url in match_urls:
                    match_data = scrape_match_data_from_link(match_url)
                    if match_data:
                        results.append(match_data)

                # Guardar resultados en el archivo JSON
                with open(output_json_path, "w") as f:
                    json.dump(results, f, indent=4)

                print(f"Resultados guardados en: {output_json_path}")
            else:
                print(f"No se encontraron URLs en el archivo: {filename}")
