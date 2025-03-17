import os
import json
import time
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.options import Options

def scrape_match_data_from_link(match_url, output_json):
    # Configuración del navegador para Selenium en modo sin interfaz (headless)
    options = Options()
    options.headless = True  # Ejecutar en modo sin interfaz
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

            # Guardar los datos directamente en el archivo JSON
            with open(output_json, "a") as f:
                f.write(json.dumps(match_data, indent=4) + ",\n")  # Añadimos cada dato con una coma

        except Exception as e:
            print(f"Error al extraer datos de {match_url}: {e}")

    finally:
        # Cerramos el navegador
        driver.quit()

def extract_match_urls_from_json(directory):
    match_urls = []
    # Iteramos sobre todos los archivos .json dentro del directorio
    for filename in os.listdir(directory):
        if filename.endswith(".json"):
            with open(os.path.join(directory, filename), 'r') as file:
                data = json.load(file)
                if isinstance(data, list):
                    match_urls.extend(data)
                else:
                    print(f"Warning: El archivo {filename} no contiene una lista de URLs.")
    return match_urls

if __name__ == "__main__":
    # Directorio donde se encuentran los archivos JSON con los links de los partidos
    directory = 'match_links'

    # Extraemos los enlaces de partidos de los archivos JSON
    match_urls = extract_match_urls_from_json(directory)

    # Nombre del archivo donde guardar los resultados
    output_json = "scraped_data_all_matches.json"

    # Si el archivo existe, borra su contenido para empezar limpio
    if os.path.exists(output_json):
        os.remove(output_json)

    # Abrimos el archivo para iniciar el JSON correctamente
    with open(output_json, "a") as f:   
        f.write("[\n")  # Inicio del array JSON

    if match_urls:
        for idx, match_url in enumerate(match_urls):
            scrape_match_data_from_link(match_url, output_json)

        # Cerramos el JSON correctamente al final del proceso
        with open(output_json, "a") as f:
            f.seek(f.tell() - 2)  # Elimina la última coma extra
            f.write("\n]\n")  # Final del array JSON
    else:
        print("No se encontraron URLs de partidos en los archivos JSON.")
