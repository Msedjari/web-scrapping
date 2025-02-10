import os
import json
import time
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC


def scrape_match_data_from_link(match_url, output_file):
    options = Options()
    options.headless = True
    options.add_argument('--no-sandbox')
    options.add_argument('--disable-dev-shm-usage')
    options.add_argument('--disable-gpu')
    options.add_argument('--window-size=1920,1080')

    driver = webdriver.Chrome(options=options)

    try:
        driver.get(match_url)
        print(f"\nAccediendo a: {match_url}")

        try:
            # Esperar hasta que los elementos necesarios estén disponibles
            WebDriverWait(driver, 10).until(
                EC.presence_of_element_located((By.CSS_SELECTOR, "div.duelParticipant__home"))
            )
            # Extraer los datos
            team_home = driver.find_element(By.CSS_SELECTOR, "div.duelParticipant__home div.participant__participantName a").text
            team_away = driver.find_element(By.CSS_SELECTOR, "div.duelParticipant__away div.participant__participantName a").text
            score = driver.find_element(By.CSS_SELECTOR, "div.detailScore__wrapper").text
            match_time = driver.find_element(By.CSS_SELECTOR, "div.duelParticipant__startTime div").text

            score_parts = score.split("\n-\n")
            score_home = score_parts[0] if len(score_parts) > 1 else None
            score_away = score_parts[1] if len(score_parts) > 1 else None

            match_data = {
                "url": match_url,
                "team_home": team_home,
                "team_away": team_away,
                "score_home": score_home,
                "score_away": score_away,
                "match_time": match_time
            }

            print(f"Datos extraídos: {team_home} vs {team_away} - {score_home}:{score_away} ({match_time})")

            # Escribir el resultado directamente al archivo JSON
            with open(output_file, "a") as f:
                f.write(json.dumps(match_data, ensure_ascii=False) + "\n")

        except Exception as e:
            print(f"Error al extraer datos de {match_url}: {e}")
            return None

    finally:
        driver.quit()


def extract_match_urls_from_json(file_path):
    try:
        with open(file_path, 'r') as file:
            data = json.load(file)
            return data if isinstance(data, list) else []
    except Exception as e:
        print(f"Error al leer {file_path}: {e}")
        return []


if __name__ == "__main__":
    directory = 'match_links'
    for filename in os.listdir(directory):
        if filename.endswith(".json"):
            input_json_path = os.path.join(directory, filename)
            output_json_path = os.path.join(directory, f"{os.path.splitext(filename)[0]}_results.json")

            if not os.path.exists(output_json_path):
                open(output_json_path, 'w').close()  # Crear archivo vacío si no existe

            match_urls = extract_match_urls_from_json(input_json_path)

            if match_urls:
                for match_url in match_urls:
                    scrape_match_data_from_link(match_url, output_json_path)

                print(f"Resultados guardados en: {output_json_path}")
            else:
                print(f"No se encontraron URLs en el archivo: {filename}")
