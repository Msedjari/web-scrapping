import os
import json
from selenium import webdriver  # type: ignore
from selenium.webdriver.common.by import By  # type: ignore
from selenium.webdriver.chrome.options import Options
import time

# Función para extraer datos de cada enlace y guardarlos en un archivo JSON
def scrape_match_data_from_links(input_json, output_json):
    # Configuración del navegador para Selenium
    options = Options()
    options.headless = False  # Cambia a True para modo headless
    driver = webdriver.Chrome(options=options)

    try:
        # Leer los enlaces desde el archivo JSON
        with open(input_json, "r") as f:
            match_links = json.load(f)

        # Lista para almacenar los datos recopilados
        match_data = []

        for match_url in match_links:
            print(f"\nAccediendo a: {match_url}")
            driver.get(match_url)
            time.sleep(5)  # Espera para permitir la carga completa de la página

            try:
                # Extraer datos deseados
                team_home = driver.find_element(By.CSS_SELECTOR, "div.tname-home span.participant__participantName").text
                team_away = driver.find_element(By.CSS_SELECTOR, "div.tname-away span.participant__participantName").text
                score = driver.find_element(By.CSS_SELECTOR, "div.detailScore__wrapper").text

                # Almacenar los datos en formato estructurado
                match_data.append({
                    "url": match_url,
                    "team_home": team_home,
                    "team_away": team_away,
                    "score": score
                })
                
                print(f"Datos extraídos: {team_home} vs {team_away} - {score}")
            except Exception as e:
                print(f"Error al extraer datos de {match_url}: {e}")

        # Guardar los datos recopilados en un archivo JSON
        with open(output_json, "w") as f:
            json.dump(match_data, f, indent=4)

        print(f"Datos guardados en {output_json}")

    finally:
        # Cerramos el navegador
        driver.quit()

if __name__ == "__main__":
    # Archivos de entrada y salida
    input_json = "match_links/laliga_matches.json"  # Archivo con enlaces de los partidos
    output_json = "scraped_match_data.json"  # Archivo donde guardar los datos extraídos

    # Llamar a la función para realizar el scraping
    scrape_match_data_from_links(input_json, output_json)