import json
import time
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.options import Options

def scrape_match_data_from_link(match_url, output_json):
    # Configuración del navegador para Selenium
    options = Options()
    options.headless = False  # Cambia a True para modo headless
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

            # Almacenar los datos en formato estructurado
            match_data = {
                "url": match_url,
                "team_home": team_home,
                "team_away": team_away,
                "score": score,
                "match_time": match_time
            }

            print(f"Datos extraídos: {team_home} vs {team_away} - {score} ({match_time})")

            # Guardar los datos recopilados en un archivo JSON
            with open(output_json, "w") as f:
                json.dump(match_data, f, indent=4)

            print(f"Datos guardados en {output_json}")

        except Exception as e:
            print(f"Error al extraer datos de {match_url}: {e}")

    finally:
        # Cerramos el navegador
        driver.quit()

if __name__ == "__main__":
    # URL del partido a extraer
    match_url = "https://www.flashscore.com/match/Y7DQ4h0T/#/match-summary"
    # Archivo donde guardar los datos extraídos
    output_json = "scraped_match_data.json"

    # Llamar a la función para realizar el scraping
    scrape_match_data_from_link(match_url, output_json)
